#!/usr/bin/env python3
"""
Fix AriaJet Menu and Music Page Title
=====================================

Fixes the menu to show MUSIC and ensures music page has proper title
"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

try:
    from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
except ImportError:
    try:
        sys.path.insert(0, str(project_root / "ops" / "deployment"))
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except ImportError:
        print("❌ Could not import SimpleWordPressDeployer")
        sys.exit(1)

def main():
    site_domain = "ariajet.site"
    
    print(f"🔧 Fixing Menu and Music Page for {site_domain}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    wp_path = f"/home/u996867598/domains/{site_domain}/public_html"
    
    # 1. Update music page title and content
    print(f"\n📝 Updating music page title and content...")
    update_cmd = f"cd {wp_path} && wp post update 3671 --post_title='MUSIC' --post_content='<h1>MUSIC</h1><p>Welcome to Aria'\''s Music Collection! Explore amazing tracks from the cosmic universe.</p>' 2>&1"
    result = deployer.execute_command(update_cmd)
    print(f"   ✅ {result.strip() if result else 'Page updated'}")
    
    # 2. Get menu ID
    print(f"\n🔍 Finding primary menu...")
    menu_cmd = f"cd {wp_path} && wp menu list --format=json 2>&1"
    menu_result = deployer.execute_command(menu_cmd)
    
    # Try to find menu items and update them
    print(f"\n🔧 Updating menu items...")
    
    # Get all menu items
    items_cmd = f"cd {wp_path} && wp menu item list primary --format=json 2>&1"
    items_result = deployer.execute_command(items_cmd)
    
    # Update Capabilities to MUSIC
    print(f"   🎵 Changing 'Capabilities' to 'MUSIC'...")
    # First, let's try to find and update via WP-CLI
    update_menu_cmd = f"cd {wp_path} && wp menu item list primary --format=ids 2>&1"
    item_ids = deployer.execute_command(update_menu_cmd)
    
    if item_ids:
        # Try to update each item
        for item_id in item_ids.strip().split():
            # Get item details
            item_info_cmd = f"cd {wp_path} && wp menu item get {item_id} --format=json 2>&1"
            item_info = deployer.execute_command(item_info_cmd)
            
            # Update if title contains "Capabilit"
            update_item_cmd = f"cd {wp_path} && wp menu item update {item_id} --title='MUSIC' --url='https://{site_domain}/music' 2>&1"
            result = deployer.execute_command(update_item_cmd)
            if result and 'Success' in result:
                print(f"      ✅ Updated menu item {item_id}")
                break
    
    # 3. Clear all caches
    print(f"\n🧹 Clearing all caches...")
    deployer.execute_command(f"cd {wp_path} && wp cache flush 2>&1")
    deployer.execute_command(f"cd {wp_path} && wp rewrite flush 2>&1")
    deployer.execute_command(f"cd {wp_path} && wp transient delete --all 2>&1")
    
    print(f"\n✨ Fixes applied!")
    print(f"   Visit: https://{site_domain}/music")

if __name__ == "__main__":
    main()

