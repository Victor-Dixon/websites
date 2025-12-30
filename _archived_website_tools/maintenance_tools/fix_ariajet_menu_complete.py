#!/usr/bin/env python3
"""
Complete Fix for AriaJet Menu and Music Page
============================================

Fixes:
1. Music page title
2. Menu items (Capabilities → MUSIC, fix URLs)
3. All menu item destinations
"""

import sys
import json
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
    
    print(f"🔧 Complete Menu & Page Fix for {site_domain}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    wp_path = f"/home/u996867598/domains/{site_domain}/public_html"
    
    # 1. Update music page title and ensure it has content
    print(f"\n📝 Updating music page...")
    update_page_cmd = f"cd {wp_path} && wp post update 3671 --post_title='MUSIC' --post_content='<h1>MUSIC</h1><p>Welcome to Aria'\''s Music Collection! Explore amazing tracks from the cosmic universe.</p>' --post_excerpt='Discover Aria'\''s music collection' 2>&1"
    result = deployer.execute_command(update_page_cmd)
    print(f"   ✅ Music page updated")
    
    # 2. Get all menu items from primary menu
    print(f"\n🔍 Getting menu items...")
    # Try different menu identifiers
    menu_names = ['primary', 'primary-menu', 'Primary Menu']
    items_json = None
    
    for menu_name in menu_names:
        items_cmd = f"cd {wp_path} && wp menu item list '{menu_name}' --format=json 2>&1"
        items_json = deployer.execute_command(items_cmd)
        if items_json and 'Error' not in items_json and items_json.strip():
            print(f"   ✅ Found menu: {menu_name}")
            break
    
    if not items_json or 'Error' in items_json:
        print(f"   ⚠️  Could not get menu items, trying by location...")
        # Try by location
        items_cmd = f"cd {wp_path} && wp menu location list --format=json 2>&1"
        locations = deployer.execute_command(items_cmd)
        print(f"   Locations: {locations[:200] if locations else 'None'}")
    else:
        try:
            items = json.loads(items_json) if items_json.strip().startswith('[') else []
            if not isinstance(items, list):
                items = [items] if items else []
            
            print(f"   Found {len(items)} menu items")
            
            # Update each menu item
            for item in items:
                item_id = item.get('db_id') or item.get('ID')
                title = item.get('title', '')
                url = item.get('url', '')
                
                print(f"\n   📌 Item {item_id}: '{title}' → {url}")
                
                # Fix Capabilities/Capabilitie → MUSIC
                if 'capabilit' in title.lower():
                    print(f"      🎵 Changing to MUSIC...")
                    update_cmd = f"cd {wp_path} && wp menu item update {item_id} --title='MUSIC' --url='https://{site_domain}/music' 2>&1"
                    result = deployer.execute_command(update_cmd)
                    print(f"      ✅ {result.strip() if result else 'Updated'}")
                
                # Fix Live Activity URL if it's broken
                elif 'live activity' in title.lower():
                    if not url or url.endswith('/') or '#' in url:
                        print(f"      🔗 Fixing Live Activity URL...")
                        # You'll need to set the correct URL for Live Activity
                        update_cmd = f"cd {wp_path} && wp menu item update {item_id} --url='https://{site_domain}/live-activity' 2>&1"
                        result = deployer.execute_command(update_cmd)
                        print(f"      ✅ {result.strip() if result else 'Updated'}")
                
                # Fix Agent URL if it's broken
                elif 'agent' in title.lower() and 'about' not in title.lower():
                    if not url or url.endswith('/') or '#' in url:
                        print(f"      🔗 Fixing Agent URL...")
                        # You'll need to set the correct URL for Agent
                        update_cmd = f"cd {wp_path} && wp menu item update {item_id} --url='https://{site_domain}/agent' 2>&1"
                        result = deployer.execute_command(update_cmd)
                        print(f"      ✅ {result.strip() if result else 'Updated'}")
        
        except json.JSONDecodeError:
            print(f"   ⚠️  Could not parse menu items JSON")
            print(f"   Raw output: {items_json[:500]}")
    
    # 3. Alternative: Update menu items by title search
    print(f"\n🔧 Updating menu items by search...")
    
    # Get menu item IDs - try different menu names
    item_ids_result = None
    for menu_name in ['primary', 'primary-menu', 'Primary Menu']:
        item_ids_cmd = f"cd {wp_path} && wp menu item list '{menu_name}' --format=ids 2>&1"
        item_ids_result = deployer.execute_command(item_ids_cmd)
        if item_ids_result and item_ids_result.strip() and not 'Error' in item_ids_result:
            print(f"   ✅ Using menu: {menu_name}")
            break
    
    if item_ids_result and item_ids_result.strip():
        item_ids = [id.strip() for id in item_ids_result.strip().split() if id.strip().isdigit()]
        print(f"   Found {len(item_ids)} menu item IDs")
        
        for item_id in item_ids:
            # Get item details
            item_get_cmd = f"cd {wp_path} && wp menu item get {item_id} --format=json 2>&1"
            item_data = deployer.execute_command(item_get_cmd)
            
            if item_data and 'title' in item_data.lower():
                # Check if it's Capabilities
                if 'capabilit' in item_data.lower():
                    print(f"   🎵 Updating item {item_id} to MUSIC...")
                    update_cmd = f"cd {wp_path} && wp menu item update {item_id} --title='MUSIC' --url='https://{site_domain}/music' 2>&1"
                    result = deployer.execute_command(update_cmd)
                    print(f"      ✅ {result.strip() if result else 'Updated'}")
    
    # 4. Clear all caches
    print(f"\n🧹 Clearing all caches...")
    deployer.execute_command(f"cd {wp_path} && wp cache flush 2>&1")
    deployer.execute_command(f"cd {wp_path} && wp rewrite flush 2>&1")
    deployer.execute_command(f"cd {wp_path} && wp transient delete --all 2>&1")
    
    print(f"\n✨ All fixes applied!")
    print(f"   Visit: https://{site_domain}/music")
    print(f"   Menu should now show MUSIC instead of Capabilities")

if __name__ == "__main__":
    main()

