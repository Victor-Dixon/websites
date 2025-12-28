#!/usr/bin/env python3
"""Activate freerideinvestor-modern theme"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('freerideinvestor.com', site_configs)

if deployer.connect():
    print("Activating freerideinvestor-modern theme...")
    result = deployer.execute_command(
        'cd domains/freerideinvestor.com/public_html && '
        'wp theme activate freerideinvestor-modern --allow-root 2>&1'
    )
    print(result)
    
    # Verify
    verify = deployer.execute_command(
        'cd domains/freerideinvestor.com/public_html && '
        'wp theme list --status=active --allow-root 2>&1'
    )
    print(f"\nActive theme: {verify}")
    
    deployer.disconnect()

