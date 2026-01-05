#!/usr/bin/env python3
"""
Check dadudekc.com database configuration
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_dadudekc_config():
    """Check dadudekc.com database configuration."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer('dadudekc.com', site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to dadudekc.com")
            return False

        print(f"🔍 Checking database config for dadudekc.com...")

        # Read database settings
        result = deployer.execute_command('grep -A 10 "DB_" domains/dadudekc.com/public_html/wp-config.php')
        print('Database configuration:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_dadudekc_config()