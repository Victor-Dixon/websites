#!/usr/bin/env python3
"""
Check recent debug log entries
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_recent_logs(site_domain):
    """Check recent debug log entries."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking recent logs for {site_domain}...")

        # Check the last few lines of debug log
        result = deployer.execute_command('tail -10 domains/freerideinvestor.com/public_html/wp-content/debug.log 2>/dev/null || echo "No debug.log found"')
        print('Last 10 lines of debug.log:')
        print(result)

        # Check when the file was last modified
        result = deployer.execute_command('stat domains/freerideinvestor.com/public_html/wp-content/debug.log 2>/dev/null || echo "No debug.log found"')
        print('Debug log info:')
        print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_recent_logs('freerideinvestor.com')