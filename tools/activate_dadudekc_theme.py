#!/usr/bin/env python3
"""Activate dadudekc theme on dadudekc.com"""

import sys
import subprocess
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer('dadudekc.com', site_configs)
    
    try:
        deployer.connect()
        
        # Use SFTP to execute WP-CLI command
        sftp = deployer.sftp_client
        stdin, stdout, stderr = deployer.ssh_client.exec_command(
            f'cd {deployer.remote_path} && wp theme activate dadudekc --allow-root'
        )
        
        output = stdout.read().decode()
        errors = stderr.read().decode()
        
        if output:
            print('Output:', output)
        if errors:
            print('Errors:', errors)
            
        if 'Success' in output or 'Activated' in output:
            print('✅ Theme activated successfully!')
        else:
            print('⚠️ Theme activation may have issues. Check output above.')
            
    finally:
        deployer.disconnect()

if __name__ == '__main__':
    main()

