#!/usr/bin/env python3
"""Diagnose freerideinvestor.com contact page error"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_domain = "freerideinvestor.com"
    
    print(f"🔍 Diagnosing Contact Page Error for {site_domain}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    wp_path = "/home/u996867598/domains/freerideinvestor.com/public_html"
    
    # Check contact page
    print(f"\n📄 Checking contact page...")
    page_info = deployer.execute_command(f"cd {wp_path} && wp post get 85 --format=json 2>&1")
    print(f"   Page info retrieved")
    
    # Check for contact template
    print(f"\n🔍 Checking for contact page template...")
    template_check = deployer.execute_command(f"cd {wp_path} && ls -la wp-content/themes/freerideinvestor-modern/page-contact.php 2>&1")
    if "No such file" in template_check:
        print(f"   ❌ page-contact.php not found")
    else:
        print(f"   ✅ page-contact.php exists")
    
    # Check page.php
    page_php_check = deployer.execute_command(f"cd {wp_path} && ls -la wp-content/themes/freerideinvestor-modern/page.php 2>&1")
    if "No such file" in page_php_check:
        print(f"   ❌ page.php not found")
    else:
        print(f"   ✅ page.php exists")
    
    # Check PHP syntax
    print(f"\n🔍 Checking PHP syntax...")
    if "page-contact.php" in template_check:
        syntax_check = deployer.execute_command(f"cd {wp_path} && php -l wp-content/themes/freerideinvestor-modern/page-contact.php 2>&1")
        print(f"   {syntax_check[:200]}")
    
    # Check error logs
    print(f"\n📋 Checking error logs...")
    error_log = deployer.execute_command(f"cd {wp_path} && tail -20 wp-content/debug.log 2>&1")
    if error_log and "No such file" not in error_log:
        print(f"   Recent errors:\n{error_log[:500]}")
    else:
        print(f"   No debug.log found")
    
    # Check PHP error log
    php_error_log = deployer.execute_command(f"cd {wp_path} && tail -20 error_log 2>&1")
    if php_error_log and "No such file" not in php_error_log and len(php_error_log) > 10:
        print(f"   PHP errors:\n{php_error_log[:500]}")
    
    # Check if contact form plugin is active
    print(f"\n🔌 Checking contact form plugins...")
    plugins = deployer.execute_command(f"cd {wp_path} && wp plugin list --status=active --format=json 2>&1")
    if "contact" in plugins.lower():
        print(f"   Contact form plugin found")
    else:
        print(f"   No contact form plugin active")
    
    deployer.disconnect()

if __name__ == "__main__":
    main()

