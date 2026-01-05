#!/usr/bin/env python3
"""Deploy dadudekc profile image to dadudekc.com"""

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

        # Deploy the profile image
        local_path = Path(__file__).parent.parent / 'dadudekc_profile.png'
        remote_path = 'wp-content/uploads/2026/01/dadudekc_profile.png'

        print(f'Deploying profile image: {local_path} to {remote_path}')
        result = deployer.deploy_file(str(local_path), remote_path)

        if result:
            print('✅ Profile image deployed successfully!')
            print('📝 Image will be available at: https://dadudekc.com/wp-content/uploads/2026/01/dadudekc_profile.png')
        else:
            print('❌ Profile image deployment failed')

    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()