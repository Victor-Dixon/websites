# Trading Robot Deployment Guide

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-27  
**Status:** ACTIVE - Deployment Documentation  
**Purpose:** Complete deployment guide for Trading Robot system including prerequisites, step-by-step deployment, and validation procedures

<!-- SSOT Domain: documentation -->

**Tags:** trading-robot, deployment, documentation

---

## Executive Summary

This guide provides comprehensive deployment procedures for the Trading Robot system, covering development, staging, and production environments. It includes prerequisites, step-by-step instructions, post-deployment validation, and troubleshooting.

**System:** Trading Robot (Alpaca/Robinhood Multi-Broker)  
**Version:** 1.0.0  
**Deployment Types:** Development, Staging, Production  
**Database:** SQLite (development) or PostgreSQL (production)

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Development Deployment](#development-deployment)
4. [Production Deployment](#production-deployment)
5. [Post-Deployment Validation](#post-deployment-validation)
6. [Configuration Management](#configuration-management)
7. [Database Setup](#database-setup)
8. [Service Management](#service-management)
9. [Monitoring & Health Checks](#monitoring--health-checks)
10. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### System Requirements

**Operating System:**
- Linux (Ubuntu 20.04+, Debian 11+, CentOS 8+)
- macOS 10.15+
- Windows 10/11 (development only, production not recommended)

**Software Requirements:**
- **Python:** 3.11 or higher
- **pip:** Latest version
- **PostgreSQL:** 13+ (for production)
- **Redis:** 6.0+ (optional, for advanced features)
- **Git:** For version control

**Network Requirements:**
- Internet connection for broker API access
- Firewall rules (if applicable):
  - Outbound: HTTPS (443) for broker APIs
  - Inbound: HTTP (8000) for dashboard (production)
  - Inbound: PostgreSQL (5432) if remote database

---

### Account Requirements

**Alpaca Account:**
- Paper Trading account (required for testing)
- Live Trading account (for production)
- API credentials (API Key + Secret Key)
- Get from: https://app.alpaca.markets/

**Optional:**
- Email account for SMTP (email campaigns)
- Domain name (if exposing dashboard publicly)

---

### Knowledge Requirements

- Basic Python knowledge
- Command-line proficiency
- Database administration (for production)
- System administration (for production)

---

## Pre-Deployment Checklist

### Before Starting Deployment

- [ ] Python 3.11+ installed and verified
- [ ] Git repository cloned
- [ ] Alpaca API credentials obtained
- [ ] PostgreSQL database ready (production)
- [ ] Network connectivity verified
- [ ] Firewall rules configured (production)
- [ ] Environment variables prepared
- [ ] Backup strategy defined (production)
- [ ] Monitoring solution ready (production)

---

## Development Deployment

### Step 1: Clone Repository

```bash
git clone <repository-url>
cd TradingRobotPlugWeb/backend
```

---

### Step 2: Create Virtual Environment

**Linux/macOS:**
```bash
python3 -m venv venv
source venv/bin/activate
```

**Windows:**
```bash
python -m venv venv
.\venv\Scripts\activate
```

**Or use provided scripts:**
```bash
# Linux/macOS
./setup_venv.sh

# Windows
setup_venv.bat
```

---

### Step 3: Install Dependencies

```bash
pip install --upgrade pip
pip install -r requirements.txt
```

**Verify Installation:**
```bash
python -c "import alpaca_trade_api; print('✅ Dependencies installed')"
```

---

### Step 4: Configure Environment

```bash
# Copy example environment file
cp env.example .env

# Edit .env with your configuration
nano .env  # or use your preferred editor
```

**Minimum Required Configuration:**
```bash
# Alpaca API (Paper Trading)
ALPACA_API_KEY=your_paper_api_key
ALPACA_SECRET_KEY=your_paper_secret_key
ALPACA_BASE_URL=https://paper-api.alpaca.markets

# Trading Mode
TRADING_MODE=paper
LIVE_TRADING_ENABLED=false

# Broker
BROKER=alpaca

# Database (Development - SQLite)
DATABASE_URL=sqlite:///trading_robot.db

# Logging
LOG_LEVEL=INFO
LOG_FILE=logs/trading_robot.log
```

---

### Step 5: Validate Configuration

```bash
python -c "from config.settings import config; is_valid, errors = config.validate_config(); print('✅ Valid' if is_valid else f'❌ Errors: {errors}')"
```

---

### Step 6: Initialize Database

```bash
# Create logs directory
mkdir -p logs

# Initialize database
python scripts/init_database.py
```

---

### Step 7: Run Pre-Flight Validation

```bash
# Validate dependencies and configuration
python scripts/validate_dependencies.py
```

---

### Step 8: Start Trading Robot

```bash
# Start with dashboard
python main.py
```

**Verify Startup:**
- Check console for "✅ Trading Robot initialized successfully"
- Access dashboard at http://localhost:8000
- Check logs for any errors

---

## Production Deployment

### Step 1: Server Preparation

**Update System:**
```bash
sudo apt update && sudo apt upgrade -y  # Ubuntu/Debian
```

**Install System Dependencies:**
```bash
sudo apt install -y python3.11 python3.11-venv python3-pip postgresql postgresql-contrib git nginx
```

**Create System User:**
```bash
sudo useradd -m -s /bin/bash trading_robot
sudo mkdir -p /opt/trading_robot
sudo chown trading_robot:trading_robot /opt/trading_robot
```

---

### Step 2: Clone Repository

```bash
sudo -u trading_robot git clone <repository-url> /opt/trading_robot/app
cd /opt/trading_robot/app/backend
```

---

### Step 3: Setup Virtual Environment

```bash
sudo -u trading_robot python3.11 -m venv /opt/trading_robot/venv
source /opt/trading_robot/venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt
```

---

### Step 4: PostgreSQL Database Setup

**Create Database:**
```bash
sudo -u postgres psql
```

```sql
CREATE DATABASE trading_robot;
CREATE USER trading_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE trading_robot TO trading_user;
\q
```

**Initialize Database:**
```bash
cd /opt/trading_robot/app/backend
source /opt/trading_robot/venv/bin/activate
export DATABASE_URL="postgresql://trading_user:secure_password@localhost/trading_robot"
python scripts/init_database.py
```

---

### Step 5: Configure Environment

**Create .env file:**
```bash
sudo -u trading_robot cp env.example .env
sudo -u trading_robot nano .env
```

**Production Configuration:**
```bash
# Alpaca API (Live Trading - ONLY after extensive testing)
ALPACA_API_KEY=your_live_api_key
ALPACA_SECRET_KEY=your_live_secret_key
ALPACA_BASE_URL=https://api.alpaca.markets

# Trading Mode
TRADING_MODE=live
LIVE_TRADING_ENABLED=true  # Explicit flag required

# Broker
BROKER=alpaca

# Database (Production - PostgreSQL)
DATABASE_URL=postgresql://trading_user:secure_password@localhost/trading_robot

# Web Dashboard
WEB_HOST=127.0.0.1  # Bind to localhost only
WEB_PORT=8000
ENABLE_DASHBOARD=true

# Logging
LOG_LEVEL=INFO
LOG_FILE=/opt/trading_robot/logs/trading_robot.log

# Risk Management (Production Settings)
MAX_POSITIONS=10
MAX_POSITION_SIZE_PCT=0.1
DAILY_LOSS_LIMIT_PCT=0.03
EMERGENCY_STOP_ENABLED=true
EMERGENCY_STOP_LOSS_PCT=0.1

# Security
# Add any additional security settings
```

**Secure .env file:**
```bash
chmod 600 .env
chown trading_robot:trading_robot .env
```

---

### Step 6: Create Systemd Service

**Create service file:**
```bash
sudo nano /etc/systemd/system/trading-robot.service
```

**Service Configuration:**
```ini
[Unit]
Description=Trading Robot Service
After=network.target postgresql.service
Requires=postgresql.service

[Service]
Type=simple
User=trading_robot
Group=trading_robot
WorkingDirectory=/opt/trading_robot/app/backend
Environment="PATH=/opt/trading_robot/venv/bin"
ExecStart=/opt/trading_robot/venv/bin/python main.py
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
```

**Enable and Start Service:**
```bash
sudo systemctl daemon-reload
sudo systemctl enable trading-robot
sudo systemctl start trading-robot
```

**Check Status:**
```bash
sudo systemctl status trading-robot
sudo journalctl -u trading-robot -f  # Follow logs
```

---

### Step 7: Setup Reverse Proxy (Nginx)

**Create Nginx Configuration:**
```bash
sudo nano /etc/nginx/sites-available/trading-robot
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name trading-robot.yourdomain.com;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name trading-robot.yourdomain.com;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }

    # WebSocket support
    location /ws/ {
        proxy_pass http://127.0.0.1:8000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 86400;
    }
}
```

**Enable Site:**
```bash
sudo ln -s /etc/nginx/sites-available/trading-robot /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### Step 8: Setup Automated Backups

**Create Backup Script:**
```bash
sudo -u trading_robot nano /opt/trading_robot/backup.sh
```

**Backup Script:**
```bash
#!/bin/bash
BACKUP_DIR="/opt/trading_robot/backups"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Backup database
pg_dump -U trading_user trading_robot > $BACKUP_DIR/db_$DATE.sql

# Backup .env file
cp /opt/trading_robot/app/backend/.env $BACKUP_DIR/env_$DATE.backup

# Keep only last 30 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.backup" -mtime +30 -delete
```

**Make Executable:**
```bash
chmod +x /opt/trading_robot/backup.sh
```

**Add to Crontab:**
```bash
sudo -u trading_robot crontab -e
```

```
# Daily backup at 2 AM
0 2 * * * /opt/trading_robot/backup.sh
```

---

## Post-Deployment Validation

### Validation Checklist

#### System Validation

- [ ] Trading robot service is running
- [ ] Database connection successful
- [ ] API credentials validated
- [ ] Pre-flight validation passed
- [ ] Dashboard accessible (http://localhost:8000 or via Nginx)

#### Configuration Validation

- [ ] Environment variables loaded correctly
- [ ] Risk limits configured appropriately
- [ ] Trading mode set correctly (paper/live)
- [ ] Logging configured and working
- [ ] Database initialized with correct schema

#### Functional Validation

- [ ] Dashboard loads without errors
- [ ] API endpoints responding (test `/api/status`)
- [ ] WebSocket connection works (test `/ws/updates`)
- [ ] Account information displayed correctly
- [ ] Market clock shows correct status

#### Trading Validation (Paper Trading)

- [ ] Can fetch market data
- [ ] Can view portfolio
- [ ] Can execute test trade (paper trading)
- [ ] Trade appears in portfolio
- [ ] Risk limits enforced

---

### Validation Commands

**Check Service Status:**
```bash
sudo systemctl status trading-robot
```

**Test API Endpoint:**
```bash
curl http://localhost:8000/api/status
```

**Check Logs:**
```bash
sudo journalctl -u trading-robot -n 50  # Last 50 lines
tail -f /opt/trading_robot/logs/trading_robot.log
```

**Validate Configuration:**
```bash
cd /opt/trading_robot/app/backend
source /opt/trading_robot/venv/bin/activate
python -c "from config.settings import config; is_valid, errors = config.validate_config(); print('✅ Valid' if is_valid else f'❌ Errors: {errors}')"
```

**Database Connection Test:**
```bash
python -c "from database.connection import get_db; db = get_db(); print('✅ Database connected')"
```

---

## Configuration Management

### Environment Variables

**Required Variables:**
- `ALPACA_API_KEY` - Alpaca API key
- `ALPACA_SECRET_KEY` - Alpaca secret key
- `TRADING_MODE` - "paper" or "live"
- `BROKER` - "alpaca" or "robinhood"

**Optional Variables:**
- `DATABASE_URL` - Database connection string
- `WEB_HOST` - Dashboard host (default: 0.0.0.0)
- `WEB_PORT` - Dashboard port (default: 8000)
- `LOG_LEVEL` - Logging level (default: INFO)
- Risk management variables (see ENV_SETUP_DOCUMENTATION.md)

**Security:**
- Never commit `.env` file to git
- Use secure password manager for credentials
- Restrict file permissions (chmod 600)
- Use environment variables in production (not .env file)

---

### Configuration Validation

**Validate Configuration:**
```bash
python -c "from config.settings import config; is_valid, errors = config.validate_config(); print('✅ Valid' if is_valid else f'❌ Errors: {errors}')"
```

**Common Validation Errors:**
- Missing API credentials
- Invalid trading mode
- Invalid database URL
- Risk limits out of range

---

## Database Setup

### SQLite (Development)

**Default Configuration:**
```bash
DATABASE_URL=sqlite:///trading_robot.db
```

**Initialize:**
```bash
python scripts/init_database.py
```

**No additional setup required** - SQLite creates database file automatically.

---

### PostgreSQL (Production)

**Installation:**
```bash
sudo apt install postgresql postgresql-contrib
```

**Create Database:**
```sql
CREATE DATABASE trading_robot;
CREATE USER trading_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE trading_robot TO trading_user;
```

**Configure Connection:**
```bash
DATABASE_URL=postgresql://trading_user:secure_password@localhost/trading_robot
```

**Initialize Schema:**
```bash
python scripts/init_database.py
```

**Backup:**
```bash
pg_dump -U trading_user trading_robot > backup.sql
```

**Restore:**
```bash
psql -U trading_user trading_robot < backup.sql
```

---

## Service Management

### Systemd Commands

**Start Service:**
```bash
sudo systemctl start trading-robot
```

**Stop Service:**
```bash
sudo systemctl stop trading-robot
```

**Restart Service:**
```bash
sudo systemctl restart trading-robot
```

**Check Status:**
```bash
sudo systemctl status trading-robot
```

**View Logs:**
```bash
sudo journalctl -u trading-robot -f  # Follow logs
sudo journalctl -u trading-robot -n 100  # Last 100 lines
```

**Enable Auto-Start:**
```bash
sudo systemctl enable trading-robot
```

---

## Monitoring & Health Checks

### Health Check Endpoint

**URL:** `GET /api/status`

**Response:**
```json
{
  "timestamp": "2025-12-27T12:00:00.000000",
  "market_open": true,
  "portfolio_value": 100000.00,
  "cash_balance": 50000.00,
  "positions": [],
  "total_positions": 0
}
```

**Monitoring Script:**
```bash
#!/bin/bash
# health_check.sh

HEALTH_URL="http://localhost:8000/api/status"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" $HEALTH_URL)

if [ $RESPONSE -eq 200 ]; then
    echo "✅ Trading Robot is healthy"
    exit 0
else
    echo "❌ Trading Robot health check failed (HTTP $RESPONSE)"
    # Restart service if needed
    # sudo systemctl restart trading-robot
    exit 1
fi
```

**Add to Crontab (Check every 5 minutes):**
```bash
*/5 * * * * /opt/trading_robot/health_check.sh
```

---

### Log Monitoring

**Watch Logs in Real-Time:**
```bash
tail -f /opt/trading_robot/logs/trading_robot.log
```

**Search Logs:**
```bash
grep "ERROR" /opt/trading_robot/logs/trading_robot.log
grep "CRITICAL" /opt/trading_robot/logs/trading_robot.log
```

**Log Rotation:**
Configure logrotate for automatic log rotation:

```bash
sudo nano /etc/logrotate.d/trading-robot
```

```
/opt/trading_robot/logs/*.log {
    daily
    rotate 30
    compress
    delaycompress
    missingok
    notifempty
    create 0644 trading_robot trading_robot
}
```

---

## Troubleshooting

### Common Issues

#### Issue: Service Won't Start

**Symptoms:**
- `systemctl status` shows failed
- Logs show error messages

**Solutions:**
1. Check logs: `sudo journalctl -u trading-robot -n 50`
2. Verify configuration: `python -c "from config.settings import config; config.validate_config()"`
3. Check file permissions
4. Verify database connection
5. Check Python version: `python --version` (must be 3.11+)

---

#### Issue: Database Connection Failed

**Symptoms:**
- Error: "Failed to connect to database"
- Service starts but crashes

**Solutions:**
1. Verify database is running: `sudo systemctl status postgresql`
2. Check connection string in .env
3. Test connection: `psql $DATABASE_URL -c "SELECT 1"`
4. Verify user permissions
5. Check firewall rules

---

#### Issue: API Authentication Failed

**Symptoms:**
- Pre-flight validation fails
- "Authentication failed" errors

**Solutions:**
1. Verify API credentials in .env
2. Check API key format (should start with PK for paper, AK for live)
3. Test credentials: `curl -H "APCA-API-KEY-ID: $ALPACA_API_KEY" -H "APCA-API-SECRET-KEY: $ALPACA_SECRET_KEY" https://paper-api.alpaca.markets/v2/account`
4. Verify correct base URL (paper vs live)
5. Check API key permissions

---

#### Issue: Dashboard Not Accessible

**Symptoms:**
- Cannot access http://localhost:8000
- Connection refused

**Solutions:**
1. Check service status: `sudo systemctl status trading-robot`
2. Verify port is not in use: `sudo netstat -tlnp | grep 8000`
3. Check firewall: `sudo ufw status`
4. Verify WEB_HOST and WEB_PORT configuration
5. Check Nginx configuration (if using reverse proxy)

---

## Deployment Checklist

### Pre-Deployment

- [ ] All prerequisites met
- [ ] Repository cloned
- [ ] Virtual environment created
- [ ] Dependencies installed
- [ ] Environment configured
- [ ] Configuration validated
- [ ] Database initialized
- [ ] Pre-flight validation passed

### Deployment

- [ ] Service file created
- [ ] Service enabled and started
- [ ] Service status verified
- [ ] Reverse proxy configured (production)
- [ ] SSL certificates installed (production)
- [ ] Backup system configured
- [ ] Monitoring setup

### Post-Deployment

- [ ] Health check passing
- [ ] Dashboard accessible
- [ ] API endpoints responding
- [ ] Database connection verified
- [ ] Logging working
- [ ] Functional tests passed
- [ ] Paper trading validated (before live)

---

## Related Documents

- **[Trading Robot Operations Runbook](OPERATIONS_RUNBOOK.md)** - Operations procedures, startup/shutdown, troubleshooting
- **[Trading Robot API Documentation](API_DOCUMENTATION.md)** - Complete API endpoint documentation
- **[Trading Robot Security Audit Report](SECURITY_AUDIT_REPORT.md)** - Security audit with deployment security recommendations
- **[Environment Setup Documentation](ENV_SETUP_DOCUMENTATION.md)** - Complete environment variable reference
- **[Main README](../../README.md)** - General system documentation

---

## References

- **Operations Runbook:** `docs/trading_robot/OPERATIONS_RUNBOOK.md`
- **API Documentation:** `docs/trading_robot/API_DOCUMENTATION.md`
- **Security Audit Report:** `docs/trading_robot/SECURITY_AUDIT_REPORT.md`
- **Environment Setup:** `ENV_SETUP_DOCUMENTATION.md`
- **Main README:** `README.md`
- **Alpaca API Documentation:** https://alpaca.markets/docs/

---

**Last Updated:** 2025-12-27 by Agent-2  
**Status:** ✅ ACTIVE - Deployment Guide Complete  
**Next Review:** After Docker deployment implementation or deployment experience feedback

