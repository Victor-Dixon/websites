#!/usr/bin/env python3
"""
Verify SouthwestSecret Theme Activation
========================================

Checks if the correct theme is activated and verifies theme files.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def verify_theme():
    """Verify theme is activated correctly."""
    site_name = "southwestsecret.com"
    
    print("=" * 70)
    print(f"🔍 VERIFYING THEME: {site_name}")
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
        
        # Check active theme via WP-CLI
        print("🔍 Checking active theme...")
        check_theme_cmd = f"cd {remote_path} && wp theme list --status=active --allow-root 2>&1"
        theme_result = deployer.execute_command(check_theme_cmd)
        
        print(f"Active theme status:")
        print(theme_result)
        print()
        
        # Check if southwestsecret theme files exist
        theme_path = f"{remote_path}/wp-content/themes/southwestsecret"
        print(f"🔍 Checking theme files at {theme_path}...")
        
        check_files_cmd = f"ls -la {theme_path}/ 2>&1 | head -20"
        files_result = deployer.execute_command(check_files_cmd)
        print(files_result)
        print()
        
        # Check index.php content
        print("🔍 Checking index.php content...")
        check_index_cmd = f"head -30 {theme_path}/index.php 2>&1"
        index_result = deployer.execute_command(check_index_cmd)
        print(index_result)
        print()
        
        # Check if theme is active
        if 'southwestsecret' in theme_result.lower() and 'active' in theme_result.lower():
            print("✅ southwestsecret theme is active")
            
            if 'Chopped & Screwed DJ' in index_result or 'SOUTHWEST' in index_result:
                print("✅ Theme files contain correct content")
                print()
                print("💡 If site still shows wrong theme, try:")
                print("   1. Clear WordPress cache")
                print("   2. Clear browser cache")
                print("   3. Check if there's a caching plugin active")
                return True
            else:
                print("⚠️  Theme files may not have correct content")
                return False
        else:
            print("❌ southwestsecret theme is NOT active")
            print()
            print("💡 Activating theme...")
            activate_cmd = f"cd {remote_path} && wp theme activate southwestsecret --allow-root 2>&1"
            activate_result = deployer.execute_command(activate_cmd)
            print(activate_result)
            
            if 'Success' in activate_result or 'activated' in activate_result.lower():
                print("✅ Theme activated successfully!")
                return True
            else:
                print("⚠️  Theme activation may have failed")
                return False
            
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = verify_theme()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())


