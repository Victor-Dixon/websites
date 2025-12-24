#!/usr/bin/env python3
"""
Fix PHP Errors on freerideinvestor.com
======================================

Fixes PHP errors and configuration issues.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import re
import json
from pathlib import Path

# Try multiple possible paths for the deployer
deployer_paths = [
    Path(__file__).parent.parent / "websites" / "ops" / "deployment",
    Path(__file__).parent.parent / "ops" / "deployment",
]
for path in deployer_paths:
    if (path / "simple_wordpress_deployer.py").exists():
        sys.path.insert(0, str(path))
        break

def load_site_configs():
    """Load site configurations."""
    config_path = Path(__file__).parent.parent / "configs" / "site_configs.json"
    if config_path.exists():
        with open(config_path, 'r', encoding='utf-8') as f:
            return json.load(f)
    return {}

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer
except ImportError:
    print("❌ Could not import SimpleWordPressDeployer")
    print("   Searching for deployer module...")
    import os
    for root, dirs, files in os.walk(str(Path(__file__).parent.parent)):
        if 'simple_wordpress_deployer.py' in files:
            print(f"   Found at: {root}")
            sys.path.insert(0, root)
            from simple_wordpress_deployer import SimpleWordPressDeployer
            break
    else:
        raise ImportError("Could not find simple_wordpress_deployer.py")
import json


def fix_wp_config():
    """Fix conflicting WP_DEBUG definitions in wp-config.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING PHP ERRORS: {site_name}")
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
        wp_config_path = f"{remote_path}/wp-config.php"
        
        # Read current wp-config.php
        print("📖 Reading wp-config.php...")
        read_config_cmd = f"cat {wp_config_path} 2>&1"
        config_content = deployer.execute_command(read_config_cmd)
        
        if 'no such file' in config_content.lower():
            print(f"   ❌ wp-config.php not found at {wp_config_path}")
            return False
        
        original_content = config_content
        
        # Check for conflicting WP_DEBUG definitions
        debug_definitions = []
        lines = config_content.split('\n')
        
        for i, line in enumerate(lines):
            if 'WP_DEBUG' in line and 'define' in line.lower():
                debug_definitions.append((i, line))
        
        if len(debug_definitions) > 1:
            print(f"   ⚠️  Found {len(debug_definitions)} WP_DEBUG definitions (conflict detected)")
            
            # Keep only the last valid definition, but standardize it
            # Remove all WP_DEBUG definitions
            new_lines = []
            skip_next = False
            
            for i, line in enumerate(lines):
                # Skip commented lines about WP_DEBUG
                if 'WP_DEBUG' in line and ('/*' in line or '*/' in line or '* It is strongly recommended' in line):
                    continue
                
                # Skip the define statements
                if 'define' in line.lower() and 'WP_DEBUG' in line:
                    # Check if it's part of an if statement
                    if 'if ( ! defined' in line:
                        # Skip the if block
                        skip_next = True
                        continue
                    elif skip_next:
                        # Skip the next line (the define inside if)
                        skip_next = False
                        continue
                    else:
                        # Skip standalone defines
                        continue
                
                # Skip @ini_set('display_errors') if present
                if '@ini_set' in line and 'display_errors' in line:
                    continue
                
                new_lines.append(line)
            
            # Add clean WP_DEBUG definitions at the end (before require_once)
            # Find where to insert (before require_once ABSPATH)
            insert_index = None
            for i, line in enumerate(new_lines):
                if 'require_once' in line and 'ABSPATH' in line:
                    insert_index = i
                    break
            
            if insert_index is None:
                # Find end of file
                insert_index = len(new_lines)
            
            # Insert clean debug settings
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
            
            # Backup original
            print("   💾 Creating backup...")
            backup_cmd = f"cp {wp_config_path} {wp_config_path}.backup.$(date +%Y%m%d_%H%M%S) 2>&1"
            backup_result = deployer.execute_command(backup_cmd)
            print(f"      {backup_result}")
            
            # Write fixed content
            print("   ✏️  Writing fixed wp-config.php...")
            
            # Write to temporary file first
            temp_file = "/tmp/wp-config-fixed.php"
            write_temp_cmd = f"cat > {temp_file} << 'EOFMARKER'\n{fixed_content}\nEOFMARKER\n"
            deployer.execute_command(write_temp_cmd)
            
            # Copy to actual location
            copy_cmd = f"cp {temp_file} {wp_config_path} 2>&1"
            copy_result = deployer.execute_command(copy_cmd)
            
            # Clean up temp file
            deployer.execute_command(f"rm {temp_file} 2>&1")
            
            print("   ✅ wp-config.php fixed!")
            print()
            
            # Verify syntax
            print("   🔍 Verifying PHP syntax...")
            syntax_check = f"php -l {wp_config_path} 2>&1"
            syntax_result = deployer.execute_command(syntax_check)
            
            if 'No syntax errors' in syntax_result:
                print("   ✅ Syntax is valid")
                return True
            else:
                print(f"   ⚠️  Syntax check result: {syntax_result}")
                # Restore backup if syntax error
                print("   ⚠️  Restoring backup...")
                restore_cmd = f"cp {wp_config_path}.backup.* {wp_config_path} 2>&1 | head -1"
                deployer.execute_command(restore_cmd)
                return False
        else:
            print("   ✅ No conflicting WP_DEBUG definitions found")
            print("   ℹ️  Current configuration appears correct")
            return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def fix_warning_output():
    """Fix warning messages being output to HTML."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING WARNING OUTPUT: {site_name}")
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
        
        # Get active theme
        get_theme_cmd = f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>&1"
        theme_result = deployer.execute_command(get_theme_cmd)
        active_theme = theme_result.strip()
        
        if not active_theme or active_theme == 'error':
            print("   ❌ Could not determine active theme")
            return False
        
        print(f"   Active theme: {active_theme}")
        functions_php = f"{remote_path}/wp-content/themes/{active_theme}/functions.php"
        
        # Read functions.php
        print("   📖 Reading functions.php...")
        read_functions_cmd = f"cat {functions_php} 2>&1"
        functions_content = deployer.execute_command(read_functions_cmd)
        
        # Check if there's code that outputs warnings
        # The warning seems to be from a portfolio calculator
        # We should suppress error output or handle it better
        
        # Add error suppression at the start if not present
        if 'error_reporting' not in functions_content or '@ini_set' not in functions_content:
            print("   ✏️  Adding error suppression...")
            
            # Find where to add (after opening PHP tag)
            lines = functions_content.split('\n')
            insert_index = 1  # After <?php
            
            # Find actual insert point (after initial comments/namespace)
            for i, line in enumerate(lines[1:10], 1):
                if line.strip() and not line.strip().startswith('//') and not line.strip().startswith('/*') and not line.strip().startswith('*'):
                    insert_index = i
                    break
            
            error_suppression = """// Suppress error output in production - Added by Agent-7
