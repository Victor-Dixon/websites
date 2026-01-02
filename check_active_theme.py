#!/usr/bin/env python3
"""
Check active theme and functions.php
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_active_theme(site_domain):
    """Check active theme and functions.php."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking active theme for {site_domain}...")

        # Check what themes are available
        result = deployer.execute_command('ls domains/freerideinvestor.com/public_html/wp-content/themes/')
        print('Available themes:')
        print(result)

        # Check the active theme's functions.php
        result = deployer.execute_command('ls -la domains/freerideinvestor.com/public_html/wp-content/themes/FreeRideInvestor/functions.php 2>/dev/null || echo "functions.php not found"')
        print('functions.php in FreeRideInvestor theme:')
        print(result)

        # Check for syntax errors in functions.php
        result = deployer.execute_command('php -l domains/freerideinvestor.com/public_html/wp-content/themes/FreeRideInvestor/functions.php 2>/dev/null || echo "Cannot check syntax"')
        print('PHP syntax check:')
        print(result)

        # Check the first few lines of functions.php
        result = deployer.execute_command('head -10 domains/freerideinvestor.com/public_html/wp-content/themes/FreeRideInvestor/functions.php 2>/dev/null || echo "Cannot read functions.php"')
        print('functions.php first 10 lines:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_active_theme('freerideinvestor.com')