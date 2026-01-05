#!/usr/bin/env python3
"""
Clear cache for dadudekc.com
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def clear_dadudekc_cache():
    """Clear cache for dadudekc.com."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer('dadudekc.com', site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to dadudekc.com")
            return False

        print(f"🔧 Clearing caches for dadudekc.com...")

        # Clear WordPress caches
        result = deployer.execute_command('rm -rf domains/dadudekc.com/public_html/wp-content/cache/* 2>/dev/null || echo "No cache files to remove"')
        print('WordPress cache cleared:', result.strip())

        # Clear PHP OPcache if possible
        result = deployer.execute_command('php -r "if(function_exists(\'opcache_reset\')){opcache_reset(); echo \'OPcache cleared\'; } else { echo \'OPcache not available\'; }"')
        print('PHP OPcache:', result.strip())

        # Check for config files that might override
        result = deployer.execute_command('find domains/dadudekc.com/public_html -name ".user.ini" -o -name "php.ini" -o -name "*.ini" | head -10')
        print('Config files found:', result.strip() or 'None')

        print('✅ Cache cleared successfully for dadudekc.com!')

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    clear_dadudekc_cache()