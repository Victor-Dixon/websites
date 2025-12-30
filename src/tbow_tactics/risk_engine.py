"""
TBOW Tactics Risk Engine

Validates signals and filters out bad trades.
Your edge protection layer.
"""

from __future__ import annotations

from dataclasses import dataclass
from datetime import datetime, timezone
from typing import Optional
from enum import Enum
import logging

from .models import Signal, Bias
from .config import (
    MAX_RISK_PER_TRADE_R,
    MIN_RISK_PER_TRADE_R,
    VALID_TIMEFRAMES,
    REQUIRED_PLAN_FIELDS,
)

logger = logging.getLogger(__name__)


class RejectionReason(str, Enum):
    """Reasons for rejecting a trade signal."""
    MISSING_FIELDS = "missing_required_fields"
    INVALID_TIMEFRAME = "invalid_timeframe"
    STOP_TOO_WIDE = "stop_distance_too_wide"
    STOP_TOO_TIGHT = "stop_distance_too_tight"
    RISK_TOO_HIGH = "risk_per_trade_too_high"
    RISK_TOO_LOW = "risk_per_trade_too_low"
    NO_TARGETS = "no_targets_defined"
    NEGATIVE_RR = "negative_risk_reward"
    ENTRY_ZONE_INVALID = "entry_zone_invalid"
    INVALIDATION_INSIDE_ZONE = "invalidation_inside_entry_zone"
    MAX_TRADES_EXCEEDED = "max_daily_trades_exceeded"
    CHOP_DETECTED = "choppy_market_conditions"
    NEWS_WINDOW = "major_news_window"
    LOW_LIQUIDITY = "low_liquidity"
    OUTSIDE_HOURS = "outside_trading_hours"


@dataclass
class ValidationResult:
    """Result of signal validation."""
    valid: bool
    signal: Optional[Signal] = None
    rejection_reasons: list[RejectionReason] | None = None
    warnings: list[str] | None = None
    adjusted_risk_r: Optional[float] = None
    
    @property
    def passed(self) -> bool:
        return self.valid
    
    def __bool__(self) -> bool:
        return self.valid


@dataclass
class RiskLimits:
    """Configurable risk limits."""
    max_risk_per_trade_r: float = MAX_RISK_PER_TRADE_R
    min_risk_per_trade_r: float = MIN_RISK_PER_TRADE_R
    max_stop_atr_multiple: float = 3.0  # Max stop distance as ATR multiple
    min_stop_atr_multiple: float = 0.3  # Min stop distance as ATR multiple
    min_risk_reward_t1: float = 1.0  # Minimum R:R to T1
    max_trades_per_day: int = 5
    
    # Market hours (EST)
    market_open_hour: int = 9
    market_open_minute: int = 30
    market_close_hour: int = 16
    market_close_minute: int = 0


