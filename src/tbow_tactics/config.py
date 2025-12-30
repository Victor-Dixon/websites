"""
TBOW Tactics Configuration

Non-negotiables and system defaults.
"""

from __future__ import annotations

from dataclasses import dataclass, field
from typing import Any
from pathlib import Path
import json

# ═══════════════════════════════════════════════════════════════════════════
# PATHS
# ═══════════════════════════════════════════════════════════════════════════

BASE_DIR = Path(__file__).parent
LEDGER_DIR = BASE_DIR / "ledger_data"
SCHEMAS_DIR = BASE_DIR / "schemas"
TEMPLATES_DIR = BASE_DIR / "templates"

# Ensure directories exist
LEDGER_DIR.mkdir(exist_ok=True)

# ═══════════════════════════════════════════════════════════════════════════
# NON-NEGOTIABLES (Credibility + Safety)
# ═══════════════════════════════════════════════════════════════════════════

# Every call must include these fields
REQUIRED_PLAN_FIELDS = frozenset([
    "entry_zone",
    "invalidation", 
    "targets",
    "timeframe",
    "setup",
])

# Supported timeframes
VALID_TIMEFRAMES = frozenset([
    "1m", "5m", "15m", "30m", "1H", "4H", "D", "W"
])

# Risk limits
MAX_RISK_PER_TRADE_R = 2.0  # Never risk more than 2R
MIN_RISK_PER_TRADE_R = 0.1  # Minimum 0.1R to be meaningful
DEFAULT_RISK_PER_TRADE_R = 0.5

# Publishing limits
MAX_POSTS_PER_HOUR = 4
MAX_POSTS_PER_DAY = 20
POST_COOLDOWN_SECONDS = 300  # 5 minutes between posts

# De-dupe window
DEDUPE_WINDOW_HOURS = 24

# ═══════════════════════════════════════════════════════════════════════════
# DISCLOSURES
# ═══════════════════════════════════════════════════════════════════════════

DISCLOSURE_SHORT = "Not financial advice — playbook + journaling in public."
DISCLOSURE_FULL = (
    "Educational purposes only. Not financial advice. "
    "Past performance does not guarantee future results. "
    "Trade at your own risk. Full ledger at FreeRideInvestor.com."
)

# ═══════════════════════════════════════════════════════════════════════════
# SETUP DEFINITIONS (expandable)
# ═══════════════════════════════════════════════════════════════════════════

SETUP_CATALOG = {
    "VWAP_Reclaim_Continuation": {
        "description": "Price reclaims VWAP with volume confirmation",
        "bias_default": "bullish",
        "min_timeframe": "5m",
    },
    "VWAP_Rejection_Short": {
        "description": "Price rejects at VWAP with volume confirmation",
        "bias_default": "bearish",
        "min_timeframe": "5m",
    },
    "HTF_Support_Bounce": {
        "description": "Bounce from higher timeframe support level",
        "bias_default": "bullish",
        "min_timeframe": "15m",
    },
    "HTF_Resistance_Rejection": {
        "description": "Rejection from higher timeframe resistance",
        "bias_default": "bearish",
        "min_timeframe": "15m",
    },
    "Breakout_Continuation": {
        "description": "Breakout above key level with momentum",
        "bias_default": "bullish",
        "min_timeframe": "5m",
    },
    "Breakdown_Continuation": {
        "description": "Breakdown below key level with momentum",
        "bias_default": "bearish",
        "min_timeframe": "5m",
    },
    "Gap_Fill_Play": {
        "description": "Trade toward unfilled gap",
        "bias_default": "neutral",
        "min_timeframe": "15m",
    },
    "Range_Mean_Reversion": {
        "description": "Reversion to range midpoint in choppy conditions",
        "bias_default": "neutral",
        "min_timeframe": "5m",
    },
    "Trend_Pullback_Entry": {
        "description": "Entry on pullback in established trend",
        "bias_default": "bullish",
        "min_timeframe": "15m",
    },
    "Opening_Range_Breakout": {
        "description": "Breakout from first 15-30 minute range",
        "bias_default": "neutral",
        "min_timeframe": "15m",
    },
}

# ═══════════════════════════════════════════════════════════════════════════
# CONTEXT TAGS
# ═══════════════════════════════════════════════════════════════════════════

VALID_CONTEXT_TAGS = frozenset([
    "above_50SMA", "below_50SMA",
    "above_200SMA", "below_200SMA",
    "HTF_support", "HTF_resistance",
    "volatility_high", "volatility_low", "volatility_normal",
    "volume_high", "volume_low",
    "trending_up", "trending_down", "ranging",
    "pre_market", "market_open", "mid_day", "power_hour", "after_hours",
    "earnings_soon", "ex_div_soon",
    "sector_strong", "sector_weak",
])

# ═══════════════════════════════════════════════════════════════════════════
# NO-TRADE FILTERS
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class NoTradeFilter:
    """Conditions that should trigger 'no trade' decision."""
    name: str
    description: str
    active: bool = True


DEFAULT_NO_TRADE_FILTERS = [
    NoTradeFilter("chop_detected", "Choppy price action with no clear direction"),
    NoTradeFilter("news_window", "Major news event within 30 minutes"),
    NoTradeFilter("low_liquidity", "Below average volume with wide spreads"),
    NoTradeFilter("stop_too_wide", "Stop distance exceeds max allowed for timeframe"),
    NoTradeFilter("stop_too_tight", "Stop distance too tight for normal volatility"),
    NoTradeFilter("max_trades_hit", "Daily trade limit reached"),
    NoTradeFilter("outside_hours", "Outside preferred trading hours"),
]


# ═══════════════════════════════════════════════════════════════════════════
# CONFIG LOADER
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class TBOWConfig:
    """Runtime configuration for TBOW Tactics."""
    
    # Risk settings
    default_risk_r: float = DEFAULT_RISK_PER_TRADE_R
    max_risk_r: float = MAX_RISK_PER_TRADE_R
    max_trades_per_day: int = 5
    
    # Publishing settings
    draft_mode: bool = True  # Phase 0: draft only
    auto_post: bool = False
    include_charts: bool = True
    
    # Rate limiting
    max_posts_per_hour: int = MAX_POSTS_PER_HOUR
    max_posts_per_day: int = MAX_POSTS_PER_DAY
    post_cooldown_seconds: int = POST_COOLDOWN_SECONDS
    
    # Stocktwits settings
    stocktwits_access_token: str = ""
    
    # Active setups
    active_setups: list[str] = field(default_factory=lambda: list(SETUP_CATALOG.keys()))
    
    # Active no-trade filters
    no_trade_filters: list[str] = field(
        default_factory=lambda: [f.name for f in DEFAULT_NO_TRADE_FILTERS if f.active]
    )
    
    @classmethod
    def load(cls, path: Path | str | None = None) -> "TBOWConfig":
        """Load config from JSON file or return defaults."""
        if path is None:
            path = LEDGER_DIR / "config.json"
        path = Path(path)
        
        if path.exists():
            with open(path, "r") as f:
                data = json.load(f)
            return cls(**{k: v for k, v in data.items() if hasattr(cls, k)})
        return cls()
    
    def save(self, path: Path | str | None = None) -> None:
        """Save config to JSON file."""
        if path is None:
            path = LEDGER_DIR / "config.json"
        path = Path(path)
        
        with open(path, "w") as f:
            json.dump(self.__dict__, f, indent=2)
