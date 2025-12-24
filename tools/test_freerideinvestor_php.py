#!/usr/bin/env python3
"""Test PHP execution directly."""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

configs = load_site_configs()
d = SimpleWordPressDeployer("freerideinvestor.com", configs)
d.connect()

wp_path = "/home/u996867598/domains/freerideinvestor.com/public_html"
theme = "freerideinvestor-modern"
index_php = f"{wp_path}/wp-content/themes/{theme}/index.php"

# Test PHP syntax
result = d.check_php_syntax(index_php)
print("PHP Syntax Check:")
print(f"  Valid: {result.get('valid')}")
if result.get('error'):
    print(f"  Error: {result.get('error')}")

# Check for fatal errors in error log
log_path = f"{wp_path}/wp-content/debug.log"
if d.execute_command(f"test -f {log_path} && echo EXISTS || echo MISSING") == "EXISTS":
    errors = d.execute_command(f"grep -i 'fatal\\|error\\|warning' {log_path} | tail -20")
    if errors and len(errors.strip()) > 10:
        print(f"\nRecent PHP errors/warnings:")
        print(errors)
    else:
        print("\n✅ No recent PHP errors in debug.log")

# Check if WordPress is loading
wp_load = f"{wp_path}/wp-load.php"
if d.execute_command(f"test -f {wp_load} && echo EXISTS || echo MISSING") == "EXISTS":
    print("\n✅ wp-load.php exists")
else:
    print("\n❌ wp-load.php missing")

d.disconnect()