class RiskEngine:
    """
    Validates signals and ensures risk rules are followed.
    
    This is your edge protection - it rejects bad setups
    before they can hurt you.
    """
    
    def __init__(
        self,
        limits: Optional[RiskLimits] = None,
        current_daily_trades: int = 0,
        atr_value: Optional[float] = None,
    ):
        self.limits = limits or RiskLimits()
        self.current_daily_trades = current_daily_trades
        self.atr_value = atr_value  # Optional: for stop distance validation
    
    def validate(self, signal: Signal) -> ValidationResult:
        """
        Validate a signal against all risk rules.
        
        Returns ValidationResult with pass/fail and reasons.
        """
        rejection_reasons = []
        warnings = []
        
        # 1. Check required fields
        missing = self._check_required_fields(signal)
        if missing:
            rejection_reasons.append(RejectionReason.MISSING_FIELDS)
            warnings.append(f"Missing fields: {missing}")
        
        # 2. Check timeframe
        if signal.timeframe not in VALID_TIMEFRAMES:
            rejection_reasons.append(RejectionReason.INVALID_TIMEFRAME)
        
        # 3. Check entry zone validity
        if not self._validate_entry_zone(signal):
            rejection_reasons.append(RejectionReason.ENTRY_ZONE_INVALID)
        
        # 4. Check invalidation not inside entry zone
        if self._invalidation_inside_zone(signal):
            rejection_reasons.append(RejectionReason.INVALIDATION_INSIDE_ZONE)
        
        # 5. Check targets exist
        if not signal.levels.targets:
            rejection_reasons.append(RejectionReason.NO_TARGETS)
        
        # 6. Check risk-reward
        rr_check = self._check_risk_reward(signal)
        if rr_check:
            rejection_reasons.append(rr_check)
        
        # 7. Check risk per trade
        risk_check = self._check_risk_per_trade(signal)
        if risk_check:
            rejection_reasons.append(risk_check)
        
        # 8. Check stop distance (if ATR available)
        if self.atr_value:
            stop_check = self._check_stop_distance(signal)
            if stop_check:
                rejection_reasons.append(stop_check)
        
        # 9. Check daily trade limit
        if self.current_daily_trades >= self.limits.max_trades_per_day:
            rejection_reasons.append(RejectionReason.MAX_TRADES_EXCEEDED)
            warnings.append(
                f"Daily limit: {self.current_daily_trades}/{self.limits.max_trades_per_day}"
            )
        
        # 10. Check trading hours (soft warning)
        if not self._within_trading_hours():
            warnings.append("Outside regular trading hours")
        
        is_valid = len(rejection_reasons) == 0
        
        if is_valid:
            logger.info(f"Signal {signal.plan_id} passed validation")
        else:
            logger.warning(
                f"Signal {signal.plan_id} rejected: {[r.value for r in rejection_reasons]}"
            )
        
        return ValidationResult(
            valid=is_valid,
            signal=signal if is_valid else None,
            rejection_reasons=rejection_reasons if rejection_reasons else None,
            warnings=warnings if warnings else None,
        )
    
    def _check_required_fields(self, signal: Signal) -> list[str]:
        """Check that all required fields are present."""
        missing = []
        
        if not signal.levels.entry_zone or signal.levels.entry_zone == (0, 0):
            missing.append("entry_zone")
        if signal.levels.invalidation == 0:
            missing.append("invalidation")
        if not signal.levels.targets:
            missing.append("targets")
        if not signal.timeframe:
            missing.append("timeframe")
        if not signal.setup:
            missing.append("setup")
        
        return missing
    
    def _validate_entry_zone(self, signal: Signal) -> bool:
        """Check entry zone is valid (low < high, positive values)."""
        low, high = signal.levels.entry_zone
        return low > 0 and high > 0 and low <= high
    
    def _invalidation_inside_zone(self, signal: Signal) -> bool:
        """Check if invalidation is inside entry zone (bad)."""
        low, high = signal.levels.entry_zone
        inv = signal.levels.invalidation
        
        # For bullish: invalidation should be below entry zone
        if signal.bias == Bias.BULLISH:
            return inv >= low
        
        # For bearish: invalidation should be above entry zone
        if signal.bias == Bias.BEARISH:
            return inv <= high
        
        # Neutral: just check it's not inside
        return low <= inv <= high
    
    def _check_risk_reward(self, signal: Signal) -> Optional[RejectionReason]:
        """Check risk-reward ratio meets minimum."""
        if not signal.levels.targets:
            return RejectionReason.NO_TARGETS
        
        rr = signal.levels.risk_reward_ratio(0)  # R:R to T1
        
        if rr <= 0:
            return RejectionReason.NEGATIVE_RR
        
        if rr < self.limits.min_risk_reward_t1:
            logger.warning(f"R:R {rr:.2f} below minimum {self.limits.min_risk_reward_t1}")
            # This is a soft reject - we might still want to take it
            return None
        
        return None
    
    def _check_risk_per_trade(self, signal: Signal) -> Optional[RejectionReason]:
        """Check risk per trade is within limits."""
        risk_r = signal.rules.risk_per_trade_r
        
        if risk_r > self.limits.max_risk_per_trade_r:
            return RejectionReason.RISK_TOO_HIGH
        
        if risk_r < self.limits.min_risk_per_trade_r:
            return RejectionReason.RISK_TOO_LOW
        
        return None
    
    def _check_stop_distance(self, signal: Signal) -> Optional[RejectionReason]:
        """Check stop distance against ATR limits."""
        if not self.atr_value or self.atr_value == 0:
            return None
        
        stop_distance = signal.levels.stop_distance
        atr_multiple = stop_distance / self.atr_value
        
        if atr_multiple > self.limits.max_stop_atr_multiple:
            logger.warning(
                f"Stop too wide: {atr_multiple:.2f}x ATR "
                f"(max {self.limits.max_stop_atr_multiple})"
            )
            return RejectionReason.STOP_TOO_WIDE
        
        if atr_multiple < self.limits.min_stop_atr_multiple:
            logger.warning(
                f"Stop too tight: {atr_multiple:.2f}x ATR "
                f"(min {self.limits.min_stop_atr_multiple})"
            )
            return RejectionReason.STOP_TOO_TIGHT
        
        return None
    
    def _within_trading_hours(self) -> bool:
        """Check if current time is within trading hours."""
        now = datetime.now(timezone.utc)
        # Convert to EST (UTC-5) - simplified
        est_hour = (now.hour - 5) % 24
        
        market_open = self.limits.market_open_hour * 60 + self.limits.market_open_minute
        market_close = self.limits.market_close_hour * 60 + self.limits.market_close_minute
        current = est_hour * 60 + now.minute
        
        return market_open <= current <= market_close
    
    def adjust_risk(self, signal: Signal, target_r: float) -> Signal:
        """
        Adjust signal risk to target R value.
        
        Useful when you want to cap risk on a specific trade.
        """
        if target_r > self.limits.max_risk_per_trade_r:
            target_r = self.limits.max_risk_per_trade_r
        if target_r < self.limits.min_risk_per_trade_r:
            target_r = self.limits.min_risk_per_trade_r
        
        # Create new signal with adjusted risk
        from dataclasses import replace
        from .models import Rules
        
        new_rules = Rules(
            trigger=signal.rules.trigger,
            risk_per_trade_r=target_r,
            max_trades_today=signal.rules.max_trades_today,
        )
        
        # We can't use replace() on frozen dataclass, so rebuild
        return Signal(
            strategy=signal.strategy,
            version=signal.version,
            ticker=signal.ticker,
            timeframe=signal.timeframe,
            timestamp_utc=signal.timestamp_utc,
            setup=signal.setup,
            bias=signal.bias,
            levels=signal.levels,
            rules=new_rules,
            context=signal.context,
            post=signal.post,
        )
    
    def increment_daily_trades(self) -> int:
        """Increment and return daily trade count."""
        self.current_daily_trades += 1
        return self.current_daily_trades
    
    def reset_daily_trades(self) -> None:
        """Reset daily trade count (call at start of day)."""
        self.current_daily_trades = 0


