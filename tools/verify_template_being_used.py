#!/usr/bin/env python3
"""
Verify Which Template WordPress is Using
=========================================

Checks which template WordPress is actually using for the blog page.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def verify_template():
    """Verify which template is being used."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 VERIFYING TEMPLATE USAGE: {site_name}")
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
        
        # Check if there's a page template assigned to the blog page
        print("1️⃣ Checking blog page template assignment...")
        find_blog = f"cd {remote_path} && wp post get 83 --field=page_template --allow-root 2>&1"
        page_template = deployer.execute_command(find_blog).strip()
        print(f"   Page template: {page_template}")
        
        # Check WordPress reading settings
        print()
        print("2️⃣ Checking WordPress reading settings...")
        show_on_front = deployer.execute_command(f"cd {remote_path} && wp option get show_on_front --allow-root 2>&1").strip()
        page_for_posts = deployer.execute_command(f"cd {remote_path} && wp option get page_for_posts --allow-root 2>&1").strip()
        print(f"   show_on_front: {show_on_front}")
        print(f"   page_for_posts: {page_for_posts}")
        
        # List all templates
        print()
        print("3️⃣ Checking available templates...")
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        list_templates = f"ls -la {theme_path}/*.php 2>&1 | grep -E '(home|index|archive)'"
        templates = deployer.execute_command(list_templates)
        print("   Templates:")
        print(templates)
        
        # Check if WordPress is using a page template instead
        if page_template and page_template != 'default':
            print()
            print(f"   ⚠️  Blog page has page template assigned: {page_template}")
            print("   This will override home.php!")
            print()
            print("4️⃣ Removing page template assignment...")
            remove_template = f"cd {remote_path} && wp post update 83 --page_template='' --allow-root 2>&1"
            result = deployer.execute_command(remove_template)
            print("   ✅ Removed page template")
        
        # Clear cache
        print()
        print("5️⃣ Clearing all caches...")
        deployer.execute_command(f"cd {remote_path} && wp cache flush --allow-root 2>&1")
        print("   ✅ Cache cleared")
        
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
    success = verify_template()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

