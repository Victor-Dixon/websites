"""
FastAPI Backend for Trading Replay Journal System.

MVP: Session management, replay engine, paper trading, journaling.
"""

from fastapi import FastAPI, HTTPException, Query
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
from datetime import datetime
from pathlib import Path
import sqlite3

from replay_engine import ReplaySession, SessionLoader, Candle

# Initialize FastAPI app
app = FastAPI(
    title="Trading Replay Journal API",
    description="MVP interactive stock chart replay with journaling",
    version="0.1.0"
)

# CORS middleware for React frontend
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:3000", "http://localhost:5173"],  # React dev servers
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Database setup
DB_PATH = Path(__file__).parent.parent / "data" / "replay.db"
DB_PATH.parent.mkdir(parents=True, exist_ok=True)

# Session loader
loader = SessionLoader(DB_PATH)

# Active replay sessions (in production, use Redis or similar)
active_sessions: Dict[int, ReplaySession] = {}


# Pydantic models
class CandleData(BaseModel):
    timestamp: str
    open: float
    high: float
    low: float
    close: float
    volume: int = 0


class CreateSessionRequest(BaseModel):
    symbol: str
    session_date: str
    timeframe: str = "1m"
    candles: Optional[List[Dict[str, Any]]] = None


class JournalEntry(BaseModel):
    timestamp: str
    candle_index: Optional[int] = None
    trade_id: Optional[int] = None
    entry_type: str = "note"
    content: str
    emotion_tag: Optional[str] = None
    template_data: Optional[Dict[str, Any]] = None


class PaperTrade(BaseModel):
    entry_timestamp: str
    entry_price: float
    quantity: int
    side: str  # 'long' or 'short'
    entry_type: str = "market"
    stop_loss: Optional[float] = None
    take_profit: Optional[float] = None


# API Routes
@app.get("/")
async def root():
    """Root endpoint."""
    return {"message": "Trading Replay Journal API", "version": "0.1.0"}


@app.get("/sessions")
async def list_sessions(symbol: Optional[str] = Query(None)):
    """List all available replay sessions."""
    sessions = loader.list_sessions(symbol=symbol)
    return {"sessions": sessions}


@app.post("/sessions")
async def create_session(request: CreateSessionRequest):
    """Create a new replay session."""
    try:
        session_id = loader.create_session(
            symbol=request.symbol,
            session_date=request.session_date,
            timeframe=request.timeframe,
            candles=request.candles
        )
        return {"session_id": session_id, "status": "created"}
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))


@app.get("/sessions/{session_id}")
async def get_session(session_id: int):
    """Get session information."""
    info = loader.get_session_info(session_id)
    if not info:
        raise HTTPException(status_code=404, detail="Session not found")
    return info


@app.post("/sessions/{session_id}/replay/start")
async def start_replay(session_id: int):
    """Start a replay session."""
    if session_id not in active_sessions:
        session = ReplaySession(DB_PATH, session_id)
        active_sessions[session_id] = session
    else:
        session = active_sessions[session_id]
    
    session.reset()
    state = session.get_state()
    return {
        "session_id": session_id,
        "state": {
            "current_index": state.current_index,
            "total_candles": state.total_candles,
            "is_playing": state.is_playing,
            "progress": session.get_progress()
        },
        "candles": [
            {
                "timestamp": c.timestamp.isoformat(),
                "open": c.open,
                "high": c.high,
                "low": c.low,
                "close": c.close,
                "volume": c.volume,
                "candle_index": c.candle_index
            }
            for c in state.visible_candles
        ]
    }


@app.get("/sessions/{session_id}/replay/state")
async def get_replay_state(session_id: int):
    """Get current replay state."""
    if session_id not in active_sessions:
        raise HTTPException(status_code=404, detail="Replay session not active")
    
    session = active_sessions[session_id]
    state = session.get_state()
    
    return {
        "session_id": session_id,
        "current_index": state.current_index,
        "total_candles": state.total_candles,
        "is_playing": state.is_playing,
        "playback_speed": state.playback_speed,
        "progress": session.get_progress(),
        "current_candle": {
            "timestamp": state.visible_candles[-1].timestamp.isoformat(),
            "open": state.visible_candles[-1].open,
            "high": state.visible_candles[-1].high,
            "low": state.visible_candles[-1].low,
            "close": state.visible_candles[-1].close,
            "volume": state.visible_candles[-1].volume,
            "candle_index": state.visible_candles[-1].candle_index
        } if state.visible_candles else None,
        "candles": [
            {
                "timestamp": c.timestamp.isoformat(),
                "open": c.open,
                "high": c.high,
                "low": c.low,
                "close": c.close,
                "volume": c.volume,
                "candle_index": c.candle_index
            }
            for c in state.visible_candles
        ]
    }


