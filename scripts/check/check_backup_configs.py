#!/usr/bin/env python3
"""
Check backup wp-config files for database credentials
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_backup_configs(site_domain):
    """Check backup config files."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking backup configs for {site_domain}...")

        # Check database users in all wp-config files
        result = deployer.execute_command('grep "DB_USER" domains/freerideinvestor.com/public_html/wp-config*')
        print('Database users in all config files:')
        print(result)

        # Check the main backup file
        result = deployer.execute_command('grep -A 5 "DB_" domains/freerideinvestor.com/public_html/wp-config.php.backup')
        print('Database config in main backup:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_backup_configs('freerideinvestor.com')