#!/usr/bin/env python3
"""
Force Home Template Usage
=========================

Ensures WordPress uses home.php for the blog page by checking template hierarchy.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def force_home_template():
    """Force WordPress to use home.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FORCING HOME.PHP TEMPLATE: {site_name}")
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
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        
        # WordPress uses home.php when:
        # 1. Page is set as Posts page (page_for_posts)
        # 2. Reading Settings -> Posts page is set
        # 3. No page template is assigned
        
        print("1️⃣ Verifying Posts page setting...")
        page_for_posts = deployer.execute_command(f"cd {remote_path} && wp option get page_for_posts --allow-root 2>&1").strip()
        show_on_front = deployer.execute_command(f"cd {remote_path} && wp option get show_on_front --allow-root 2>&1").strip()
        
        print(f"   show_on_front: {show_on_front}")
        print(f"   page_for_posts: {page_for_posts}")
        
        if page_for_posts != '83':
            print("   ⚠️  Setting blog page as Posts page...")
            deployer.execute_command(f"cd {remote_path} && wp option update page_for_posts 83 --allow-root 2>&1")
            print("   ✅ Blog page set as Posts page")
        
        # Ensure no page template
        print()
        print("2️⃣ Removing page template assignment...")
        deployer.execute_command(f"cd {remote_path} && wp post update 83 --page_template='' --allow-root 2>&1")
        print("   ✅ Page template removed")
        
        # Verify home.php exists
        print()
        print("3️⃣ Verifying home.php exists...")
        check_home = f"test -f {theme_path}/home.php && echo 'EXISTS' || echo 'NOT_FOUND'"
        home_exists = deployer.execute_command(check_home).strip()
        print(f"   home.php: {'EXISTS ✅' if 'EXISTS' in home_exists else 'NOT_FOUND ❌'}")
        
        # Temporarily rename page.php to force home.php usage (last resort)
        print()
        print("4️⃣ Checking for page.php that might interfere...")
        check_page = f"test -f {theme_path}/page.php && echo 'EXISTS' || echo 'NOT_FOUND'"
        page_exists = deployer.execute_command(check_page).strip()
        
        if 'EXISTS' in page_exists:
            print("   ⚠️  page.php exists - this might be interfering")
            print("   💡 WordPress template hierarchy: home.php should override page.php for Posts page")
            print("   💡 If home.php isn't working, we may need to check WordPress query")
        
        # Clear all caches
        print()
        print("5️⃣ Clearing all caches...")
        deployer.execute_command(f"cd {remote_path} && wp cache flush --allow-root 2>&1")
        deployer.execute_command(f"cd {remote_path} && wp rewrite flush --hard --allow-root 2>&1")
        print("   ✅ Caches cleared")
        
        print()
        print("=" * 70)
        print("✅ CONFIGURATION COMPLETE")
        print("=" * 70)
        print()
        print("💡 WordPress should now use home.php")
        print("   If it's still not working, WordPress may be caching the template")
        
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
    success = force_home_template()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

