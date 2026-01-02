#!/usr/bin/env python3
"""
Check Server Database Configuration
=================================

Reads the actual wp-config.php from the server to see current database settings.
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_server_config(site_domain):
    """Check database config on server."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        # Read database config from server
        result = deployer.execute_command(f"grep -E 'DB_NAME|DB_USER|DB_PASSWORD' domains/{site_domain}/public_html/wp-config.php")
        print(f"Current database config on {site_domain} server:")
        print(result.strip())

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def main():
    """Check server configs."""
    sites = ['freerideinvestor.com', 'prismblossom.online', 'ariajet.site']

    for site in sites:
        print(f"\n🔍 Checking {site}...")
        check_server_config(site)

if __name__ == "__main__":
    main()