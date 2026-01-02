#!/usr/bin/env python3
"""
Check WordPress theme files for PHP syntax errors
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_theme_syntax(site_domain):
    """Check theme files for PHP syntax errors."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking theme syntax for {site_domain}...")

        # Check functions.php syntax
        result = deployer.execute_command('php -l domains/freerideinvestor.com/public_html/wp-content/themes/FreeRideInvestor/functions.php')
        print('functions.php syntax check:')
        print(result)

        # Check all PHP files in theme for syntax errors
        result = deployer.execute_command('find domains/freerideinvestor.com/public_html/wp-content/themes/FreeRideInvestor -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors detected" | head -10')
        print('Theme PHP syntax errors:')
        print(result or 'No syntax errors found in theme files')

        # Check if there are any obviously problematic includes
        result = deployer.execute_command('grep -r "require\|include" domains/freerideinvestor.com/public_html/wp-content/themes/FreeRideInvestor/ --include="*.php" | head -10')
        print('Theme include/require statements:')
        print(result or 'No includes found')

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_theme_syntax('freerideinvestor.com')