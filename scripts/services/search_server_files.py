#!/usr/bin/env python3
"""
Search for wp-config files and old database user on server
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def search_server_files(site_domain):
    """Search for wp-config files and old database user."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Searching {site_domain} server...")

        # Check for any wp-config files
        result = deployer.execute_command('find domains/freerideinvestor.com/public_html -name "wp-config*" -type f')
        print('WP-Config files found:')
        print(result)

        # Check for any PHP cache files or files with old user
        result = deployer.execute_command('find domains/freerideinvestor.com/public_html -name "*.php" -exec grep -l "freerideinvestor_user" {} \; 2>/dev/null || echo "No files found with old user"')
        print('Files containing old database user:')
        print(result)

        # Check if there's a cached config or opcode cache
        result = deployer.execute_command('ls -la domains/freerideinvestor.com/public_html/wp-content/cache/ 2>/dev/null || echo "No cache directory found"')
        print('Cache directory contents:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    search_server_files('freerideinvestor.com')