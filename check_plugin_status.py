#!/usr/bin/env python3
"""
Check WordPress plugin status
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_plugin_status(site_domain):
    """Check plugin status and look for issues."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking plugin status for {site_domain}...")

        # Check plugin directories
        result = deployer.execute_command('ls -la domains/freerideinvestor.com/public_html/wp-content/ | grep plugin')
        print('Plugin directories:')
        print(result)

        # Check if there are any active plugins
        result = deployer.execute_command('ls domains/freerideinvestor.com/public_html/wp-content/plugins/ 2>/dev/null || echo "No active plugins directory"')
        print('Active plugins:')
        print(result)

        # Check disabled plugins
        result = deployer.execute_command('ls domains/freerideinvestor.com/public_html/wp-content/plugins_disabled/ 2>/dev/null | head -10 || echo "No disabled plugins"')
        print('Disabled plugins:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_plugin_status('freerideinvestor.com')