# Manual Deployment & Handoff Instructions

**Date:** December 27, 2025
**Author:** Agent-7 (Cursor AI)

## Overview
This repository currently utilizes **manual deployment** scripts to push changes to live servers. There is no automated CI/CD pipeline active for these sites.

## 1. Deployment Mechanism
Changes are deployed using Python scripts located in `ops/deployment/`. These scripts utilize SFTP (via `paramiko`) to upload files to the Hostinger servers.

### Scripts Used
*   **`ops/deployment/deploy_improvements.py`**: The specific script used to deploy the "High-Impact Improvements" for Crosby Ultimate Events, Digital Dreamscape, and FreeRideInvestor.
*   **`ops/deployment/simple_wordpress_deployer.py`**: The core utility class that handles SFTP connections and file uploads. It reads credentials from `configs/site_configs.json`.

### How to Deploy (Future Changes)
To deploy future changes, you can:
1.  **Modify/Reuse** `ops/deployment/deploy_improvements.py` to target new files or sites.
2.  **Use the Class directly**: Import `SimpleWordPressDeployer` to create custom deployment logic.

**Example Usage:**
```python
import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent)) # Add ops/deployment to path
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
# Site key matches keys in site_configs.json (e.g., "crosbyultimateevents.com")
deployer = SimpleWordPressDeployer("crosbyultimateevents.com", site_configs)

if deployer.connect():
    # Deploy a single file
    # Remote path is relative to the site's public_html root (usually configured in site_configs)
    deployer.deploy_file(Path("local/path/to/file.php"), "wp-content/themes/my-theme/file.php")
    deployer.disconnect()
```

## 2. Clearing Cache
After ANY deployment, it is **critical** to clear the WordPress object cache, transients, and rewrite rules to ensure changes are visible immediately.

### Script
*   **`ops/deployment/clear_wordpress_cache.py`**

### Usage
Run the following commands from the repository root:

```bash
# Syntax: python3 ops/deployment/clear_wordpress_cache.py <site_domain>

python3 ops/deployment/clear_wordpress_cache.py crosbyultimateevents.com
python3 ops/deployment/clear_wordpress_cache.py digitaldreamscape.site
python3 ops/deployment/clear_wordpress_cache.py freerideinvestor.com
```

**What it does:**
This script connects via SSH and executes `wp-cli` commands on the server:
*   `wp cache flush` (Object Cache)
*   `wp rewrite flush` (Permalinks/Rewrites)
*   `wp transient delete --all` (Transients)
*   `wp litespeed-purge all` (LiteSpeed Cache, if applicable)

## 3. Server Logs & Debugging
For **FreeRideInvestor.com** performance monitoring:
*   **Profiler**: A custom profiler was added to the theme's `functions.php`. It logs request duration, memory usage, and slow queries to `wp-content/debug.log` on the server.
*   **Log Checker**: Use `ops/deployment/check_fri_logs.py` to view the tail of the debug log without logging in manually.

```bash
python3 ops/deployment/check_fri_logs.py
```

## 4. Credentials
Credentials are stored in `configs/site_configs.json`.
*   **Host**: 157.173.214.121
*   **User**: u996867598
*   **Port**: 65002
*   **Passwords**: Stored in the JSON file.

**Note:** Ensure `paramiko` and `python-dotenv` are installed in your environment:
```bash
pip install paramiko python-dotenv
```
