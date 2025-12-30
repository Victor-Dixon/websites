#!/usr/bin/env python
"""Create TradingRobotPlug legal pages via WP-CLI"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    configs = load_site_configs()
    deployer = SimpleWordPressDeployer('tradingrobotplug.com', configs)
    
    if not deployer.connect():
        print("Failed to connect!")
        return 1
    
    pages = [
        ('Privacy Policy', 'privacy'),
        ('Terms of Service', 'terms-of-service'),
        ('Product Terms', 'product-terms')
    ]
    
    for title, slug in pages:
        cmd = f'cd domains/tradingrobotplug.com/public_html && wp post create --post_type=page --post_title="{title}" --post_name={slug} --post_status=publish --allow-root'
        result = deployer.execute_command(cmd)
        print(f'{slug}: {result}')
    
    deployer.disconnect()
    print('DONE!')
    return 0

if __name__ == '__main__':
    sys.exit(main())