@app.post("/sessions/{session_id}/replay/step")
async def step_replay(session_id: int, direction: str = Query("forward")):
    """Step replay forward or backward."""
    if session_id not in active_sessions:
        raise HTTPException(status_code=404, detail="Replay session not active")
    
    session = active_sessions[session_id]
    
    if direction == "forward":
        candles = session.step()
    else:
        candles = session.step_back()
    
    state = session.get_state()
    return {
        "session_id": session_id,
        "current_index": state.current_index,
        "progress": session.get_progress(),
        "candles": [
            {
                "timestamp": c.timestamp.isoformat(),
                "open": c.open,
                "high": c.high,
                "low": c.low,
                "close": c.close,
                "volume": c.volume,
                "candle_index": c.candle_index
            }
            for c in candles
        ]
    }


@app.post("/sessions/{session_id}/replay/jump")
async def jump_to_time(session_id: int, timestamp: str = Query(...)):
    """Jump to specific timestamp."""
    if session_id not in active_sessions:
        raise HTTPException(status_code=404, detail="Replay session not active")
    
    session = active_sessions[session_id]
    target_time = datetime.fromisoformat(timestamp)
    candles = session.jump_to_time(target_time)
    
    state = session.get_state()
    return {
        "session_id": session_id,
        "current_index": state.current_index,
        "progress": session.get_progress(),
        "candles": [
            {
                "timestamp": c.timestamp.isoformat(),
                "open": c.open,
                "high": c.high,
                "low": c.low,
                "close": c.close,
                "volume": c.volume,
                "candle_index": c.candle_index
            }
            for c in candles
        ]
    }


@app.post("/sessions/{session_id}/replay/play")
async def play_replay(session_id: int):
    """Start playing replay."""
    if session_id not in active_sessions:
        raise HTTPException(status_code=404, detail="Replay session not active")
    
    active_sessions[session_id].play()
    return {"status": "playing"}


@app.post("/sessions/{session_id}/replay/pause")
async def pause_replay(session_id: int):
    """Pause replay."""
    if session_id not in active_sessions:
        raise HTTPException(status_code=404, detail="Replay session not active")
    
    active_sessions[session_id].pause()
    return {"status": "paused"}


@app.post("/sessions/{session_id}/replay/speed")
async def set_speed(session_id: int, speed: float = Query(...)):
    """Set playback speed."""
    if session_id not in active_sessions:
        raise HTTPException(status_code=404, detail="Replay session not active")
    
    active_sessions[session_id].set_speed(speed)
    return {"status": "speed_set", "speed": speed}


@app.post("/sessions/{session_id}/trades")
async def create_paper_trade(session_id: int, trade: PaperTrade):
    """Create a paper trade."""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    cursor.execute("""
        INSERT INTO paper_trades
        (session_id, entry_timestamp, entry_price, quantity, side, entry_type, stop_loss, take_profit, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open')
    """, (
        session_id,
        trade.entry_timestamp,
        trade.entry_price,
        trade.quantity,
        trade.side,
        trade.entry_type,
        trade.stop_loss,
        trade.take_profit
    ))
    
    trade_id = cursor.lastrowid
    conn.commit()
    conn.close()
    
    return {"trade_id": trade_id, "status": "created"}


@app.post("/sessions/{session_id}/journal")
async def create_journal_entry(session_id: int, entry: JournalEntry):
    """Create a journal entry."""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    template_json = json.dumps(entry.template_data) if entry.template_data else None
    
    cursor.execute("""
        INSERT INTO journal_entries
        (session_id, timestamp, candle_index, trade_id, entry_type, content, emotion_tag, template_data)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    """, (
        session_id,
        entry.timestamp,
        entry.candle_index,
        entry.trade_id,
        entry.entry_type,
        entry.content,
        entry.emotion_tag,
        template_json
    ))
    
    entry_id = cursor.lastrowid
    conn.commit()
    conn.close()
    
    return {"entry_id": entry_id, "status": "created"}


@app.get("/sessions/{session_id}/journal")
async def get_journal_entries(session_id: int):
    """Get all journal entries for a session."""
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    
    cursor.execute("""
        SELECT id, timestamp, candle_index, trade_id, entry_type, content, emotion_tag, template_data, created_at
        FROM journal_entries
        WHERE session_id = ?
        ORDER BY timestamp ASC
    """, (session_id,))
    
    rows = cursor.fetchall()
    conn.close()
    
    entries = []
    for row in rows:
        entries.append({
            "id": row[0],
            "timestamp": row[1],
            "candle_index": row[2],
            "trade_id": row[3],
            "entry_type": row[4],
            "content": row[5],
            "emotion_tag": row[6],
            "template_data": json.loads(row[7]) if row[7] else None,
            "created_at": row[8]
        })
    
    return {"entries": entries}


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)



