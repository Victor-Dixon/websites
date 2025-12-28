#!/usr/bin/env python3
"""Verify remote functions.php has MUSIC update"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

deployer = SimpleWordPressDeployer("ariajet.site", load_site_configs())
deployer.connect()

remote_path = deployer.remote_path
wp_path = f"/home/u996867598/{remote_path}"

# Read the function
result = deployer.execute_command(f"grep -A 10 'Capabilitie' {wp_path}/wp-content/themes/ariajet/functions.php")
print("Remote functions.php (Capabilitie section):")
print(result)

