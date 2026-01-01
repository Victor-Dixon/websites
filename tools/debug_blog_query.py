#!/usr/bin/env python3
"""
Debug Blog Query Issue
======================

Debug why blog posts aren't showing despite having 14 published posts.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def debug_query():
    """Debug blog query issue."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 DEBUGGING BLOG QUERY: {site_name}")
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
        
        # Check which template WordPress is using
        print("1️⃣ Checking template hierarchy...")
        check_home = f"test -f {remote_path}/wp-content/themes/freerideinvestor-modern/home.php && echo 'EXISTS' || echo 'NOT_FOUND'"
        home_exists = deployer.execute_command(check_home)
        print(f"   home.php: {'EXISTS' if 'EXISTS' in home_exists else 'NOT_FOUND'}")
        
        check_index = f"test -f {remote_path}/wp-content/themes/freerideinvestor-modern/index.php && echo 'EXISTS' || echo 'NOT_FOUND'"
        index_exists = deployer.execute_command(check_index)
        print(f"   index.php: {'EXISTS' if 'EXISTS' in index_exists else 'NOT_FOUND'}")
        
        # Read first 100 lines of home.php to verify content
        if 'EXISTS' in home_exists:
            print()
            print("2️⃣ Reading home.php content (first 100 lines)...")
            read_home = f"head -100 {remote_path}/wp-content/themes/freerideinvestor-modern/home.php"
            home_content = deployer.execute_command(read_home)
            print(home_content)
            
            # Check for have_posts
            if 'have_posts' in home_content:
                print("   ✅ home.php has have_posts()")
            else:
                print("   ❌ home.php missing have_posts()")
        
        # Test WordPress query directly
        print()
        print("3️⃣ Testing WordPress query for posts...")
        test_query = f"cd {remote_path} && wp post list --post_type=post --post_status=publish --format=ids --limit=5 --allow-root 2>&1"
        post_ids = deployer.execute_command(test_query).strip()
        print(f"   Post IDs: {post_ids}")
        
        # Check posts per page setting
        print()
        print("4️⃣ Checking WordPress reading settings...")
        posts_per_page = deployer.execute_command(f"cd {remote_path} && wp option get posts_per_page --allow-root 2>&1").strip()
        page_for_posts = deployer.execute_command(f"cd {remote_path} && wp option get page_for_posts --allow-root 2>&1").strip()
        show_on_front = deployer.execute_command(f"cd {remote_path} && wp option get show_on_front --allow-root 2>&1").strip()
        
        print(f"   posts_per_page: {posts_per_page}")
        print(f"   page_for_posts: {page_for_posts}")
        print(f"   show_on_front: {show_on_front}")
        
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
    success = debug_query()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

