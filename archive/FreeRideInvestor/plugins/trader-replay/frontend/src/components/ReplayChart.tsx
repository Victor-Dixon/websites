/**
 * Replay Chart Component
 * Uses lightweight-charts for fast, simple candlestick rendering
 */

import React, { useEffect, useRef } from 'react';
import { createChart, ColorType, IChartApi, ISeriesApi, CandlestickData } from 'lightweight-charts';

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
  side: 'long' | 'short';
  status: 'open' | 'closed' | 'stopped';
}

interface ReplayChartProps {
  candles: Candle[];
  currentIndex: number;
  trades: Trade[];
}

export const ReplayChart: React.FC<ReplayChartProps> = ({ candles, currentIndex, trades }) => {
  const chartContainerRef = useRef<HTMLDivElement>(null);
  const chartRef = useRef<IChartApi | null>(null);
  const seriesRef = useRef<ISeriesApi<'Candlestick'> | null>(null);

  useEffect(() => {
    if (!chartContainerRef.current) return;

    // Create chart
    const chart = createChart(chartContainerRef.current, {
      layout: {
        background: { type: ColorType.Solid, color: '#1e1e1e' },
        textColor: '#d1d5db',
      },
      width: chartContainerRef.current.clientWidth,
      height: 600,
      grid: {
        vertLines: { color: '#2a2a2a' },
        horzLines: { color: '#2a2a2a' },
      },
      timeScale: {
        timeVisible: true,
        secondsVisible: false,
      },
    });

    chartRef.current = chart;

    // Create candlestick series
    const candlestickSeries = chart.addCandlestickSeries({
      upColor: '#26a69a',
      downColor: '#ef5350',
      borderVisible: false,
      wickUpColor: '#26a69a',
      wickDownColor: '#ef5350',
    });

    seriesRef.current = candlestickSeries;

    // Handle resize
    const handleResize = () => {
      if (chartContainerRef.current && chart) {
        chart.applyOptions({
          width: chartContainerRef.current.clientWidth,
        });
      }
    };

    window.addEventListener('resize', handleResize);

    return () => {
      window.removeEventListener('resize', handleResize);
      chart.remove();
    };
  }, []);

  // Update chart data when candles change
  useEffect(() => {
    if (!seriesRef.current) return;

    // Convert candles to chart format (only visible candles - fog of war)
    const chartData: CandlestickData[] = candles.map((candle) => {
      const timestamp = new Date(candle.timestamp).getTime() / 1000; // Convert to Unix timestamp
      return {
        time: timestamp as any,
        open: candle.open,
        high: candle.high,
        low: candle.low,
        close: candle.close,
      };
    });

    seriesRef.current.setData(chartData);

    // Scroll to current candle
    if (chartData.length > 0 && chartRef.current) {
      const currentCandle = chartData[chartData.length - 1];
      chartRef.current.timeScale().scrollToPosition(-1, false);
    }
  }, [candles, currentIndex]);

  return (
    <div className="replay-chart-container">
      <div ref={chartContainerRef} className="replay-chart" />
      {candles.length === 0 && (
        <div className="chart-empty-state">
          <p>No candles loaded. Start a replay session to begin.</p>
        </div>
      )}
    </div>
  );
};



