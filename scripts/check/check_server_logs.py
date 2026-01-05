#!/usr/bin/env python3
"""
Check server-level error logs
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_server_logs(site_domain):
    """Check server-level error logs."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        print(f"🔍 Checking server logs for {site_domain}...")

        # Check Apache/Nginx error logs
        log_paths = [
            f"/var/log/apache2/error.log",
            f"/var/log/apache2/{site_domain}_error.log",
            f"/var/log/nginx/error.log",
            f"/var/log/httpd/error.log",
            f"/home/u996867598/logs/{site_domain}_error.log"
        ]

        for log_path in log_paths:
            result = deployer.execute_command(f'tail -10 {log_path} 2>/dev/null || echo "Log not found: {log_path}"')
            if "Log not found" not in result:
                print(f'Found log: {log_path}')
                print(result)
                break

        # Check PHP-FPM error log
        result = deployer.execute_command('tail -10 /var/log/php8.2-fpm.log 2>/dev/null || echo "PHP-FPM log not found"')
        print('PHP-FPM log:')
        print(result)

        # Try to check if there are any .htaccess issues
        result = deployer.execute_command('ls -la domains/freerideinvestor.com/public_html/.htaccess')
        print('.htaccess file:')
        print(result)

        # Check .htaccess content for syntax errors
        result = deployer.execute_command('head -20 domains/freerideinvestor.com/public_html/.htaccess 2>/dev/null || echo "No .htaccess found"')
        print('.htaccess content:')
        print(result[:500])

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    check_server_logs('freerideinvestor.com')