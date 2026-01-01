#!/usr/bin/env python3
"""Deploy dark theme fix to dadudekc.com"""

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
        
        local_path = Path(__file__).parent.parent / 'sites' / 'dadudekc.com' / 'wp' / 'theme' / 'dadudekc' / 'style.css'
        remote_path = 'wp-content/themes/dadudekc/style.css'
        
        print(f'Deploying: {local_path}')
        result = deployer.deploy_file(str(local_path), remote_path)
        
        if result:
            print('✅ Dark theme CSS deployed successfully!')
        else:
            print('❌ Deployment failed')
            
    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()

