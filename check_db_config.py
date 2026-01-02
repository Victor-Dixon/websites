#!/usr/bin/env python3
"""
Check database configuration on server
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_db_config(site_domain):
    """Check database configuration."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking database config for {site_domain}...")

        # Read database settings
        result = deployer.execute_command('grep -A 10 "DB_" domains/freerideinvestor.com/public_html/wp-config.php')
        print('Database configuration:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_db_config('freerideinvestor.com')