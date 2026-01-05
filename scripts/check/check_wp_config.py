#!/usr/bin/env python3
"""
Check wp-config.php contents on server
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_wp_config(site_domain):
    """Check wp-config.php contents."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking {site_domain} wp-config.php...")

        # Check DB_USER
        result = deployer.execute_command('grep "DB_USER" domains/freerideinvestor.com/public_html/wp-config.php')
        print('DB_USER in main wp-config.php:')
        print(result)

        # Check for old user
        result = deployer.execute_command('grep "freerideinvestor_user" domains/freerideinvestor.com/public_html/wp-config.php')
        print('Old user in main wp-config.php:')
        print(result or 'Not found')

        # Check if there's a different wp-config being used
        result = deployer.execute_command('php -r "echo php_ini_loaded_file();"')
        print('PHP ini file:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_wp_config('freerideinvestor.com')