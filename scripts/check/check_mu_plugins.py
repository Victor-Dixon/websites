#!/usr/bin/env python3
"""
Check must-use plugins
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_mu_plugins(site_domain):
    """Check must-use plugins."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking mu-plugins for {site_domain}...")

        # Check mu-plugins directory
        result = deployer.execute_command('ls -la domains/freerideinvestor.com/public_html/wp-content/mu-plugins/')
        print('Must-use plugins directory:')
        print(result)

        # Check for PHP files in mu-plugins
        result = deployer.execute_command('find domains/freerideinvestor.com/public_html/wp-content/mu-plugins/ -name "*.php" 2>/dev/null || echo "No PHP files found"')
        print('PHP files in mu-plugins:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_mu_plugins('freerideinvestor.com')