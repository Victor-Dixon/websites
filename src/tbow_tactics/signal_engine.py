"""
TBOW Tactics Signal Engine

Generates deterministic trade signals based on technical analysis.
Outputs Signal JSON (SSOT) for downstream processing.
"""

from __future__ import annotations

from dataclasses import dataclass, field
from datetime import datetime, timezone
from typing import Any, Optional, Protocol
import logging

from .models import Signal, Levels, Rules, PostConfig, Bias, PostType
from .config import (
    SETUP_CATALOG,
    VALID_TIMEFRAMES,
    VALID_CONTEXT_TAGS,
    DEFAULT_RISK_PER_TRADE_R,
)

logger = logging.getLogger(__name__)


# ═══════════════════════════════════════════════════════════════════════════
# MARKET DATA PROTOCOL
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class OHLCV:
    """Single candle data."""
    timestamp: datetime
    open: float
    high: float
    low: float
    close: float
    volume: float


class MarketDataProvider(Protocol):
    """Protocol for market data sources."""
    
    def get_candles(
        self, 
        ticker: str, 
        timeframe: str, 
        count: int
    ) -> list[OHLCV]:
        """Get historical candles."""
        ...
    
    def get_current_price(self, ticker: str) -> float:
        """Get current price."""
        ...


# ═══════════════════════════════════════════════════════════════════════════
# INDICATOR CALCULATIONS
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class Indicators:
    """Calculated indicators for signal generation."""
    vwap: float = 0.0
    sma_20: float = 0.0
    sma_50: float = 0.0
    sma_200: float = 0.0
    rsi_14: float = 50.0
    atr_14: float = 0.0
    volume_sma_20: float = 0.0
    current_volume: float = 0.0
    daily_high: float = 0.0
    daily_low: float = 0.0
    premarket_high: float = 0.0
    premarket_low: float = 0.0
    prev_day_close: float = 0.0
    pivot_point: float = 0.0
    resistance_1: float = 0.0
    support_1: float = 0.0


def calculate_sma(closes: list[float], period: int) -> float:
    """Simple Moving Average."""
    if len(closes) < period:
        return 0.0
    return sum(closes[-period:]) / period


def calculate_rsi(closes: list[float], period: int = 14) -> float:
    """Relative Strength Index."""
    if len(closes) < period + 1:
        return 50.0
    
    gains = []
    losses = []
    for i in range(1, len(closes)):
        change = closes[i] - closes[i - 1]
        gains.append(max(0, change))
        losses.append(max(0, -change))
    
    if len(gains) < period:
        return 50.0
    
    avg_gain = sum(gains[-period:]) / period
    avg_loss = sum(losses[-period:]) / period
    
    if avg_loss == 0:
        return 100.0
    
    rs = avg_gain / avg_loss
    return 100 - (100 / (1 + rs))


def calculate_atr(candles: list[OHLCV], period: int = 14) -> float:
    """Average True Range."""
    if len(candles) < period + 1:
        return 0.0
    
    true_ranges = []
    for i in range(1, len(candles)):
        high_low = candles[i].high - candles[i].low
        high_close = abs(candles[i].high - candles[i - 1].close)
        low_close = abs(candles[i].low - candles[i - 1].close)
        true_ranges.append(max(high_low, high_close, low_close))
    
    return sum(true_ranges[-period:]) / period


def calculate_vwap(candles: list[OHLCV]) -> float:
    """Volume Weighted Average Price (for current session)."""
    if not candles:
        return 0.0
    
    cumulative_tp_vol = 0.0
    cumulative_vol = 0.0
    
    for candle in candles:
        typical_price = (candle.high + candle.low + candle.close) / 3
        cumulative_tp_vol += typical_price * candle.volume
        cumulative_vol += candle.volume
    
    return cumulative_tp_vol / cumulative_vol if cumulative_vol > 0 else 0.0


def calculate_pivot_points(high: float, low: float, close: float) -> tuple[float, float, float]:
    """Calculate pivot point, R1, and S1."""
    pivot = (high + low + close) / 3
    r1 = (2 * pivot) - low
    s1 = (2 * pivot) - high
    return pivot, r1, s1


