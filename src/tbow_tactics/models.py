"""
TBOW Tactics Data Models

Core data structures for signals, plans, and results.
All JSON-serializable for storage and publishing.
"""

from __future__ import annotations

from dataclasses import dataclass, field, asdict
from datetime import datetime, timezone
from enum import Enum
from typing import Any, Optional
import hashlib
import json
import uuid


class Bias(str, Enum):
    """Trade bias direction."""
    BULLISH = "bullish"
    BEARISH = "bearish"
    NEUTRAL = "neutral"


class Outcome(str, Enum):
    """Trade outcome types."""
    TARGET1 = "target1"
    TARGET2 = "target2"
    TARGET3 = "target3"
    STOPPED_OUT = "stopped_out"
    BREAKEVEN = "breakeven"
    PARTIAL = "partial"
    INVALIDATED = "invalidated"  # Setup never triggered
    CANCELLED = "cancelled"  # Manually cancelled
    PENDING = "pending"  # Still active


class PostType(str, Enum):
    """Stocktwits post types."""
    PLAN = "plan"
    UPDATE = "update"
    STOPOUT = "stopout"
    TARGET_HIT = "target_hit"
    RECAP = "recap"


@dataclass
class Levels:
    """Price levels for a trade plan."""
    entry_zone: tuple[float, float]  # (low, high)
    invalidation: float
    targets: list[float]
    
    def to_dict(self) -> dict[str, Any]:
        return {
            "entry_zone": list(self.entry_zone),
            "invalidation": self.invalidation,
            "targets": self.targets,
        }
    
    @classmethod
    def from_dict(cls, d: dict[str, Any]) -> "Levels":
        entry = d.get("entry_zone", [0, 0])
        return cls(
            entry_zone=(float(entry[0]), float(entry[1])),
            invalidation=float(d.get("invalidation", 0)),
            targets=[float(t) for t in d.get("targets", [])],
        )
    
    @property
    def entry_mid(self) -> float:
        """Midpoint of entry zone."""
        return (self.entry_zone[0] + self.entry_zone[1]) / 2
    
    @property
    def stop_distance(self) -> float:
        """Distance from entry mid to invalidation."""
        return abs(self.entry_mid - self.invalidation)
    
    def risk_reward_ratio(self, target_index: int = 0) -> float:
        """Calculate R:R for a specific target."""
        if target_index >= len(self.targets):
            return 0.0
        reward = abs(self.targets[target_index] - self.entry_mid)
        risk = self.stop_distance
        return reward / risk if risk > 0 else 0.0


@dataclass
class Rules:
    """Trade rules and constraints."""
    trigger: str  # e.g., "break_and_hold_above_entry_zone"
    risk_per_trade_r: float = 0.5
    max_trades_today: int = 3
    
    def to_dict(self) -> dict[str, Any]:
        return asdict(self)
    
    @classmethod
    def from_dict(cls, d: dict[str, Any]) -> "Rules":
        return cls(
            trigger=str(d.get("trigger", "")),
            risk_per_trade_r=float(d.get("risk_per_trade_r", 0.5)),
            max_trades_today=int(d.get("max_trades_today", 3)),
        )


@dataclass
class PostConfig:
    """Configuration for post generation."""
    include_chart: bool = True
    post_type: PostType = PostType.PLAN
    
    def to_dict(self) -> dict[str, Any]:
        return {
            "include_chart": self.include_chart,
            "post_type": self.post_type.value,
        }
    
    @classmethod
    def from_dict(cls, d: dict[str, Any]) -> "PostConfig":
        return cls(
            include_chart=bool(d.get("include_chart", True)),
            post_type=PostType(d.get("post_type", "plan")),
        )


