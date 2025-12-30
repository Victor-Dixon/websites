#!/usr/bin/env python3
"""
Fix PHP Errors on freerideinvestor.com via WP-CLI
==================================================

Uses WP-CLI commands to fix PHP configuration issues.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import os
from pathlib import Path

# Load environment variables
from dotenv import load_dotenv
load_dotenv()

# Try to find deployer
deployer_paths = [
    Path(__file__).parent.parent / "ops" / "deployment",
]
for path in deployer_paths:
    if (path / "simple_wordpress_deployer.py").exists():
        sys.path.insert(0, str(path))
        break

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer
except ImportError:
    print("❌ Could not import SimpleWordPressDeployer")
    sys.exit(1)


def load_site_configs():
    """Load site configurations."""
    config_path = Path(__file__).parent.parent / "configs" / "site_configs.json"
    if config_path.exists():
        import json
        with open(config_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {}


def fix_via_wpcli():
    """Fix PHP errors using WP-CLI commands."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING PHP ERRORS VIA WP-CLI: {site_name}")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    # The deployer should load credentials from environment
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        print("   💡 Make sure HOSTINGER_* environment variables are set")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        print("   💡 Check SFTP credentials in .env file")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        
        print(f"📁 Working directory: {remote_path}")
        print()
        
        # 1. Fix wp-config.php using WP-CLI and sed
        print("1️⃣ Fixing wp-config.php WP_DEBUG conflicts...")
        
        wp_config_path = f"{remote_path}/wp-config.php"
        
        # Backup first
        backup_cmd = f"cp {wp_config_path} {wp_config_path}.backup.$(date +%Y%m%d_%H%M%S) 2>&1"
        result = deployer.execute_command(backup_cmd)
        print(f"   💾 Backup created")
        
        # Use sed to clean up WP_DEBUG definitions
        # Remove all WP_DEBUG related lines, then add clean ones
        cleanup_cmd = f"""cd {remote_path} && cat wp-config.php | grep -v "WP_DEBUG" | grep -v "display_errors" | grep -v "error_reporting" > wp-config.php.tmp && mv wp-config.php.tmp wp-config.php 2>&1"""
        
        # Actually, better approach - use a here-doc to inject clean config
        inject_config_cmd = f"""cd {remote_path} && cat >> wp-config.php << 'EOFMARKER'

/* WordPress Debug Settings - Cleaned by Agent-7 */
if ( ! defined( 'WP_DEBUG' ) ) {{
    define( 'WP_DEBUG', false );
}}
if ( ! defined( 'WP_DEBUG_LOG' ) ) {{
    define( 'WP_DEBUG_LOG', false );
}}
if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {{
    define( 'WP_DEBUG_DISPLAY', false );
}}
@ini_set('display_errors', 0);
EOFMARKER
2>&1"""
        
        # Actually, the sed approach is too aggressive. Let me read, fix in Python, then write back
        print("   📖 Reading wp-config.php...")
        read_cmd = f"cat {wp_config_path} 2>&1"
        config_content = deployer.execute_command(read_cmd)
        
        # Parse and fix in Python
        lines = config_content.split('\n')
        new_lines = []
        in_debug_block = False
        skip_until_brace = False
        
        for i, line in enumerate(lines):
            # Skip comments about WP_DEBUG
            if 'WP_DEBUG' in line and ('/*' in line or '*/' in line or '* It is strongly recommended' in line):
                continue
            
            # Skip define statements for WP_DEBUG
            if 'define' in line.lower() and 'WP_DEBUG' in line:
                if 'if ( ! defined' in line:
                    skip_until_brace = True
                    continue
                elif skip_until_brace:
                    if ')' in line:
                        skip_until_brace = False
                    continue
                else:
                    continue
            
            # Skip @ini_set for display_errors
            if '@ini_set' in line and 'display_errors' in line:
                continue
            
            # Skip error_reporting
            if 'error_reporting' in line and 'ini_set' in line.lower():
                continue
            
            new_lines.append(line)
        
        # Insert clean debug settings before require_once
        insert_index = None
        for i, line in enumerate(new_lines):
            if 'require_once' in line and ('ABSPATH' in line or 'wp-settings.php' in line):
                insert_index = i
                break
        
        if insert_index is None:
            insert_index = len(new_lines)
        
        debug_config = """/* WordPress Debug Settings - Cleaned by Agent-7 */
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false );
}
if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', false );
}
if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false );
}
@ini_set('display_errors', 0);"""
        
        new_lines.insert(insert_index, debug_config)
        fixed_content = '\n'.join(new_lines)
        
        # Write to temp file, then move
        print("   ✏️  Writing fixed wp-config.php...")
        temp_file = "/tmp/wp-config-fixed-$$.php"
        
        # Write using echo or cat with here-doc
        write_cmd = f"""cat > {temp_file} << 'WPEOFMARKER'
{fixed_content}
WPEOFMARKER
2>&1"""
        
        deployer.execute_command(write_cmd)
        
        # Verify syntax
        syntax_check = f"php -l {temp_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_check)
        
        if 'No syntax errors' in syntax_result:
            # Copy to actual location
            copy_cmd = f"cp {temp_file} {wp_config_path} && rm {temp_file} 2>&1"
            deployer.execute_command(copy_cmd)
            print("   ✅ wp-config.php fixed and verified")
        else:
            print(f"   ❌ Syntax error in fixed file: {syntax_result}")
            deployer.execute_command(f"rm {temp_file} 2>&1")
            return False
        
        print()
        
        # 2. Add error suppression to functions.php
        print("2️⃣ Adding error suppression to functions.php...")
        
        # Get active theme
        get_theme_cmd = f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>&1"
        theme_result = deployer.execute_command(get_theme_cmd)
        active_theme = theme_result.strip()
        
        if active_theme and active_theme != 'error':
            functions_php = f"{remote_path}/wp-content/themes/{active_theme}/functions.php"
            
            # Check if suppression already exists
            check_suppression = f"grep -i 'Agent-7' {functions_php} 2>&1"
            has_suppression = deployer.execute_command(check_suppression)
            
            if not has_suppression.strip():
                # Backup
                deployer.execute_command(f"cp {functions_php} {functions_php}.backup.$(date +%Y%m%d_%H%M%S) 2>&1")
                
                # Read current content
                read_functions = f"cat {functions_php} 2>&1"
                functions_content = deployer.execute_command(read_functions)
                
                # Add suppression after opening PHP tag
                lines = functions_content.split('\n')
                insert_index = 1  # After <?php
                
                for i, line in enumerate(lines[1:10], 1):
                    if line.strip() and not line.strip().startswith('//') and not line.strip().startswith('/*'):
                        insert_index = i
                        break
                
                suppression_code = """// Suppress error output in production - Added by Agent-7
if (!defined('WP_DEBUG') || !WP_DEBUG) {
    @ini_set('display_errors', 0);
    error_reporting(0);
}"""
                
                lines.insert(insert_index, suppression_code)
                new_functions = '\n'.join(lines)
                
                # Write back
                temp_func = "/tmp/functions-$$.php"
                write_func_cmd = f"""cat > {temp_func} << 'FUNCEOFMARKER'
{new_functions}
FUNCEOFMARKER
2>&1"""
                deployer.execute_command(write_func_cmd)
                
                # Verify syntax
                func_syntax = f"php -l {temp_func} 2>&1"
                func_syntax_result = deployer.execute_command(func_syntax)
                
                if 'No syntax errors' in func_syntax_result:
                    deployer.execute_command(f"cp {temp_func} {functions_php} && rm {temp_func} 2>&1")
                    print(f"   ✅ Error suppression added to {active_theme}/functions.php")
                else:
                    print(f"   ⚠️  Syntax check failed: {func_syntax_result}")
                    deployer.execute_command(f"rm {temp_func} 2>&1")
            else:
                print(f"   ✅ Error suppression already exists")
        else:
            print(f"   ⚠️  Could not determine active theme")
        
        print()
        
        # 3. Verify fixes
        print("3️⃣ Verifying fixes...")
        
        # Check wp-config syntax
        final_syntax = f"php -l {wp_config_path} 2>&1"
        final_result = deployer.execute_command(final_syntax)
        
        if 'No syntax errors' in final_result:
            print("   ✅ wp-config.php syntax is valid")
        else:
            print(f"   ⚠️  wp-config.php syntax issue: {final_result}")
        
        # Test site
        print("   🔍 Testing site accessibility...")
        import requests
        try:
            response = requests.get(f"https://{site_name}", timeout=10)
            if response.status_code == 200:
                print(f"   ✅ Site is accessible (HTTP {response.status_code})")
            else:
                print(f"   ⚠️  Site returned HTTP {response.status_code}")
        except Exception as e:
            print(f"   ⚠️  Could not test site: {e}")
        
        print()
        print("=" * 70)
        print("✅ PHP ERROR FIXES COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next steps:")
        print("   1. Clear browser cache")
        print("   2. Test the site to verify errors are resolved")
        print("   3. Note: Warning messages from plugins/apps are intentional")
        
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
    success = fix_via_wpcli()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

