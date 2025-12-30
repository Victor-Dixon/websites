"""
TBOW Tactics Ledger

Stores and tracks all trade plans and results.
This is what makes you legit - a complete audit trail.
"""

from __future__ import annotations

import json
import logging
from dataclasses import dataclass, field
from datetime import datetime, timezone, timedelta
from pathlib import Path
from typing import Any, Optional, Iterator
import sqlite3

from .models import Signal, Result, TradePlan, WeeklyRecap, Outcome, Bias
from .config import LEDGER_DIR

logger = logging.getLogger(__name__)


# ═══════════════════════════════════════════════════════════════════════════
# JSON FILE LEDGER (Simple)
# ═══════════════════════════════════════════════════════════════════════════

class JSONLedger:
    """
    Simple JSON-file based ledger.
    
    Good for Phase 0/1 when you don't need a full database.
    Stores one JSON file per plan.
    """
    
    def __init__(self, ledger_dir: Optional[Path] = None):
        self.ledger_dir = ledger_dir or (LEDGER_DIR / "plans")
        self.ledger_dir.mkdir(parents=True, exist_ok=True)
        self.index_file = self.ledger_dir / "_index.json"
        self._index: dict[str, str] = self._load_index()
    
    def _load_index(self) -> dict[str, str]:
        """Load plan_id -> filename index."""
        if self.index_file.exists():
            try:
                with open(self.index_file, "r") as f:
                    return json.load(f)
            except json.JSONDecodeError:
                pass
        return {}
    
    def _save_index(self) -> None:
        """Save index to file."""
        with open(self.index_file, "w") as f:
            json.dump(self._index, f, indent=2)
    
    def _get_filename(self, plan_id: str) -> str:
        """Generate filename for a plan."""
        # Sanitize plan_id for filename
        safe_id = plan_id.replace(":", "_").replace("/", "_")
        return f"{safe_id}.json"
    
    def save(self, plan: TradePlan) -> Path:
        """
        Save a trade plan to the ledger.
        
        Creates a new file or updates existing.
        """
        filename = self._get_filename(plan.plan_id)
        filepath = self.ledger_dir / filename
        
        with open(filepath, "w") as f:
            f.write(plan.to_json())
        
        self._index[plan.plan_id] = filename
        self._save_index()
        
        logger.info(f"Saved plan {plan.plan_id} to ledger")
        return filepath
    
    def load(self, plan_id: str) -> Optional[TradePlan]:
        """Load a trade plan by ID."""
        filename = self._index.get(plan_id)
        if not filename:
            return None
        
        filepath = self.ledger_dir / filename
        if not filepath.exists():
            return None
        
        try:
            with open(filepath, "r") as f:
                data = json.load(f)
            return TradePlan.from_dict(data)
        except (json.JSONDecodeError, KeyError) as e:
            logger.error(f"Error loading plan {plan_id}: {e}")
            return None
    
    def update_result(
        self,
        plan_id: str,
        outcome: Outcome,
        result_r: float,
        notes: str = "",
        followed_rules: bool = True,
    ) -> Optional[TradePlan]:
        """
        Update a plan with its result.
        
        This is called when a trade is resolved.
        """
        plan = self.load(plan_id)
        if not plan:
            logger.error(f"Plan {plan_id} not found")
            return None
        
        plan.resolve(outcome, result_r, notes, followed_rules)
        self.save(plan)
        
        logger.info(f"Updated plan {plan_id} with result: {outcome.value} ({result_r:+.1f}R)")
        return plan
    
    def get_all(self) -> list[TradePlan]:
        """Get all plans in the ledger."""
        plans = []
        for plan_id in self._index:
            plan = self.load(plan_id)
            if plan:
                plans.append(plan)
        return plans
    
    def get_pending(self) -> list[TradePlan]:
        """Get all plans without results (pending resolution)."""
        return [
            p for p in self.get_all()
            if p.result is None or p.result.outcome == Outcome.PENDING
        ]
    
    def get_by_ticker(self, ticker: str) -> list[TradePlan]:
        """Get all plans for a specific ticker."""
        return [
            p for p in self.get_all()
            if p.signal.ticker.upper() == ticker.upper()
        ]
    
    def get_by_date_range(
        self,
        start: datetime,
        end: datetime,
    ) -> list[TradePlan]:
        """Get plans created within date range."""
        plans = []
        for plan in self.get_all():
            created = datetime.fromisoformat(plan.created_at.replace("Z", "+00:00"))
            if start <= created <= end:
                plans.append(plan)
        return plans
    
    def get_resolved(self) -> list[TradePlan]:
        """Get all resolved plans."""
        return [p for p in self.get_all() if p.is_resolved]


