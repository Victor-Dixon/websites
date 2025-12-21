-- Trading Replay Journal System - SQLite Schema
-- MVP: Simple, durable, local-first

-- Symbols table
CREATE TABLE IF NOT EXISTS symbols (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    symbol TEXT NOT NULL UNIQUE,
    exchange TEXT,
    asset_type TEXT DEFAULT 'stock',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sessions table (frozen replay datasets)
CREATE TABLE IF NOT EXISTS sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    symbol_id INTEGER NOT NULL,
    session_date DATE NOT NULL,
    timeframe TEXT DEFAULT '1m',
    candle_count INTEGER DEFAULT 0,
    status TEXT DEFAULT 'ready', -- ready, in_progress, completed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (symbol_id) REFERENCES symbols(id),
    UNIQUE(symbol_id, session_date, timeframe)
);

-- Candles table (OHLCV data)
CREATE TABLE IF NOT EXISTS candles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL,
    timestamp TIMESTAMP NOT NULL,
    open REAL NOT NULL,
    high REAL NOT NULL,
    low REAL NOT NULL,
    close REAL NOT NULL,
    volume INTEGER DEFAULT 0,
    candle_index INTEGER NOT NULL, -- order within session
    FOREIGN KEY (session_id) REFERENCES sessions(id),
    UNIQUE(session_id, timestamp)
);

CREATE INDEX IF NOT EXISTS idx_candles_session ON candles(session_id, candle_index);

-- Paper trades (simulated trading)
CREATE TABLE IF NOT EXISTS paper_trades (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL,
    entry_timestamp TIMESTAMP NOT NULL,
    exit_timestamp TIMESTAMP,
    entry_price REAL NOT NULL,
    exit_price REAL,
    quantity INTEGER NOT NULL,
    side TEXT NOT NULL, -- 'long' or 'short'
    entry_type TEXT DEFAULT 'market', -- 'market' or 'limit'
    stop_loss REAL,
    take_profit REAL,
    pnl REAL,
    r_multiple REAL,
    status TEXT DEFAULT 'open', -- 'open', 'closed', 'stopped'
    FOREIGN KEY (session_id) REFERENCES sessions(id)
);

CREATE INDEX IF NOT EXISTS idx_trades_session ON paper_trades(session_id, entry_timestamp);

-- Journal entries (timestamped notes)
CREATE TABLE IF NOT EXISTS journal_entries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL,
    timestamp TIMESTAMP NOT NULL,
    candle_index INTEGER,
    trade_id INTEGER, -- optional link to paper trade
    entry_type TEXT DEFAULT 'note', -- 'note', 'setup', 'trigger', 'risk', 'result', 'lesson'
    content TEXT NOT NULL,
    emotion_tag TEXT, -- 'fear', 'greed', 'confidence', 'doubt', etc.
    screenshot_path TEXT,
    template_data TEXT, -- JSON for structured templates
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id),
    FOREIGN KEY (trade_id) REFERENCES paper_trades(id)
);

CREATE INDEX IF NOT EXISTS idx_journal_session ON journal_entries(session_id, timestamp);

-- Behavioral scores
CREATE TABLE IF NOT EXISTS scores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL,
    score_type TEXT NOT NULL, -- 'stop_integrity', 'patience', 'rule_adherence', etc.
    score_value REAL NOT NULL, -- 0-100
    details TEXT, -- JSON with score breakdown
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id),
    UNIQUE(session_id, score_type)
);

-- Session summaries
CREATE TABLE IF NOT EXISTS session_summaries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_id INTEGER NOT NULL UNIQUE,
    total_trades INTEGER DEFAULT 0,
    winning_trades INTEGER DEFAULT 0,
    losing_trades INTEGER DEFAULT 0,
    total_pnl REAL DEFAULT 0,
    best_trade_pnl REAL,
    worst_trade_pnl REAL,
    average_r_multiple REAL,
    planned_trades TEXT, -- JSON array
    actual_trades TEXT, -- JSON array
    missed_opportunities TEXT, -- JSON array
    overtrade_alerts TEXT, -- JSON array
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id)
);



