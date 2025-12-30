#!/usr/bin/env python3
"""Check freerideinvestor.com error logs"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('freerideinvestor.com', site_configs)

if deployer.connect():
    print("Checking error logs...")
    result = deployer.execute_command('cd domains/freerideinvestor.com/public_html && tail -50 error_log 2>/dev/null | tail -20')
    print(result if result else "No error_log found")
    
    print("\nChecking PHP error log...")
    result2 = deployer.execute_command('cd domains/freerideinvestor.com/public_html && tail -50 wp-content/debug.log 2>/dev/null | tail -20')
    print(result2 if result2 else "No debug.log found")
    
    deployer.disconnect()

