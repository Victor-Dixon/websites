#!/usr/bin/env python3
"""Deploy Twitch integration updates to digitaldreamscape.site"""

import sys
import os
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer('dream', site_configs)

    try:
        deployer.connect()

        # Files to deploy
        files_to_deploy = [
            ('wp/wp-content/themes/digitaldreamscape/functions.php', 'wp-content/themes/digitaldreamscape/functions.php'),
            ('wp/wp-content/themes/digitaldreamscape/front-page.php', 'wp-content/themes/digitaldreamscape/front-page.php'),
            ('wp/wp-content/themes/digitaldreamscape/style.css', 'wp-content/themes/digitaldreamscape/style.css'),
        ]

        for local_path, remote_path in files_to_deploy:
            full_local_path = Path(__file__).parent.parent / 'websites' / 'digitaldreamscape.site' / local_path
            print(f'Deploying {local_path} to {remote_path}')
            result = deployer.deploy_file(str(full_local_path), remote_path)

            if result:
                print(f'✅ {local_path} deployed successfully!')
            else:
                print(f'❌ {local_path} deployment failed')

    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()