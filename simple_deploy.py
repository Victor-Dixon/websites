#!/usr/bin/env python3
"""Simple SFTP deployment for digitaldreamscape.site"""

import paramiko
import json
from pathlib import Path

def deploy_files():
    # Load site config
    config_path = Path('config/site_configs.json')
    with open(config_path, 'r') as f:
        configs = json.load(f)

    site_config = configs['digitaldreamscape.site']
    sftp_config = site_config['sftp']

    # Files to deploy
    script_dir = Path(__file__).parent
    local_base = script_dir / 'websites' / 'digitaldreamscape.site'
    files_to_deploy = [
        'wp/wp-content/themes/digitaldreamscape/style.css',
        'wp/wp-content/themes/digitaldreamscape/page-blog.php',
        'wp/wp-content/themes/digitaldreamscape/functions.php',
        'wp/wp-content/themes/digitaldreamscape/header.php'
    ]

    print("🚀 Starting simple SFTP deployment...")
    print(f"Host: {sftp_config['host']}:{sftp_config['port']}")
    print(f"Remote path: {sftp_config['remote_path']}")

    # Create SSH client
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

    try:
        # Connect
        ssh.connect(
            hostname=sftp_config['host'],
            port=sftp_config['port'],
            username=sftp_config['username'],
            password=sftp_config['password']
        )

        # Open SFTP session
        sftp = ssh.open_sftp()
        remote_base = sftp_config['remote_path']

        # Check if remote directory exists
        try:
            remote_contents = sftp.listdir(remote_base)
            print(f"✅ Remote directory exists: {remote_base}")
            print(f"   Contents: {remote_contents[:10]}")  # Show first 10 items
        except IOError:
            print(f"❌ Remote directory does not exist: {remote_base}")
            return False

        # Check if wp-content/themes/digitaldreamscape exists
        theme_dir = f"{remote_base}/wp-content/themes/digitaldreamscape"
        try:
            sftp.listdir(theme_dir)
            print(f"✅ Theme directory exists: {theme_dir}")
        except IOError:
            print(f"⚠️ Theme directory does not exist, will create during upload: {theme_dir}")
            # We'll create directories as needed during upload

        success_count = 0
        fail_count = 0

        for file_path in files_to_deploy:
            local_path = local_base / file_path
            remote_path = f"{remote_base}/{file_path}"

            print(f"Local: {local_path} (exists: {local_path.exists()})")
            print(f"Remote: {remote_path}")
            if not local_path.exists():
                print(f"❌ Local file not found: {local_path}")
                fail_count += 1
                continue

            try:
                print(f"📤 Uploading: {file_path}")

                # Ensure remote directory exists
                remote_dir = str(Path(remote_path).parent)
                try:
                    sftp.listdir(remote_dir)
                except IOError:
                    # Directory doesn't exist, create it
                    print(f"   Creating directory: {remote_dir}")
                    try:
                        # Create parent directories
                        parts = remote_dir.split('/')
                        current_path = ""
                        for part in parts:
                            if part:
                                current_path += f"/{part}"
                                try:
                                    sftp.listdir(current_path)
                                except IOError:
                                    sftp.mkdir(current_path)
                                    print(f"   Created: {current_path}")
                    except Exception as dir_e:
                        print(f"   ⚠️ Could not create directory {remote_dir}: {dir_e}")

                # Now upload the file
                sftp.put(str(local_path), remote_path)
                print(f"✅ Uploaded: {file_path}")
                success_count += 1
            except Exception as e:
                print(f"❌ Failed to upload {file_path}: {e}")
                fail_count += 1

        sftp.close()
        ssh.close()

        print("\n📊 DEPLOYMENT SUMMARY")
        print("="*40)
        print(f"✅ Succeeded: {success_count}")
        print(f"❌ Failed: {fail_count}")

        if fail_count == 0:
            print("🎉 All files deployed successfully!")
            return True
        else:
            print("⚠️ Some files failed to deploy")
            return False

    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

if __name__ == '__main__':
    success = deploy_files()
    exit(0 if success else 1)