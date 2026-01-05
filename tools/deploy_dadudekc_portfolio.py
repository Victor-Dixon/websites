#!/usr/bin/env python3
"""Deploy portfolio page template to dadudekc.com"""

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

        # Deploy the portfolio page template
        local_path = Path(__file__).parent.parent / 'websites' / 'dadudekc.com' / 'overlays' / 'wp' / 'theme' / 'dadudekc' / 'page-portfolio.php'
        remote_path = 'wp-content/themes/dadudekc/page-portfolio.php'

        print(f'Deploying portfolio template: {local_path}')
        result = deployer.deploy_file(str(local_path), remote_path)

        if result:
            print('✅ Portfolio page template deployed successfully!')
            print('📝 Next steps:')
            print('   1. Create a WordPress page with slug "portfolio"')
            print('   2. Set the page template to "Portfolio"')
            print('   3. Update navigation menu to include portfolio link')
            print('   4. Test the portfolio page at https://dadudekc.com/portfolio')
        else:
            print('❌ Portfolio template deployment failed')

    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()