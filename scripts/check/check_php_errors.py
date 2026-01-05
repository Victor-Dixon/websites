#!/usr/bin/env python3
"""
Check PHP error logs on servers
==============================

Reads PHP error logs to identify fatal errors causing HTTP 500 issues.
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_php_errors(site_domain):
    """Check PHP error logs on server."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        # Common PHP error log locations
        error_log_paths = [
            f"domains/{site_domain}/logs/error_log",
            f"domains/{site_domain}/public_html/error_log",
            "/home/u996867598/logs/error_log",
            f"/home/u996867598/logs/{site_domain}/error_log"
        ]

        found_logs = []
        for log_path in error_log_paths:
            result = deployer.execute_command(f"ls -la {log_path} 2>/dev/null || echo 'Not found'")
            if 'Not found' not in result and log_path not in result:
                found_logs.append(log_path)
                print(f"📋 Found error log: {log_path}")

        if found_logs:
            # Check the most recent error log
            log_path = found_logs[0]
            result = deployer.execute_command(f"tail -20 {log_path} 2>/dev/null || echo 'Could not read log'")
            if 'Could not read log' not in result:
                print(f"\n📋 Last 20 lines of {log_path}:")
                print(result)
            else:
                print(f"⚠️  Could not read {log_path}")
        else:
            print("⚠️  No PHP error logs found")
            # Try to check for any recent PHP errors in system logs
            result = deployer.execute_command("grep -r 'PHP Fatal' /home/u996867598/logs/ 2>/dev/null | tail -5 || echo 'No fatal errors found'")
            if 'No fatal errors found' not in result:
                print("\n🚨 Found PHP Fatal errors:")
                print(result)

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error checking {site_domain}: {e}")
        return False

def main():
    """Check PHP error logs for problematic sites."""
    print("🔍 CHECKING PHP ERROR LOGS")
    print("=" * 50)

    sites = ['freerideinvestor.com', 'prismblossom.online']

    for site in sites:
        print(f"\n🔍 Checking {site}...")
        check_php_errors(site)

if __name__ == "__main__":
    main()