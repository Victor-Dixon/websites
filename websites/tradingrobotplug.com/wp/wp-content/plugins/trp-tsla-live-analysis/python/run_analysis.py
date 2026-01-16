#!/usr/bin/env python3
"""
TSLA Analysis Runner for WordPress Plugin
==========================================

Runs the TSLA analysis system and outputs JSON for WordPress consumption.
"""

import sys
import os
import json
from datetime import datetime, timezone

# Add the main repository path to Python path
sys.path.insert(0, 'D:/Agent_Cellphone_V2_Repository/src')

try:
    from trading_robot.tsla_report.reporting import build_snapshot
    from trading_robot.tsla_report.providers.alpha_vantage import AlphaVantageMarketDataProvider

    PYTHON_AVAILABLE = True
except ImportError as e:
    print(f"Warning: Could not import trading modules: {e}", file=sys.stderr)
    PYTHON_AVAILABLE = False

def main():
    if not PYTHON_AVAILABLE:
        # Output mock data if Python modules not available
        output_mock_data()
        return

    try:
        # Initialize Alpha Vantage provider
        # Note: In production, you'd get API key from WordPress options
        api_key = os.environ.get('ALPHA_VANTAGE_API_KEY', 'demo')
        provider = AlphaVantageMarketDataProvider(api_key)

        # Build TSLA snapshot
        snapshot, recommendations = build_snapshot('TSLA', provider)

        # Format for WordPress
        output = {
            'status': 'success',
            'data': {
                'snapshot': {
                    'price': snapshot.get('price', 0),
                    'vwap': snapshot.get('vwap', 0),
                    'ema9': snapshot.get('ema9', 0),
                    'ema21': snapshot.get('ema21', 0),
                    'regime': snapshot.get('regime', 'unknown'),
                    'confidence': snapshot.get('confidence_score', 0),
                    'levels': snapshot.get('levels', {}),
                    'gap_pct': snapshot.get('gap_pct', 0)
                },
                'recommendations': {
                    'action': recommendations.get('action', 'HOLD'),
                    'confidence': recommendations.get('confidence', 0),
                    'target_price': recommendations.get('target_price', 0),
                    'stop_loss': recommendations.get('stop_loss', 0),
                    'timeframe': recommendations.get('timeframe', 'unknown'),
                    'regime': recommendations.get('regime', 'unknown'),
                    'reasoning': recommendations.get('reasoning', '')
                }
            },
            'timestamp': datetime.now(timezone.utc).isoformat(),
            'powered_by': 'TradingRobotPlug AI Swarm'
        }

        print(json.dumps(output, indent=2))

    except Exception as e:
        print(f"Error running TSLA analysis: {e}", file=sys.stderr)
        output_mock_data()

def output_mock_data():
    """Output mock data when Python analysis is not available"""
    mock_data = {
        'status': 'mock_data',
        'message': 'Using demonstration data - Full analysis requires Alpha Vantage API key',
        'data': {
            'snapshot': {
                'price': 252.75,
                'vwap': 249.30,
                'ema9': 251.45,
                'ema21': 248.90,
                'regime': 'bullish_trend',
                'confidence': 0.82,
                'levels': {
                    'PMH': 255.20,
                    'PML': 250.10,
                    'PDH': 248.90,
                    'PDL': 235.40,
                    'VWAP': 249.30,
                    'ATR_UP': 257.15,
                    'ATR_DN': 241.85
                },
                'gap_pct': 1.8
            },
            'recommendations': {
                'action': 'BUY',
                'confidence': 0.79,
                'target_price': 268.50,
                'stop_loss': 238.00,
                'timeframe': '3-5 days',
                'regime': 'bullish_trend',
                'reasoning': 'Strong technical setup with VWAP support, EMA9 crossing above EMA21, positive momentum indicators, and gap up opening suggesting bullish sentiment.'
            }
        },
        'timestamp': datetime.now(timezone.utc).isoformat(),
        'powered_by': 'TradingRobotPlug AI Swarm (Demo Mode)'
    }

    print(json.dumps(mock_data, indent=2))

if __name__ == '__main__':
    main()