#!/usr/bin/env python3
"""
Check Blog Template for Errors
================================

Checks PHP syntax and error logs for the blog template.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_errors():
    """Check for template errors."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 CHECKING BLOG TEMPLATE ERRORS: {site_name}")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        template_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/page-templates/page-blog-stunning.php"
        
        # Check PHP syntax
        print("1️⃣ Checking PHP syntax...")
        syntax_check = f"php -l {template_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_check)
        print(syntax_result)
        
        # Check error log
        print()
        print("2️⃣ Checking error logs...")
        error_log = f"{remote_path}/wp-content/debug.log"
        check_log = f"tail -30 {error_log} 2>&1"
        log_result = deployer.execute_command(check_log)
        
        if 'no such file' not in log_result.lower():
            print("Recent errors:")
            print(log_result)
        else:
            print("No debug.log found")
        
        # Check PHP error log
        print()
        print("3️⃣ Checking PHP error log...")
        php_error_log = f"{remote_path}/error_log"
        check_php_log = f"tail -30 {php_error_log} 2>&1"
        php_log_result = deployer.execute_command(check_php_log)
        
        if 'no such file' not in php_log_result.lower() and php_log_result.strip():
            print("PHP errors:")
            print(php_log_result[-500:])  # Last 500 chars
        else:
            print("No PHP error log found or empty")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = check_errors()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

