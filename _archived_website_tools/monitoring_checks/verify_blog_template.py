#!/usr/bin/env python3
"""
Verify Blog Template Assignment
================================

Checks which template the blog page is using.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def verify_blog_template():
    """Verify blog template assignment."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 VERIFYING BLOG TEMPLATE: {site_name}")
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
        
        # Find blog page
        print("1️⃣ Finding blog page...")
        find_blog = f"cd {remote_path} && wp post list --post_type=page --name=blog --format=ids --allow-root 2>&1"
        blog_id = deployer.execute_command(find_blog).strip()
        
        if blog_id:
            print(f"   Blog page ID: {blog_id}")
            
            # Get page meta to check template
            print("2️⃣ Checking page template assignment...")
            get_meta = f"cd {remote_path} && wp post meta get {blog_id} _wp_page_template --allow-root 2>&1"
            template_meta = deployer.execute_command(get_meta).strip()
            print(f"   Template meta: {template_meta}")
            
            # Get full page data
            print("3️⃣ Getting full page data...")
            get_page = f"cd {remote_path} && wp post get {blog_id} --field=post_name --allow-root 2>&1"
            page_name = deployer.execute_command(get_page).strip()
            print(f"   Page name: {page_name}")
            
            # Check if it's set as posts page
            print("4️⃣ Checking reading settings...")
            posts_page = f"cd {remote_path} && wp option get page_for_posts --allow-root 2>&1"
            posts_page_id = deployer.execute_command(posts_page).strip()
            print(f"   Posts page ID: {posts_page_id}")
            
            if posts_page_id == blog_id:
                print("   ⚠️  Blog page is set as 'Posts page' in Settings → Reading")
                print("   💡 When a page is set as Posts page, WordPress uses archive.php or index.php")
                print("   💡 Solution: Unset it as Posts page OR update archive.php/index.php")
            else:
                print("   ✅ Not set as Posts page - should use page template")
            
            # Verify template file exists
            print("5️⃣ Verifying template file exists...")
            template_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/page-templates/page-blog-stunning.php"
            check_file = f"test -f {template_file} && echo 'EXISTS' || echo 'NOT_FOUND'"
            file_exists = deployer.execute_command(check_file)
            print(f"   Template file: {'✅ EXISTS' if 'EXISTS' in file_exists else '❌ NOT FOUND'}")
            
        else:
            print("   ❌ Blog page not found")
        
        print()
        print("=" * 70)
        print("✅ VERIFICATION COMPLETE")
        print("=" * 70)
        
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
    success = verify_blog_template()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

