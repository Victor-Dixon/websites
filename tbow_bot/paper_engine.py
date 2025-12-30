"""
TBOW Bot Paper Trade Engine

Converts signals into paper trades with P&L calculations.

Position model (v1 - underlying):
- One position at a time
- Entry on CALL/PUT entry event
- Exit on matching exit event
- P&L calculated on underlying price movement

v2 (delta-proxy):
- option_pnl ≈ underlying_move * (delta * 100) - friction
"""

from __future__ import annotations

import logging
from dataclasses import dataclass
from datetime import datetime, date, time, timezone, timedelta
from typing import Optional
from pathlib import Path

from .db import (
    Signal, Trade,
    get_signals_for_date, get_all_signals,
    save_trade, get_trades_for_date, clear_trades_for_date,
    DailyStats, save_daily_stats,
)
from .config import (
    Config,
    IGNORE_FIRST_MINUTES,
    ENTRY_CUTOFF_HOUR,
    ENTRY_CUTOFF_MINUTE,
    MARKET_OPEN_HOUR,
    MARKET_OPEN_MINUTE,
    DEFAULT_DELTA,
    OPTION_MULTIPLIER,
    PER_TRADE_FRICTION,
    POSITION_NOTIONAL,
)

logger = logging.getLogger("tbow_bot.paper_engine")


# ═══════════════════════════════════════════════════════════════════════════
# TIME HELPERS
# ═══════════════════════════════════════════════════════════════════════════

def parse_timestamp(ts: str) -> datetime:
    """Parse ISO timestamp, handling various formats."""
    try:
        # Try ISO format with timezone
        if "+" in ts or "Z" in ts:
            return datetime.fromisoformat(ts.replace("Z", "+00:00"))
        # Assume UTC if no timezone
        return datetime.fromisoformat(ts).replace(tzinfo=timezone.utc)
    except ValueError:
        # Fallback: try common formats
        for fmt in ["%Y-%m-%d %H:%M:%S", "%Y-%m-%dT%H:%M:%S", "%Y-%m-%d"]:
            try:
                return datetime.strptime(ts, fmt).replace(tzinfo=timezone.utc)
            except ValueError:
                continue
        # Last resort
        return datetime.now(timezone.utc)


def is_within_trading_window(
    ts: datetime,
    ignore_first_minutes: int = IGNORE_FIRST_MINUTES,
    cutoff_hour: int = ENTRY_CUTOFF_HOUR,
    cutoff_minute: int = ENTRY_CUTOFF_MINUTE,
) -> bool:
    """
    Check if timestamp is within valid trading window.
    
    Filters:
    - First N minutes after market open
    - After entry cutoff time
    """
    # Convert to Eastern Time (simplified: UTC-5)
    # In production, use pytz for proper DST handling
    et_offset = timedelta(hours=-5)
    et_time = (ts + et_offset).time()
    
    # Market open time
    market_open = time(MARKET_OPEN_HOUR, MARKET_OPEN_MINUTE)
    
    # Ignore first N minutes
    ignore_until = time(
        MARKET_OPEN_HOUR,
        MARKET_OPEN_MINUTE + ignore_first_minutes
    )
    if et_time < ignore_until:
        return False
    
    # Entry cutoff
    entry_cutoff = time(cutoff_hour, cutoff_minute)
    if et_time > entry_cutoff:
        return False
    
    return True


# ═══════════════════════════════════════════════════════════════════════════
# SIGNAL PARSING
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class ParsedEvent:
    """Parsed signal event."""
    is_entry: bool
    is_exit: bool
    side: str  # "CALL" or "PUT"
    
    @classmethod
    def from_event(cls, event: str) -> "ParsedEvent":
        """Parse event string into structured data."""
        e = event.upper()
        
        is_call = "CALL" in e
        is_put = "PUT" in e
        is_entry = "ENTRY" in e
        is_exit = "EXIT" in e
        
        side = "CALL" if is_call else "PUT" if is_put else "UNKNOWN"
        
        return cls(
            is_entry=is_entry and not is_exit,
            is_exit=is_exit,
            side=side,
        )


# ═══════════════════════════════════════════════════════════════════════════
# P&L CALCULATIONS
# ═══════════════════════════════════════════════════════════════════════════