def build_indicators(candles: list[OHLCV], session_candles: list[OHLCV]) -> Indicators:
    """Build all indicators from candle data."""
    if not candles:
        return Indicators()
    
    closes = [c.close for c in candles]
    volumes = [c.volume for c in candles]
    
    # Previous day data (last candle from daily)
    prev_close = candles[-2].close if len(candles) > 1 else candles[-1].close
    prev_high = candles[-2].high if len(candles) > 1 else candles[-1].high
    prev_low = candles[-2].low if len(candles) > 1 else candles[-1].low
    
    pivot, r1, s1 = calculate_pivot_points(prev_high, prev_low, prev_close)
    
    return Indicators(
        vwap=calculate_vwap(session_candles) if session_candles else 0.0,
        sma_20=calculate_sma(closes, 20),
        sma_50=calculate_sma(closes, 50),
        sma_200=calculate_sma(closes, 200),
        rsi_14=calculate_rsi(closes, 14),
        atr_14=calculate_atr(candles, 14),
        volume_sma_20=calculate_sma(volumes, 20),
        current_volume=volumes[-1] if volumes else 0.0,
        daily_high=max(c.high for c in session_candles) if session_candles else 0.0,
        daily_low=min(c.low for c in session_candles) if session_candles else 0.0,
        prev_day_close=prev_close,
        pivot_point=pivot,
        resistance_1=r1,
        support_1=s1,
    )


# ═══════════════════════════════════════════════════════════════════════════
# SETUP DETECTION
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class SetupDetection:
    """Result of setup detection."""
    detected: bool
    setup_name: str
    bias: Bias
    entry_zone: tuple[float, float]
    invalidation: float
    targets: list[float]
    trigger: str
    confidence: float  # 0.0 - 1.0
    context_tags: list[str] = field(default_factory=list)


