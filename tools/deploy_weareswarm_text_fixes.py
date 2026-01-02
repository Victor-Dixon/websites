#!/usr/bin/env python3
"""
Deploy We Are Swarm text rendering fixes
"""

import json
import paramiko
import os
from pathlib import Path

def load_site_config():
    """Load weareswarm.online config"""
    with open('config/site_configs.json', 'r') as f:
        configs = json.load(f)

    return configs.get('weareswarm.online', {})

def deploy_files_sftp():
    """Deploy files using SFTP"""
    config = load_site_config()
    sftp_config = config.get('sftp', {})

    if not sftp_config:
        print("❌ No SFTP config found for weareswarm.online")
        return False

    # Files to deploy
    files_to_deploy = [
        'websites/weareswarm.online/overlays/theme/text_rendering_fixes.css',
        'websites/weareswarm.online/overlays/theme/text_rendering_content_filter.php',
        'websites/weareswarm.online/overlays/theme/temp_weareswarm_site_seo.php'
    ]

    try:
        # Connect via SFTP
        transport = paramiko.Transport((sftp_config['host'], sftp_config['port']))
        transport.connect(username=sftp_config['username'], password=sftp_config['password'])

        sftp = paramiko.SFTPClient.from_transport(transport)

        # Deploy each file
        remote_base = sftp_config['remote_path'] + '/wp-content/themes/'

        for local_file in files_to_deploy:
            if not os.path.exists(local_file):
                print(f"❌ Local file not found: {local_file}")
                continue

            # Determine remote filename
            filename = os.path.basename(local_file)
            if filename == 'text_rendering_fixes.css':
                remote_file = remote_base + 'swarm-theme/style.css'  # Append to existing style.css
            elif filename == 'text_rendering_content_filter.php':
                remote_file = remote_base + 'swarm-theme/functions.php'  # This needs to be included
            elif filename == 'temp_weareswarm_site_seo.php':
                remote_file = remote_base + 'swarm-theme/temp_weareswarm_site_seo.php'

            print(f"📤 Deploying {local_file} -> {remote_file}")

            # For CSS file, append to existing style.css
            if filename == 'text_rendering_fixes.css':
                try:
                    # Read existing style.css
                    with sftp.open(remote_base + 'swarm-theme/style.css', 'r') as f:
                        existing_css = f.read().decode('utf-8')
                except:
                    existing_css = ""

                # Read new CSS
                with open(local_file, 'r') as f:
                    new_css = f.read()

                # Append new CSS
                updated_css = existing_css + "\n\n/* ===== WEARESWARM TEXT RENDERING FIXES ===== */\n" + new_css

                # Write back
                with sftp.open(remote_base + 'swarm-theme/style.css', 'w') as f:
                    f.write(updated_css)

            else:
                # Upload other files
                sftp.put(local_file, remote_file)

        sftp.close()
        transport.close()

        print("✅ Deployment completed successfully!")
        return True

    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

def main():
    print("🚀 Deploying We Are Swarm text rendering fixes...")

    success = deploy_files_sftp()

    if success:
        print("\n📋 NEXT STEPS:")
        print("1. Clear WordPress cache on weareswarm.online")
        print("2. Clear browser cache (Ctrl+F5)")
        print("3. Test site: https://weareswarm.online")
        print("4. Verify text renders correctly")
    else:
        print("\n❌ Deployment failed. Check credentials and try again.")

if __name__ == '__main__':
    main()