# TBOW Bot - TradingView to WordPress Pipeline

Automated trading signal pipeline that receives TradingView alerts, paper trades them, and publishes daily recaps to tradingrobotplug.com.

---

## 🎯 Pipeline Overview

```
TradingView Alerts → Webhook Receiver → Paper Trade Engine → Report Generator → WordPress
         ↓                  ↓                  ↓                   ↓              ↓
    JSON payload      SQLite storage     Trades + P&L         HTML report     Auto-post
```

---

## 📦 Quick Start

### 1. Install Dependencies

```bash
pip install fastapi uvicorn requests pydantic
```

### 2. Configure Environment

Create a `.env` file:

```bash
# Webhook Security
TBOW_WEBHOOK_SECRET=your_long_random_secret_here

# WordPress (tradingrobotplug.com)
WP_BASE=https://tradingrobotplug.com
WP_USER=your_wordpress_username
WP_APP_PASSWORD=xxxx xxxx xxxx xxxx xxxx xxxx

# Options (defaults shown)
TBOW_POST_STATUS=draft
TBOW_WEBHOOK_HOST=0.0.0.0
TBOW_WEBHOOK_PORT=8000
```

### 3. Initialize Database

```bash
python -m tbow_bot init
```

### 4. Start Webhook Server

```bash
python -m tbow_bot serve
# → Listening on http://0.0.0.0:8000/tv-webhook
```

### 5. Configure TradingView Alerts

In TradingView, set webhook URL to:
```
https://YOUR_DOMAIN.com/tv-webhook
```

With JSON payload:
```json
{
  "secret": "your_long_random_secret_here",
  "strategy": "TBOW_v5",
  "symbol": "{{ticker}}",
  "tf": "{{interval}}",
  "event": "{{alert_name}}",
  "price": {{close}},
  "time": "{{time}}"
}
```

### 6. Run Daily Report (after market close)

```bash
# Dry run (generate report, don't publish)
python -m tbow_bot.jobs.daily_report --dry-run

# Publish as draft
python -m tbow_bot.jobs.daily_report

# Publish immediately
python -m tbow_bot.jobs.daily_report --publish
```

---

## 📂 File Structure

```
tbow_bot/
├── __init__.py           # Package init
├── __main__.py           # CLI entry point
├── app.py                # FastAPI webhook server
├── cli.py                # Command-line interface
├── config.py             # Configuration + env vars
├── db.py                 # SQLite database layer
├── paper_engine.py       # Signal → Trade processing
├── post_wp.py            # WordPress publisher
├── report.py             # HTML report generator
├── jobs/
│   └── daily_report.py   # Cron job script
├── templates/            # HTML templates (future)
├── output/               # Generated reports (dry run)
└── tbow.sqlite3          # SQLite database
```

---

## 🔧 CLI Commands

| Command | Description |
|---------|-------------|
| `init` | Initialize the database |
| `serve` | Start webhook server |
| `signals` | List received signals |
| `trades` | List paper trades |
| `process` | Process signals → trades |
| `report` | Generate HTML report |
| `publish` | Publish to WordPress |
| `stats` | Show cumulative statistics |
| `status` | Show system status |

### Examples

```bash
# Check system status
python -m tbow_bot status

# List today's signals
python -m tbow_bot signals --date 2025-12-29

# Process specific date
python -m tbow_bot process --date 2025-12-29 --reprocess

# Generate and save report
python -m tbow_bot report --date 2025-12-29 --output report.html

# Test WordPress connection
python -m tbow_bot publish --test

# Publish report as draft
python -m tbow_bot publish --date 2025-12-29
```

---

## ⏰ Cron Setup

Run daily at 4:10pm CT (after market close + data settlement):

```bash
# Edit crontab
crontab -e

# Add this line (4:10pm CT = 22:10 UTC during CST)
10 22 * * 1-5 cd /path/to/workspace && python -m tbow_bot.jobs.daily_report >> /var/log/tbow_bot.log 2>&1
```

Or use GitHub Actions:

```yaml
# .github/workflows/daily-report.yml
name: TBOW Daily Report
on:
  schedule:
    - cron: '10 22 * * 1-5'  # 4:10pm CT on weekdays
jobs:
  report:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-python@v5
        with:
          python-version: '3.12'
      - run: pip install -r requirements.txt
      - run: python -m tbow_bot.jobs.daily_report
        env:
          WP_BASE: ${{ secrets.WP_BASE }}
          WP_USER: ${{ secrets.WP_USER }}
          WP_APP_PASSWORD: ${{ secrets.WP_APP_PASSWORD }}
```

