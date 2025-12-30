#!/usr/bin/env python3
"""Deep debugging for freerideinvestor.com 500 error"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('freerideinvestor.com', site_configs)

if deployer.connect():
    print("="*60)
    print("CHECKING ERROR LOGS")
    print("="*60)
    
    # Check PHP error log
    result = deployer.execute_command('cd domains/freerideinvestor.com/public_html && tail -50 error_log 2>/dev/null | tail -20')
    print("PHP error_log:")
    print(result if result else "No error_log found")
    
    # Check WordPress debug log
    result2 = deployer.execute_command('cd domains/freerideinvestor.com/public_html && tail -50 wp-content/debug.log 2>/dev/null | tail -20')
    print("\nWordPress debug.log:")
    print(result2 if result2 else "No debug.log found")
    
    # Check Apache/LiteSpeed error log
    result3 = deployer.execute_command('cd domains/freerideinvestor.com/public_html && tail -50 ../logs/error_log 2>/dev/null | tail -20')
    print("\nServer error_log:")
    print(result3 if result3 else "No server error_log found")
    
    # Check active plugins
    print("\n" + "="*60)
    print("CHECKING ACTIVE PLUGINS")
    print("="*60)
    result4 = deployer.execute_command('cd domains/freerideinvestor.com/public_html && wp plugin list --status=active --allow-root 2>&1 | head -20')
    print(result4 if result4 else "Could not list plugins")
    
    # Check theme status
    print("\n" + "="*60)
    print("CHECKING THEME STATUS")
    print("="*60)
    result5 = deployer.execute_command('cd domains/freerideinvestor.com/public_html && wp theme list --allow-root 2>&1')
    print(result5 if result5 else "Could not list themes")
    
    deployer.disconnect()

