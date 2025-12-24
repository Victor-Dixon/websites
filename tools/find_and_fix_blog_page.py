#!/usr/bin/env python3
"""
Find and Fix Blog Page
=======================

Finds the blog page and configures it properly.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def find_and_fix_blog():
    """Find and fix blog page."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 FINDING AND FIXING BLOG PAGE: {site_name}")
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
        
        # Try multiple ways to find blog page
        print("1️⃣ Finding blog page...")
        
        # Method 1: By slug
        find1 = f"cd {remote_path} && wp post list --post_type=page --name=blog --format=ids --allow-root 2>&1"
        blog_id = deployer.execute_command(find1).strip()
        
        # Method 2: By title
        if not blog_id or not blog_id.isdigit():
            find2 = f"cd {remote_path} && wp post list --post_type=page --s='Blog' --format=ids --allow-root 2>&1"
            blog_id = deployer.execute_command(find2).strip()
        
        # Method 3: List all pages
        if not blog_id or not blog_id.isdigit():
            find3 = f"cd {remote_path} && wp post list --post_type=page --format=table --allow-root 2>&1"
            all_pages = deployer.execute_command(find3)
            print(f"   All pages:\n{all_pages[:500]}")
            # Extract ID from table if "blog" appears
            import re
            match = re.search(r'(\d+).*blog', all_pages, re.IGNORECASE)
            if match:
                blog_id = match.group(1)
        
        if blog_id and blog_id.isdigit():
            print(f"   ✅ Found blog page (ID: {blog_id})")
            
            # Remove page template
            print()
            print("2️⃣ Removing page template assignment...")
            remove_template = f"cd {remote_path} && wp post update {blog_id} --page_template='' --allow-root 2>&1"
            result = deployer.execute_command(remove_template)
            print(f"   Result: {result[:200] if result else 'Success'}")
            
            # Set as posts page
            print()
            print("3️⃣ Setting as Posts page...")
            set_posts = f"cd {remote_path} && wp option update page_for_posts {blog_id} --allow-root 2>&1"
            result = deployer.execute_command(set_posts)
            print(f"   Result: {result[:200] if result else 'Success'}")
            
        else:
            print("   ⚠️  Blog page not found - may need to create it")
            print("   💡 WordPress will use archive.php for /blog/ if Posts page is set")
        
        # Flush rewrite rules
        print()
        print("4️⃣ Flushing rewrite rules...")
        flush_cmd = f"cd {remote_path} && wp rewrite flush --hard --allow-root 2>&1"
        flush_result = deployer.execute_command(flush_cmd)
        print(f"   Result: {flush_result[:200] if flush_result else 'Success'}")
        
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
    success = find_and_fix_blog()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

