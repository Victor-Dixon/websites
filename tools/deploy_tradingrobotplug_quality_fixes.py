#!/usr/bin/env python3
"""
Deploy Trading Robot Plug quality fixes
"""

import json
import paramiko
import os
from pathlib import Path

def load_site_config():
    """Load tradingrobotplug.com config"""
    with open('config/site_configs.json', 'r') as f:
        configs = json.load(f)

    return configs.get('tradingrobotplug.com', {})

def deploy_files_sftp():
    """Deploy files using SFTP"""
    config = load_site_config()
    sftp_config = config.get('sftp', {})

    if not sftp_config:
        print("❌ No SFTP config found for tradingrobotplug.com")
        return False

    # Files to deploy
    files_to_deploy = [
        'websites/tradingrobotplug.com/overlays/wp/theme/tradingrobotplug-theme/quality_fixes.php',
        'websites/tradingrobotplug.com/overlays/wp/theme/tradingrobotplug-theme/functions.php',
        'websites/tradingrobotplug.com/overlays/wp/theme/tradingrobotplug-theme/front-page.php'
    ]

    try:
        # Connect via SFTP
        transport = paramiko.Transport((sftp_config['host'], sftp_config['port']))
        transport.connect(username=sftp_config['username'], password=sftp_config['password'])

        sftp = paramiko.SFTPClient.from_transport(transport)

        # Deploy each file
        remote_base = sftp_config['remote_path'] + '/wp-content/themes/tradingrobotplug-theme/'

        for local_file in files_to_deploy:
            if not os.path.exists(local_file):
                print(f"❌ Local file not found: {local_file}")
                continue

            filename = os.path.basename(local_file)
            remote_file = remote_base + filename

            print(f"📤 Deploying {local_file} -> {remote_file}")
            sftp.put(local_file, remote_file)

        sftp.close()
        transport.close()

        print("✅ Deployment completed successfully!")
        return True

    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

def main():
    print("🚀 Deploying Trading Robot Plug quality fixes...")

    success = deploy_files_sftp()

    if success:
        print("\n📋 NEXT STEPS:")
        print("1. Clear WordPress cache on tradingrobotplug.com")
        print("2. Clear browser cache (Ctrl+F5)")
        print("3. Test site: https://tradingrobotplug.com")
        print("4. Verify navigation shows 'Capabilities' (not 'Capabilitie')")
        print("5. Verify footer shows 'All rights reserved' (not 'All right re erved')")
        print("6. Verify homepage has substantial content")
    else:
        print("\n❌ Deployment failed. Check credentials and try again.")

if __name__ == '__main__':
    main()