def calculate_underlying_pnl(
    side: str,
    entry_price: float,
    exit_price: float,
    notional: float = POSITION_NOTIONAL,
) -> float:
    """
    Calculate P&L based on underlying price movement.
    
    For CALL: profit when price goes up
    For PUT: profit when price goes down
    
    Returns dollar P&L for the notional position.
    """
    shares = notional / entry_price
    
    if side == "CALL":
        pnl = (exit_price - entry_price) * shares
    else:  # PUT
        pnl = (entry_price - exit_price) * shares
    
    return round(pnl, 2)


def calculate_option_pnl(
    underlying_pnl: float,
    delta: float = DEFAULT_DELTA,
    multiplier: int = OPTION_MULTIPLIER,
    friction: float = PER_TRADE_FRICTION,
) -> float:
    """
    Calculate option P&L using delta proxy (v2).
    
    option_pnl ≈ underlying_move * (delta * 100) - friction
    
    This is a simplified model assuming:
    - ATM 7DTE options
    - Constant delta (ignores gamma, theta, vega)
    """
    # The underlying P&L already accounts for shares
    # For options, we scale by delta
    option_pnl = underlying_pnl * delta - friction
    
    return round(option_pnl, 2)


# ═══════════════════════════════════════════════════════════════════════════
# PAPER TRADE ENGINE
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class EngineResult:
    """Result of processing signals."""
    trades: list[Trade]
    signals_processed: int
    signals_filtered: int
    stats: DailyStats


