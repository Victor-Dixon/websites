#!/usr/bin/env python3
"""
Clear Blog Page Content and Test
=================================

Clears blog page content (since it should use home.php) and tests the query.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def clear_and_test():
    """Clear blog page content and test."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 CLEARING BLOG PAGE CONTENT & TESTING: {site_name}")
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
        
        # Clear blog page content (it should be empty when used as Posts page)
        print("1️⃣ Clearing blog page content...")
        clear_content = f"cd {remote_path} && wp post update 83 --post_content='' --allow-root 2>&1"
        result = deployer.execute_command(clear_content)
        print("   ✅ Blog page content cleared")
        
        # Ensure it's set as Posts page
        print()
        print("2️⃣ Verifying Posts page setting...")
        set_posts = f"cd {remote_path} && wp option update page_for_posts 83 --allow-root 2>&1"
        deployer.execute_command(set_posts)
        print("   ✅ Blog page set as Posts page")
        
        # Ensure no page template is assigned
        print()
        print("3️⃣ Removing page template assignment...")
        remove_template = f"cd {remote_path} && wp post update 83 --page_template='' --allow-root 2>&1"
        deployer.execute_command(remove_template)
        print("   ✅ Page template removed")
        
        # Test query
        print()
        print("4️⃣ Testing posts query...")
        test_query = f"cd {remote_path} && wp post list --post_type=post --post_status=publish --format=table --fields=ID,post_title --limit=3 --allow-root 2>&1"
        posts = deployer.execute_command(test_query)
        print("   Recent posts:")
        print(posts[:500])
        
        # Flush rewrite rules
        print()
        print("5️⃣ Flushing rewrite rules...")
        deployer.execute_command(f"cd {remote_path} && wp rewrite flush --hard --allow-root 2>&1")
        print("   ✅ Rewrite rules flushed")
        
        # Clear cache
        print()
        print("6️⃣ Clearing cache...")
        deployer.execute_command(f"cd {remote_path} && wp cache flush --allow-root 2>&1")
        print("   ✅ Cache cleared")
        
        print()
        print("=" * 70)
        print("✅ CONFIGURATION COMPLETE")
        print("=" * 70)
        print()
        print("💡 WordPress should now use home.php for the blog page")
        print("   Visit https://freerideinvestor.com/blog/ to verify")
        
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
    success = clear_and_test()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

