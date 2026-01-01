# FreeRide Automated Trading Plan Plugin

Automates daily trading plans based on TradingView strategies with MA, RSI, and risk management. Generates actionable daily plans for TSLA and other stocks.

## Features

- **Automated Strategy Calculation**: Implements TradingView strategy logic (MA, RSI, risk management)
- **Daily Plan Generation**: Automatically generates trading plans via cron job
- **TBOW Integration**: Automatically creates TBOW tactic posts in WordPress format
- **Multiple API Support**: Works with Alpha Vantage and Finnhub APIs
- **Risk Management**: True risk-based position sizing and stop loss calculation
- **Trailing Stops**: Configurable trailing stop functionality
- **Frontend Display**: Shortcodes for displaying plans on frontend
- **Admin Dashboard**: Full admin interface for configuration and plan management

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Trading Plans > Settings** to configure the plugin

## Configuration

### API Keys

You need at least one API key to fetch market data:

- **Alpha Vantage**: Get a free API key at [alphavantage.co](https://www.alphavantage.co/support/#api-key)
- **Finnhub**: Get a free API key at [finnhub.io](https://finnhub.io/register)

### Strategy Parameters

- **Short Moving Average**: Period length (default: 50)
- **Long Moving Average**: Period length (default: 200)
- **RSI Length**: Period length (default: 14)
- **RSI Overbought**: Threshold (default: 60)
- **RSI Oversold**: Threshold (default: 40)

### Risk Management

- **Risk % of Equity**: Risk percentage per trade (default: 0.5%)
- **Stop Loss %**: Stop loss percentage of price (default: 1.0%)
- **Profit Target %**: Profit target percentage (default: 15.0%)

### Trailing Stop

- **Use Trailing Stop**: Enable/disable trailing stops
- **Trail Offset %**: Trail offset percentage (default: 0.5%)
- **Trail Trigger %**: Trail trigger percentage (default: 5.0%)

### TBOW Integration

- **Create TBOW Posts**: Automatically create TBOW tactic posts from trading plans
- **TBOW Category**: WordPress category slug for TBOW posts (default: tbow-tactic)

When enabled, each daily trading plan is automatically published as a TBOW tactic post using the standard TBOW template format with 7 sections:
1. Contextual Insight
2. Tactic Objective
3. Key Levels to Watch
4. Actionable Steps (Short/Long/Options)
5. Real-Time Monitoring
6. Risk Management and Adaptability
7. Execution Checklist

## Usage

### Shortcodes

**Display Daily Plan:**
```
[fratp_daily_plan symbol="TSLA" date="today"]
```

**Display Strategy Status:**
```
[fratp_strategy_status symbol="TSLA"]
```

### Cron Job

The plugin automatically generates daily plans via WordPress cron. Plans are generated once per day for all configured stock symbols.

### Manual Generation

You can manually generate plans from:
- **Trading Plans > Settings** (bottom of page)
- **Trading Plans > Daily Plans** (generate new plan section)

### TBOW Post Creation

When "Create TBOW Posts" is enabled in settings:
- Each daily plan automatically creates a WordPress post in TBOW format
- Posts are categorized under the specified TBOW category
- Posts use the standard TBOW template structure
- Existing posts are updated if they already exist for that symbol/date

## Strategy Logic

The plugin implements the following TradingView strategy:

### Entry Conditions

**Long Entry:**
- Price is above both MA50 and MA200
- RSI is below overbought threshold (default: 60)

**Short Entry:**
- Price is below both MA50 and MA200
- RSI is above oversold threshold (default: 40)

### Position Sizing

Position size is calculated based on:
- Current equity
- Risk percentage per trade
- Stop loss distance from entry price

Formula: `Position Size = (Equity × Risk%) / Stop Distance`

### Exit Conditions

- **Stop Loss**: Fixed percentage from entry price
- **Profit Target**: Fixed percentage from entry price
- **Trailing Stop**: Optional trailing stop after trigger

## Database

The plugin creates a custom table `wp_fratp_trading_plans` to store generated plans.

## Requirements

- WordPress 5.0+
- PHP 7.4+
- At least one API key (Alpha Vantage or Finnhub)

## Support

For issues or questions, visit [FreeRideInvestor.com](https://freerideinvestor.com)

## License

GPL v2 or later

