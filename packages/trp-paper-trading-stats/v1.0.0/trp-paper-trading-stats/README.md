# TRP Paper Trading Stats Plugin

WordPress plugin to display paper trading bot statistics and performance metrics on TradingRobotPlug website.

## Features

- **Real-time Stats Display**: Shows paper trading performance metrics
- **Auto-refresh**: Automatically updates stats at configurable intervals
- **Multiple Display Modes**: Full, summary, or compact views
- **Easy Live Trading Switch**: Ready to switch to live trading stats when ready
- **REST API Endpoint**: `/wp-json/trp/v1/paper-trading-stats` for programmatic access

## Installation

1. Upload `trp-paper-trading-stats` folder to `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure project root path (if needed) in plugin settings

## Usage

### Shortcode

Add the shortcode to any page or post:

```
[trp_trading_stats]
```

**Options:**
- `mode`: Display mode (`full`, `summary`, `compact`) - Default: `full`
- `refresh`: Auto-refresh interval in seconds - Default: `60`

**Examples:**
```
[trp_trading_stats mode="full" refresh="60"]
[trp_trading_stats mode="summary"]
[trp_trading_stats mode="compact" refresh="30"]
```

### REST API

Get stats programmatically:

```
GET /wp-json/trp/v1/paper-trading-stats
```

**Response:**
```json
{
  "status": "success",
  "mode": "paper_trading",
  "last_updated": "2025-11-21T18:00:00",
  "stats": {
    "total_trades": 25,
    "winning_trades": 18,
    "losing_trades": 7,
    "total_pnl": 1250.50,
    "win_rate": 72.0,
    "average_win": 150.25,
    "average_loss": -75.50,
    "current_balance": 11250.50,
    "starting_balance": 10000.0,
    "open_positions": 3,
    "closed_positions": 22
  }
}
```

## Configuration

If the Python script path is not automatically detected, configure it:

1. Go to WordPress Admin → Settings → TRP Paper Trading Stats
2. Set "Project Root Path" to your Agent_Cellphone_V2_Repository directory
3. Set "Python Command" if needed (default: `python`)

## Switching to Live Trading

When ready to switch from paper trading to live trading:

1. Update `tools/get_paper_trading_stats.py` to use live trading data source
2. Change `mode` from `"paper_trading"` to `"live_trading"` in the response
3. Plugin will automatically display "Live" badge instead of "Paper Trading"

## Requirements

- WordPress 5.0+
- Python 3.8+ with trading bot dependencies
- Access to `Agent_Cellphone_V2_Repository/tools/get_paper_trading_stats.py`

## Support

For issues or questions, contact the Swarm Intelligence System.

