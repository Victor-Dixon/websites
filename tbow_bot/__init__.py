"""
TBOW Bot - TradingView to WordPress Pipeline

Receives TradingView webhook alerts, paper trades signals,
and publishes daily recaps to tradingrobotplug.com.

Pipeline:
    TradingView → Webhook → Paper Engine → Report → WordPress
"""

__version__ = "1.0.0"