class SetupDetector:
    """Detects TBOW setups from price action and indicators."""
    
    def __init__(self, atr_multiplier: float = 1.5):
        self.atr_multiplier = atr_multiplier
    
    def detect_vwap_reclaim(
        self,
        current_price: float,
        indicators: Indicators,
        candles: list[OHLCV],
    ) -> Optional[SetupDetection]:
        """Detect VWAP reclaim continuation setup."""
        if not candles or indicators.vwap == 0:
            return None
        
        # Price must be near or just above VWAP
        vwap_distance = (current_price - indicators.vwap) / indicators.vwap
        
        # Conditions for VWAP reclaim
        if not (0 <= vwap_distance <= 0.005):  # Within 0.5% above VWAP
            return None
        
        # Volume confirmation
        volume_strong = indicators.current_volume > indicators.volume_sma_20 * 1.2
        
        # Above 50 SMA
        above_sma50 = current_price > indicators.sma_50 if indicators.sma_50 > 0 else True
        
        if not volume_strong:
            return None
        
        atr = indicators.atr_14
        entry_low = indicators.vwap
        entry_high = indicators.vwap + (atr * 0.3)
        invalidation = indicators.vwap - (atr * 0.5)
        
        targets = [
            current_price + atr,
            current_price + (atr * 2),
            current_price + (atr * 3),
        ]
        
        context = ["HTF_support", "volume_high"]
        if above_sma50:
            context.append("above_50SMA")
        if indicators.atr_14 > indicators.atr_14 * 1.2:  # Simplified volatility check
            context.append("volatility_high")
        
        return SetupDetection(
            detected=True,
            setup_name="VWAP_Reclaim_Continuation",
            bias=Bias.BULLISH,
            entry_zone=(entry_low, entry_high),
            invalidation=invalidation,
            targets=targets,
            trigger="break_and_hold_above_entry_zone",
            confidence=0.7 if above_sma50 else 0.5,
            context_tags=context,
        )
    
    def detect_htf_support_bounce(
        self,
        current_price: float,
        indicators: Indicators,
        candles: list[OHLCV],
        support_level: float,
    ) -> Optional[SetupDetection]:
        """Detect bounce from higher timeframe support."""
        if not candles or support_level == 0:
            return None
        
        # Price must be near support
        distance_to_support = (current_price - support_level) / support_level
        
        if not (0 <= distance_to_support <= 0.01):  # Within 1% of support
            return None
        
        atr = indicators.atr_14
        entry_low = support_level
        entry_high = support_level + (atr * 0.3)
        invalidation = support_level - (atr * 0.5)
        
        targets = [
            current_price + atr,
            current_price + (atr * 1.5),
            indicators.resistance_1 if indicators.resistance_1 > current_price else current_price + (atr * 2),
        ]
        
        context = ["HTF_support"]
        if current_price > indicators.sma_50:
            context.append("above_50SMA")
        
        return SetupDetection(
            detected=True,
            setup_name="HTF_Support_Bounce",
            bias=Bias.BULLISH,
            entry_zone=(entry_low, entry_high),
            invalidation=invalidation,
            targets=targets,
            trigger="hold_above_support_with_reversal_candle",
            confidence=0.65,
            context_tags=context,
        )
    
    def detect_breakout(
        self,
        current_price: float,
        indicators: Indicators,
        candles: list[OHLCV],
        breakout_level: float,
    ) -> Optional[SetupDetection]:
        """Detect breakout continuation setup."""
        if not candles or breakout_level == 0:
            return None
        
        # Price must be just above breakout level
        above_breakout = (current_price - breakout_level) / breakout_level
        
        if not (0 < above_breakout <= 0.02):  # Just broke out, within 2%
            return None
        
        # Volume confirmation
        volume_strong = indicators.current_volume > indicators.volume_sma_20 * 1.5
        if not volume_strong:
            return None
        
        atr = indicators.atr_14
        entry_low = breakout_level
        entry_high = breakout_level + (atr * 0.5)
        invalidation = breakout_level - (atr * 0.3)
        
        targets = [
            current_price + atr,
            current_price + (atr * 2),
            current_price + (atr * 3),
        ]
        
        return SetupDetection(
            detected=True,
            setup_name="Breakout_Continuation",
            bias=Bias.BULLISH,
            entry_zone=(entry_low, entry_high),
            invalidation=invalidation,
            targets=targets,
            trigger="breakout_confirmed_with_volume",
            confidence=0.6,
            context_tags=["volume_high", "trending_up"],
        )
    
    def detect_all(
        self,
        current_price: float,
        indicators: Indicators,
        candles: list[OHLCV],
        key_levels: Optional[dict[str, float]] = None,
    ) -> list[SetupDetection]:
        """Run all setup detectors and return matches."""
        detections = []
        key_levels = key_levels or {}
        
        # VWAP Reclaim
        vwap = self.detect_vwap_reclaim(current_price, indicators, candles)
        if vwap:
            detections.append(vwap)
        
        # HTF Support Bounce
        support = key_levels.get("support", indicators.support_1)
        if support:
            bounce = self.detect_htf_support_bounce(current_price, indicators, candles, support)
            if bounce:
                detections.append(bounce)
        
        # Breakout
        resistance = key_levels.get("resistance", indicators.resistance_1)
        if resistance:
            breakout = self.detect_breakout(current_price, indicators, candles, resistance)
            if breakout:
                detections.append(breakout)
        
        # Sort by confidence
        detections.sort(key=lambda x: x.confidence, reverse=True)
        
        return detections


# ═══════════════════════════════════════════════════════════════════════════
# SIGNAL ENGINE
# ═══════════════════════════════════════════════════════════════════════════

