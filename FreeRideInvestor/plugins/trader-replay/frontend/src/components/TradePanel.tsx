/**
 * Trade Panel Component
 * Paper trading interface
 */

import React, { useState } from 'react';
import './TradePanel.css';

interface Candle {
  timestamp: string;
  open: number;
  high: number;
  low: number;
  close: number;
  volume: number;
  candle_index: number;
}

interface Trade {
  id?: number;
  entry_timestamp: string;
  exit_timestamp?: string;
  entry_price: number;
  exit_price?: number;
  quantity: number;
  side: 'long' | 'short';
  status: 'open' | 'closed' | 'stopped';
  pnl?: number;
}

interface TradePanelProps {
  currentCandle: Candle | undefined;
  trades: Trade[];
  onAddTrade: (trade: Partial<Trade>) => void;
}

export const TradePanel: React.FC<TradePanelProps> = ({ currentCandle, trades, onAddTrade }) => {
  const [showForm, setShowForm] = useState(false);
  const [side, setSide] = useState<'long' | 'short'>('long');
  const [quantity, setQuantity] = useState(100);
  const [stopLoss, setStopLoss] = useState('');
  const [takeProfit, setTakeProfit] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!currentCandle) return;

    onAddTrade({
      side,
      quantity,
      entry_type: 'market',
      stop_loss: stopLoss ? parseFloat(stopLoss) : undefined,
      take_profit: takeProfit ? parseFloat(takeProfit) : undefined,
    });

    // Reset form
    setStopLoss('');
    setTakeProfit('');
    setShowForm(false);
  };

  const openTrades = trades.filter((t) => t.status === 'open');
  const closedTrades = trades.filter((t) => t.status !== 'open');

  return (
    <div className="trade-panel">
      <div className="panel-header">
        <h3>💼 Paper Trades</h3>
        {currentCandle && (
          <button className="add-btn" onClick={() => setShowForm(!showForm)}>
            {showForm ? '✕' : '+'}
          </button>
        )}
      </div>

      {showForm && currentCandle && (
        <form className="trade-form" onSubmit={handleSubmit}>
          <div className="form-row">
            <label>Side:</label>
            <select value={side} onChange={(e) => setSide(e.target.value as 'long' | 'short')}>
              <option value="long">Long</option>
              <option value="short">Short</option>
            </select>
          </div>

          <div className="form-row">
            <label>Quantity:</label>
            <input
              type="number"
              value={quantity}
              onChange={(e) => setQuantity(parseInt(e.target.value) || 0)}
              min="1"
            />
          </div>

          <div className="form-row">
            <label>Entry Price:</label>
            <input type="number" value={currentCandle.close.toFixed(2)} disabled />
          </div>

          <div className="form-row">
            <label>Stop Loss:</label>
            <input
              type="number"
              value={stopLoss}
              onChange={(e) => setStopLoss(e.target.value)}
              placeholder="Optional"
              step="0.01"
            />
          </div>

          <div className="form-row">
            <label>Take Profit:</label>
            <input
              type="number"
              value={takeProfit}
              onChange={(e) => setTakeProfit(e.target.value)}
              placeholder="Optional"
              step="0.01"
            />
          </div>

          <button type="submit" className="submit-btn">
            Place Trade
          </button>
        </form>
      )}

      <div className="trades-list">
        <h4>Open Trades ({openTrades.length})</h4>
        {openTrades.length === 0 ? (
          <p className="empty-state">No open trades</p>
        ) : (
          openTrades.map((trade, idx) => (
            <div key={trade.id || idx} className="trade-item open">
              <div className="trade-header">
                <span className="trade-side">{trade.side.toUpperCase()}</span>
                <span className="trade-quantity">{trade.quantity} shares</span>
              </div>
              <div className="trade-price">
                Entry: ${trade.entry_price.toFixed(2)}
                {trade.pnl !== undefined && (
                  <span className={`pnl ${trade.pnl >= 0 ? 'positive' : 'negative'}`}>
                    P&L: ${trade.pnl.toFixed(2)}
                  </span>
                )}
              </div>
            </div>
          ))
        )}

        {closedTrades.length > 0 && (
          <>
            <h4>Closed Trades ({closedTrades.length})</h4>
            {closedTrades.map((trade, idx) => (
              <div key={trade.id || idx} className="trade-item closed">
                <div className="trade-header">
                  <span className="trade-side">{trade.side.toUpperCase()}</span>
                  <span className="trade-status">{trade.status}</span>
                </div>
                <div className="trade-price">
                  ${trade.entry_price.toFixed(2)} → ${trade.exit_price?.toFixed(2) || 'N/A'}
                  {trade.pnl !== undefined && (
                    <span className={`pnl ${trade.pnl >= 0 ? 'positive' : 'negative'}`}>
                      ${trade.pnl.toFixed(2)}
                    </span>
                  )}
                </div>
              </div>
            ))}
          </>
        )}
      </div>
    </div>
  );
};



