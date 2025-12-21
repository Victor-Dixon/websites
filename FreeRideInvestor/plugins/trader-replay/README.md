# 📊 Trading Replay Journal System

**MVP Interactive Stock Chart Replay Trainer with Journaling**

Think **"TradingView Replay + Journaling + Coach"** in one loop.

---

## 🎯 **What This Is**

A training simulator that lets you:
1. Pick a symbol + date
2. Load that day's intraday data
3. Hit **Replay** - chart advances candle-by-candle
4. Place simulated entries/exits
5. Write **micro-journal notes** at exact timestamps
6. Get auto-scored on behavior (risk discipline, patience, etc.)

---

## 🚀 **Quick Start**

### **Backend (FastAPI)**

```bash
cd trader_replay/backend
pip install fastapi uvicorn sqlite3
python main.py
```

API runs on `http://localhost:8000`

### **Frontend (React)**

```bash
cd trader_replay/frontend
npm install
npm install lightweight-charts
npm start
```

Frontend runs on `http://localhost:3000`

---

## 📁 **Project Structure**

```
trader_replay/
├── backend/
│   ├── main.py              # FastAPI server
│   ├── replay_engine.py     # Core replay logic
│   └── schema.sql           # SQLite schema
├── frontend/
│   └── src/
│       ├── App.tsx          # Main app
│       └── components/
│           ├── ReplayChart.tsx
│           ├── ReplayControls.tsx
│           ├── JournalPanel.tsx
│           ├── TradePanel.tsx
│           └── SessionSelector.tsx
├── data/
│   └── replay.db            # SQLite database (created on first run)
└── README.md
```

---

## 🔧 **API Endpoints**

### **Sessions**
- `GET /sessions` - List all sessions
- `POST /sessions` - Create new session
- `GET /sessions/{id}` - Get session info

### **Replay**
- `POST /sessions/{id}/replay/start` - Start replay
- `GET /sessions/{id}/replay/state` - Get current state
- `POST /sessions/{id}/replay/step` - Step forward/backward
- `POST /sessions/{id}/replay/jump` - Jump to timestamp
- `POST /sessions/{id}/replay/play` - Start playing
- `POST /sessions/{id}/replay/pause` - Pause
- `POST /sessions/{id}/replay/speed` - Set playback speed

### **Trading**
- `POST /sessions/{id}/trades` - Create paper trade

### **Journaling**
- `POST /sessions/{id}/journal` - Add journal entry
- `GET /sessions/{id}/journal` - Get all entries

---

## 📊 **Data Model**

See `backend/schema.sql` for full schema.

**Key Tables**:
- `sessions` - Frozen replay datasets
- `candles` - OHLCV data
- `paper_trades` - Simulated trades
- `journal_entries` - Timestamped notes
- `scores` - Behavioral scoring

---

## 🎮 **MVP Features**

✅ **Replay Core**
- Intraday candles (1m default)
- Play / Pause / Step controls
- Speed control (x1/x2/x5/x10)
- Fog-of-war mode (can't see future)

✅ **Paper Trading**
- Market/limit orders
- Stop loss / Take profit
- Auto P&L calculation

✅ **Journaling**
- Timestamped notes
- Emotion tags
- Template support

---

## 🔮 **Roadmap**

**V1**: Strategy overlays, behavioral scoring, review mode  
**V2**: Multi-day campaigns, AI coach, agent-driven pipeline

---

## 📝 **License**

MIT

---

**Built for disciplined trading training. Turn your journal into a skill lab.** 🎯