# ═══════════════════════════════════════════════════════════════════════════
# SQLITE LEDGER (Production)
# ═══════════════════════════════════════════════════════════════════════════

class SQLiteLedger:
    """
    SQLite-based ledger for production use.
    
    Better for querying and analytics.
    """
    
    def __init__(self, db_path: Optional[Path] = None):
        self.db_path = db_path or (LEDGER_DIR / "tbow_ledger.db")
        self.db_path.parent.mkdir(parents=True, exist_ok=True)
        self._init_db()
    
    def _init_db(self) -> None:
        """Initialize database schema."""
        with sqlite3.connect(self.db_path) as conn:
            conn.execute("""
                CREATE TABLE IF NOT EXISTS plans (
                    plan_id TEXT PRIMARY KEY,
                    ticker TEXT NOT NULL,
                    timeframe TEXT NOT NULL,
                    setup TEXT NOT NULL,
                    bias TEXT NOT NULL,
                    entry_zone_low REAL NOT NULL,
                    entry_zone_high REAL NOT NULL,
                    invalidation REAL NOT NULL,
                    targets TEXT NOT NULL,  -- JSON array
                    risk_r REAL NOT NULL,
                    context TEXT,  -- JSON array
                    created_at TEXT NOT NULL,
                    updated_at TEXT NOT NULL,
                    signal_json TEXT NOT NULL
                )
            """)
            
            conn.execute("""
                CREATE TABLE IF NOT EXISTS results (
                    plan_id TEXT PRIMARY KEY,
                    outcome TEXT NOT NULL,
                    result_r REAL NOT NULL,
                    notes TEXT,
                    followed_rules INTEGER NOT NULL,
                    resolved_at TEXT NOT NULL,
                    mae_r REAL,
                    mfe_r REAL,
                    FOREIGN KEY (plan_id) REFERENCES plans(plan_id)
                )
            """)
            
            conn.execute("""
                CREATE TABLE IF NOT EXISTS posts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    plan_id TEXT NOT NULL,
                    post_type TEXT NOT NULL,
                    post_id TEXT,
                    content TEXT NOT NULL,
                    posted_at TEXT NOT NULL,
                    FOREIGN KEY (plan_id) REFERENCES plans(plan_id)
                )
            """)
            
            # Indexes for common queries
            conn.execute("CREATE INDEX IF NOT EXISTS idx_plans_ticker ON plans(ticker)")
            conn.execute("CREATE INDEX IF NOT EXISTS idx_plans_created ON plans(created_at)")
            conn.execute("CREATE INDEX IF NOT EXISTS idx_results_outcome ON results(outcome)")
            
            conn.commit()
    
    def save(self, plan: TradePlan) -> None:
        """Save or update a trade plan."""
        with sqlite3.connect(self.db_path) as conn:
            conn.execute("""
                INSERT OR REPLACE INTO plans (
                    plan_id, ticker, timeframe, setup, bias,
                    entry_zone_low, entry_zone_high, invalidation, targets,
                    risk_r, context, created_at, updated_at, signal_json
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """, (
                plan.plan_id,
                plan.signal.ticker,
                plan.signal.timeframe,
                plan.signal.setup,
                plan.signal.bias.value,
                plan.signal.levels.entry_zone[0],
                plan.signal.levels.entry_zone[1],
                plan.signal.levels.invalidation,
                json.dumps(plan.signal.levels.targets),
                plan.signal.rules.risk_per_trade_r,
                json.dumps(plan.signal.context),
                plan.created_at,
                plan.updated_at,
                plan.signal.to_json(),
            ))
            
            if plan.result and plan.result.outcome != Outcome.PENDING:
                conn.execute("""
                    INSERT OR REPLACE INTO results (
                        plan_id, outcome, result_r, notes, followed_rules,
                        resolved_at, mae_r, mfe_r
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                """, (
                    plan.plan_id,
                    plan.result.outcome.value,
                    plan.result.result_r,
                    plan.result.notes,
                    1 if plan.result.followed_rules else 0,
                    plan.result.resolved_timestamp_utc,
                    plan.result.max_adverse_excursion_r,
                    plan.result.max_favorable_excursion_r,
                ))
            
            for post in plan.post_history:
                conn.execute("""
                    INSERT INTO posts (plan_id, post_type, post_id, content, posted_at)
                    VALUES (?, ?, ?, ?, ?)
                """, (
                    plan.plan_id,
                    post.get("post_type", ""),
                    post.get("post_id", ""),
                    post.get("content_preview", ""),
                    post.get("timestamp_utc", ""),
                ))
            
            conn.commit()
    
    def load(self, plan_id: str) -> Optional[TradePlan]:
        """Load a plan by ID."""
        with sqlite3.connect(self.db_path) as conn:
            conn.row_factory = sqlite3.Row
            
            row = conn.execute(
                "SELECT * FROM plans WHERE plan_id = ?",
                (plan_id,)
            ).fetchone()
            
            if not row:
                return None
            
            signal = Signal.from_json(row["signal_json"])
            
            result = None
            result_row = conn.execute(
                "SELECT * FROM results WHERE plan_id = ?",
                (plan_id,)
            ).fetchone()
            
            if result_row:
                result = Result(
                    plan_id=plan_id,
                    resolved_timestamp_utc=result_row["resolved_at"],
                    outcome=Outcome(result_row["outcome"]),
                    result_r=result_row["result_r"],
                    notes=result_row["notes"] or "",
                    followed_rules=bool(result_row["followed_rules"]),
                    max_adverse_excursion_r=result_row["mae_r"],
                    max_favorable_excursion_r=result_row["mfe_r"],
                )
            
            post_rows = conn.execute(
                "SELECT * FROM posts WHERE plan_id = ? ORDER BY posted_at",
                (plan_id,)
            ).fetchall()
            
            post_history = [
                {
                    "post_type": p["post_type"],
                    "post_id": p["post_id"],
                    "content_preview": p["content"],
                    "timestamp_utc": p["posted_at"],
                }
                for p in post_rows
            ]
            
            return TradePlan(
                signal=signal,
                result=result,
                created_at=row["created_at"],
                updated_at=row["updated_at"],
                post_history=post_history,
            )
    
    def get_stats(
        self,
        start: Optional[datetime] = None,
        end: Optional[datetime] = None,
        ticker: Optional[str] = None,
    ) -> dict[str, Any]:
        """Get performance statistics."""
        with sqlite3.connect(self.db_path) as conn:
            # Build WHERE clause
            conditions = []
            params = []
            
            if start:
                conditions.append("p.created_at >= ?")
                params.append(start.isoformat())
            if end:
                conditions.append("p.created_at <= ?")
                params.append(end.isoformat())
            if ticker:
                conditions.append("p.ticker = ?")
                params.append(ticker.upper())
            
            where = "WHERE " + " AND ".join(conditions) if conditions else ""
            
            # Get resolved trades
            query = f"""
                SELECT 
                    COUNT(*) as total_trades,
                    SUM(CASE WHEN r.result_r > 0 THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN r.result_r < 0 THEN 1 ELSE 0 END) as losses,
                    SUM(CASE WHEN r.result_r = 0 THEN 1 ELSE 0 END) as breakevens,
                    SUM(r.result_r) as total_r,
                    AVG(r.result_r) as avg_r,
                    MAX(r.result_r) as best_r,
                    MIN(r.result_r) as worst_r
                FROM plans p
                JOIN results r ON p.plan_id = r.plan_id
                {where}
            """
            
            row = conn.execute(query, params).fetchone()
            
            total_trades = row[0] or 0
            wins = row[1] or 0
            losses = row[2] or 0
            
            return {
                "total_trades": total_trades,
                "wins": wins,
                "losses": losses,
                "breakevens": row[3] or 0,
                "win_rate": (wins / total_trades * 100) if total_trades > 0 else 0,
                "total_r": row[4] or 0,
                "avg_r": row[5] or 0,
                "best_r": row[6] or 0,
                "worst_r": row[7] or 0,
                "expectancy": (row[4] / total_trades) if total_trades > 0 else 0,
            }
    
    def get_setup_stats(self) -> dict[str, dict[str, Any]]:
        """Get performance breakdown by setup."""
        with sqlite3.connect(self.db_path) as conn:
            rows = conn.execute("""
                SELECT 
                    p.setup,
                    COUNT(*) as count,
                    SUM(CASE WHEN r.result_r > 0 THEN 1 ELSE 0 END) as wins,
                    SUM(r.result_r) as total_r,
                    AVG(r.result_r) as avg_r
                FROM plans p
                JOIN results r ON p.plan_id = r.plan_id
                GROUP BY p.setup
                ORDER BY total_r DESC
            """).fetchall()
            
            return {
                row[0]: {
                    "count": row[1],
                    "wins": row[2],
                    "win_rate": (row[2] / row[1] * 100) if row[1] > 0 else 0,
                    "total_r": row[3],
                    "avg_r": row[4],
                }
                for row in rows
            }


