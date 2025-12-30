"""
TBOW Tactics Automation System for FreeRideInvestor

Generates deterministic TA trade plans, logs outcomes in R,
and publishes consistent, non-spammy Stocktwits posts.

Architecture:
    Signal Engine → Risk Engine → Plan Composer → Publisher → Ledger
"""

__version__ = "1.0.0"
__author__ = "FreeRideInvestor"

from .models import Signal, Result, TradePlan, Bias, Outcome
from .signal_engine import SignalEngine
from .risk_engine import RiskEngine
from .plan_composer import PlanComposer
from .publisher import StocktwitsPublisher
from .ledger import Ledger

__all__ = [
    "Signal",
    "Result", 
    "TradePlan",
    "Bias",
    "Outcome",
    "SignalEngine",
    "RiskEngine",
    "PlanComposer",
    "StocktwitsPublisher",
    "Ledger",
]
