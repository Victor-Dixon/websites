#!/usr/bin/env python3
"""
Diagnose freerideinvestor.com WordPress Critical Error
======================================================

Attempts to identify the root cause of the WordPress critical error
by checking common issues and enabling debug mode.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "freerideinvestor.com"

def check_error_log(deployer, remote_base: str) -> str:
    """Check WordPress debug.log for errors."""
    print("\n🔍 Checking WordPress debug.log...")
    
    debug_log_paths = [
        f"{remote_base}/wp-content/debug.log",
        f"{remote_base}/debug.log",
    ]
    
    for log_path in debug_log_paths:
        try:
            command = f"tail -n 50 {log_path} 2>/dev/null"
            result = deployer.execute_command(command)
            if result and "PHP" in result:
                print(f"   ✅ Found debug.log at {log_path}")
                return result
        except Exception as e:
            continue
    
    print("   ⚠️  Debug.log not found or empty")
    return ""

def enable_wp_debug(deployer, remote_base: str) -> bool:
    """Enable WordPress debug mode in wp-config.php."""
    print("\n🔧 Enabling WordPress debug mode...")
    
    wp_config_path = f"{remote_base}/wp-config.php"
    
    try:
        # Read current wp-config
        command = f"cat {wp_config_path}"
        config_content = deployer.execute_command(command)
        
        if not config_content:
            print("   ❌ Cannot read wp-config.php")
            return False
        
        # Check if debug is already enabled
        if "WP_DEBUG" in config_content and "define('WP_DEBUG', true)" in config_content:
            print("   ✅ WP_DEBUG already enabled")
            return True
        
        # Add debug constants if not present
        debug_constants = """
/* Enable WordPress Debug Mode - Added by Agent-5 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
"""
        
        # Find the insertion point (before the "That's all, stop editing!" line)
        if "That's all, stop editing" in config_content:
            config_content = config_content.replace(
                "/* That's all, stop editing!",
                f"{debug_constants}\n/* That's all, stop editing!"
            )
        else:
            # Append at the end before closing PHP tag
            if config_content.strip().endswith("?>"):
                config_content = config_content.replace("?>", f"{debug_constants}\n?>")
            else:
                config_content += debug_constants
        
        # Write back (this would require a deployment approach)
        print("   ℹ️  Debug mode configuration prepared")
        print("   ⚠️  Manual edit required in wp-config.php")
        return False
        
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return False

def check_plugin_conflicts(deployer, remote_base: str) -> list:
    """Check for potentially problematic plugins."""
    print("\n🔍 Checking plugins...")
    
    plugins_path = f"{remote_base}/wp-content/plugins"
    
    try:
        command = f"ls -la {plugins_path} | grep '^d' | awk '{{print $NF}}'"
        plugins = deployer.execute_command(command)
        
        if plugins:
            plugin_list = [p.strip() for p in plugins.split('\n') if p.strip() and p not in ['.', '..']]
            print(f"   ✅ Found {len(plugin_list)} plugins")
            
            # Check for known problematic plugins
            problematic_patterns = ['error', 'debug', 'test', 'dev']
            problematic = [p for p in plugin_list if any(pattern in p.lower() for pattern in problematic_patterns)]
            
            if problematic:
                print(f"   ⚠️  Potentially problematic plugins: {', '.join(problematic)}")
            
            return plugin_list
        else:
            print("   ⚠️  Could not list plugins")
            return []
            
    except Exception as e:
        print(f"   ⚠️  Error checking plugins: {e}")
        return []

def main():
    print("🔍 Diagnosing WordPress Critical Error...\n")
    
    site_configs = load_site_configs()
    if SITE_NAME not in site_configs:
        print("❌ Site config not found")
        return 1
    
    deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    if not deployer.connect():
        print("❌ Could not connect to server")
        return 1
    
    try:
        remote_base = deployer.remote_path or f"/home/u996867598/domains/{SITE_NAME}/public_html"
        if not remote_base.startswith('/'):
            username = site_configs[SITE_NAME].get('username') or site_configs[SITE_NAME].get('sftp', {}).get('username', '')
            if username:
                remote_base = f"/home/{username}/{remote_base}"
        
        # Check error log
        error_log = check_error_log(deployer, remote_base)
        if error_log:
            print("\n📋 Recent Errors:")
            print(error_log[:1000])  # First 1000 chars
        
        # Check plugins
        plugins = check_plugin_conflicts(deployer, remote_base)
        
        # Enable debug (informational)
        enable_wp_debug(deployer, remote_base)
        
        print("\n📊 Diagnosis Summary:")
        print("   - Checked error logs")
        print("   - Checked plugin list")
        print("   - Reviewed debug configuration")
        print("\n💡 Next Steps:")
        print("   1. Review error log output above")
        print("   2. Temporarily disable plugins to test")
        print("   3. Check theme compatibility")
        print("   4. Verify PHP version compatibility")
        
        return 0
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    sys.exit(main())

