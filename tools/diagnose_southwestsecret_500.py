#!/usr/bin/env python3
"""
Diagnose southwestsecret.com HTTP 500 Error
============================================

Investigates and fixes the HTTP 500 error on southwestsecret.com.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def diagnose_500_error():
    """Diagnose HTTP 500 error on southwestsecret.com."""
    print("=" * 70)
    print("🔍 DIAGNOSING HTTP 500 ERROR: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        
        print("📋 Step 1: Checking error logs...")
        print("-" * 70)
        
        # Check multiple log locations
        log_paths = [
            f"{remote_path}/error_log",
            f"{remote_path}/.error_log",
            f"{remote_path}/wp-content/debug.log",
            f"{remote_path}/wp-content/error_log",
        ]
        
        found_errors = False
        for log_path in log_paths:
            print(f"   Checking: {log_path}")
            log_cmd = f"tail -n 50 {log_path} 2>/dev/null"
            log_result = deployer.execute_command(log_cmd)
            
            if log_result and log_result.strip():
                print(f"   ✅ Found log entries:")
                print(f"   {log_result[:500]}")
                found_errors = True
            else:
                print(f"   ⚠️  No log entries found")
            print()
        
        if not found_errors:
            print("   ⚠️  No error logs found in common locations")
            print()
        
        print("📋 Step 2: Checking PHP syntax errors...")
        print("-" * 70)
        
        # Check theme functions.php
        theme_path = f"{remote_path}/wp-content/themes"
        list_themes_cmd = f"ls -1 {theme_path}/ 2>/dev/null | head -5"
        themes_list = deployer.execute_command(list_themes_cmd)
        
        if themes_list:
            themes = [t.strip() for t in themes_list.strip().split('\n') if t.strip()]
            print(f"   Found themes: {', '.join(themes)}")
            
            for theme_name in themes:
                functions_file = f"{theme_path}/{theme_name}/functions.php"
                print(f"\n   Checking: {theme_name}/functions.php")
                
                syntax_cmd = f"php -l {functions_file} 2>&1"
                syntax_result = deployer.execute_command(syntax_cmd)
                
                if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                    print(f"      ✅ Syntax OK")
                else:
                    print(f"      ❌ Syntax error found:")
                    print(f"      {syntax_result[:300]}")
        
        print()
        
        print("📋 Step 3: Checking wp-config.php...")
        print("-" * 70)
        
        wp_config = f"{remote_path}/wp-config.php"
        check_config_cmd = f"test -f {wp_config} && echo 'exists' || echo 'not found'"
        if 'exists' in deployer.execute_command(check_config_cmd):
            print(f"   ✅ wp-config.php exists")
            
            # Check for syntax errors
            syntax_cmd = f"php -l {wp_config} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print(f"      ✅ Syntax OK")
            else:
                print(f"      ❌ Syntax error:")
                print(f"      {syntax_result[:300]}")
        else:
            print(f"   ❌ wp-config.php not found!")
        
        print()
        
        print("📋 Step 4: Checking .htaccess...")
        print("-" * 70)
        
        htaccess = f"{remote_path}/.htaccess"
        check_htaccess_cmd = f"test -f {htaccess} && echo 'exists' || echo 'not found'"
        if 'exists' in deployer.execute_command(check_htaccess_cmd):
            print(f"   ✅ .htaccess exists")
            
            # Read .htaccess to check for issues
            read_htaccess_cmd = f"cat {htaccess}"
            htaccess_content = deployer.execute_command(read_htaccess_cmd)
            
            if htaccess_content:
                # Check for common issues
                if 'RewriteEngine On' in htaccess_content and 'RewriteBase' not in htaccess_content:
                    print(f"      ⚠️  RewriteEngine On without RewriteBase (may cause issues)")
                
                # Check file size
                if len(htaccess_content) > 50000:
                    print(f"      ⚠️  .htaccess is very large ({len(htaccess_content)} bytes)")
                
                print(f"      📄 .htaccess size: {len(htaccess_content)} bytes")
        else:
            print(f"   ℹ️  .htaccess not found (this is OK)")
        
        print()
        
        print("📋 Step 5: Checking WordPress database connection...")
        print("-" * 70)
        
        # Try to get WordPress info via WP-CLI
        wp_cli_cmd = f"cd {remote_path} && wp core version 2>&1"
        wp_result = deployer.execute_command(wp_cli_cmd)
        
        if wp_result and "WordPress" in wp_result:
            print(f"   ✅ WordPress detected: {wp_result.strip()}")
        else:
            print(f"   ⚠️  Could not verify WordPress installation")
            print(f"   Result: {wp_result[:200]}")
        
        print()
        
        print("📋 Step 6: Checking active plugins...")
        print("-" * 70)
        
        plugins_cmd = f"cd {remote_path} && wp plugin list --status=active --format=json 2>&1"
        plugins_result = deployer.execute_command(plugins_cmd)
        
        if plugins_result and plugins_result.strip():
            try:
                import json
                plugins = json.loads(plugins_result)
                if plugins:
                    print(f"   Found {len(plugins)} active plugins:")
                    for plugin in plugins[:10]:  # Show first 10
                        print(f"      - {plugin.get('name', 'Unknown')} ({plugin.get('status', 'Unknown')})")
                else:
                    print(f"   ℹ️  No active plugins")
            except:
                print(f"   ⚠️  Could not parse plugin list")
                print(f"   Raw output: {plugins_result[:300]}")
        else:
            print(f"   ⚠️  Could not get plugin list")
        
        print()
        
        print("=" * 70)
        print("📊 DIAGNOSIS COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next steps:")
        print("   1. Review error logs above")
        print("   2. Fix any PHP syntax errors found")
        print("   3. Check for plugin conflicts")
        print("   4. Verify database connection")
        print("   5. Check file permissions")
        
        return True
        
    except Exception as e:
        print(f"❌ Error during diagnosis: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if diagnose_500_error() else 1)

