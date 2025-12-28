#!/usr/bin/env python3
"""
Deploy freerideinvestor.com Menu Navigation Fix (Theme-Styled Version)
====================================================================

Deploys the theme-styled menu navigation fix to freerideinvestor.com.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "freerideinvestor.com"
THEME_NAME = "freerideinvestor-modern"

def deploy_menu_fix():
    """Deploy the theme-styled menu navigation fix."""
    print("=" * 60)
    print(f"Deploying Menu Navigation Fix to {SITE_NAME}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    if SITE_NAME not in site_configs:
        print("❌ Site config not found")
        return False
    
    deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    if not deployer.connect():
        print("❌ Could not connect to server")
        return False
    
    try:
        project_root = Path(__file__).parent.parent
        menu_fix_file = project_root / "docs" / "freerideinvestor" / "freerideinvestor_menu_fix_DEPLOY.php"
        
        if not menu_fix_file.exists():
            print(f"❌ Menu fix file not found: {menu_fix_file}")
            return False
        
        # Read the menu fix code
        menu_fix_code = menu_fix_file.read_text(encoding="utf-8")
        
        remote_base = deployer.remote_path or f"/home/u996867598/domains/{SITE_NAME}/public_html"
        if not remote_base.startswith('/'):
            username = site_configs[SITE_NAME].get('username') or site_configs[SITE_NAME].get('sftp', {}).get('username', '')
            if username:
                remote_base = f"/home/{username}/{remote_base}"
        
        theme_path_remote = f"{remote_base}/wp-content/themes/{THEME_NAME}"
        functions_file_remote = f"{theme_path_remote}/functions.php"
        
        # Read current functions.php
        print("\n📄 Reading current functions.php...")
        try:
            command = f"cat {functions_file_remote}"
            current_functions = deployer.execute_command(command)
            if not current_functions:
                print("   ⚠️  Could not read functions.php, will append")
                current_functions = ""
        except Exception as e:
            print(f"   ⚠️  Error reading functions.php: {e}")
            current_functions = ""
        
        # Check if menu fix already exists
        if "freerideinvestor_menu_navigation" in current_functions or "main-nav" in current_functions:
            print("   ⚠️  Menu fix may already be present in functions.php")
            print("   💡 Consider reviewing and updating if needed")
        
        # Append menu fix to functions.php
        print("\n📤 Appending menu fix to functions.php...")
        updated_functions = current_functions
        if not updated_functions.endswith("\n"):
            updated_functions += "\n"
        updated_functions += "\n" + menu_fix_code + "\n"
        
        # Write updated functions.php to temp file and deploy
        import tempfile
        with tempfile.NamedTemporaryFile(mode='w', suffix='.php', delete=False, encoding='utf-8') as tmp_file:
            tmp_file.write(updated_functions)
            tmp_path = Path(tmp_file.name)
        
        try:
            if deployer.deploy_file(tmp_path, functions_file_remote):
                print("   ✅ Menu fix deployed successfully")
            else:
                print("   ❌ Failed to deploy menu fix")
                return False
        finally:
            # Clean up temp file
            try:
                tmp_path.unlink()
            except:
                pass
        
        # Clear WordPress cache
        print("\n🧹 Clearing WordPress cache...")
        try:
            cache_clear = deployer.execute_command(f"wp cache flush --path={remote_base} 2>/dev/null || true")
            print("   ✅ Cache clear attempted")
        except Exception as e:
            print(f"   ⚠️  Cache clear may have failed: {e}")
        
        print("\n✅ Deployment complete!")
        print("\n📋 Next Steps:")
        print("   1. Test menu on desktop and mobile")
        print("   2. Verify menu styling matches theme")
        print("   3. Test mobile menu toggle functionality")
        print("   4. Clear browser cache (Ctrl+F5) if needed")
        
        return True
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    success = deploy_menu_fix()
    sys.exit(0 if success else 1)

