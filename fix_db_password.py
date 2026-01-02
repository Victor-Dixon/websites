#!/usr/bin/env python3
"""
Fix database password in wp-config.php
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def fix_db_password(site_domain):
    """Fix database password."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔧 Fixing database password for {site_domain}...")

        # Update the password from Falcons#1247 to 3aZq7XTxA6
        result = deployer.execute_command('sed -i "s/Falcons#1247/3aZq7XTxA6/g" domains/freerideinvestor.com/public_html/wp-config.php')
        print('Password update command result:', result)

        # Verify the change
        result = deployer.execute_command('grep "DB_PASSWORD" domains/freerideinvestor.com/public_html/wp-config.php')
        print('Updated password in config:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    fix_db_password('freerideinvestor.com')