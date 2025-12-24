#!/usr/bin/env python3
"""Update music page content"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

deployer = SimpleWordPressDeployer("ariajet.site", load_site_configs())
deployer.connect()

wp_path = "/home/u996867598/domains/ariajet.site/public_html"

# Update page content
content = "<h1>MUSIC</h1><p>Welcome to Aria's Music Collection! Explore amazing tracks from the cosmic universe.</p>"
cmd = f"cd {wp_path} && wp post update 3671 --post_content='{content}' 2>&1"
result = deployer.execute_command(cmd)
print(result)

