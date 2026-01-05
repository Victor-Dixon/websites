#!/usr/bin/env python3
"""
Get Database Configuration from Server
====================================

Reads the current wp-config.php from the server to get the correct
database credentials.
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def get_db_config(site_domain):
    """Get database configuration from server."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return None

        # Read wp-config.php
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_domain}/public_html"
        command = f"cat {remote_path}/wp-config.php"
        result = deployer.execute_command(command)

        if not result or "cat:" in result:
            print(f"❌ Failed to read wp-config.php for {site_domain}")
            deployer.disconnect()
            return None

        # Extract database config
        db_config = {}
        lines = result.split('\n')

        for line in lines:
            line = line.strip()
            if line.startswith("define( 'DB_NAME'"):
                db_config['name'] = line.split("'")[3]
            elif line.startswith("define( 'DB_USER'"):
                db_config['user'] = line.split("'")[3]
            elif line.startswith("define( 'DB_PASSWORD'"):
                db_config['password'] = line.split("'")[3]
            elif line.startswith("define( 'DB_HOST'"):
                db_config['host'] = line.split("'")[3]

        deployer.disconnect()

        print(f"✅ Retrieved database config for {site_domain}:")
        print(f"   Database: {db_config.get('name', 'NOT FOUND')}")
        print(f"   User: {db_config.get('user', 'NOT FOUND')}")
        print(f"   Password: {'*' * len(db_config.get('password', '')) if db_config.get('password') else 'NOT FOUND'}")
        print(f"   Host: {db_config.get('host', 'NOT FOUND')}")

        return db_config

    except Exception as e:
        print(f"❌ Error: {e}")
        return None

if __name__ == "__main__":
    sites = ['freerideinvestor.com', 'prismblossom.online']

    for site in sites:
        print(f"\n🔍 Getting config for {site}...")
        config = get_db_config(site)
        if config:
            # Update local wp-config.php with correct credentials
            wp_config_path = Path(__file__).parent / "websites" / site / "wp-config.php"

            if wp_config_path.exists():
                with open(wp_config_path, 'r') as f:
                    content = f.read()

                # Update database settings
                replacements = {
                    f"define( 'DB_NAME', '{site.replace('.', '_')}_db' );": f"define( 'DB_NAME', '{config['name']}' );",
                    f"define( 'DB_USER', '{site.replace('.', '_')}_user' );": f"define( 'DB_USER', '{config['user']}' );",
                    f"define( 'DB_PASSWORD', '{site.replace('.', '_')}_password' );": f"define( 'DB_PASSWORD', '{config['password']}' );",
                    "define( 'DB_HOST', 'localhost' );": f"define( 'DB_HOST', '{config['host']}' );",
                }

                for old, new in replacements.items():
                    content = content.replace(old, new)

                with open(wp_config_path, 'w') as f:
                    f.write(content)

                print(f"✅ Updated local wp-config.php for {site}")
            else:
                print(f"❌ Local wp-config.php not found for {site}")
        else:
            print(f"❌ Could not get config for {site}")