class PaperTradeEngine:
    """
    Processes signals and generates paper trades.
    
    Rules:
    - One position at a time
    - Entry on CALL/PUT entry event
    - Exit on matching exit event (CALL exit for CALL, PUT exit for PUT)
    - Filters applied for trading window
    """
    
    def __init__(
        self,
        config: Optional[Config] = None,
        use_option_proxy: bool = False,  # v2
    ):
        self.config = config or Config.from_env()
        self.use_option_proxy = use_option_proxy
    
    def process_signals(
        self,
        signals: list[Signal],
        trade_date: str,
        symbol: str,
    ) -> list[Trade]:
        """
        Process a list of signals and generate trades.
        
        Returns list of completed trades.
        """
        trades: list[Trade] = []
        current_position: Optional[dict] = None
        
        for signal in signals:
            ts = parse_timestamp(signal.ts)
            event = ParsedEvent.from_event(signal.event)
            
            # Skip unknown events
            if event.side == "UNKNOWN":
                continue
            
            # No position open - look for entry
            if current_position is None:
                if event.is_entry:
                    # Check trading window for entries
                    if not is_within_trading_window(ts):
                        logger.debug(f"Filtered entry outside window: {signal.event} @ {ts}")
                        continue
                    
                    current_position = {
                        "side": event.side,
                        "entry_ts": signal.ts,
                        "entry_price": signal.price,
                    }
                    logger.info(
                        f"Opened {event.side} @ {signal.price:.2f}"
                    )
                continue
            
            # Position open - look for matching exit
            if event.is_exit and event.side == current_position["side"]:
                # Calculate P&L
                pnl_underlying = calculate_underlying_pnl(
                    side=current_position["side"],
                    entry_price=current_position["entry_price"],
                    exit_price=signal.price,
                    notional=self.config.position_notional,
                )
                
                pnl_option = None
                if self.use_option_proxy:
                    pnl_option = calculate_option_pnl(
                        underlying_pnl=pnl_underlying,
                        delta=self.config.default_delta,
                        friction=self.config.per_trade_friction,
                    )
                
                trade = Trade(
                    id=None,
                    trade_date=trade_date,
                    symbol=symbol,
                    side=current_position["side"],
                    entry_ts=current_position["entry_ts"],
                    entry_price=current_position["entry_price"],
                    exit_ts=signal.ts,
                    exit_price=signal.price,
                    pnl_underlying=pnl_underlying,
                    pnl_option=pnl_option,
                    status="closed",
                )
                
                trades.append(trade)
                
                logger.info(
                    f"Closed {trade.side} @ {signal.price:.2f} | "
                    f"P&L: ${pnl_underlying:+.2f}"
                )
                
                current_position = None
        
        return trades
    
    def process_date(
        self,
        target_date: date,
        symbol: str = "TSLA",
        reprocess: bool = False,
        db_path: Optional[Path] = None,
    ) -> EngineResult:
        """
        Process all signals for a specific date.
        
        Args:
            target_date: Date to process
            symbol: Symbol to filter signals
            reprocess: If True, clear existing trades and reprocess
            db_path: Optional database path
        
        Returns:
            EngineResult with trades and stats
        """
        date_str = target_date.strftime("%Y-%m-%d")
        
        # Clear existing trades if reprocessing
        if reprocess:
            deleted = clear_trades_for_date(target_date, db_path)
            if deleted:
                logger.info(f"Cleared {deleted} existing trades for {date_str}")
        
        # Get signals for date
        signals = get_signals_for_date(target_date, symbol, db_path)
        
        if not signals:
            logger.info(f"No signals for {symbol} on {date_str}")
            return EngineResult(
                trades=[],
                signals_processed=0,
                signals_filtered=0,
                stats=self._compute_stats(date_str, [], len(signals)),
            )
        
        logger.info(f"Processing {len(signals)} signals for {symbol} on {date_str}")
        
        # Process signals
        trades = self.process_signals(signals, date_str, symbol)
        
        # Save trades
        for trade in trades:
            save_trade(trade, db_path)
        
        # Compute and save stats
        signals_filtered = len(signals) - sum(
            1 for s in signals 
            if ParsedEvent.from_event(s.event).is_entry or ParsedEvent.from_event(s.event).is_exit
        )
        
        stats = self._compute_stats(date_str, trades, len(signals), signals_filtered)
        save_daily_stats(stats, db_path)
        
        logger.info(
            f"Completed: {len(trades)} trades | "
            f"P&L: ${stats.total_pnl:+.2f} | "
            f"Win Rate: {stats.win_rate:.0f}%"
        )
        
        return EngineResult(
            trades=trades,
            signals_processed=len(signals),
            signals_filtered=signals_filtered,
            stats=stats,
        )
    
    def _compute_stats(
        self,
        date_str: str,
        trades: list[Trade],
        signals_received: int,
        signals_filtered: int = 0,
    ) -> DailyStats:
        """Compute daily statistics from trades."""
        if not trades:
            return DailyStats(
                stat_date=date_str,
                total_trades=0,
                wins=0,
                losses=0,
                total_pnl=0,
                win_rate=0,
                avg_win=None,
                avg_loss=None,
                max_drawdown=None,
                signals_received=signals_received,
                signals_filtered=signals_filtered,
            )
        
        wins = [t for t in trades if t.is_winner]
        losses = [t for t in trades if not t.is_winner]
        
        total_pnl = sum(t.pnl_underlying or 0 for t in trades)
        
        avg_win = None
        if wins:
            avg_win = sum(t.pnl_underlying or 0 for t in wins) / len(wins)
        
        avg_loss = None
        if losses:
            avg_loss = sum(t.pnl_underlying or 0 for t in losses) / len(losses)
        
        # Calculate max drawdown (cumulative)
        cumulative = 0
        peak = 0
        max_dd = 0
        for t in trades:
            cumulative += t.pnl_underlying or 0
            peak = max(peak, cumulative)
            dd = peak - cumulative
            max_dd = max(max_dd, dd)
        
        win_rate = len(wins) / len(trades) * 100 if trades else 0
        
        return DailyStats(
            stat_date=date_str,
            total_trades=len(trades),
            wins=len(wins),
            losses=len(losses),
            total_pnl=round(total_pnl, 2),
            win_rate=round(win_rate, 1),
            avg_win=round(avg_win, 2) if avg_win else None,
            avg_loss=round(avg_loss, 2) if avg_loss else None,
            max_drawdown=round(max_dd, 2) if max_dd else None,
            signals_received=signals_received,
            signals_filtered=signals_filtered,
        )


# ═══════════════════════════════════════════════════════════════════════════
# CONVENIENCE FUNCTIONS
# ═══════════════════════════════════════════════════════════════════════════

def process_today(
    symbol: str = "TSLA",
    reprocess: bool = False,
) -> EngineResult:
    """Process signals for today."""
    engine = PaperTradeEngine()
    today = date.today()
    return engine.process_date(today, symbol, reprocess)


def process_date_range(
    start_date: date,
    end_date: date,
    symbol: str = "TSLA",
    reprocess: bool = False,
) -> list[EngineResult]:
    """Process signals for a date range."""
    engine = PaperTradeEngine()
    results = []
    
    current = start_date
    while current <= end_date:
        result = engine.process_date(current, symbol, reprocess)
        results.append(result)
        current += timedelta(days=1)
    
    return results
