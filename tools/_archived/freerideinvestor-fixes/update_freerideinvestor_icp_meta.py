#!/usr/bin/env python3
"""Update ICP meta fields for freerideinvestor.com"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('freerideinvestor.com', site_configs)

if deployer.connect():
    print("Updating ICP meta fields for Post ID 110...")
    
    meta_updates = [
        ('target_demographic', 'Active traders (day/swing traders, $10K-$500K accounts)'),
        ('pain_points', 'inconsistent results, guesswork'),
        ('desired_outcomes', 'consistent edge, reduced losses, trading confidence'),
        ('site_assignment', 'freerideinvestor.com')
    ]
    
    for key, value in meta_updates:
        # Escape single quotes for shell
        escaped_value = value.replace("'", "'\\''")
        cmd = f"cd domains/freerideinvestor.com/public_html && wp post meta update 110 {key} '{escaped_value}' --allow-root 2>&1"
        result = deployer.execute_command(cmd)
        print(f"  {key}: {'✅' if 'Success' in result or 'Updated' in result else '❌'}")
    
    # Verify
    verify = deployer.execute_command('cd domains/freerideinvestor.com/public_html && wp post get 110 --field=title --allow-root 2>&1')
    print(f"\n✅ ICP Post verified: {verify.strip() if verify else 'Not found'}")
    
    deployer.disconnect()

