#!/usr/bin/env python3
"""Deploy index.php template to dadudekc.com"""

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

        # Deploy the index.php template
        local_path = Path(__file__).parent.parent / 'websites' / 'dadudekc.com' / 'overlays' / 'wp' / 'theme' / 'dadudekc' / 'index.php'
        remote_path = 'wp-content/themes/dadudekc/index.php'

        print(f'Deploying index template: {local_path}')
        result = deployer.deploy_file(str(local_path), remote_path)

        if result:
            print('✅ Index template deployed successfully!')
        else:
            print('❌ Index template deployment failed')

    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()