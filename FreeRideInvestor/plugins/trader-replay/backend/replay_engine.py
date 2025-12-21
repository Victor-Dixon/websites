"""
Replay Engine - Core logic for candle-by-candle replay simulation.

MVP: Simple, testable, deterministic replay system.
"""

from dataclasses import dataclass
from datetime import datetime, timedelta
from typing import List, Optional, Dict, Any
from pathlib import Path
import sqlite3
import json


@dataclass
class Candle:
    """Single OHLCV candle."""
    timestamp: datetime
    open: float
    high: float
    low: float
    close: float
    volume: int
    candle_index: int


@dataclass
class ReplayState:
    """Current replay state."""
    session_id: int
    current_index: int
    is_playing: bool
    playback_speed: float  # 1.0 = normal, 2.0 = 2x, etc.
    visible_candles: List[Candle]
    total_candles: int


class ReplaySession:
    """Manages a replay session with candle-by-candle progression."""
    
    def __init__(self, db_path: Path, session_id: int):
        """Initialize replay session from database."""
        self.db_path = db_path
        self.session_id = session_id
        self.candles: List[Candle] = []
        self.current_index = 0
        self.is_playing = False
        self.playback_speed = 1.0
        
        # Load candles from database
        self._load_candles()
    
    def _load_candles(self) -> None:
        """Load all candles for this session from database."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT timestamp, open, high, low, close, volume, candle_index
            FROM candles
            WHERE session_id = ?
            ORDER BY candle_index ASC
        """, (self.session_id,))
        
        rows = cursor.fetchall()
        conn.close()
        
        self.candles = [
            Candle(
                timestamp=datetime.fromisoformat(row[0]),
                open=float(row[1]),
                high=float(row[2]),
                low=float(row[3]),
                close=float(row[4]),
                volume=int(row[5]),
                candle_index=int(row[6])
            )
            for row in rows
        ]
    
    def get_state(self) -> ReplayState:
        """Get current replay state."""
        return ReplayState(
            session_id=self.session_id,
            current_index=self.current_index,
            is_playing=self.is_playing,
            playback_speed=self.playback_speed,
            visible_candles=self.candles[:self.current_index + 1],
            total_candles=len(self.candles)
        )
    
    def step(self) -> List[Candle]:
        """Move forward one candle."""
        if self.current_index < len(self.candles) - 1:
            self.current_index += 1
        return self.candles[:self.current_index + 1]
    
    def step_back(self) -> List[Candle]:
        """Move backward one candle."""
        if self.current_index > 0:
            self.current_index -= 1
        return self.candles[:self.current_index + 1]
    
    def jump_to_time(self, target_time: datetime) -> List[Candle]:
        """Jump to specific timestamp."""
        for i, candle in enumerate(self.candles):
            if candle.timestamp >= target_time:
                self.current_index = i
                break
        else:
            # Past end, go to last candle
            self.current_index = len(self.candles) - 1
        
        return self.candles[:self.current_index + 1]
    
    def jump_to_index(self, index: int) -> List[Candle]:
        """Jump to specific candle index."""
        if 0 <= index < len(self.candles):
            self.current_index = index
        return self.candles[:self.current_index + 1]
    
    def play(self) -> None:
        """Start playing."""
        self.is_playing = True
    
    def pause(self) -> None:
        """Pause playing."""
        self.is_playing = False
    
    def set_speed(self, speed: float) -> None:
        """Set playback speed (1.0 = normal, 2.0 = 2x, etc.)."""
        self.playback_speed = max(0.1, min(10.0, speed))
    
    def reset(self) -> List[Candle]:
        """Reset to beginning."""
        self.current_index = 0
        self.is_playing = False
        return self.candles[:1] if self.candles else []
    
    def get_current_candle(self) -> Optional[Candle]:
        """Get current candle."""
        if 0 <= self.current_index < len(self.candles):
            return self.candles[self.current_index]
        return None
    
    def get_progress(self) -> float:
        """Get replay progress (0.0 to 1.0)."""
        if not self.candles:
            return 0.0
        return (self.current_index + 1) / len(self.candles)
    
    def is_complete(self) -> bool:
        """Check if replay is complete."""
        return self.current_index >= len(self.candles) - 1


