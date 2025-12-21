/**
 * Replay Controls Component
 * Play/pause, step, speed control, progress bar
 */

import React from 'react';
import './ReplayControls.css';

interface ReplayControlsProps {
  currentIndex: number;
  totalCandles: number;
  isPlaying: boolean;
  playbackSpeed: number;
  onStepForward: () => void;
  onStepBackward: () => void;
  onTogglePlayPause: () => void;
  onSetSpeed: (speed: number) => void;
}

export const ReplayControls: React.FC<ReplayControlsProps> = ({
  currentIndex,
  totalCandles,
  isPlaying,
  playbackSpeed,
  onStepForward,
  onStepBackward,
  onTogglePlayPause,
  onSetSpeed,
}) => {
  const progress = totalCandles > 0 ? ((currentIndex + 1) / totalCandles) * 100 : 0;

  return (
    <div className="replay-controls">
      <div className="controls-row">
        {/* Step Backward */}
        <button
          className="control-btn step-btn"
          onClick={onStepBackward}
          disabled={currentIndex === 0}
          title="Step Backward"
        >
          ⏮
        </button>

        {/* Play/Pause */}
        <button
          className="control-btn play-pause-btn"
          onClick={onTogglePlayPause}
          title={isPlaying ? 'Pause' : 'Play'}
        >
          {isPlaying ? '⏸' : '▶'}
        </button>

        {/* Step Forward */}
        <button
          className="control-btn step-btn"
          onClick={onStepForward}
          disabled={currentIndex >= totalCandles - 1}
          title="Step Forward"
        >
          ⏭
        </button>

        {/* Speed Controls */}
        <div className="speed-controls">
          <span>Speed:</span>
          {[0.5, 1, 2, 5, 10].map((speed) => (
            <button
              key={speed}
              className={`speed-btn ${playbackSpeed === speed ? 'active' : ''}`}
              onClick={() => onSetSpeed(speed)}
              title={`${speed}x speed`}
            >
              {speed}x
            </button>
          ))}
        </div>
      </div>

      {/* Progress Bar */}
      <div className="progress-container">
        <div className="progress-bar">
          <div
            className="progress-fill"
            style={{ width: `${progress}%` }}
          />
        </div>
        <div className="progress-text">
          {currentIndex + 1} / {totalCandles} candles ({progress.toFixed(1)}%)
        </div>
      </div>
    </div>
  );
};



