#!/usr/bin/env python3
"""
Check WordPress debug logs on servers
===================================

Reads the WordPress debug.log files to identify the actual errors
causing the HTTP 500 issues.
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_debug_log(site_domain):
    """Check WordPress debug log on server."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_domain}/public_html"

        # Check if debug.log exists
        result = deployer.execute_command(f"ls -la {remote_path}/wp-content/debug.log 2>/dev/null || echo 'No debug.log found'")
        print(f"Debug log status for {site_domain}: {result.strip()}")

        if 'debug.log' in result:
            # Read the last 20 lines of debug log
            log_content = deployer.execute_command(f"tail -20 {remote_path}/wp-content/debug.log")
            if log_content and 'tail:' not in log_content:
                print(f"\n📋 Last 20 lines of {site_domain} debug.log:")
                print(log_content)
            else:
                print(f"⚠️  Could not read debug.log for {site_domain}")
        else:
            print(f"⚠️  No debug.log found for {site_domain}")

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error checking {site_domain}: {e}")
        return False

def main():
    """Check debug logs for problematic sites."""
    print("🔍 CHECKING WORDPRESS DEBUG LOGS")
    print("=" * 50)

    sites = ['freerideinvestor.com', 'prismblossom.online']

    for site in sites:
        print(f"\n🔍 Checking {site}...")
        check_debug_log(site)

if __name__ == "__main__":
    main()