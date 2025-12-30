#!/usr/bin/env python3
"""Verify index.php deployment."""

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

# Read server file
content = d.execute_command(f"cat {index_php}")

print(f"Server index.php length: {len(content)}")
print(f"Has 'hero-section': {'hero-section' in content}")
print(f"Has 'FreeRideInvestor': {'FreeRideInvestor' in content}")
print(f"Has 'MINIMAL': {'MINIMAL' in content}")

print(f"\nFirst 100 chars:")
print(content[:100])

print(f"\nLast 100 chars:")
print(content[-100:])

# Check file size
size = d.execute_command(f"wc -c < {index_php}")
print(f"\nFile size: {size.strip()} bytes")

d.disconnect()


