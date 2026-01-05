#!/usr/bin/env python3
"""Deploy blog page with series functionality to dadudekc.com"""

import sys
import os
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer('dadudekc.com', site_configs)

    try:
        deployer.connect()

        # Deploy the blog page template with series functionality
        local_path = Path(__file__).parent.parent / 'websites' / 'dadudekc.com' / 'overlays' / 'wp' / 'theme' / 'dadudekc' / 'page-blog.php'
        remote_path = 'wp-content/themes/dadudekc/page-blog.php'

        print(f'Deploying blog template with series functionality: {local_path}')
        result = deployer.deploy_file(str(local_path), remote_path)

        if result:
            print('✅ Blog page with series functionality deployed successfully!')
            print('📝 Testing:')
            print('   - Visit: https://dadudekc.com/blog/?series=trading-systems')
            print('   - Should show "Trading Systems Series" header')
            print('   - Should filter posts by trading-systems category')
            print('   - Should show series description and filter UI')
        else:
            print('❌ Blog template deployment failed')

    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()