class SessionLoader:
    """Loads session data from database."""
    
    def __init__(self, db_path: Path):
        """Initialize with database path."""
        self.db_path = db_path
        self._ensure_database()
    
    def _ensure_database(self) -> None:
        """Create database and tables if they don't exist."""
        schema_path = Path(__file__).parent / "schema.sql"
        if schema_path.exists():
            conn = sqlite3.connect(self.db_path)
            with open(schema_path, 'r') as f:
                conn.executescript(f.read())
            conn.close()
    
    def create_session(
        self,
        symbol: str,
        session_date: str,
        timeframe: str = "1m",
        candles: List[Dict[str, Any]] = None
    ) -> int:
        """Create a new replay session."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        # Get or create symbol
        cursor.execute("SELECT id FROM symbols WHERE symbol = ?", (symbol,))
        symbol_row = cursor.fetchone()
        if not symbol_row:
            cursor.execute(
                "INSERT INTO symbols (symbol, asset_type) VALUES (?, ?)",
                (symbol, "stock")
            )
            symbol_id = cursor.lastrowid
        else:
            symbol_id = symbol_row[0]
        
        # Create session
        cursor.execute("""
            INSERT OR IGNORE INTO sessions (symbol_id, session_date, timeframe, candle_count)
            VALUES (?, ?, ?, ?)
        """, (symbol_id, session_date, timeframe, len(candles) if candles else 0))
        
        cursor.execute("""
            SELECT id FROM sessions
            WHERE symbol_id = ? AND session_date = ? AND timeframe = ?
        """, (symbol_id, session_date, timeframe))
        
        session_row = cursor.fetchone()
        session_id = session_row[0] if session_row else cursor.lastrowid
        
        # Insert candles if provided
        if candles:
            for idx, candle in enumerate(candles):
                cursor.execute("""
                    INSERT OR REPLACE INTO candles
                    (session_id, timestamp, open, high, low, close, volume, candle_index)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                """, (
                    session_id,
                    candle['timestamp'],
                    candle['open'],
                    candle['high'],
                    candle['low'],
                    candle['close'],
                    candle.get('volume', 0),
                    idx
                ))
            
            # Update candle count
            cursor.execute("""
                UPDATE sessions SET candle_count = ? WHERE id = ?
            """, (len(candles), session_id))
        
        conn.commit()
        conn.close()
        
        return session_id
    
    def get_session_info(self, session_id: int) -> Dict[str, Any]:
        """Get session information."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT s.id, s.session_date, s.timeframe, s.candle_count, sym.symbol
            FROM sessions s
            JOIN symbols sym ON s.symbol_id = sym.id
            WHERE s.id = ?
        """, (session_id,))
        
        row = cursor.fetchone()
        conn.close()
        
        if not row:
            return {}
        
        return {
            'session_id': row[0],
            'session_date': row[1],
            'timeframe': row[2],
            'candle_count': row[3],
            'symbol': row[4]
        }
    
    def list_sessions(self, symbol: Optional[str] = None) -> List[Dict[str, Any]]:
        """List all available sessions."""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        if symbol:
            cursor.execute("""
                SELECT s.id, s.session_date, s.timeframe, s.candle_count, sym.symbol
                FROM sessions s
                JOIN symbols sym ON s.symbol_id = sym.id
                WHERE sym.symbol = ?
                ORDER BY s.session_date DESC
            """, (symbol,))
        else:
            cursor.execute("""
                SELECT s.id, s.session_date, s.timeframe, s.candle_count, sym.symbol
                FROM sessions s
                JOIN symbols sym ON s.symbol_id = sym.id
                ORDER BY s.session_date DESC
            """)
        
        rows = cursor.fetchall()
        conn.close()
        
        return [
            {
                'session_id': row[0],
                'session_date': row[1],
                'timeframe': row[2],
                'candle_count': row[3],
                'symbol': row[4]
            }
            for row in rows
        ]



