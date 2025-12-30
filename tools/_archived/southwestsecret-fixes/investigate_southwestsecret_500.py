#!/usr/bin/env python3
"""
Investigate Remaining HTTP 500 Error
=====================================

Deep investigation of remaining 500 error after syntax fix.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def investigate_500():
    """Comprehensive investigation of 500 error."""
    print("=" * 70)
    print("🔍 DEEP INVESTIGATION: southwestsecret.com HTTP 500")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        
        # Step 1: Enable WordPress debug mode
        print("📋 Step 1: Enabling WordPress debug mode...")
        print("-" * 70)
        
        wp_config = f"{remote_path}/wp-config.php"
        read_cmd = f"cat {wp_config}"
        wp_config_content = deployer.execute_command(read_cmd)
        
        if wp_config_content:
            # Check if debug is enabled
            if "define('WP_DEBUG', true);" not in wp_config_content:
                print("   ⚠️  Debug mode not enabled")
                print("   🔧 Enabling debug mode...")
                
                # Add debug constants
                debug_constants = '''
// Enable WordPress debug mode - Added by Agent-7
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
'''
                
                if '?>' in wp_config_content:
                    new_config = wp_config_content.replace('?>', debug_constants + '\n?>')
                else:
                    new_config = wp_config_content + '\n' + debug_constants
                
                # Save locally
                local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_wpconfig_debug.php"
                local_file.parent.mkdir(parents=True, exist_ok=True)
                local_file.write_text(new_config, encoding='utf-8')
                
                # Deploy
                success = deployer.deploy_file(local_file, wp_config)
                if success:
                    print("   ✅ Debug mode enabled")
                else:
                    print("   ❌ Failed to enable debug mode")
            else:
                print("   ✅ Debug mode already enabled")
        
        print()
        
        # Step 2: Check debug.log for recent errors
        print("📋 Step 2: Checking debug.log for errors...")
        print("-" * 70)
        
        debug_log = f"{remote_path}/wp-content/debug.log"
        log_cmd = f"tail -n 100 {debug_log} 2>/dev/null"
        log_result = deployer.execute_command(log_cmd)
        
        if log_result and log_result.strip():
            print("   ✅ Found error log entries:")
            print()
            # Show last 20 lines
            lines = log_result.strip().split('\n')
            for line in lines[-20:]:
                print(f"   {line[:120]}")
        else:
            print("   ⚠️  No debug.log entries found")
            print("   💡 Errors may be logged elsewhere or not being logged")
        
        print()
        
        # Step 3: Check server error logs
        print("📋 Step 3: Checking server error logs...")
        print("-" * 70)
        
        error_logs = [
            f"{remote_path}/error_log",
            f"{remote_path}/.error_log",
            f"{remote_path}/logs/error_log",
            "/home/u996867598/logs/southwestsecret.com/error_log",
        ]
        
        for log_path in error_logs:
            print(f"   Checking: {log_path}")
            log_cmd = f"tail -n 50 {log_path} 2>/dev/null"
            log_result = deployer.execute_command(log_cmd)
            
            if log_result and log_result.strip():
                print(f"   ✅ Found entries:")
                lines = log_result.strip().split('\n')
                for line in lines[-10:]:
                    if 'southwestsecret' in line.lower() or 'php' in line.lower() or 'fatal' in line.lower():
                        print(f"      {line[:120]}")
                break
            else:
                print(f"   ⚠️  No entries")
        
        print()
        
        # Step 4: Test database connection
        print("📋 Step 4: Testing database connection...")
        print("-" * 70)
        
        db_test_cmd = f"cd {remote_path} && wp db check 2>&1"
        db_result = deployer.execute_command(db_test_cmd)
        
        if db_result:
            if "Success" in db_result or "OK" in db_result:
                print("   ✅ Database connection OK")
            elif "Error" in db_result or "Failed" in db_result:
                print(f"   ❌ Database connection issue:")
                print(f"   {db_result[:300]}")
            else:
                print(f"   ⚠️  Database check result: {db_result[:200]}")
        else:
            print("   ⚠️  Could not test database connection")
        
        print()
        
        # Step 5: Check file permissions
        print("📋 Step 5: Checking file permissions...")
        print("-" * 70)
        
        key_files = [
            f"{remote_path}/wp-config.php",
            f"{remote_path}/wp-content",
            f"{remote_path}/wp-content/themes/southwestsecret",
            f"{remote_path}/wp-content/plugins",
            f"{remote_path}/wp-content/uploads",
        ]
        
        for file_path in key_files:
            perm_cmd = f"ls -ld {file_path} 2>/dev/null"
            perm_result = deployer.execute_command(perm_cmd)
            
            if perm_result:
                print(f"   {Path(file_path).name}: {perm_result.strip()}")
            else:
                print(f"   {Path(file_path).name}: ⚠️  Could not check")
        
        print()
        
        # Step 6: Check PHP memory limit and errors
        print("📋 Step 6: Checking PHP configuration...")
        print("-" * 70)
        
        php_info_cmd = f"cd {remote_path} && php -r 'echo ini_get(\"memory_limit\"); echo \"\\n\"; echo ini_get(\"max_execution_time\");' 2>&1"
        php_info = deployer.execute_command(php_info_cmd)
        
        if php_info:
            print(f"   Memory limit: {php_info.strip().split(chr(10))[0] if chr(10) in php_info else 'Unknown'}")
            print(f"   Max execution time: {php_info.strip().split(chr(10))[1] if chr(10) in php_info and len(php_info.strip().split(chr(10))) > 1 else 'Unknown'}")
        
        print()
        
        # Step 7: Test WordPress core
        print("📋 Step 7: Testing WordPress core integrity...")
        print("-" * 70)
        
        core_check_cmd = f"cd {remote_path} && wp core verify-checksums 2>&1"
        core_result = deployer.execute_command(core_check_cmd)
        
        if core_result:
            if "Success" in core_result or "OK" in core_result or "No differences" in core_result:
                print("   ✅ WordPress core files are intact")
            elif "Error" in core_result or "Failed" in core_result:
                print(f"   ⚠️  Core verification issues:")
                print(f"   {core_result[:300]}")
            else:
                print(f"   Result: {core_result[:200]}")
        
        print()
        
        # Step 8: Check for fatal errors in theme
        print("📋 Step 8: Testing theme activation...")
        print("-" * 70)
        
        # Get current theme
        theme_cmd = f"cd {remote_path} && wp theme list --status=active --format=json 2>&1"
        theme_result = deployer.execute_command(theme_cmd)
        
        if theme_result:
            try:
                import json
                themes = json.loads(theme_result)
                if themes:
                    current_theme = themes[0].get('name', 'Unknown')
                    print(f"   Current active theme: {current_theme}")
                    
                    # Try to get theme info
                    theme_info_cmd = f"cd {remote_path} && wp theme get {current_theme} --format=json 2>&1"
                    theme_info = deployer.execute_command(theme_info_cmd)
                    if theme_info and "Error" not in theme_info:
                        print(f"   ✅ Theme information retrieved successfully")
                    else:
                        print(f"   ⚠️  Could not get theme info: {theme_info[:200]}")
            except:
                print(f"   ⚠️  Could not parse theme list: {theme_result[:200]}")
        
        print()
        
        # Step 9: Try to access site via WP-CLI
        print("📋 Step 9: Testing site via WP-CLI...")
        print("-" * 70)
        
        # Try to get site URL and test
        url_cmd = f"cd {remote_path} && wp option get siteurl 2>&1"
        url_result = deployer.execute_command(url_cmd)
        
        if url_result:
            site_url = url_result.strip()
            print(f"   Site URL: {site_url}")
        
        # Try to get a simple option
        test_cmd = f"cd {remote_path} && wp option get blogname 2>&1"
        test_result = deployer.execute_command(test_cmd)
        
        if test_result:
            if "Error" in test_result or "Fatal" in test_result:
                print(f"   ❌ Fatal error detected:")
                print(f"   {test_result[:500]}")
            else:
                print(f"   ✅ WP-CLI can access database: {test_result.strip()}")
        
        print()
        
        print("=" * 70)
        print("📊 INVESTIGATION COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next steps based on findings above:")
        print("   1. Review error logs for specific error messages")
        print("   2. Check if any plugins are causing conflicts")
        print("   3. Verify database credentials in wp-config.php")
        print("   4. Check file permissions on wp-content directory")
        print("   5. Test with default theme to isolate theme issues")
        
        return True
        
    except Exception as e:
        print(f"❌ Error during investigation: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if investigate_500() else 1)





