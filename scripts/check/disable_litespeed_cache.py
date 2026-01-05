#!/usr/bin/env python3
"""
Disable LiteSpeed cache in .htaccess
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def disable_litespeed_cache(site_domain):
    """Disable LiteSpeed cache."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔧 Disabling LiteSpeed cache for {site_domain}...")

        # Comment out LSCACHE blocks
        deployer.execute_command('sed -i "s/# BEGIN LSCACHE/# BEGIN LSCACHE_DISABLED/g" domains/freerideinvestor.com/public_html/.htaccess')
        deployer.execute_command('sed -i "s/# END LSCACHE/# END LSCACHE_DISABLED/g" domains/freerideinvestor.com/public_html/.htaccess')

        print('✅ LiteSpeed cache disabled')

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    disable_litespeed_cache('freerideinvestor.com')