#!/usr/bin/env python3
"""Grant Administrator role to user for dadudekc.com via WP-CLI"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('dadudekc.com', site_configs)

if deployer.connect():
    username = 'DadudeKC@Gmail.com'
    print(f"Granting Administrator role to {username}...")
    
    # Use WP-CLI to set user role
    result = deployer.execute_command(
        f'cd domains/dadudekc.com/public_html && '
        f'wp user set-role {username} administrator --allow-root 2>&1'
    )
    
    print(result)
    
    # Verify
    verify = deployer.execute_command(
        f'cd domains/dadudekc.com/public_html && '
        f'wp user get {username} --field=roles --allow-root 2>&1'
    )
    print(f"\nUser roles: {verify}")
    
    deployer.disconnect()



