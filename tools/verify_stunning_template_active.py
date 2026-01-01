#!/usr/bin/env python3
"""
Verify Stunning Template is Active
===================================

Checks if the stunning template is properly set and clears cache.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def verify_and_fix():
    """Verify template is active and fix if needed."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 VERIFYING STUNNING TEMPLATE: {site_name}")
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
        
        # Check front page settings
        print("1️⃣ Checking front page settings...")
        check_front = f"cd {remote_path} && wp option get page_on_front --allow-root 2>&1"
        front_page_id = deployer.execute_command(check_front).strip()
        print(f"   Front page ID: {front_page_id}")
        
        if front_page_id and front_page_id.isdigit():
            # Check template assigned to this page
            check_template = f"cd {remote_path} && wp post get {front_page_id} --field=page_template --allow-root 2>&1"
            template = deployer.execute_command(check_template).strip()
            print(f"   Current template: {template}")
            
            if 'stunning' not in template.lower():
                print("   ⚠️  Template is not the stunning template!")
                print("   🔧 Updating template...")
                update_template = f"cd {remote_path} && wp post update {front_page_id} --page_template=page-templates/page-front-page-stunning.php --allow-root 2>&1"
                result = deployer.execute_command(update_template)
                print(f"   Result: {result[:200]}")
            
            # Check page content
            check_content = f"cd {remote_path} && wp post get {front_page_id} --field=post_content --allow-root 2>&1"
            content = deployer.execute_command(check_content).strip()
            print(f"   Page has content: {'Yes' if content else 'No'}")
        else:
            print("   ⚠️  No front page set!")
        
        print()
        
        # Clear WordPress cache
        print("2️⃣ Clearing WordPress cache...")
        clear_cache = f"cd {remote_path} && wp cache flush --allow-root 2>&1"
        cache_result = deployer.execute_command(clear_cache)
        print(f"   Cache clear result: {cache_result[:100] if cache_result else 'Success'}")
        
        # Clear object cache if available
        clear_obj_cache = f"cd {remote_path} && wp cache delete --allow-root 2>&1"
        deployer.execute_command(clear_obj_cache)
        
        print()
        
        print("=" * 70)
        print("✅ VERIFICATION COMPLETE")
        print("=" * 70)
        print()
        print("💡 If the template still doesn't show:")
        print("   1. Clear browser cache (Ctrl+Shift+Delete)")
        print("   2. Try incognito/private browsing mode")
        print("   3. Check if caching plugin is active")
        print("   4. Verify template file exists on server")
        
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
    success = verify_and_fix()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

