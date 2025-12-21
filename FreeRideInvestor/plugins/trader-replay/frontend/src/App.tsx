/**
 * Trading Replay Journal - Main App Component
 * MVP: Single "Replay Day" screen with chart + controls
 */

import React, { useState, useEffect } from 'react';
import { ReplayChart } from './components/ReplayChart';
import { ReplayControls } from './components/ReplayControls';
import { JournalPanel } from './components/JournalPanel';
import { TradePanel } from './components/TradePanel';
import { SessionSelector } from './components/SessionSelector';
import './App.css';

const API_BASE = 'http://localhost:8000';

interface Candle {
  timestamp: string;
  open: number;
  high: number;
  low: number;
  close: number;
  volume: number;
  candle_index: number;
}

interface Session {
  session_id: number;
  symbol: string;
  session_date: string;
  timeframe: string;
  candle_count: number;
}

function App() {
  const [sessions, setSessions] = useState<Session[]>([]);
  const [selectedSession, setSelectedSession] = useState<number | null>(null);
  const [candles, setCandles] = useState<Candle[]>([]);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isPlaying, setIsPlaying] = useState(false);
  const [playbackSpeed, setPlaybackSpeed] = useState(1.0);
  const [journalEntries, setJournalEntries] = useState<any[]>([]);
  const [trades, setTrades] = useState<any[]>([]);

  // Load sessions on mount
  useEffect(() => {
    fetchSessions();
  }, []);

  // Auto-step when playing
  useEffect(() => {
    if (!isPlaying || !selectedSession) return;

    const interval = setInterval(() => {
      stepForward();
    }, 1000 / playbackSpeed); // Adjust interval based on speed

    return () => clearInterval(interval);
  }, [isPlaying, playbackSpeed, selectedSession]);

  const fetchSessions = async () => {
    try {
      const response = await fetch(`${API_BASE}/sessions`);
      const data = await response.json();
      setSessions(data.sessions || []);
    } catch (error) {
      console.error('Failed to fetch sessions:', error);
    }
  };

  const startSession = async (sessionId: number) => {
    try {
      const response = await fetch(`${API_BASE}/sessions/${sessionId}/replay/start`, {
        method: 'POST',
      });
      const data = await response.json();
      setSelectedSession(sessionId);
      setCandles(data.candles || []);
      setCurrentIndex(0);
      setIsPlaying(false);
      await loadJournalEntries(sessionId);
      await loadTrades(sessionId);
    } catch (error) {
      console.error('Failed to start session:', error);
    }
  };

  const stepForward = async () => {
    if (!selectedSession) return;
    try {
      const response = await fetch(`${API_BASE}/sessions/${selectedSession}/replay/step?direction=forward`, {
        method: 'POST',
      });
      const data = await response.json();
      setCandles(data.candles || []);
      setCurrentIndex(data.current_index || 0);
    } catch (error) {
      console.error('Failed to step forward:', error);
    }
  };

  const stepBackward = async () => {
    if (!selectedSession) return;
    try {
      const response = await fetch(`${API_BASE}/sessions/${selectedSession}/replay/step?direction=backward`, {
        method: 'POST',
      });
      const data = await response.json();
      setCandles(data.candles || []);
      setCurrentIndex(data.current_index || 0);
    } catch (error) {
      console.error('Failed to step backward:', error);
    }
  };

  const togglePlayPause = async () => {
    if (!selectedSession) return;
    try {
      if (isPlaying) {
        await fetch(`${API_BASE}/sessions/${selectedSession}/replay/pause`, { method: 'POST' });
        setIsPlaying(false);
      } else {
        await fetch(`${API_BASE}/sessions/${selectedSession}/replay/play`, { method: 'POST' });
        setIsPlaying(true);
      }
    } catch (error) {
      console.error('Failed to toggle play/pause:', error);
    }
  };

  const setSpeed = async (speed: number) => {
    if (!selectedSession) return;
    try {
      await fetch(`${API_BASE}/sessions/${selectedSession}/replay/speed?speed=${speed}`, {
        method: 'POST',
      });
      setPlaybackSpeed(speed);
    } catch (error) {
      console.error('Failed to set speed:', error);
    }
  };

  const loadJournalEntries = async (sessionId: number) => {
    try {
      const response = await fetch(`${API_BASE}/sessions/${sessionId}/journal`);
      const data = await response.json();
      setJournalEntries(data.entries || []);
    } catch (error) {
      console.error('Failed to load journal entries:', error);
    }
  };

  const loadTrades = async (sessionId: number) => {
    // TODO: Implement trade loading
    setTrades([]);
  };

  const addJournalEntry = async (entry: any) => {
    if (!selectedSession) return;
    try {
      const currentCandle = candles[currentIndex];
      await fetch(`${API_BASE}/sessions/${selectedSession}/journal`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          timestamp: currentCandle?.timestamp || new Date().toISOString(),
          candle_index: currentIndex,
          entry_type: entry.entry_type || 'note',
          content: entry.content,
          emotion_tag: entry.emotion_tag,
        }),
      });
      await loadJournalEntries(selectedSession);
    } catch (error) {
      console.error('Failed to add journal entry:', error);
    }
  };

  const addPaperTrade = async (trade: any) => {
    if (!selectedSession) return;
    try {
      const currentCandle = candles[currentIndex];
      await fetch(`${API_BASE}/sessions/${selectedSession}/trades`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          entry_timestamp: currentCandle?.timestamp || new Date().toISOString(),
          entry_price: currentCandle?.close || 0,
          quantity: trade.quantity,
          side: trade.side,
          entry_type: trade.entry_type || 'market',
          stop_loss: trade.stop_loss,
          take_profit: trade.take_profit,
        }),
      });
      await loadTrades(selectedSession);
    } catch (error) {
      console.error('Failed to add paper trade:', error);
    }
  };

  return (
    <div className="app">
      <header className="app-header">
        <h1>📊 Trading Replay Journal</h1>
        <p>Train. Journal. Level Up.</p>
      </header>

      <div className="app-content">
        {/* Session Selector */}
        <SessionSelector
          sessions={sessions}
          selectedSession={selectedSession}
          onSelectSession={startSession}
        />

        {/* Main Replay Area */}
        {selectedSession && (
          <>
            <div className="replay-area">
              {/* Chart */}
              <ReplayChart
                candles={candles}
                currentIndex={currentIndex}
                trades={trades}
              />

              {/* Controls */}
              <ReplayControls
                currentIndex={currentIndex}
                totalCandles={candles.length}
                isPlaying={isPlaying}
                playbackSpeed={playbackSpeed}
                onStepForward={stepForward}
                onStepBackward={stepBackward}
                onTogglePlayPause={togglePlayPause}
                onSetSpeed={setSpeed}
              />
            </div>

            {/* Side Panels */}
            <div className="side-panels">
              <TradePanel
                currentCandle={candles[currentIndex]}
                trades={trades}
                onAddTrade={addPaperTrade}
              />
              <JournalPanel
                entries={journalEntries}
                currentIndex={currentIndex}
                onAddEntry={addJournalEntry}
              />
            </div>
          </>
        )}

        {!selectedSession && (
          <div className="welcome-screen">
            <h2>Select a session to begin</h2>
            <p>Load a market day to start your replay training session.</p>
          </div>
        )}
      </div>
    </div>
  );
}

export default App;



