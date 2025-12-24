#!/usr/bin/env python3
"""
Check Blog Page Content Source
===============================

Checks what's actually being rendered on the blog page to understand why it's empty.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_content_source():
    """Check blog page content source."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 CHECKING BLOG PAGE CONTENT SOURCE: {site_name}")
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
        
        # Check if there's a page.php that might be overriding
        print("1️⃣ Checking for page.php template...")
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        check_page = f"test -f {theme_path}/page.php && echo 'EXISTS' || echo 'NOT_FOUND'"
        page_exists = deployer.execute_command(check_page)
        print(f"   page.php: {'EXISTS' if 'EXISTS' in page_exists else 'NOT_FOUND'}")
        
        # Check if blog page has content
        print()
        print("2️⃣ Checking blog page content...")
        get_content = f"cd {remote_path} && wp post get 83 --field=content --allow-root 2>&1"
        page_content = deployer.execute_command(get_content).strip()
        if page_content:
            print(f"   Page has content: {len(page_content)} characters")
            print(f"   Preview: {page_content[:200]}")
        else:
            print("   Page content is empty")
        
        # Check if blog page is set as Posts page
        print()
        print("3️⃣ Checking Posts page setting...")
        page_for_posts = deployer.execute_command(f"cd {remote_path} && wp option get page_for_posts --allow-root 2>&1").strip()
        print(f"   page_for_posts: {page_for_posts}")
        
        if page_for_posts == '83':
            print("   ✅ Blog page is set as Posts page - WordPress should use home.php")
        else:
            print("   ⚠️  Blog page is NOT set as Posts page")
        
        # Test query directly
        print()
        print("4️⃣ Testing WordPress query directly...")
        test_query = f"cd {remote_path} && wp post list --post_type=post --post_status=publish --format=count --allow-root 2>&1"
        post_count = deployer.execute_command(test_query).strip()
        print(f"   Published posts: {post_count}")
        
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
    success = check_content_source()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