# ═══════════════════════════════════════════════════════════════════════════
# UNIFIED LEDGER INTERFACE
# ═══════════════════════════════════════════════════════════════════════════

class Ledger:
    """
    Unified ledger interface.
    
    Wraps either JSON or SQLite backend.
    """
    
    def __init__(
        self,
        use_sqlite: bool = False,
        ledger_dir: Optional[Path] = None,
    ):
        if use_sqlite:
            db_path = (ledger_dir or LEDGER_DIR) / "tbow_ledger.db" if ledger_dir else None
            self._backend = SQLiteLedger(db_path)
        else:
            self._backend = JSONLedger(ledger_dir)
        
        self.use_sqlite = use_sqlite
    
    def save_signal(self, signal: Signal) -> TradePlan:
        """
        Save a new signal as a trade plan.
        
        Creates the plan and saves to ledger.
        """
        plan = TradePlan(signal=signal)
        self._backend.save(plan)
        return plan
    
    def save_plan(self, plan: TradePlan) -> None:
        """Save a trade plan."""
        self._backend.save(plan)
    
    def load(self, plan_id: str) -> Optional[TradePlan]:
        """Load a plan by ID."""
        return self._backend.load(plan_id)
    
    def resolve(
        self,
        plan_id: str,
        outcome: Outcome,
        result_r: float,
        notes: str = "",
        followed_rules: bool = True,
    ) -> Optional[TradePlan]:
        """
        Resolve a trade plan with its outcome.
        
        This is the key audit function - records the result.
        """
        plan = self.load(plan_id)
        if not plan:
            logger.error(f"Plan {plan_id} not found")
            return None
        
        plan.resolve(outcome, result_r, notes, followed_rules)
        self._backend.save(plan)
        
        logger.info(
            f"Resolved {plan_id}: {outcome.value} for {result_r:+.1f}R"
            f" (rules followed: {followed_rules})"
        )
        return plan
    
    def get_pending(self) -> list[TradePlan]:
        """Get all pending (unresolved) plans."""
        if isinstance(self._backend, JSONLedger):
            return self._backend.get_pending()
        else:
            # SQLite query for pending
            with sqlite3.connect(self._backend.db_path) as conn:
                rows = conn.execute("""
                    SELECT p.plan_id FROM plans p
                    LEFT JOIN results r ON p.plan_id = r.plan_id
                    WHERE r.plan_id IS NULL
                """).fetchall()
            
            return [self.load(row[0]) for row in rows if self.load(row[0])]
    
    def get_resolved(self) -> list[TradePlan]:
        """Get all resolved plans."""
        if isinstance(self._backend, JSONLedger):
            return self._backend.get_resolved()
        else:
            with sqlite3.connect(self._backend.db_path) as conn:
                rows = conn.execute("""
                    SELECT plan_id FROM results
                """).fetchall()
            
            return [self.load(row[0]) for row in rows if self.load(row[0])]
    
    def generate_weekly_recap(
        self,
        week_start: Optional[datetime] = None,
        week_end: Optional[datetime] = None,
    ) -> WeeklyRecap:
        """
        Generate a weekly recap from the ledger.
        
        Automatically calculates all metrics.
        """
        if not week_end:
            week_end = datetime.now(timezone.utc)
        if not week_start:
            week_start = week_end - timedelta(days=7)
        
        # Get plans from the week
        if isinstance(self._backend, SQLiteLedger):
            stats = self._backend.get_stats(week_start, week_end)
            setup_stats = self._backend.get_setup_stats()
            
            # Find best/worst setups
            best_setup = max(setup_stats.items(), key=lambda x: x[1]["avg_r"])[0] if setup_stats else ""
            
        else:
            # JSON backend - manual calculation
            plans = [
                p for p in self._backend.get_all()
                if p.is_resolved
            ]
            
            week_plans = []
            for plan in plans:
                created = datetime.fromisoformat(
                    plan.created_at.replace("Z", "+00:00")
                )
                if week_start <= created <= week_end:
                    week_plans.append(plan)
            
            wins = sum(1 for p in week_plans if p.result and p.result.result_r > 0)
            losses = sum(1 for p in week_plans if p.result and p.result.result_r < 0)
            total_r = sum(p.result.result_r for p in week_plans if p.result)
            
            stats = {
                "total_trades": len(week_plans),
                "wins": wins,
                "losses": losses,
                "breakevens": len(week_plans) - wins - losses,
                "total_r": total_r,
                "best_r": max((p.result.result_r for p in week_plans if p.result), default=0),
                "worst_r": min((p.result.result_r for p in week_plans if p.result), default=0),
            }
            
            # Simple best setup calculation
            setup_results: dict[str, list[float]] = {}
            for p in week_plans:
                if p.result:
                    setup = p.signal.setup
                    if setup not in setup_results:
                        setup_results[setup] = []
                    setup_results[setup].append(p.result.result_r)
            
            best_setup = ""
            if setup_results:
                best_setup = max(
                    setup_results.items(),
                    key=lambda x: sum(x[1]) / len(x[1])
                )[0]
        
        return WeeklyRecap(
            week_start=week_start.strftime("%Y-%m-%d"),
            week_end=week_end.strftime("%Y-%m-%d"),
            total_trades=stats["total_trades"],
            wins=stats["wins"],
            losses=stats["losses"],
            breakevens=stats.get("breakevens", 0),
            total_r=stats["total_r"],
            best_trade_r=stats.get("best_r", 0),
            worst_trade_r=stats.get("worst_r", 0),
            best_setup=best_setup,
        )
    
    def export_to_json(self, output_file: Path) -> None:
        """Export all ledger data to a single JSON file."""
        if isinstance(self._backend, JSONLedger):
            plans = self._backend.get_all()
        else:
            # Get all from SQLite
            with sqlite3.connect(self._backend.db_path) as conn:
                rows = conn.execute("SELECT plan_id FROM plans").fetchall()
            plans = [self.load(row[0]) for row in rows if self.load(row[0])]
        
        data = {
            "exported_at": datetime.now(timezone.utc).isoformat(),
            "total_plans": len(plans),
            "plans": [p.to_dict() for p in plans],
        }
        
        with open(output_file, "w") as f:
            json.dump(data, f, indent=2)
        
        logger.info(f"Exported {len(plans)} plans to {output_file}")