class ChopDetector:
    """
    Detects choppy market conditions where trading should be avoided.
    """
    
    def __init__(
        self,
        atr_threshold: float = 0.5,  # If current range < 0.5 * ATR = chop
        bar_count: int = 10,
    ):
        self.atr_threshold = atr_threshold
        self.bar_count = bar_count
    
    def is_choppy(
        self,
        recent_highs: list[float],
        recent_lows: list[float],
        atr: float,
    ) -> bool:
        """
        Detect if recent price action is choppy.
        
        Chop = small range relative to ATR, no clear direction.
        """
        if not recent_highs or not recent_lows or atr == 0:
            return False
        
        # Use last N bars
        highs = recent_highs[-self.bar_count:]
        lows = recent_lows[-self.bar_count:]
        
        if len(highs) < self.bar_count:
            return False
        
        # Range of recent bars
        range_high = max(highs)
        range_low = min(lows)
        total_range = range_high - range_low
        
        # If total range < threshold * ATR = choppy
        return total_range < (self.atr_threshold * atr * self.bar_count)


class NoTradeFilter:
    """
    Composite filter that combines multiple no-trade conditions.
    """
    
    def __init__(
        self,
        risk_engine: RiskEngine,
        chop_detector: Optional[ChopDetector] = None,
    ):
        self.risk_engine = risk_engine
        self.chop_detector = chop_detector or ChopDetector()
    
    def should_trade(
        self,
        signal: Signal,
        recent_highs: Optional[list[float]] = None,
        recent_lows: Optional[list[float]] = None,
        atr: Optional[float] = None,
        has_upcoming_news: bool = False,
        volume_ratio: float = 1.0,  # current/average
    ) -> tuple[bool, list[str]]:
        """
        Determine if we should take this trade.
        
        Returns (should_trade, reasons_if_not)
        """
        reasons = []
        
        # Validate through risk engine
        validation = self.risk_engine.validate(signal)
        if not validation.valid:
            reasons.extend([r.value for r in (validation.rejection_reasons or [])])
        
        # Check for chop
        if recent_highs and recent_lows and atr:
            if self.chop_detector.is_choppy(recent_highs, recent_lows, atr):
                reasons.append("chop_detected")
        
        # Check for news
        if has_upcoming_news:
            reasons.append("news_window")
        
        # Check volume
        if volume_ratio < 0.5:
            reasons.append("low_liquidity")
        
        return (len(reasons) == 0, reasons)
