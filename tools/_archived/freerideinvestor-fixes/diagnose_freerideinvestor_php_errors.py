#!/usr/bin/env python3
"""
Diagnose PHP Errors on freerideinvestor.com
============================================

Checks for PHP errors, warnings, and notices on the site.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import requests
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_php_errors():
    """Check for PHP errors on the site."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 DIAGNOSING PHP ERRORS: {site_name}")
    print("=" * 70)
    print()
    
    # First, check via HTTP
    print("1️⃣ Checking site via HTTP for visible errors...")
    try:
        response = requests.get(f"https://{site_name}", timeout=15)
        content = response.text
        
        # Look for PHP errors in HTML
        php_error_keywords = [
            'fatal error',
            'parse error',
            'warning:',
            'notice:',
            'deprecated:',
            'call to undefined',
            'undefined function',
            'undefined variable',
            'undefined index',
            'cannot redeclare',
            'already been declared',
        ]
        
        found_errors = []
        for keyword in php_error_keywords:
            if keyword.lower() in content.lower():
                # Find context around the error
                idx = content.lower().find(keyword.lower())
                context_start = max(0, idx - 100)
                context_end = min(len(content), idx + 500)
                context = content[context_start:context_end]
                found_errors.append({
                    'keyword': keyword,
                    'context': context.replace('\n', ' ').strip()
                })
        
        if found_errors:
            print(f"   ⚠️  Found {len(found_errors)} potential PHP error(s):")
            for error in found_errors[:5]:  # Show first 5
                print(f"      - {error['keyword']}: {error['context'][:200]}...")
        else:
            print("   ✅ No obvious PHP errors in HTML output")
        
        print()
        
    except Exception as e:
        print(f"   ❌ Error checking site: {e}")
        print()
    
    # Check WordPress error logs via SSH
    print("2️⃣ Checking WordPress error logs...")
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"   ❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("   ❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        
        # Check debug.log
        debug_log_path = f"{remote_path}/wp-content/debug.log"
        print(f"   Checking {debug_log_path}...")
        
        check_log_cmd = f"tail -50 {debug_log_path} 2>&1"
        log_result = deployer.execute_command(check_log_cmd)
        
        if 'no such file' in log_result.lower() or 'cannot access' in log_result.lower():
            print("   ℹ️  No debug.log found (WP_DEBUG may be off)")
        else:
            lines = log_result.strip().split('\n')
            if lines and len(lines) > 0 and lines[0]:
                print(f"   ⚠️  Found error log entries:")
                for line in lines[-10:]:  # Show last 10 lines
                    if line.strip():
                        print(f"      {line}")
            else:
                print("   ✅ No errors in debug.log")
        
        print()
        
        # Check wp-config.php for WP_DEBUG settings
        print("3️⃣ Checking wp-config.php for debug settings...")
        wp_config_path = f"{remote_path}/wp-config.php"
        
        check_config_cmd = f"grep -i 'WP_DEBUG\\|error_reporting\\|display_errors' {wp_config_path} 2>&1"
        config_result = deployer.execute_command(check_config_cmd)
        
        if config_result.strip():
            print(f"   Current debug settings:")
            for line in config_result.strip().split('\n'):
                if line.strip():
                    print(f"      {line}")
        else:
            print("   ℹ️  No explicit debug settings found")
        
        print()
        
        # Check for common problematic files
        print("4️⃣ Checking for common error sources...")
        
        # Check functions.php in active theme
        get_theme_cmd = f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>&1"
        theme_result = deployer.execute_command(get_theme_cmd)
        active_theme = theme_result.strip()
        
        if active_theme and active_theme != 'error':
            print(f"   Active theme: {active_theme}")
            functions_php = f"{remote_path}/wp-content/themes/{active_theme}/functions.php"
            
            # Check for syntax errors
            syntax_check_cmd = f"php -l {functions_php} 2>&1"
            syntax_result = deployer.execute_command(syntax_check_cmd)
            
            if 'no syntax errors' in syntax_result.lower():
                print(f"   ✅ functions.php syntax is valid")
            else:
                print(f"   ❌ functions.php has syntax errors:")
                print(f"      {syntax_result}")
        
        # Check plugins for errors
        print("5️⃣ Checking active plugins for issues...")
        check_plugins_cmd = f"cd {remote_path} && wp plugin list --status=active --allow-root 2>&1 | head -20"
        plugins_result = deployer.execute_command(check_plugins_cmd)
        
        if 'name' in plugins_result.lower() or 'plugin' in plugins_result.lower():
            print(f"   Active plugins:")
            for line in plugins_result.strip().split('\n')[1:6]:  # Show first 5 plugins
                if line.strip():
                    print(f"      {line}")
        
        print()
        
        return True
        
    except Exception as e:
        print(f"   ❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = check_php_errors()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

