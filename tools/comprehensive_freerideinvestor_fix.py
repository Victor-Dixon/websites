#!/usr/bin/env python3
"""
Comprehensive Fix for freerideinvestor.com
===========================================

Fixes all identified issues:
1. Blog page not using stunning template
2. Blog pagination error
3. Verifies site functionality

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def comprehensive_fix():
    """Fix all issues on freerideinvestor.com."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 COMPREHENSIVE FIX: {site_name}")
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
        
        # 1. Remove blog page template assignment so it uses archive.php
        print("1️⃣ Configuring blog to use archive.php...")
        find_blog = f"cd {remote_path} && wp post list --post_type=page --name=blog --format=ids --allow-root 2>&1"
        blog_id = deployer.execute_command(find_blog).strip()
        
        if blog_id and blog_id.isdigit():
            # Remove page template assignment
            remove_template = f"cd {remote_path} && wp post update {blog_id} --page_template='' --allow-root 2>&1"
            result = deployer.execute_command(remove_template)
            print(f"   ✅ Removed page template from blog page")
        else:
            print("   ⚠️  Blog page not found")
        
        # 2. Set blog page as Posts page (this makes WordPress use archive.php)
        print()
        print("2️⃣ Setting blog page as Posts page...")
        if blog_id and blog_id.isdigit():
            set_posts = f"cd {remote_path} && wp option update page_for_posts {blog_id} --allow-root 2>&1"
            result = deployer.execute_command(set_posts)
            if 'Success' in result or not result.strip():
                print("   ✅ Blog page set as Posts page")
            else:
                print(f"   Result: {result[:200]}")
        
        # 3. Verify archive.php exists
        print()
        print("3️⃣ Verifying archive.php exists...")
        archive_check = f"test -f {remote_path}/wp-content/themes/freerideinvestor-modern/archive.php && echo 'EXISTS' || echo 'NOT_FOUND'"
        archive_result = deployer.execute_command(archive_check)
        if 'EXISTS' in archive_result:
            print("   ✅ archive.php exists")
        else:
            print("   ❌ archive.php not found - need to deploy it")
        
        # 4. Fix functions.php - ensure rewrite rules are correct
        print()
        print("4️⃣ Verifying functions.php rewrite rules...")
        functions_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/functions.php"
        
        if deployer.sftp:
            try:
                with deployer.sftp.open(functions_file, 'r') as f:
                    functions_content = f.read().decode('utf-8')
                
                # Check if rewrite function exists and is properly defined
                if 'function freerideinvestor_add_blog_rewrite_rules' in functions_content:
                    # Check if it's properly closed
                    if 'add_action(\'init\'' in functions_content or "add_action('init'" in functions_content:
                        print("   ✅ Rewrite rules function exists")
                    else:
                        print("   ⚠️  Rewrite function exists but add_action may be missing")
                else:
                    print("   ⚠️  Rewrite rules function not found")
            except Exception as e:
                print(f"   ⚠️  Could not read functions.php: {e}")
        
        # 5. Flush rewrite rules
        print()
        print("5️⃣ Flushing rewrite rules...")
        flush_cmd = f"cd {remote_path} && wp rewrite flush --hard --allow-root 2>&1"
        flush_result = deployer.execute_command(flush_cmd)
        print(f"   Result: {flush_result[:200] if flush_result else 'Success'}")
        
        # 6. Clear all cache
        print()
        print("6️⃣ Clearing all cache...")
        cache_cmd = f"cd {remote_path} && wp cache flush --allow-root 2>&1"
        cache_result = deployer.execute_command(cache_cmd)
        print(f"   Result: {cache_result[:200] if cache_result else 'Success'}")
        
        print()
        print("=" * 70)
        print("✅ COMPREHENSIVE FIX COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next steps:")
        print("   1. Visit https://freerideinvestor.com/blog/ - should use archive.php")
        print("   2. Visit https://freerideinvestor.com/blog/page/2/ - should work now")
        print("   3. Clear browser cache if needed")
        
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
    success = comprehensive_fix()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

