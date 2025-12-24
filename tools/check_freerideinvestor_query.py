#!/usr/bin/env python3
"""Check WordPress query and template parts."""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

configs = load_site_configs()
d = SimpleWordPressDeployer("freerideinvestor.com", configs)
d.connect()

wp_path = "/home/u996867598/domains/freerideinvestor.com/public_html"
theme = "freerideinvestor-modern"

# Check content-none.php
content_none = f"{wp_path}/wp-content/themes/{theme}/template-parts/content-none.php"
exists = d.execute_command(f"test -f {content_none} && echo EXISTS || echo MISSING")
print(f"content-none.php: {exists.strip()}")

if "EXISTS" in exists:
    content = d.execute_command(f"cat {content_none}")
    print(f"\n=== content-none.php content ===\n{content[:500]}")

# Check functions.php for pre_get_posts filters
functions = f"{wp_path}/wp-content/themes/{theme}/functions.php"
func_content = d.execute_command(f"grep -n 'pre_get_posts' {functions} | head -10")
if func_content:
    print(f"\n=== pre_get_posts filters ===\n{func_content}")

d.disconnect()


