#!/usr/bin/env python3
"""
Set Blog Page as Posts Page
============================

Sets the Blog page as the Posts page in WordPress Reading settings.
This makes WordPress use archive.php instead of page templates.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def set_posts_page():
    """Set blog page as posts page."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"⚙️  SETTING BLOG AS POSTS PAGE: {site_name}")
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
        
        # Find blog page ID
        print("1️⃣ Finding blog page...")
        find_blog = f"cd {remote_path} && wp post list --post_type=page --name=blog --format=ids --allow-root 2>&1"
        blog_id = deployer.execute_command(find_blog).strip()
        
        if not blog_id:
            print("   ❌ Blog page not found")
            return False
        
        print(f"   ✅ Found blog page (ID: {blog_id})")
        print()
        
        # Set as posts page
        print("2️⃣ Setting blog page as Posts page...")
        set_posts = f"cd {remote_path} && wp option update page_for_posts {blog_id} --allow-root 2>&1"
        result = deployer.execute_command(set_posts)
        print(f"   Result: {result[:200] if result else 'Success'}")
        
        print()
        print("=" * 70)
        print("✅ BLOG PAGE SET AS POSTS PAGE")
        print("=" * 70)
        print()
        print("💡 WordPress will now use archive.php for:")
        print("   - /blog/ (main blog archive)")
        print("   - /blog/page/2/ (pagination)")
        print()
        print("⚠️  This means archive.php will be used instead of page template")
        
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
    success = set_posts_page()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

