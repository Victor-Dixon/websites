/**
 * Session Selector Component
 * List and select replay sessions
 */

import React from 'react';
import './SessionSelector.css';

interface Session {
  session_id: number;
  symbol: string;
  session_date: string;
  timeframe: string;
  candle_count: number;
}

interface SessionSelectorProps {
  sessions: Session[];
  selectedSession: number | null;
  onSelectSession: (sessionId: number) => void;
}

export const SessionSelector: React.FC<SessionSelectorProps> = ({
  sessions,
  selectedSession,
  onSelectSession,
}) => {
  return (
    <div className="session-selector">
      <h3>Available Sessions</h3>
      {sessions.length === 0 ? (
        <p className="empty-state">No sessions available. Create one via API.</p>
      ) : (
        <div className="sessions-list">
          {sessions.map((session) => (
            <button
              key={session.session_id}
              className={`session-card ${selectedSession === session.session_id ? 'active' : ''}`}
              onClick={() => onSelectSession(session.session_id)}
            >
              <div className="session-symbol">{session.symbol}</div>
              <div className="session-date">{session.session_date}</div>
              <div className="session-meta">
                {session.timeframe} • {session.candle_count} candles
              </div>
            </button>
          ))}
        </div>
      )}
    </div>
  );
};



