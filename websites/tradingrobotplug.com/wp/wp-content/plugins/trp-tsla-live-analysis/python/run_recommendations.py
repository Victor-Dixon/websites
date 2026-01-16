#!/usr/bin/env python3
"""
TSLA Recommendations Runner for WordPress Plugin
===============================================

Runs TSLA AI recommendations and outputs JSON for WordPress consumption.
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
        output_mock_recommendations()
        return

    try:
        # Initialize Alpha Vantage provider
        api_key = os.environ.get('ALPHA_VANTAGE_API_KEY', 'demo')
        provider = AlphaVantageMarketDataProvider(api_key)

        # Build TSLA snapshot and get recommendations
        snapshot, recommendations = build_snapshot('TSLA', provider)

        # Format recommendations for WordPress
        output = {
            'status': 'success',
            'recommendations': {
                'action': recommendations.get('action', 'HOLD'),
                'confidence': recommendations.get('confidence', 0.5),
                'target_price': recommendations.get('target_price', 0),
                'stop_loss': recommendations.get('stop_loss', 0),
                'timeframe': recommendations.get('timeframe', 'unknown'),
                'regime': recommendations.get('regime', 'neutral'),
                'reasoning': recommendations.get('reasoning', 'Analysis in progress'),
                'key_levels': recommendations.get('key_levels', {}),
                'risk_reward_ratio': recommendations.get('risk_reward_ratio', 0)
            },
            'market_context': {
                'trend': snapshot.get('regime', 'neutral'),
                'volatility': snapshot.get('volatility', 'medium'),
                'momentum': snapshot.get('momentum', 'neutral'),
                'support_levels': snapshot.get('support_levels', []),
                'resistance_levels': snapshot.get('resistance_levels', [])
            },
            'timestamp': datetime.now(timezone.utc).isoformat(),
            'powered_by': 'TradingRobotPlug AI Swarm'
        }

        print(json.dumps(output, indent=2))

    except Exception as e:
        print(f"Error generating recommendations: {e}", file=sys.stderr)
        output_mock_recommendations()

def output_mock_recommendations():
    """Output mock recommendation data"""
    mock_data = {
        'status': 'mock_data',
        'message': 'Using demonstration data - Full AI recommendations require Alpha Vantage API key',
        'recommendations': {
            'action': 'BUY',
            'confidence': 0.84,
            'target_price': 275.00,
            'stop_loss': 240.00,
            'timeframe': '3-7 days',
            'regime': 'bullish_trend',
            'reasoning': 'TSLA showing strong bullish momentum with VWAP support established, EMA9 crossing above EMA21 indicating upward trend continuation. Premarket gap up suggests positive sentiment. Volume analysis shows accumulation pattern. Risk-reward ratio favors long position with 15% upside potential vs 5% downside risk.',
            'key_levels': {
                'entry': 252.75,
                'target_1': 265.00,
                'target_2': 275.00,
                'stop_loss': 240.00,
                'support': 248.50,
                'resistance': 258.75
            },
            'risk_reward_ratio': 2.4
        },
        'market_context': {
            'trend': 'bullish',
            'volatility': 'moderate',
            'momentum': 'strong_positive',
            'support_levels': [248.50, 244.25, 238.00],
            'resistance_levels': [258.75, 265.00, 275.00]
        },
        'timestamp': datetime.now(timezone.utc).isoformat(),
        'powered_by': 'TradingRobotPlug AI Swarm (Demo Mode)'
    }

    print(json.dumps(mock_data, indent=2))

if __name__ == '__main__':
    main()