---

## 🎮 TradingView Alert Setup

Create 4 alerts in TradingView for your TBOW indicator:

1. **TBOW CALL ENTRY** - When call entry condition fires
2. **TBOW PUT ENTRY** - When put entry condition fires
3. **TBOW EXIT CALL** - When call exit condition fires
4. **TBOW EXIT PUT** - When put exit condition fires

Each alert uses the same JSON payload template:

```json
{
  "secret": "YOUR_LONG_SECRET",
  "strategy": "TBOW_v5",
  "symbol": "{{ticker}}",
  "tf": "{{interval}}",
  "event": "CALL ENTRY",
  "price": {{close}},
  "time": "{{time}}"
}
```

Change `"event"` value for each alert type.

---

## 📊 Paper Trading Rules (v1)

**Position Model (Underlying):**
- One position at a time
- Entry on CALL/PUT entry event
- Exit on matching exit event
- Fixed $10k notional for comparability

**Filters:**
- Ignore first 5 minutes after market open
- No new entries after 3:30pm ET

**P&L Calculation:**
- CALL: `(exit_price - entry_price) * shares`
- PUT: `(entry_price - exit_price) * shares`

---

## 🚀 v2 Upgrade: Options Pricing

### Option A: Delta Proxy (Fast)

```python
# In config.py
DEFAULT_DELTA = 0.50  # ATM 7DTE assumption
PER_TRADE_FRICTION = 5.0  # Slippage + commission

# P&L calculation
option_pnl = underlying_pnl * delta - friction
```

### Option B: Real Options Data

Use Polygon/Tradier/ThetaData API to fetch actual option premiums at entry/exit timestamps.

---

## 🔐 WordPress Setup

### Create Application Password

1. Go to WP Admin → Users → Your Profile
2. Scroll to "Application Passwords"
3. Enter name: `TBOW Bot`
4. Click "Add New Application Password"
5. Copy the generated password (spaces included)

### Test Connection

```bash
python -m tbow_bot publish --test
```

---

## 📈 Report Output

The generated HTML report includes:

- **Header**: Date, symbol, strategy
- **Stats Grid**: P&L, trades, win rate, wins/losses
- **Chart Placeholder**: For TradingView screenshot
- **Trade Table**: All trades with entry/exit details
- **Cumulative Stats**: Running totals
- **Disclaimer**: Legal notice

---

## 🛡️ Security Notes

1. **Webhook Secret**: Always use a long, random secret
2. **HTTPS**: Deploy behind HTTPS (use Cloudflare, nginx, etc.)
3. **Rate Limiting**: Consider adding rate limits for production
4. **Validation**: All payloads are validated before processing

---

## 📋 Environment Variables

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `TBOW_WEBHOOK_SECRET` | Yes | - | Webhook authentication secret |
| `WP_BASE` | Yes | tradingrobotplug.com | WordPress site URL |
| `WP_USER` | Yes | - | WordPress username |
| `WP_APP_PASSWORD` | Yes | - | WP Application Password |
| `TBOW_WEBHOOK_HOST` | No | 0.0.0.0 | Webhook server host |
| `TBOW_WEBHOOK_PORT` | No | 8000 | Webhook server port |
| `TBOW_POST_STATUS` | No | draft | Default post status |

---

## 🧪 Testing

```bash
# Test webhook manually
curl -X POST http://localhost:8000/tv-webhook \
  -H "Content-Type: application/json" \
  -d '{
    "secret": "your_secret",
    "symbol": "TSLA",
    "event": "CALL ENTRY",
    "price": 350.50,
    "tf": "5m"
  }'

# Check signals
python -m tbow_bot signals

# Process and generate report
python -m tbow_bot process
python -m tbow_bot report --output test.html
```

---

## 📝 Questions to Finalize

1. **Is tradingrobotplug.com WordPress?** → If yes, WP REST API is perfect ✓
2. **Daily after close or intraday live feed + daily recap?** → Daily after close ✓
3. **For 7DTE options: ATM or 1 ITM?** → Default: ATM

---

## 📜 License

Part of the FreeRideInvestor/TradingRobotPlug project.