@dataclass
class Signal:
    """
    Signal JSON (SSOT) - Single Source of Truth for a trade signal.
    
    This is what the signal engine outputs.
    """
    strategy: str = "TBOW_Tactics"
    version: str = "1.0.0"
    ticker: str = ""
    timeframe: str = ""
    timestamp_utc: str = ""
    setup: str = ""
    bias: Bias = Bias.NEUTRAL
    levels: Levels = field(default_factory=lambda: Levels((0, 0), 0, []))
    rules: Rules = field(default_factory=lambda: Rules(""))
    context: list[str] = field(default_factory=list)
    post: PostConfig = field(default_factory=PostConfig)
    
    def __post_init__(self):
        if not self.timestamp_utc:
            self.timestamp_utc = datetime.now(timezone.utc).isoformat()
    
    @property
    def plan_id(self) -> str:
        """Generate unique plan ID."""
        ts = self.timestamp_utc.replace(":", "").replace("-", "")[:15]
        return f"TBOW-{self.ticker}-{ts}-{self.timeframe}-{self._short_hash()}"
    
    def _short_hash(self) -> str:
        """Short hash for uniqueness."""
        content = f"{self.ticker}{self.setup}{self.levels.entry_zone}{self.levels.invalidation}"
        return hashlib.sha256(content.encode()).hexdigest()[:6]
    
    def dedupe_hash(self) -> str:
        """Hash for de-duplication (same ticker + setup + levels = duplicate)."""
        content = json.dumps({
            "ticker": self.ticker,
            "setup": self.setup,
            "entry_zone": list(self.levels.entry_zone),
            "invalidation": self.levels.invalidation,
            "targets": self.levels.targets,
        }, sort_keys=True)
        return hashlib.sha256(content.encode()).hexdigest()
    
    def to_dict(self) -> dict[str, Any]:
        return {
            "strategy": self.strategy,
            "version": self.version,
            "ticker": self.ticker,
            "timeframe": self.timeframe,
            "timestamp_utc": self.timestamp_utc,
            "setup": self.setup,
            "bias": self.bias.value if isinstance(self.bias, Bias) else self.bias,
            "levels": self.levels.to_dict(),
            "rules": self.rules.to_dict(),
            "context": self.context,
            "post": self.post.to_dict(),
        }
    
    def to_json(self, indent: int = 2) -> str:
        return json.dumps(self.to_dict(), indent=indent)
    
    @classmethod
    def from_dict(cls, d: dict[str, Any]) -> "Signal":
        return cls(
            strategy=str(d.get("strategy", "TBOW_Tactics")),
            version=str(d.get("version", "1.0.0")),
            ticker=str(d.get("ticker", "")),
            timeframe=str(d.get("timeframe", "")),
            timestamp_utc=str(d.get("timestamp_utc", "")),
            setup=str(d.get("setup", "")),
            bias=Bias(d.get("bias", "neutral")),
            levels=Levels.from_dict(d.get("levels", {})),
            rules=Rules.from_dict(d.get("rules", {})),
            context=list(d.get("context", [])),
            post=PostConfig.from_dict(d.get("post", {})),
        )
    
    @classmethod
    def from_json(cls, json_str: str) -> "Signal":
        return cls.from_dict(json.loads(json_str))


@dataclass
class Result:
    """
    Result JSON - Outcome record for credibility tracking.
    """
    plan_id: str
    resolved_timestamp_utc: str = ""
    outcome: Outcome = Outcome.PENDING
    result_r: float = 0.0
    notes: str = ""
    followed_rules: bool = True
    max_adverse_excursion_r: Optional[float] = None
    max_favorable_excursion_r: Optional[float] = None
    
    def __post_init__(self):
        if not self.resolved_timestamp_utc and self.outcome != Outcome.PENDING:
            self.resolved_timestamp_utc = datetime.now(timezone.utc).isoformat()
    
    def to_dict(self) -> dict[str, Any]:
        return {
            "plan_id": self.plan_id,
            "resolved_timestamp_utc": self.resolved_timestamp_utc,
            "outcome": self.outcome.value if isinstance(self.outcome, Outcome) else self.outcome,
            "result_r": self.result_r,
            "notes": self.notes,
            "followed_rules": self.followed_rules,
            "max_adverse_excursion_r": self.max_adverse_excursion_r,
            "max_favorable_excursion_r": self.max_favorable_excursion_r,
        }
    
    def to_json(self, indent: int = 2) -> str:
        return json.dumps(self.to_dict(), indent=indent)
    
    @classmethod
    def from_dict(cls, d: dict[str, Any]) -> "Result":
        return cls(
            plan_id=str(d.get("plan_id", "")),
            resolved_timestamp_utc=str(d.get("resolved_timestamp_utc", "")),
            outcome=Outcome(d.get("outcome", "pending")),
            result_r=float(d.get("result_r", 0.0)),
            notes=str(d.get("notes", "")),
            followed_rules=bool(d.get("followed_rules", True)),
            max_adverse_excursion_r=d.get("max_adverse_excursion_r"),
            max_favorable_excursion_r=d.get("max_favorable_excursion_r"),
        )


