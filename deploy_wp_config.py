#!/usr/bin/env python3
"""
Deploy wp-config.php files to servers
===================================

Uses SFTP to deploy the corrected wp-config.php files to the servers.
"""

import paramiko
import json
from pathlib import Path
import sys

def deploy_wp_config(site_domain):
    """Deploy wp-config.php to server using SFTP."""

    # Load site config
    config_path = Path(__file__).parent / "config" / "site_configs.json"
    with open(config_path, 'r') as f:
        site_configs = json.load(f)

    if site_domain not in site_configs:
        print(f"❌ No configuration found for {site_domain}")
        return False

    site_config = site_configs[site_domain]
    sftp_config = site_config.get('sftp', {})

    if not sftp_config:
        print(f"❌ No SFTP config found for {site_domain}")
        return False

    # Local wp-config.php path
    local_wp_config = Path(__file__).parent / "websites" / site_domain / "wp-config.php"
    if not local_wp_config.exists():
        print(f"❌ Local wp-config.php not found for {site_domain}")
        return False

    try:
        # Connect via SSH
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

        print(f"🔌 Connecting to {sftp_config['host']}:{sftp_config['port']} as {sftp_config['username']}...")
        ssh.connect(
            hostname=sftp_config['host'],
            port=sftp_config['port'],
            username=sftp_config['username'],
            password=sftp_config['password']
        )

        # Open SFTP
        sftp = ssh.open_sftp()

        # Remote path
        remote_path = f"{sftp_config['remote_path']}/wp-config.php"
        print(f"📤 Uploading to {remote_path}...")

        # Upload file
        sftp.put(str(local_wp_config), remote_path)

        # Close connections
        sftp.close()
        ssh.close()

        print(f"✅ Successfully deployed wp-config.php to {site_domain}")
        return True

    except Exception as e:
        print(f"❌ Deployment failed: {e}")
        return False

def main():
    """Main deployment function."""
    print("🚀 DEPLOYING WP-CONFIG.PHP FILES")
    print("=" * 50)

    sites = ['freerideinvestor.com', 'prismblossom.online']
    success_count = 0

    for site in sites:
        print(f"\n📤 Deploying to {site}...")
        if deploy_wp_config(site):
            success_count += 1
        else:
            print(f"❌ Failed to deploy to {site}")

    print("\n📊 DEPLOYMENT SUMMARY")
    print("=" * 30)
    print(f"✅ Successfully deployed: {success_count}/{len(sites)}")

    if success_count == len(sites):
        print("\n🎉 All configurations deployed successfully!")
        print("🔍 Next: Test the websites to verify fixes")
    else:
        print("\n⚠️  Some deployments failed")

if __name__ == "__main__":
    main()