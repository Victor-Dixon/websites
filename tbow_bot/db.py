"""
TBOW Bot Database Layer

SQLite storage for signals and trades.
"""

from __future__ import annotations

import sqlite3
import json
from datetime import datetime, timezone, date
from pathlib import Path
from typing import Optional, Any
from dataclasses import dataclass, asdict

from .config import DB_PATH


# ═══════════════════════════════════════════════════════════════════════════
# DATABASE INITIALIZATION
# ═══════════════════════════════════════════════════════════════════════════

def get_connection(db_path: Optional[Path] = None) -> sqlite3.Connection:
    """Get a database connection with row factory."""
    path = db_path or DB_PATH
    conn = sqlite3.connect(str(path))
    conn.row_factory = sqlite3.Row
    return conn


def init_db(db_path: Optional[Path] = None) -> None:
    """Initialize database schema."""
    conn = get_connection(db_path)
    
    # Signals table - raw webhook data
    conn.execute("""
        CREATE TABLE IF NOT EXISTS signals (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ts TEXT NOT NULL,
            symbol TEXT NOT NULL,
            tf TEXT NOT NULL,
            event TEXT NOT NULL,
            price REAL NOT NULL,
            raw_json TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        )
    """)
    
    # Trades table - computed paper trades
    conn.execute("""
        CREATE TABLE IF NOT EXISTS trades (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trade_date TEXT NOT NULL,
            symbol TEXT NOT NULL,
            side TEXT NOT NULL,
            entry_ts TEXT NOT NULL,
            entry_price REAL NOT NULL,
            exit_ts TEXT,
            exit_price REAL,
            pnl_underlying REAL,
            pnl_option REAL,
            status TEXT NOT NULL DEFAULT 'open',
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        )
    """)
    
    # Daily stats table - aggregated daily performance
    conn.execute("""
        CREATE TABLE IF NOT EXISTS daily_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            stat_date TEXT NOT NULL UNIQUE,
            total_trades INTEGER NOT NULL DEFAULT 0,
            wins INTEGER NOT NULL DEFAULT 0,
            losses INTEGER NOT NULL DEFAULT 0,
            total_pnl REAL NOT NULL DEFAULT 0,
            win_rate REAL NOT NULL DEFAULT 0,
            avg_win REAL,
            avg_loss REAL,
            max_drawdown REAL,
            signals_received INTEGER NOT NULL DEFAULT 0,
            signals_filtered INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        )
    """)
    
    # Posts table - track what's been published
    conn.execute("""
        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_date TEXT NOT NULL,
            wp_post_id INTEGER,
            wp_post_url TEXT,
            status TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now'))
        )
    """)
    
    # Indexes
    conn.execute("CREATE INDEX IF NOT EXISTS idx_signals_ts ON signals(ts)")
    conn.execute("CREATE INDEX IF NOT EXISTS idx_signals_symbol ON signals(symbol)")
    conn.execute("CREATE INDEX IF NOT EXISTS idx_trades_date ON trades(trade_date)")
    conn.execute("CREATE INDEX IF NOT EXISTS idx_trades_symbol ON trades(symbol)")
    
    conn.commit()
    conn.close()


# ═══════════════════════════════════════════════════════════════════════════
# SIGNAL OPERATIONS
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class Signal:
    """A TradingView signal."""
    id: Optional[int]
    ts: str
    symbol: str
    tf: str
    event: str
    price: float
    raw_json: str
    created_at: Optional[str] = None
    
    @classmethod
    def from_row(cls, row: sqlite3.Row) -> "Signal":
        return cls(
            id=row["id"],
            ts=row["ts"],
            symbol=row["symbol"],
            tf=row["tf"],
            event=row["event"],
            price=row["price"],
            raw_json=row["raw_json"],
            created_at=row["created_at"] if "created_at" in row.keys() else None,
        )


def insert_signal(
    ts: str,
    symbol: str,
    tf: str,
    event: str,
    price: float,
    raw_json: str,
    db_path: Optional[Path] = None,
) -> int:
    """Insert a new signal and return its ID."""
    conn = get_connection(db_path)
    cursor = conn.execute(
        """
        INSERT INTO signals (ts, symbol, tf, event, price, raw_json)
        VALUES (?, ?, ?, ?, ?, ?)
        """,
        (ts, symbol, tf, event, price, raw_json),
    )
    conn.commit()
    signal_id = cursor.lastrowid
    conn.close()
    return signal_id


