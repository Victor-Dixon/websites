# TRP TSLA Live Analysis Plugin

**Live TSLA market analysis with real-time indicators, regime analysis, and AI-powered trading insights powered by the swarm's advanced trading algorithms.**

## 🚀 Features

- **Real-time Technical Indicators**: VWAP, EMA9/21, ATR, Premarket stats
- **AI-Powered Recommendations**: Buy/Sell signals with confidence scores
- **Live Market Data**: Alpha Vantage integration for real-time TSLA data
- **Responsive Design**: Mobile-first design with touch support
- **REST API**: WordPress REST endpoints for data access
- **Caching System**: 5-minute cache for performance
- **Fallback Mode**: Graceful degradation with demo data

## 📦 Installation

1. Upload the `trp-tsla-live-analysis` folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress admin
3. Configure Alpha Vantage API key (optional - works with demo data)

## ⚙️ Configuration

### Alpha Vantage API Key (Recommended)

For live data, add your Alpha Vantage API key to `wp-config.php`:

```php
define('ALPHA_VANTAGE_API_KEY', 'your_api_key_here');
```

Get your free API key at: https://www.alphavantage.co/support/#api-key

### Environment Variables (Alternative)

Set the API key as an environment variable:
```bash
export ALPHA_VANTAGE_API_KEY=your_api_key_here
```

## 🎯 Usage

### Shortcodes

#### Full Analysis
```
[trp_tsla_analysis]
```

#### Recommendations Only
```
[trp_tsla_recommendations]
```

#### Indicators Only
```
[trp_tsla_indicators]
```

### PHP Integration

```php
<?php echo do_shortcode('[trp_tsla_analysis]'); ?>
```

### REST API Endpoints

- `GET /wp-json/trp-tsla/v1/analysis` - Full analysis data
- `GET /wp-json/trp-tsla/v1/recommendations` - AI recommendations
- `GET /wp-json/trp-tsla/v1/indicators` - Technical indicators

## 📊 Data Sources

### Live Data (With API Key)
- **Intraday Bars**: 1-minute and 5-minute intervals
- **Daily Bars**: Historical daily data
- **Premarket Stats**: Opening price action
- **Real-time Updates**: Every 30 seconds

### Demo Data (Fallback)
- **Mock Indicators**: Realistic sample data
- **AI Recommendations**: Swarm-generated analysis
- **Educational Examples**: Teaching tool functionality

## 🎨 Customization

### CSS Classes

```css
.trp-tsla-analysis         /* Main container */
.indicators-grid          /* Technical indicators grid */
.recommendations-card     /* AI recommendations card */
.indicator-card           /* Individual indicator cards */
.stat-item                /* Recommendation stats */
```

### JavaScript Events

```javascript
// Refresh all data
jQuery(document).trigger('trp-tsla-refresh');

// Load specific analysis
window.trpTSLAAnalysis.loadAnalysisData(jQuery('.trp-tsla-analysis'));
```

## 🔧 Technical Details

### Dependencies

- **Python Trading Robot**: `D:/Agent_Cellphone_V2_Repository/src/trading_robot/`
- **Alpha Vantage API**: Real-time market data
- **WordPress REST API**: Data endpoints
- **jQuery**: Frontend interactions

### File Structure

```
trp-tsla-live-analysis/
├── trp-tsla-live-analysis.php    # Main plugin file
├── python/
│   ├── run_analysis.py          # Full analysis runner
│   ├── run_indicators.py        # Indicators calculator
│   └── run_recommendations.py   # AI recommendations
├── assets/
│   ├── css/analysis.css         # Frontend styles
│   └── js/analysis.js           # Frontend JavaScript
└── README.md                    # This file
```

### Performance

- **Caching**: 5-minute data cache
- **Lazy Loading**: Progressive content loading
- **Error Handling**: Graceful degradation
- **Responsive**: Mobile-optimized

## 🚨 Important Notes

### Security
- **API Keys**: Never commit API keys to version control
- **File Permissions**: Ensure Python scripts are executable
- **WordPress Security**: Keep WordPress and plugins updated

### Troubleshooting

#### No Data Showing
1. Check PHP error logs
2. Verify Python path in plugin settings
3. Check Alpha Vantage API key
4. Test REST endpoints directly

#### Slow Loading
1. Enable caching in WordPress
2. Check server resources
3. Reduce API call frequency
4. Use demo mode for testing

#### Python Errors
1. Verify Python 3.8+ installation
2. Check module imports
3. Test Python scripts directly
4. Check file permissions

## 🤝 Contributing

This plugin integrates with the TradingRobotPlug swarm intelligence system. For contributions:

1. Test with both live and demo data
2. Follow WordPress coding standards
3. Add proper error handling
4. Update documentation

## 📈 Roadmap

- **Real-time WebSocket Updates**: Live price feeds
- **Strategy Backtesting Interface**: Historical performance
- **Portfolio Integration**: User portfolio tracking
- **Multi-Asset Support**: Beyond TSLA
- **Advanced AI Models**: Enhanced prediction algorithms

## 📞 Support

For issues with this plugin, check:

1. WordPress error logs
2. Python script execution
3. API key configuration
4. Network connectivity

**Built by the TradingRobotPlug AI Swarm** 🐝🤖

---

*This plugin demonstrates the power of swarm intelligence by exposing the sophisticated trading algorithms developed by the 8-agent AI system.*