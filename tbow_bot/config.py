"""
TBOW Bot Configuration

Environment variables and defaults.
"""

from __future__ import annotations

import os
from pathlib import Path
from dataclasses import dataclass
from typing import Optional

# ═══════════════════════════════════════════════════════════════════════════
# PATHS
# ═══════════════════════════════════════════════════════════════════════════

BASE_DIR = Path(__file__).parent
DB_PATH = BASE_DIR / "tbow.sqlite3"
TEMPLATES_DIR = BASE_DIR / "templates"

# ═══════════════════════════════════════════════════════════════════════════
# WEBHOOK CONFIG
# ═══════════════════════════════════════════════════════════════════════════

WEBHOOK_SECRET = os.getenv("TBOW_WEBHOOK_SECRET", "")
WEBHOOK_HOST = os.getenv("TBOW_WEBHOOK_HOST", "0.0.0.0")
WEBHOOK_PORT = int(os.getenv("TBOW_WEBHOOK_PORT", "8000"))

# ═══════════════════════════════════════════════════════════════════════════
# WORDPRESS CONFIG
# ═══════════════════════════════════════════════════════════════════════════

WP_BASE = os.getenv("WP_BASE", "https://tradingrobotplug.com")
WP_USER = os.getenv("WP_USER", "")
WP_APP_PASSWORD = os.getenv("WP_APP_PASSWORD", "")

# ═══════════════════════════════════════════════════════════════════════════
# TRADING RULES
# ═══════════════════════════════════════════════════════════════════════════

# Default symbol to track
DEFAULT_SYMBOL = "TSLA"

# Trading hours (Eastern Time)
MARKET_OPEN_HOUR = 9
MARKET_OPEN_MINUTE = 30
MARKET_CLOSE_HOUR = 16
MARKET_CLOSE_MINUTE = 0

# Ignore signals in first N minutes after open
IGNORE_FIRST_MINUTES = 5

# No new entries after this time (Eastern)
ENTRY_CUTOFF_HOUR = 15
ENTRY_CUTOFF_MINUTE = 30

# Position sizing (v1: underlying)
POSITION_NOTIONAL = 10000  # $10k notional for comparability

# ═══════════════════════════════════════════════════════════════════════════
# OPTIONS CONFIG (v2)
# ═══════════════════════════════════════════════════════════════════════════

# Delta proxy settings (v2 upgrade)
DEFAULT_DELTA = 0.50  # ATM 7DTE delta assumption
OPTION_MULTIPLIER = 100  # Standard options contract
PER_TRADE_FRICTION = 5.0  # Slippage + commission estimate

# Strike selection: "ATM" or "1_ITM"
STRIKE_SELECTION = os.getenv("TBOW_STRIKE_SELECTION", "ATM")

# ═══════════════════════════════════════════════════════════════════════════
# REPORT CONFIG
# ═══════════════════════════════════════════════════════════════════════════

# Post as draft or publish immediately
POST_STATUS = os.getenv("TBOW_POST_STATUS", "draft")  # "draft" or "publish"

# Include chart screenshot placeholder
INCLUDE_CHART_PLACEHOLDER = True

# Report timezone
REPORT_TIMEZONE = "America/Chicago"


@dataclass
class Config:
    """Runtime configuration."""
    
    # Webhook
    webhook_secret: str = WEBHOOK_SECRET
    webhook_host: str = WEBHOOK_HOST
    webhook_port: int = WEBHOOK_PORT
    
    # WordPress
    wp_base: str = WP_BASE
    wp_user: str = WP_USER
    wp_app_password: str = WP_APP_PASSWORD
    
    # Trading
    default_symbol: str = DEFAULT_SYMBOL
    ignore_first_minutes: int = IGNORE_FIRST_MINUTES
    entry_cutoff_hour: int = ENTRY_CUTOFF_HOUR
    entry_cutoff_minute: int = ENTRY_CUTOFF_MINUTE
    position_notional: float = POSITION_NOTIONAL
    
    # Options (v2)
    default_delta: float = DEFAULT_DELTA
    option_multiplier: int = OPTION_MULTIPLIER
    per_trade_friction: float = PER_TRADE_FRICTION
    strike_selection: str = STRIKE_SELECTION
    
    # Report
    post_status: str = POST_STATUS
    
    @classmethod
    def from_env(cls) -> "Config":
        """Load config from environment."""
        return cls(
            webhook_secret=os.getenv("TBOW_WEBHOOK_SECRET", ""),
            webhook_host=os.getenv("TBOW_WEBHOOK_HOST", "0.0.0.0"),
            webhook_port=int(os.getenv("TBOW_WEBHOOK_PORT", "8000")),
            wp_base=os.getenv("WP_BASE", "https://tradingrobotplug.com"),
            wp_user=os.getenv("WP_USER", ""),
            wp_app_password=os.getenv("WP_APP_PASSWORD", ""),
            post_status=os.getenv("TBOW_POST_STATUS", "draft"),
        )
