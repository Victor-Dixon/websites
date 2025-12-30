#!/usr/bin/env python3
"""Diagnose why index.php content isn't rendering."""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

configs = load_site_configs()
d = SimpleWordPressDeployer("freerideinvestor.com", configs)
d.connect()

wp_path = "/home/u996867598/domains/freerideinvestor.com/public_html"
theme = "freerideinvestor-modern"

# Check front-page.php
front_page = f"{wp_path}/wp-content/themes/{theme}/front-page.php"
exists = d.execute_command(f"test -f {front_page} && echo EXISTS || echo MISSING")
print(f"front-page.php: {exists.strip()}")

if "EXISTS" in exists:
    content = d.execute_command(f"head -30 {front_page}")
    print(f"\nFirst 30 lines of front-page.php:")
    print(content)

# Check homepage setting
show_on_front = d.execute_command(f"wp option get show_on_front --path={wp_path}")
page_on_front = d.execute_command(f"wp option get page_on_front --path={wp_path}")
print(f"\nHomepage settings:")
print(f"  show_on_front: {show_on_front.strip()}")
print(f"  page_on_front: {page_on_front.strip()}")

# Check for PHP errors
log_path = f"{wp_path}/wp-content/debug.log"
exists = d.execute_command(f"test -f {log_path} && echo EXISTS || echo MISSING")
print(f"\ndebug.log: {exists.strip()}")

if "EXISTS" in exists:
    errors = d.execute_command(f"tail -30 {log_path}")
    if errors and len(errors.strip()) > 10:
        print(f"\nRecent errors:")
        print(errors)
    else:
        print("  No recent errors")

# Check if index.php has our custom content
index_php = f"{wp_path}/wp-content/themes/{theme}/index.php"
content = d.execute_command(f"head -20 {index_php}")
print(f"\nFirst 20 lines of index.php:")
print(content)

d.disconnect()