def get_signals_for_date(
    target_date: date,
    symbol: Optional[str] = None,
    db_path: Optional[Path] = None,
) -> list[Signal]:
    """Get all signals for a specific date."""
    conn = get_connection(db_path)
    
    date_str = target_date.strftime("%Y-%m-%d")
    
    if symbol:
        rows = conn.execute(
            """
            SELECT * FROM signals 
            WHERE date(ts) = ? AND symbol = ?
            ORDER BY ts ASC
            """,
            (date_str, symbol),
        ).fetchall()
    else:
        rows = conn.execute(
            """
            SELECT * FROM signals 
            WHERE date(ts) = ?
            ORDER BY ts ASC
            """,
            (date_str,),
        ).fetchall()
    
    conn.close()
    return [Signal.from_row(row) for row in rows]


def get_all_signals(
    symbol: Optional[str] = None,
    db_path: Optional[Path] = None,
) -> list[Signal]:
    """Get all signals, optionally filtered by symbol."""
    conn = get_connection(db_path)
    
    if symbol:
        rows = conn.execute(
            "SELECT * FROM signals WHERE symbol = ? ORDER BY ts ASC",
            (symbol,),
        ).fetchall()
    else:
        rows = conn.execute(
            "SELECT * FROM signals ORDER BY ts ASC"
        ).fetchall()
    
    conn.close()
    return [Signal.from_row(row) for row in rows]


# ═══════════════════════════════════════════════════════════════════════════
# TRADE OPERATIONS
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class Trade:
    """A paper trade."""
    id: Optional[int]
    trade_date: str
    symbol: str
    side: str  # "CALL" or "PUT"
    entry_ts: str
    entry_price: float
    exit_ts: Optional[str]
    exit_price: Optional[float]
    pnl_underlying: Optional[float]
    pnl_option: Optional[float]
    status: str  # "open" or "closed"
    
    @classmethod
    def from_row(cls, row: sqlite3.Row) -> "Trade":
        return cls(
            id=row["id"],
            trade_date=row["trade_date"],
            symbol=row["symbol"],
            side=row["side"],
            entry_ts=row["entry_ts"],
            entry_price=row["entry_price"],
            exit_ts=row["exit_ts"],
            exit_price=row["exit_price"],
            pnl_underlying=row["pnl_underlying"],
            pnl_option=row["pnl_option"],
            status=row["status"],
        )
    
    @property
    def is_winner(self) -> bool:
        return (self.pnl_underlying or 0) > 0


def save_trade(trade: Trade, db_path: Optional[Path] = None) -> int:
    """Save a trade (insert or update)."""
    conn = get_connection(db_path)
    
    if trade.id:
        conn.execute(
            """
            UPDATE trades SET
                exit_ts = ?, exit_price = ?, pnl_underlying = ?,
                pnl_option = ?, status = ?
            WHERE id = ?
            """,
            (
                trade.exit_ts, trade.exit_price, trade.pnl_underlying,
                trade.pnl_option, trade.status, trade.id,
            ),
        )
        trade_id = trade.id
    else:
        cursor = conn.execute(
            """
            INSERT INTO trades (
                trade_date, symbol, side, entry_ts, entry_price,
                exit_ts, exit_price, pnl_underlying, pnl_option, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """,
            (
                trade.trade_date, trade.symbol, trade.side, trade.entry_ts,
                trade.entry_price, trade.exit_ts, trade.exit_price,
                trade.pnl_underlying, trade.pnl_option, trade.status,
            ),
        )
        trade_id = cursor.lastrowid
    
    conn.commit()
    conn.close()
    return trade_id


def get_trades_for_date(
    target_date: date,
    symbol: Optional[str] = None,
    db_path: Optional[Path] = None,
) -> list[Trade]:
    """Get all trades for a specific date."""
    conn = get_connection(db_path)
    
    date_str = target_date.strftime("%Y-%m-%d")
    
    if symbol:
        rows = conn.execute(
            """
            SELECT * FROM trades 
            WHERE trade_date = ? AND symbol = ?
            ORDER BY entry_ts ASC
            """,
            (date_str, symbol),
        ).fetchall()
    else:
        rows = conn.execute(
            """
            SELECT * FROM trades 
            WHERE trade_date = ?
            ORDER BY entry_ts ASC
            """,
            (date_str,),
        ).fetchall()
    
    conn.close()
    return [Trade.from_row(row) for row in rows]


