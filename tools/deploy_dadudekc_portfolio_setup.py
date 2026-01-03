#!/usr/bin/env python3
"""Deploy and run portfolio setup script on dadudekc.com"""

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

        # Deploy the setup script
        local_script = Path(__file__).parent / 'setup_dadudekc_portfolio.php'
        remote_script = 'setup_dadudekc_portfolio.php'

        print(f'Deploying setup script: {local_script}')
        result = deployer.deploy_file(str(local_script), remote_script)

        if result:
            print('✅ Setup script deployed successfully!')

            # Run the setup script via SSH
            print('🔧 Running portfolio setup...')
            command = f'cd public_html && php setup_dadudekc_portfolio.php'
            output = deployer.run_command(command)

            if output:
                print('📝 Setup output:')
                print(output)
            else:
                print('⚠️  No output from setup script')

            # Clean up the script
            deployer.run_command('rm setup_dadudekc_portfolio.php')

        else:
            print('❌ Setup script deployment failed')

    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()