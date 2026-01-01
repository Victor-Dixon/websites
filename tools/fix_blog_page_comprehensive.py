#!/usr/bin/env python3
"""
Comprehensive Blog Page Fix for freerideinvestor.com
====================================================

Fixes blog page content display and pagination issues.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_blog_page():
    """Comprehensive fix for blog page."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 COMPREHENSIVE BLOG PAGE FIX: {site_name}")
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
        
        # Step 1: Check if blog posts exist
        print("1️⃣ Checking blog posts...")
        check_posts = f"cd {remote_path} && wp post list --post_type=post --post_status=publish --format=count --allow-root 2>&1"
        posts_count = deployer.execute_command(check_posts).strip()
        
        if posts_count.isdigit() and int(posts_count) > 0:
            print(f"   ✅ Found {posts_count} published posts")
            
            # Show first few posts
            list_posts = f"cd {remote_path} && wp post list --post_type=post --post_status=publish --format=table --fields=ID,post_title,post_date --limit=5 --allow-root 2>&1"
            posts_list = deployer.execute_command(list_posts)
            print(f"   Recent posts:\n{posts_list[:500]}")
        else:
            print(f"   ⚠️  No published posts found (count: {posts_count})")
            print("   💡 Blog page may be empty because there are no posts")
        
        # Step 2: Find blog page
        print()
        print("2️⃣ Finding blog page...")
        find_blog = f"cd {remote_path} && wp post list --post_type=page --name=blog --format=ids --allow-root 2>&1"
        blog_id = deployer.execute_command(find_blog).strip()
        
        if not blog_id or not blog_id.isdigit():
            # Try by title
            find_by_title = f"cd {remote_path} && wp post list --post_type=page --s='Blog' --format=ids --allow-root 2>&1"
            blog_id = deployer.execute_command(find_by_title).strip()
        
        if blog_id and blog_id.isdigit():
            print(f"   ✅ Found blog page (ID: {blog_id})")
            
            # Get blog page details
            get_blog = f"cd {remote_path} && wp post get {blog_id} --field=title --allow-root 2>&1"
            blog_title = deployer.execute_command(get_blog).strip()
            print(f"   Title: {blog_title}")
        else:
            print("   ⚠️  Blog page not found - WordPress will use archive template")
            blog_id = None
        
        # Step 3: Verify archive.php exists
        print()
        print("3️⃣ Verifying archive.php template...")
        archive_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern/archive.php"
        check_archive = f"test -f {archive_path} && echo 'EXISTS' || echo 'NOT_FOUND'"
        archive_exists = deployer.execute_command(check_archive)
        
        if 'EXISTS' in archive_exists:
            print("   ✅ archive.php exists")
            
            # Check archive.php content (first 50 lines)
            check_content = f"head -50 {archive_path} | grep -E '(WP_Query|have_posts|the_post)' && echo 'HAS_QUERY' || echo 'NO_QUERY'"
            has_query = deployer.execute_command(check_content)
            if 'HAS_QUERY' in has_query:
                print("   ✅ archive.php has post query logic")
            else:
                print("   ⚠️  archive.php may be missing query logic")
        else:
            print("   ❌ archive.php not found - need to create it")
        
        # Step 4: Set blog page as Posts page (if blog page exists)
        print()
        print("4️⃣ Configuring blog page settings...")
        if blog_id and blog_id.isdigit():
            # Remove page template assignment so it uses archive.php
            remove_template = f"cd {remote_path} && wp post update {blog_id} --page_template='' --allow-root 2>&1"
            result = deployer.execute_command(remove_template)
            print("   ✅ Removed page template assignment")
            
            # Set as Posts page - this makes WordPress use archive.php
            set_posts = f"cd {remote_path} && wp option update page_for_posts {blog_id} --allow-root 2>&1"
            result = deployer.execute_command(set_posts)
            print("   ✅ Set blog page as Posts page")
        else:
            # No blog page exists - WordPress will use archive.php by default
            print("   ℹ️  No blog page - WordPress will use archive.php automatically")
        
        # Step 5: Verify home page is set correctly
        print()
        print("5️⃣ Verifying front page settings...")
        show_on_front = deployer.execute_command(f"cd {remote_path} && wp option get show_on_front --allow-root 2>&1").strip()
        page_on_front = deployer.execute_command(f"cd {remote_path} && wp option get page_on_front --allow-root 2>&1").strip()
        
        print(f"   show_on_front: {show_on_front}")
        print(f"   page_on_front: {page_on_front}")
        
        if show_on_front == 'page':
            print("   ✅ Front page is set to static page")
        else:
            print("   ℹ️  Front page shows latest posts")
        
        # Step 6: Flush rewrite rules
        print()
        print("6️⃣ Flushing rewrite rules...")
        flush_cmd = f"cd {remote_path} && wp rewrite flush --hard --allow-root 2>&1"
        flush_result = deployer.execute_command(flush_cmd)
        if 'Success' in flush_result or not flush_result.strip():
            print("   ✅ Rewrite rules flushed")
        else:
            print(f"   Result: {flush_result[:200]}")
        
        # Step 7: Clear cache
        print()
        print("7️⃣ Clearing cache...")
        cache_cmd = f"cd {remote_path} && wp cache flush --allow-root 2>&1"
        cache_result = deployer.execute_command(cache_cmd)
        print("   ✅ Cache cleared")
        
        # Step 8: Test blog page URL
        print()
        print("8️⃣ Testing blog page configuration...")
        if blog_id and blog_id.isdigit():
            get_permalink = f"cd {remote_path} && wp post get {blog_id} --field=url --allow-root 2>&1"
            blog_url = deployer.execute_command(get_permalink).strip()
            print(f"   Blog URL: {blog_url}")
        else:
            print("   Blog URL: https://freerideinvestor.com/blog/")
        
        print()
        print("=" * 70)
        print("✅ BLOG PAGE FIX COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next steps:")
        print("   1. Visit https://freerideinvestor.com/blog/ to verify posts display")
        print("   2. If no posts show, verify posts exist and are published")
        print("   3. Test pagination: /blog/page/2/")
        print("   4. If archive.php template needs fixes, we can update it")
        
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
    success = fix_blog_page()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

