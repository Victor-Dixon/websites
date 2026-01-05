#!/usr/bin/env python3
"""
Verify Debug Logging is Working
==============================

Checks that debug.log is being written to after the hardening.
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def verify_debug_logging(site_domain):
    """Verify debug logging is working."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_domain}/public_html"

        # Check debug.log exists and get recent entries
        result = deployer.execute_command(f"tail -5 {remote_path}/wp-content/debug.log 2>/dev/null || echo 'No recent log entries'")
        print(f"Recent debug.log entries for {site_domain}:")
        print(result.strip())

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def main():
    """Verify debug logging for both sites."""
    print("🔍 VERIFYING DEBUG LOGGING")
    print("=" * 40)

    sites = ['freerideinvestor.com', 'prismblossom.online']

    for site in sites:
        print(f"\n🔍 Checking {site}...")
        verify_debug_logging(site)

if __name__ == "__main__":
    main()