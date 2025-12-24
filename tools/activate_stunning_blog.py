#!/usr/bin/env python3
"""
Activate Stunning Blog Template
================================

Activates the stunning blog template on the blog page.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def activate_stunning_blog():
    """Activate the stunning blog template."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🎨 ACTIVATING STUNNING BLOG TEMPLATE: {site_name}")
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
        
        # Find blog page by slug or title
        print("1️⃣ Finding blog page...")
        find_blog = f"cd {remote_path} && wp post list --post_type=page --name=blog --format=ids --allow-root 2>&1"
        blog_id = deployer.execute_command(find_blog).strip()
        
        if not blog_id:
            # Try by title
            find_blog = f"cd {remote_path} && wp post list --post_type=page --title='Blog' --format=ids --allow-root 2>&1"
            blog_id = deployer.execute_command(find_blog).strip()
        
        if not blog_id:
            print("   ⚠️  Blog page not found, creating...")
            create_blog = f"""cd {remote_path} && wp post create --post_type=page --post_title="Blog" --post_name="blog" --post_status=publish --page_template=page-templates/page-blog-stunning.php --allow-root 2>&1"""
            result = deployer.execute_command(create_blog)
            print(f"   Create result: {result[:200]}")
            # Extract ID
            id_match = re.search(r'ID (\d+)', result)
            if id_match:
                blog_id = id_match.group(1)
            else:
                blog_id = deployer.execute_command(f"cd {remote_path} && wp post list --post_type=page --name=blog --format=ids --allow-root 2>&1").strip()
        else:
            print(f"   ✅ Found blog page (ID: {blog_id})")
        
        if not blog_id:
            print("   ❌ Could not create or find blog page")
            return False
        
        print()
        
        # Update template
        print("2️⃣ Updating blog page template...")
        update_template = f"cd {remote_path} && wp post update {blog_id} --page_template=page-templates/page-blog-stunning.php --allow-root 2>&1"
        update_result = deployer.execute_command(update_template)
        print(f"   Result: {update_result[:200] if update_result else 'Success'}")
        
        # Clear cache
        print()
        print("3️⃣ Clearing cache...")
        deployer.execute_command(f"cd {remote_path} && wp cache flush --allow-root 2>&1")
        
        print()
        print("=" * 70)
        print("✅ STUNNING BLOG TEMPLATE ACTIVATED!")
        print("=" * 70)
        print()
        print(f"💡 Visit https://{site_name}/blog/ to see the new design!")
        
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
    success = activate_stunning_blog()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

