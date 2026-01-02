#!/usr/bin/env python3
"""
Clear WordPress and PHP caches
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def clear_caches(site_domain):
    """Clear caches and check for config overrides."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔧 Clearing caches for {site_domain}...")

        # Clear WordPress caches
        result = deployer.execute_command('rm -rf domains/freerideinvestor.com/public_html/wp-content/cache/* 2>/dev/null || echo "No cache files to remove"')
        print('WordPress cache cleared:', result.strip())

        # Clear PHP OPcache if possible
        result = deployer.execute_command('php -r "if(function_exists(\'opcache_reset\')){opcache_reset(); echo \'OPcache cleared\'; } else { echo \'OPcache not available\'; }"')
        print('PHP OPcache:', result.strip())

        # Check for config files that might override
        result = deployer.execute_command('find domains/freerideinvestor.com/public_html -name ".user.ini" -o -name "php.ini" -o -name "*.ini" | head -10')
        print('Config files found:', result.strip() or 'None')

        # Try to restart PHP if possible
        result = deployer.execute_command('which systemctl && systemctl reload php8.2-fpm 2>/dev/null || which service && service php8.2-fpm reload 2>/dev/null || echo "PHP reload not available"')
        print('PHP service reload:', result.strip() or 'Not attempted')

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    clear_caches('freerideinvestor.com')