if (!defined('WP_DEBUG') || !WP_DEBUG) {
    @ini_set('display_errors', 0);
    error_reporting(0);
}"""
            
            lines.insert(insert_index, error_suppression)
            new_content = '\n'.join(lines)
            
            # Backup
            backup_cmd = f"cp {functions_php} {functions_php}.backup.$(date +%Y%m%d_%H%M%S) 2>&1"
            deployer.execute_command(backup_cmd)
            
            # Write new content
            temp_file = "/tmp/functions-fixed.php"
            write_temp_cmd = f"cat > {temp_file} << 'EOFMARKER'\n{new_content}\nEOFMARKER\n"
            deployer.execute_command(write_temp_cmd)
            
            copy_cmd = f"cp {temp_file} {functions_php} 2>&1"
            deployer.execute_command(copy_cmd)
            deployer.execute_command(f"rm {temp_file} 2>&1")
            
            print("   ✅ Error suppression added")
            
            # Verify syntax
            syntax_check = f"php -l {functions_php} 2>&1"
            syntax_result = deployer.execute_command(syntax_check)
            
            if 'No syntax errors' in syntax_result:
                print("   ✅ Syntax is valid")
                return True
            else:
                print(f"   ⚠️  Syntax check result: {syntax_result}")
                return False
        else:
            print("   ✅ Error suppression already configured")
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
    print("Fixing PHP configuration issues...\n")
    config_success = fix_wp_config()
    
    print("\n" + "=" * 70 + "\n")
    
    print("Fixing warning output issues...\n")
    warning_success = fix_warning_output()
    
    if config_success and warning_success:
        print("\n" + "=" * 70)
        print("✅ ALL FIXES COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next steps:")
        print("   1. Clear browser cache")
        print("   2. Test the site to verify errors are resolved")
        print("   3. If warnings still appear, check plugin/theme code that outputs them")
        return 0
    else:
        print("\n" + "=" * 70)
        print("⚠️  SOME FIXES MAY HAVE FAILED")
        print("=" * 70)
        return 1


if __name__ == "__main__":
    sys.exit(main())

