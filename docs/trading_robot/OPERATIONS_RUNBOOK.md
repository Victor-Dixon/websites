# Trading Robot Operations Runbook

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-27  
**Status:** ACTIVE - Operations Documentation  
**Purpose:** Complete operations guide for Trading Robot system startup, shutdown, emergency procedures, and troubleshooting

<!-- SSOT Domain: documentation -->

---

## Executive Summary

This runbook provides comprehensive operational procedures for the Trading Robot system, including startup, shutdown, emergency stop procedures, troubleshooting, and incident response. This document is essential for safe and reliable system operations.

**System:** Trading Robot (Alpaca/Robinhood Multi-Broker)  
**Version:** 1.0.0  
**Trading Modes:** Paper Trading (default), Live Trading  
**Risk Management:** Built-in (daily loss limits, position limits, emergency stops)

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Prerequisites](#prerequisites)
3. [Startup Procedures](#startup-procedures)
4. [Shutdown Procedures](#shutdown-procedures)
5. [Emergency Stop Procedures](#emergency-stop-procedures)
6. [Daily Operations](#daily-operations)
7. [Troubleshooting](#troubleshooting)
8. [Common Issues and Solutions](#common-issues-and-solutions)
9. [Incident Response](#incident-response)
10. [Monitoring and Alerts](#monitoring-and-alerts)

---

## System Overview

### Architecture

The Trading Robot system consists of:

- **Trading Engine** (`core/trading_engine.py`): Core trading logic and broker API management
- **Web Dashboard** (`web/dashboard.py`): Real-time monitoring and control interface
- **Risk Manager** (`core/risk_manager.py`): Risk management and safety mechanisms
- **Strategy System** (`strategies/`): Trading strategy implementations
- **Plugin System** (`plugins/`): Extensible plugin architecture
- **Database** (`database/`): Trade history and performance tracking

### Key Features

- **Multi-Broker Support:** Alpaca and Robinhood
- **Paper Trading:** Safe testing environment (default)
- **Live Trading:** Production trading (requires explicit enablement)
- **Risk Management:** Daily loss limits, position limits, emergency stops
- **Real-time Monitoring:** Web dashboard for live monitoring
- **Automated Trading:** Strategy-based automated trading execution

---

## Prerequisites

### System Requirements

- **Python:** 3.11 or higher
- **Operating System:** Windows, Linux, or macOS
- **Network:** Internet connection for broker API access
- **Database:** SQLite (development) or PostgreSQL (production)

### Required Credentials

1. **Alpaca API Credentials** (for paper trading):
   - `ALPACA_API_KEY`: Your Alpaca API key
   - `ALPACA_SECRET_KEY`: Your Alpaca secret key
   - Get from: https://app.alpaca.markets/paper/dashboard/overview

2. **Environment Configuration:**
   - Copy `env.example` to `.env`
   - Configure all required environment variables
   - See `ENV_SETUP_DOCUMENTATION.md` for details

### Pre-Startup Checklist

- [ ] Python 3.11+ installed and verified
- [ ] Dependencies installed (`pip install -r requirements.txt`)
- [ ] `.env` file configured with API credentials
- [ ] Configuration validated (`python -c "from config.settings import config; is_valid, errors = config.validate_config(); print('Valid' if is_valid else 'Errors:', errors)"`)
- [ ] Database initialized (if using PostgreSQL)
- [ ] Log directory exists (`logs/`)
- [ ] Trading mode confirmed (paper trading recommended for first run)
- [ ] Risk limits reviewed and appropriate for your risk tolerance

---

## Startup Procedures

### Standard Startup (Interactive Mode)

**Purpose:** Start the trading robot with web dashboard for interactive monitoring

**Steps:**

1. **Navigate to trading robot directory:**
   ```bash
   cd TradingRobotPlugWeb/backend
   ```

2. **Activate virtual environment (if using):**
   ```bash
   # Windows
   .\venv\Scripts\activate
   
   # Linux/macOS
   source venv/bin/activate
   ```

3. **Verify configuration:**
   ```bash
   python -c "from config.settings import config; is_valid, errors = config.validate_config(); print('✅ Valid' if is_valid else f'❌ Errors: {errors}')"
   ```

4. **Start trading robot:**
   ```bash
   python main.py
   ```

5. **Verify startup:**
   - Check console output for "✅ Trading Robot initialized successfully"
   - Check console output for "🎯 Trading Robot started successfully"
   - Access web dashboard at http://localhost:8000 (if enabled)

6. **Monitor initial status:**
   - Check account balance and portfolio value
   - Verify market clock status (open/closed)
   - Review risk limits and current positions

**Expected Output:**
```
🚀 Initializing ALPACA Trading Robot...
🔗 Connecting to ALPACA API...
✅ Pre-flight validation passed
✅ ALPACA API connection established
💰 Account: $100000.00 cash, $100000.00 portfolio value
✅ Trading Robot initialized successfully
🎯 Starting ALPACA Trading Robot...
🎯 Trading Robot started successfully
```

**Troubleshooting:**
- If pre-flight validation fails, check API credentials and network connectivity
- If initialization fails, check logs in `logs/trading_robot.log`
- If dashboard doesn't load, check `WEB_PORT` configuration and firewall settings

---

### Automated Daily Startup (Scheduled)

**Purpose:** Run daily automation tasks (strategy execution, trade management)

**Steps:**

1. **Set up scheduled task (Windows Task Scheduler / Linux cron / macOS launchd):**
   
   **Windows Task Scheduler:**
   - Create new task
   - Trigger: Daily at 9:00 AM (market open)
   - Action: Run program
   - Program: `python`
   - Arguments: `run_daily_automation.py`
   - Start in: `TradingRobotPlugWeb/backend`

   **Linux cron:**
   ```bash
   # Edit crontab
   crontab -e
   
   # Add line (runs daily at 9:00 AM)
   0 9 * * * cd /path/to/TradingRobotPlugWeb/backend && python run_daily_automation.py >> logs/cron.log 2>&1
   ```

2. **Verify scheduled execution:**
   - Check logs in `logs/daily_automation_YYYY-MM-DD.log`
   - Verify trades executed (if applicable)
   - Review performance report

**Expected Output:**
```
🤖 Starting Daily Trading Robot Automation
📦 Loading plugin: tsla_improved_strategy
📅 Executing daily trading plan...
✅ Daily plan executed: TRADE
   📝 Trade: BUY 10 TSLA @ $250.00
🔄 Checking for trade exits...
📊 Generating performance report...
   Total Trades: 15
   Win Rate: 60.0%
   Total P&L: $1250.00
   Profit Factor: 1.85
✅ Daily automation complete
```

---

### Headless Startup (No Dashboard)

**Purpose:** Run trading robot without web dashboard (production/server environments)

**Steps:**

1. **Set `ENABLE_DASHBOARD=false` in `.env`:**
   ```bash
   ENABLE_DASHBOARD=false
   ```

2. **Start trading robot:**
   ```bash
   python main.py
   ```

3. **Monitor via logs:**
   - Watch `logs/trading_robot.log` for status updates
   - Use log monitoring tools (tail, grep, etc.)

---

## Shutdown Procedures

### Graceful Shutdown (Standard)

**Purpose:** Safely stop the trading robot, close positions (if configured), and save state

**Steps:**

1. **Send shutdown signal:**
   - **Interactive mode:** Press `Ctrl+C` in terminal
   - **Systemd service:** `systemctl stop trading-robot`
   - **Docker:** `docker-compose stop trading-robot`

2. **Wait for graceful shutdown:**
   - System will:
     - Stop accepting new trades
     - Complete in-progress trades (if safe)
     - Close positions (if configured)
     - Save state to database
     - Stop web dashboard
     - Close broker API connections

3. **Verify shutdown:**
   - Check console output for "✅ Trading Robot stopped successfully"
   - Verify no active processes: `ps aux | grep main.py` (Linux/macOS) or `tasklist | findstr python` (Windows)
   - Check logs for shutdown confirmation

**Expected Output:**
```
Received signal 2, shutting down...
🛑 Stopping ALPACA Trading Robot...
✅ Trading Robot stopped successfully
```

**Timeout:** If graceful shutdown takes longer than 5 minutes, system will force shutdown

---

### Force Shutdown (Emergency)

**Purpose:** Immediately stop the trading robot when graceful shutdown fails

**Steps:**

1. **Kill process:**
   ```bash
   # Linux/macOS
   pkill -f "python.*main.py"
   
   # Windows
   taskkill /F /IM python.exe /FI "WINDOWTITLE eq main.py"
   ```

2. **Verify shutdown:**
   - Check no processes running
   - Review logs for any incomplete operations
   - Check broker API for any pending orders

3. **Manual cleanup (if needed):**
   - Cancel any pending orders via broker dashboard
   - Review and close positions manually if needed
   - Check database for consistency

**⚠️ Warning:** Force shutdown may leave trades in inconsistent state. Review logs and broker account after force shutdown.

---

## Emergency Stop Procedures

### Automatic Emergency Stop

The system automatically triggers emergency stop when:

- **Daily loss limit exceeded:** Portfolio loss exceeds `DAILY_LOSS_LIMIT_PCT` (default: 3%)
- **Emergency stop loss triggered:** Portfolio loss exceeds `EMERGENCY_STOP_LOSS_PCT` (default: 10%)
- **Risk limit breach:** Position limits or risk limits exceeded
- **System error:** Critical system error detected

**Automatic Actions:**
1. Stop accepting new trades
2. Cancel pending orders
3. Close positions (if configured)
4. Log emergency stop event
5. Send alert notifications
6. Shutdown system

---

### Manual Emergency Stop

**Purpose:** Immediately stop all trading activity due to external factors (market conditions, system issues, etc.)

**Methods:**

#### Method 1: Via Web Dashboard

1. **Access dashboard:** http://localhost:8000
2. **Click "Emergency Stop" button**
3. **Confirm emergency stop**
4. **Verify stop:** Check dashboard status shows "STOPPED"

#### Method 2: Via API

```bash
# Send emergency stop request
curl -X POST http://localhost:8000/api/stop_trading
```

#### Method 3: Via Code

```python
from core.risk_manager import RiskManager

risk_manager = RiskManager()
risk_manager._trigger_emergency_stop("Manual emergency stop - market volatility")
```

#### Method 4: Via Environment Variable

1. **Set emergency stop flag:**
   ```bash
   export EMERGENCY_STOP_ENABLED=true
   ```

2. **Restart system** (will detect flag and stop)

---

### Emergency Stop Recovery

**After Emergency Stop:**

1. **Review emergency stop reason:**
   - Check logs: `logs/trading_robot.log`
   - Review dashboard alerts
   - Check risk manager status

2. **Assess situation:**
   - Review portfolio status
   - Check open positions
   - Verify account balance
   - Review recent trades

3. **Resolve issue:**
   - Fix configuration if needed
   - Adjust risk limits if appropriate
   - Resolve system errors

4. **Restart system:**
   - Only restart after resolving root cause
   - Verify configuration before restart
   - Monitor closely after restart

---

## Daily Operations

### Pre-Market Checklist (9:00 AM ET)

- [ ] Verify system is running
- [ ] Check account balance and portfolio value
- [ ] Review overnight positions
- [ ] Verify market is open (check market clock)
- [ ] Review risk limits and current exposure
- [ ] Check for pending orders
- [ ] Review daily automation logs

### During Market Hours

- [ ] Monitor dashboard for active trades
- [ ] Review risk metrics (exposure, concentration)
- [ ] Check for alerts or warnings
- [ ] Review trade execution logs
- [ ] Monitor system performance

### Post-Market Checklist (4:00 PM ET)

- [ ] Review daily performance
- [ ] Check for any open positions
- [ ] Review trade history
- [ ] Verify daily loss limits not exceeded
- [ ] Review system logs for errors
- [ ] Backup database (if configured)

### Weekly Review

- [ ] Review weekly performance metrics
- [ ] Analyze strategy performance
- [ ] Review risk management effectiveness
- [ ] Update strategy parameters if needed
- [ ] Review and update risk limits
- [ ] Archive old logs

---

## Troubleshooting

### Common Startup Issues

#### Issue: Pre-flight Validation Fails

**Symptoms:**
- Error: "Pre-flight validation failed"
- System won't start

**Solutions:**
1. **Check API credentials:**
   ```bash
   python -c "from config.settings import config; print(f'API Key: {config.alpaca_api_key[:10]}...')"
   ```

2. **Test API connectivity:**
   ```bash
   curl https://paper-api.alpaca.markets/v2/clock
   ```

3. **Verify network connectivity:**
   - Check internet connection
   - Check firewall settings
   - Verify proxy settings (if applicable)

4. **Review validation report:**
   - Check logs for specific validation failures
   - Review `core/preflight_validator.py` for validation criteria

---

#### Issue: Configuration Validation Fails

**Symptoms:**
- Error: "Configuration validation failed"
- System won't start

**Solutions:**
1. **Validate configuration:**
   ```bash
   python -c "from config.settings import config; is_valid, errors = config.validate_config(); print('Valid' if is_valid else f'Errors: {errors}')"
   ```

2. **Check required variables:**
   - `ALPACA_API_KEY` must be set
   - `ALPACA_SECRET_KEY` must be set
   - `TRADING_MODE` must be "paper" or "live"

3. **Review `.env` file:**
   - Ensure all required variables are set
   - Check for typos or formatting errors
   - Verify no extra spaces or quotes

---

#### Issue: Database Connection Fails

**Symptoms:**
- Error: "Failed to connect to database"
- System won't start

**Solutions:**
1. **Check database URL:**
   ```bash
   echo $DATABASE_URL
   ```

2. **Test database connection:**
   ```bash
   # SQLite
   python -c "import sqlite3; conn = sqlite3.connect('trading_robot.db'); print('✅ Connected')"
   
   # PostgreSQL
   psql $DATABASE_URL -c "SELECT 1"
   ```

3. **Verify database exists:**
   - SQLite: Check file exists and is writable
   - PostgreSQL: Verify database exists and user has permissions

4. **Initialize database:**
   ```bash
   python scripts/init_database.py
   ```

---

### Runtime Issues

#### Issue: Trading Engine Stops Responding

**Symptoms:**
- No new trades executed
- Dashboard shows "STOPPED" status
- Logs show no activity

**Solutions:**
1. **Check system status:**
   ```bash
   # Check if process is running
   ps aux | grep main.py
   ```

2. **Review logs:**
   ```bash
   tail -f logs/trading_robot.log
   ```

3. **Check broker API status:**
   - Verify API credentials still valid
   - Check broker API status page
   - Test API connectivity

4. **Restart system:**
   - Graceful shutdown first
   - Restart with monitoring

---

#### Issue: Risk Limits Triggered Unexpectedly

**Symptoms:**
- Emergency stop triggered
- "Daily loss limit exceeded" error
- Trading stopped

**Solutions:**
1. **Review risk limits:**
   - Check `DAILY_LOSS_LIMIT_PCT` setting
   - Review current portfolio value
   - Calculate actual loss percentage

2. **Verify calculations:**
   - Check risk manager calculations
   - Review trade history
   - Verify account balance

3. **Adjust limits (if appropriate):**
   - Only adjust after thorough review
   - Consider market conditions
   - Update `.env` file and restart

---

## Common Issues and Solutions

### Issue: "No module named 'xxx'" Error

**Solution:**
```bash
# Install dependencies
pip install -r requirements.txt

# Or install specific module
pip install <module-name>
```

---

### Issue: "Port 8000 already in use"

**Solution:**
```bash
# Find process using port
# Linux/macOS
lsof -i :8000

# Windows
netstat -ano | findstr :8000

# Kill process or change WEB_PORT in .env
```

---

### Issue: "API rate limit exceeded"

**Solution:**
- Reduce trading frequency
- Implement rate limiting in strategies
- Use paper trading API (higher limits)
- Contact broker support

---

### Issue: "Database locked" (SQLite)

**Solution:**
- Ensure only one instance running
- Check for stale database locks
- Restart system
- Consider PostgreSQL for production

---

## Incident Response

### Incident Classification

**Severity Levels:**

1. **CRITICAL:** System down, live trading affected, financial loss
2. **HIGH:** System degraded, paper trading affected, risk of financial loss
3. **MEDIUM:** System issues, no trading impact, performance degradation
4. **LOW:** Minor issues, no impact, cosmetic problems

### Incident Response Procedure

1. **Immediate Actions:**
   - Trigger emergency stop (if trading active)
   - Assess severity and impact
   - Document incident details

2. **Investigation:**
   - Review logs and error messages
   - Check system status and health
   - Review recent trades and positions
   - Identify root cause

3. **Resolution:**
   - Implement fix or workaround
   - Verify fix effectiveness
   - Test system before restart
   - Restart system with monitoring

4. **Post-Incident:**
   - Document incident report
   - Update procedures if needed
   - Review and update risk limits
   - Schedule follow-up review

### Incident Report Template

```markdown
# Incident Report

**Date:** YYYY-MM-DD HH:MM
**Severity:** CRITICAL/HIGH/MEDIUM/LOW
**Status:** RESOLVED/IN PROGRESS

## Summary
Brief description of incident

## Impact
- Trading affected: Yes/No
- Financial impact: $XXX
- Duration: XX minutes

## Root Cause
Description of root cause

## Resolution
Steps taken to resolve

## Prevention
Actions to prevent recurrence
```

---

## Monitoring and Alerts

### Key Metrics to Monitor

1. **System Health:**
   - Process status (running/stopped)
   - API connectivity
   - Database connectivity
   - System resources (CPU, memory)

2. **Trading Metrics:**
   - Active positions
   - Daily P&L
   - Win rate
   - Trade frequency

3. **Risk Metrics:**
   - Portfolio exposure
   - Position concentration
   - Daily loss percentage
   - Risk limit utilization

### Alert Configuration

**Email Alerts:**
- Configure SMTP settings in `.env`
- Set up alert thresholds
- Test alert delivery

**System Alerts:**
- Monitor logs for ERROR/CRITICAL levels
- Set up log monitoring tools
- Configure alert notifications

### Dashboard Monitoring

**Access Dashboard:**
- URL: http://localhost:8000
- Monitor real-time metrics
- Review trade history
- Check system status

---

## Related Documents

- **[Trading Robot API Documentation](API_DOCUMENTATION.md)** - Complete API endpoint documentation with usage examples
- **[Trading Robot Deployment Guide](DEPLOYMENT_GUIDE.md)** - Deployment procedures, prerequisites, and validation
- **[Trading Robot Security Audit Report](SECURITY_AUDIT_REPORT.md)** - Security audit findings and recommendations

## References

- **API Documentation:** `docs/trading_robot/API_DOCUMENTATION.md`
- **Deployment Guide:** `docs/trading_robot/DEPLOYMENT_GUIDE.md`
- **Security Audit Report:** `docs/trading_robot/SECURITY_AUDIT_REPORT.md`
- **Environment Setup:** `ENV_SETUP_DOCUMENTATION.md`
- **Main README:** `README.md`
- **Configuration:** `config/settings.py`

---

**Last Updated:** 2025-12-27 by Agent-2  
**Status:** ✅ ACTIVE - Operations Runbook Complete  
**Next Review:** After production deployment and operational experience

