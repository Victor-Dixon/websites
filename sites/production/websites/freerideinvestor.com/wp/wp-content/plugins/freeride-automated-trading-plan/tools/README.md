# Plugin Deployment & Management Tools

## Tools Available

### 1. SSH Deployment Script (Python) ⚡ **RECOMMENDED**
**File:** `tools/deploy-ssh.py`

**Purpose:** Deploy plugin files directly to server via SSH/SCP (integrated with FreeRideInvestor deployment workflow)

**Setup:**
```bash
# Install dependencies
pip install paramiko scp

# Create config file (copy example)
cp deploy-config.json.example deploy-config.json
# Edit deploy-config.json with your SSH credentials
```

**Usage:**
```bash
# Deploy plugin to server
python deploy-ssh.py --action=deploy

# Verify installation
python deploy-ssh.py --action=verify

# Run WordPress setup
python deploy-ssh.py --action=setup

# Do everything
python deploy-ssh.py --action=all
```

**Features:**
- ✅ Direct SSH/SCP deployment (no manual uploads)
- ✅ Automatic file synchronization
- ✅ Remote verification
- ✅ WordPress CLI integration
- ✅ Integrated with FreeRideInvestor deployment workflow

**Config File (`deploy-config.json`):**
```json
{
  "host": "us-bos-web1616.main-hosting.eu",
  "port": 65002,
  "username": "u996867598",
  "remote_base": "/home/u996867598/public_html",
  "ssh_key_path": "~/.ssh/id_rsa"
}
```

### 2. Deployment Manager (Web Interface)
**File:** `tools/deploy-manager.php`

**Access:** 
```
https://yoursite.com/wp-content/plugins/freeride-automated-trading-plan/tools/deploy-manager.php
```

**Features:**
- ✅ Verify all plugin files are present
- ✅ Create required WordPress pages
- ✅ Generate test trading plans
- ✅ Check plugin status (active, database, cron, etc.)
- ✅ Quick links to admin pages
- ✅ Frontend page links

**Usage:**
1. Log in as admin
2. Navigate to the URL above
3. Click buttons to perform actions
4. **DELETE THIS FILE AFTER SETUP FOR SECURITY!**

### 3. Plugin Deployer (PHP CLI & Web)
**File:** `tools/plugin-deployer.php`

**CLI Usage:**
```bash
cd wp-content/plugins/freeride-automated-trading-plan/tools
php plugin-deployer.php --action=deploy
php plugin-deployer.php --action=verify
php plugin-deployer.php --action=setup
```

**Web Usage:**
```
https://yoursite.com/wp-content/plugins/freeride-automated-trading-plan/tools/plugin-deployer.php?action=deploy
```

**Actions:**
- `deploy` - Copy plugin files to WordPress plugins directory (local)
- `verify` - Check if all files are in place
- `setup` - Create database tables, pages, roles, cron jobs

## Quick Setup Workflow

### Option A: SSH Deployment (Recommended)
1. **Configure SSH:**
   ```bash
   cd plugins/freeride-automated-trading-plan/tools
   cp deploy-config.json.example deploy-config.json
   # Edit deploy-config.json with your SSH credentials
   ```

2. **Deploy to Server:**
   ```bash
   python deploy-ssh.py --action=all
   ```
   This will deploy, verify, and setup everything automatically.

3. **Activate Plugin:**
   - Go to WordPress Admin → Plugins
   - Find "FreeRide Automated Trading Plan"
   - Click "Activate"

### Option B: Web Interface
1. **Access Deployment Manager:**
   - Go to: `tools/deploy-manager.php` (web interface)
   - Or use: `plugin-deployer.php` (CLI)

2. **Verify Files:**
   - Click "Verify All Files"
   - Should show: ✅ All required plugin files are present!

3. **Run Setup:**
   - Click "Run Setup" (or use CLI: `--action=setup`)
   - Creates database tables, user roles, pages, cron jobs

4. **Check Status:**
   - Click "Check Status"
   - Verify plugin is active, database exists, cron scheduled

5. **Generate Test Plan:**
   - Enter symbol (e.g., TSLA)
   - Click "Generate"
   - Creates a test trading plan

6. **Activate Plugin:**
   - Go to WordPress Admin → Plugins
   - Find "FreeRide Automated Trading Plan"
   - Click "Activate"

## Tool Integration

All three tools work together:
- **SSH Deployer** (`deploy-ssh.py`) - Deploy from local to server via SSH
- **PHP Deployer** (`plugin-deployer.php`) - Manage plugin on server (local operations)
- **Web Manager** (`deploy-manager.php`) - Browser-based setup and management

**Recommended Workflow:**
1. Use `deploy-ssh.py` to deploy files to server
2. Use `deploy-manager.php` for initial setup and testing
3. Keep `deploy-ssh.py` for future updates
4. Delete `deploy-manager.php` and `plugin-deployer.php` after setup

## Security

⚠️ **IMPORTANT:** Delete these tool files after setup:
- `tools/deploy-manager.php` (web interface - security risk)
- `tools/plugin-deployer.php` (web interface - security risk)

**Keep:**
- `tools/deploy-ssh.py` (CLI only - safe to keep for updates)
- `tools/deploy-config.json` (contains credentials - keep secure, add to .gitignore)

## Troubleshooting

**Plugin not showing in WordPress?**
- Use "Verify Files" to check all files are present
- Check file permissions (should be 644 for files, 755 for directories)
- Check for PHP errors in WordPress debug log

**Setup not working?**
- Make sure you're logged in as administrator
- Check WordPress error logs
- Verify database user has CREATE TABLE permissions