class SignalEngine:
    """
    Main signal engine that generates trade signals.
    
    Inputs: OHLCV data, indicators
    Outputs: Signal JSON (SSOT)
    """
    
    def __init__(
        self,
        data_provider: Optional[MarketDataProvider] = None,
        risk_per_trade_r: float = DEFAULT_RISK_PER_TRADE_R,
        max_trades_per_day: int = 3,
    ):
        self.data_provider = data_provider
        self.risk_per_trade_r = risk_per_trade_r
        self.max_trades_per_day = max_trades_per_day
        self.detector = SetupDetector()
    
    def generate_signal(
        self,
        ticker: str,
        timeframe: str,
        candles: list[OHLCV],
        session_candles: Optional[list[OHLCV]] = None,
        current_price: Optional[float] = None,
        key_levels: Optional[dict[str, float]] = None,
    ) -> Optional[Signal]:
        """
        Generate a trade signal from market data.
        
        Args:
            ticker: Stock symbol
            timeframe: Chart timeframe (1m, 5m, 15m, 1H, 4H, D)
            candles: Historical candles for indicator calculation
            session_candles: Current session candles for VWAP
            current_price: Current market price (uses last candle close if not provided)
            key_levels: Dict of key support/resistance levels
        
        Returns:
            Signal object if setup detected, None otherwise
        """
        if timeframe not in VALID_TIMEFRAMES:
            logger.warning(f"Invalid timeframe: {timeframe}")
            return None
        
        if not candles:
            logger.warning("No candles provided")
            return None
        
        # Use session candles for VWAP or default to all candles
        session = session_candles or candles[-20:]  # Last 20 candles as proxy
        
        # Build indicators
        indicators = build_indicators(candles, session)
        
        # Get current price
        price = current_price or candles[-1].close
        
        # Detect setups
        detections = self.detector.detect_all(price, indicators, candles, key_levels)
        
        if not detections:
            logger.debug(f"No setups detected for {ticker} on {timeframe}")
            return None
        
        # Take best detection
        best = detections[0]
        
        # Build signal
        signal = Signal(
            ticker=ticker.upper(),
            timeframe=timeframe,
            timestamp_utc=datetime.now(timezone.utc).isoformat(),
            setup=best.setup_name,
            bias=best.bias,
            levels=Levels(
                entry_zone=best.entry_zone,
                invalidation=best.invalidation,
                targets=best.targets,
            ),
            rules=Rules(
                trigger=best.trigger,
                risk_per_trade_r=self.risk_per_trade_r,
                max_trades_today=self.max_trades_per_day,
            ),
            context=[tag for tag in best.context_tags if tag in VALID_CONTEXT_TAGS],
            post=PostConfig(include_chart=True, post_type=PostType.PLAN),
        )
        
        logger.info(f"Signal generated: {signal.plan_id} - {best.setup_name}")
        return signal
    
    def generate_from_provider(
        self,
        ticker: str,
        timeframe: str,
        candle_count: int = 200,
        key_levels: Optional[dict[str, float]] = None,
    ) -> Optional[Signal]:
        """Generate signal using the configured data provider."""
        if not self.data_provider:
            raise ValueError("No data provider configured")
        
        candles = self.data_provider.get_candles(ticker, timeframe, candle_count)
        current_price = self.data_provider.get_current_price(ticker)
        
        return self.generate_signal(
            ticker=ticker,
            timeframe=timeframe,
            candles=candles,
            current_price=current_price,
            key_levels=key_levels,
        )
    
    def generate_manual_signal(
        self,
        ticker: str,
        timeframe: str,
        setup: str,
        bias: Bias,
        entry_zone: tuple[float, float],
        invalidation: float,
        targets: list[float],
        trigger: str = "break_and_hold_above_entry_zone",
        context: Optional[list[str]] = None,
    ) -> Signal:
        """
        Generate a manual signal for when you spot a setup yourself.
        
        This is useful for Phase 0 (draft-only) when you're manually
        identifying setups and want to formalize them.
        """
        if setup not in SETUP_CATALOG:
            logger.warning(f"Setup {setup} not in catalog, using anyway")
        
        signal = Signal(
            ticker=ticker.upper(),
            timeframe=timeframe,
            timestamp_utc=datetime.now(timezone.utc).isoformat(),
            setup=setup,
            bias=bias,
            levels=Levels(
                entry_zone=entry_zone,
                invalidation=invalidation,
                targets=targets,
            ),
            rules=Rules(
                trigger=trigger,
                risk_per_trade_r=self.risk_per_trade_r,
                max_trades_today=self.max_trades_per_day,
            ),
            context=context or [],
            post=PostConfig(include_chart=True, post_type=PostType.PLAN),
        )
        
        logger.info(f"Manual signal created: {signal.plan_id}")
        return signal