@dataclass
class TradePlan:
    """
    Combined Signal + Result for full trade lifecycle.
    """
    signal: Signal
    result: Optional[Result] = None
    created_at: str = ""
    updated_at: str = ""
    post_history: list[dict[str, Any]] = field(default_factory=list)
    
    def __post_init__(self):
        now = datetime.now(timezone.utc).isoformat()
        if not self.created_at:
            self.created_at = now
        self.updated_at = now
    
    @property
    def plan_id(self) -> str:
        return self.signal.plan_id
    
    @property
    def is_resolved(self) -> bool:
        return self.result is not None and self.result.outcome != Outcome.PENDING
    
    def resolve(
        self,
        outcome: Outcome,
        result_r: float,
        notes: str = "",
        followed_rules: bool = True,
    ) -> Result:
        """Resolve the trade plan with an outcome."""
        self.result = Result(
            plan_id=self.plan_id,
            outcome=outcome,
            result_r=result_r,
            notes=notes,
            followed_rules=followed_rules,
        )
        self.updated_at = datetime.now(timezone.utc).isoformat()
        return self.result
    
    def add_post(self, post_type: PostType, post_id: str, content: str) -> None:
        """Record a published post."""
        self.post_history.append({
            "post_type": post_type.value,
            "post_id": post_id,
            "timestamp_utc": datetime.now(timezone.utc).isoformat(),
            "content_preview": content[:100] + "..." if len(content) > 100 else content,
        })
        self.updated_at = datetime.now(timezone.utc).isoformat()
    
    def to_dict(self) -> dict[str, Any]:
        return {
            "signal": self.signal.to_dict(),
            "result": self.result.to_dict() if self.result else None,
            "created_at": self.created_at,
            "updated_at": self.updated_at,
            "post_history": self.post_history,
        }
    
    def to_json(self, indent: int = 2) -> str:
        return json.dumps(self.to_dict(), indent=indent)
    
    @classmethod
    def from_dict(cls, d: dict[str, Any]) -> "TradePlan":
        result_data = d.get("result")
        return cls(
            signal=Signal.from_dict(d.get("signal", {})),
            result=Result.from_dict(result_data) if result_data else None,
            created_at=str(d.get("created_at", "")),
            updated_at=str(d.get("updated_at", "")),
            post_history=list(d.get("post_history", [])),
        )


@dataclass
class WeeklyRecap:
    """Weekly performance summary for community engagement."""
    week_start: str
    week_end: str
    total_trades: int = 0
    wins: int = 0
    losses: int = 0
    breakevens: int = 0
    total_r: float = 0.0
    best_trade_r: float = 0.0
    worst_trade_r: float = 0.0
    best_setup: str = ""
    worst_mistake: str = ""
    notes: str = ""
    
    @property
    def win_rate(self) -> float:
        """Win rate as percentage."""
        total = self.wins + self.losses
        return (self.wins / total * 100) if total > 0 else 0.0
    
    @property
    def expectancy(self) -> float:
        """Average R per trade."""
        return self.total_r / self.total_trades if self.total_trades > 0 else 0.0
    
    def to_dict(self) -> dict[str, Any]:
        return {
            "week_start": self.week_start,
            "week_end": self.week_end,
            "total_trades": self.total_trades,
            "wins": self.wins,
            "losses": self.losses,
            "breakevens": self.breakevens,
            "total_r": self.total_r,
            "best_trade_r": self.best_trade_r,
            "worst_trade_r": self.worst_trade_r,
            "best_setup": self.best_setup,
            "worst_mistake": self.worst_mistake,
            "notes": self.notes,
            "win_rate": self.win_rate,
            "expectancy": self.expectancy,
        }
    
    def to_json(self, indent: int = 2) -> str:
        return json.dumps(self.to_dict(), indent=indent)