def clear_trades_for_date(
    target_date: date,
    db_path: Optional[Path] = None,
) -> int:
    """Clear trades for a date (for reprocessing). Returns count deleted."""
    conn = get_connection(db_path)
    date_str = target_date.strftime("%Y-%m-%d")
    cursor = conn.execute("DELETE FROM trades WHERE trade_date = ?", (date_str,))
    conn.commit()
    deleted = cursor.rowcount
    conn.close()
    return deleted


# ═══════════════════════════════════════════════════════════════════════════
# DAILY STATS OPERATIONS
# ═══════════════════════════════════════════════════════════════════════════

@dataclass
class DailyStats:
    """Daily performance statistics."""
    stat_date: str
    total_trades: int
    wins: int
    losses: int
    total_pnl: float
    win_rate: float
    avg_win: Optional[float]
    avg_loss: Optional[float]
    max_drawdown: Optional[float]
    signals_received: int
    signals_filtered: int


def save_daily_stats(stats: DailyStats, db_path: Optional[Path] = None) -> None:
    """Save daily stats (upsert)."""
    conn = get_connection(db_path)
    
    conn.execute(
        """
        INSERT INTO daily_stats (
            stat_date, total_trades, wins, losses, total_pnl,
            win_rate, avg_win, avg_loss, max_drawdown,
            signals_received, signals_filtered
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON CONFLICT(stat_date) DO UPDATE SET
            total_trades = excluded.total_trades,
            wins = excluded.wins,
            losses = excluded.losses,
            total_pnl = excluded.total_pnl,
            win_rate = excluded.win_rate,
            avg_win = excluded.avg_win,
            avg_loss = excluded.avg_loss,
            max_drawdown = excluded.max_drawdown,
            signals_received = excluded.signals_received,
            signals_filtered = excluded.signals_filtered
        """,
        (
            stats.stat_date, stats.total_trades, stats.wins, stats.losses,
            stats.total_pnl, stats.win_rate, stats.avg_win, stats.avg_loss,
            stats.max_drawdown, stats.signals_received, stats.signals_filtered,
        ),
    )
    
    conn.commit()
    conn.close()


def get_cumulative_stats(db_path: Optional[Path] = None) -> dict[str, Any]:
    """Get cumulative stats across all days."""
    conn = get_connection(db_path)
    
    row = conn.execute(
        """
        SELECT
            COUNT(*) as total_days,
            SUM(total_trades) as total_trades,
            SUM(wins) as total_wins,
            SUM(losses) as total_losses,
            SUM(total_pnl) as total_pnl,
            AVG(win_rate) as avg_win_rate,
            SUM(signals_received) as total_signals,
            SUM(signals_filtered) as total_filtered
        FROM daily_stats
        """
    ).fetchone()
    
    conn.close()
    
    return {
        "total_days": row["total_days"] or 0,
        "total_trades": row["total_trades"] or 0,
        "total_wins": row["total_wins"] or 0,
        "total_losses": row["total_losses"] or 0,
        "total_pnl": row["total_pnl"] or 0,
        "avg_win_rate": row["avg_win_rate"] or 0,
        "total_signals": row["total_signals"] or 0,
        "total_filtered": row["total_filtered"] or 0,
    }


# ═══════════════════════════════════════════════════════════════════════════
# POST TRACKING
# ═══════════════════════════════════════════════════════════════════════════

def record_post(
    post_date: str,
    wp_post_id: Optional[int],
    wp_post_url: Optional[str],
    status: str,
    db_path: Optional[Path] = None,
) -> None:
    """Record that a post was created."""
    conn = get_connection(db_path)
    
    conn.execute(
        """
        INSERT INTO posts (post_date, wp_post_id, wp_post_url, status)
        VALUES (?, ?, ?, ?)
        """,
        (post_date, wp_post_id, wp_post_url, status),
    )
    
    conn.commit()
    conn.close()


def was_posted(post_date: str, db_path: Optional[Path] = None) -> bool:
    """Check if a date was already posted."""
    conn = get_connection(db_path)
    
    row = conn.execute(
        "SELECT COUNT(*) as cnt FROM posts WHERE post_date = ? AND status = 'published'",
        (post_date,),
    ).fetchone()
    
    conn.close()
    return row["cnt"] > 0


# Initialize database on import
init_db()
