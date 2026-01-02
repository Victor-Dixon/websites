#!/usr/bin/env python3
import paramiko
import os
from pathlib import Path

# SFTP credentials for weareswarm.site
SFTP_CONFIG = {
    'host': '157.173.214.121',
    'username': 'u996867598',
    'password': 'Falcons#1247',
    'port': 65002
}

def deploy_css():
    try:
        # Local file path
        local_file = Path('websites/weareswarm.site/wp/wp-content/themes/swarm-theme/style.css')

        # Remote file path
        remote_file = 'domains/weareswarm.site/public_html/wp-content/themes/swarm-theme/style.css'

        if not local_file.exists():
            print(f'❌ Local file not found: {local_file}')
            return False

        # Establish SFTP connection
        transport = paramiko.Transport((SFTP_CONFIG['host'], SFTP_CONFIG['port']))
        transport.connect(username=SFTP_CONFIG['username'], password=SFTP_CONFIG['password'])

        sftp = paramiko.SFTPClient.from_transport(transport)

        # Upload file
        print(f'📤 Uploading {local_file} to {remote_file}')
        sftp.put(str(local_file), remote_file)

        # Close connection
        sftp.close()
        transport.close()

        print('✅ CSS file deployed successfully')
        return True

    except Exception as e:
        print(f'❌ Deployment failed: {e}')
        return False

if __name__ == '__main__':
    deploy_css()