#!/usr/bin/env python3
"""
Activate Stunning Front Page Template
======================================

Activates the stunning front page template via WP-CLI.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def activate_stunning_front_page():
    """Activate the stunning front page template."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🎨 ACTIVATING STUNNING FRONT PAGE: {site_name}")
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
        
        # Check if template exists
        print("1️⃣ Verifying template file exists...")
        template_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern/page-templates/page-front-page-stunning.php"
        check_template = f"test -f {template_path} && echo 'EXISTS' || echo 'NOT_FOUND'"
        result = deployer.execute_command(check_template)
        
        if 'EXISTS' in result:
            print("   ✅ Template file exists")
        else:
            print("   ❌ Template file not found")
            return False
        
        print()
        
        # Create or update front page with template
        print("2️⃣ Creating/updating front page with stunning template...")
        
        # Check if front page already exists
        check_page = f"cd {remote_path} && wp post list --post_type=page --name=home --format=ids --allow-root 2>&1"
        page_ids = deployer.execute_command(check_page).strip()
        
        if page_ids:
            print(f"   Found existing front page (ID: {page_ids})")
            # Update existing page
            update_cmd = f"cd {remote_path} && wp post update {page_ids} --post_status=publish --page_template=page-templates/page-front-page-stunning.php --allow-root 2>&1"
            update_result = deployer.execute_command(update_cmd)
            print(f"   Update result: {update_result[:200]}")
        else:
            # Create new page
            create_cmd = f"""cd {remote_path} && wp post create --post_type=page --post_title="Home" --post_name="home" --post_status=publish --page_template=page-templates/page-front-page-stunning.php --allow-root 2>&1"""
            create_result = deployer.execute_command(create_cmd)
            print(f"   Create result: {create_result}")
            # Extract page ID - try multiple patterns
            import re
            page_id_match = re.search(r'ID (\d+)', create_result)
            if not page_id_match:
                page_id_match = re.search(r'Created post (\d+)', create_result, re.IGNORECASE)
            if page_id_match:
                page_ids = page_id_match.group(1)
                print(f"   ✅ Extracted page ID: {page_ids}")
            else:
                # Try to find by name
                find_cmd = f"cd {remote_path} && wp post list --post_type=page --name=home --format=ids --allow-root 2>&1"
                page_ids = deployer.execute_command(find_cmd).strip()
                if page_ids:
                    print(f"   ✅ Found page ID by name: {page_ids}")
        
        if not page_ids:
            print("   ⚠️  Could not determine page ID")
            print("   💡 Trying to find by name 'home'...")
            find_cmd = f"cd {remote_path} && wp post list --post_type=page --name=home --format=ids --allow-root 2>&1"
            page_ids = deployer.execute_command(find_cmd).strip()
            if page_ids:
                print(f"   ✅ Found page ID: {page_ids}")
            else:
                print("   ❌ Could not find page")
                return False
        
        print()
        
        # Set as front page in reading settings
        print("3️⃣ Setting as front page...")
        set_front_page = f"cd {remote_path} && wp option update show_on_front page --allow-root 2>&1 && wp option update page_on_front {page_ids} --allow-root 2>&1"
        set_result = deployer.execute_command(set_front_page)
        print(f"   Result: {set_result[:200] if set_result else 'Success'}")
        
        print()
        
        print("=" * 70)
        print("✅ STUNNING FRONT PAGE ACTIVATED!")
        print("=" * 70)
        print()
        print("💡 Visit the site to see the new stunning design!")
        print(f"   URL: https://{site_name}")
        
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
    success = activate_stunning_front_page()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

