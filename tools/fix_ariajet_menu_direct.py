#!/usr/bin/env python3
"""
Direct Menu Fix for AriaJet
===========================

Directly updates menu items in WordPress database
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
    
    print(f"🔧 Direct Menu Fix for {site_domain}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    wp_path = f"/home/u996867598/domains/{site_domain}/public_html"
    
    # Get menu term ID for primary-menu
    print(f"\n🔍 Finding primary menu...")
    menu_cmd = f"cd {wp_path} && wp menu list --format=json 2>&1"
    menu_result = deployer.execute_command(menu_cmd)
    
    # Find primary-menu term_id
    primary_menu_id = None
    if menu_result and 'primary-menu' in menu_result.lower():
        # Extract term_id from JSON
        import json
        try:
            menus = json.loads(menu_result) if menu_result.strip().startswith('[') else []
            if not isinstance(menus, list):
                menus = [menus] if menus else []
            for menu in menus:
                if menu.get('slug') == 'primary-menu':
                    primary_menu_id = menu.get('term_id')
                    print(f"   ✅ Found primary-menu with term_id: {primary_menu_id}")
                    break
        except:
            pass
    
    if not primary_menu_id:
        print(f"   ⚠️  Could not find primary-menu, trying to create/assign...")
        # Get or create menu
        create_cmd = f"cd {wp_path} && wp menu create 'Primary Menu' 2>&1"
        create_result = deployer.execute_command(create_cmd)
        print(f"   Menu creation: {create_result[:100] if create_result else 'None'}")
        
        # Assign to location
        assign_cmd = f"cd {wp_path} && wp menu location assign 'Primary Menu' primary 2>&1"
        assign_result = deployer.execute_command(assign_cmd)
        print(f"   Location assignment: {assign_result[:100] if assign_result else 'None'}")
    
    # Now add/update menu items
    print(f"\n🎵 Adding/Updating MUSIC menu item...")
    
    # Check if MUSIC item exists
    check_music_cmd = f"cd {wp_path} && wp menu item list primary-menu --format=json 2>&1"
    items_result = deployer.execute_command(check_music_cmd)
    
    music_exists = False
    if items_result and 'MUSIC' in items_result:
        music_exists = True
        print(f"   ℹ️  MUSIC item already exists")
    
    if not music_exists:
        # Add MUSIC menu item
        add_music_cmd = f"cd {wp_path} && wp menu item add-custom primary-menu 'MUSIC' 'https://{site_domain}/music' 2>&1"
        result = deployer.execute_command(add_music_cmd)
        print(f"   ✅ {result.strip() if result else 'MUSIC item added'}")
    
    # Update existing items
    print(f"\n🔧 Updating existing menu items...")
    
    # Get all menu items
    list_cmd = f"cd {wp_path} && wp menu item list primary-menu 2>&1"
    items_list = deployer.execute_command(list_cmd)
    print(f"   Current items:\n{items_list}")
    
    # Update Capabilities if it exists
    if 'Capabilit' in items_list:
        # Find the item ID
        items_json_cmd = f"cd {wp_path} && wp menu item list primary-menu --format=json 2>&1"
        items_json = deployer.execute_command(items_json_cmd)
        
        if items_json:
            import json
            try:
                items = json.loads(items_json) if items_json.strip().startswith('[') else []
                if not isinstance(items, list):
                    items = [items] if items else []
                
                for item in items:
                    title = item.get('title', '')
                    item_id = item.get('db_id') or item.get('ID')
                    
                    if 'capabilit' in title.lower():
                        print(f"   🎵 Updating '{title}' (ID: {item_id}) to MUSIC...")
                        update_cmd = f"cd {wp_path} && wp menu item update {item_id} --title='MUSIC' --url='https://{site_domain}/music' 2>&1"
                        result = deployer.execute_command(update_cmd)
                        print(f"      ✅ {result.strip() if result else 'Updated'}")
            except Exception as e:
                print(f"   ⚠️  Error parsing items: {e}")
    
    # Clear cache
    print(f"\n🧹 Clearing cache...")
    deployer.execute_command(f"cd {wp_path} && wp cache flush 2>&1")
    deployer.execute_command(f"cd {wp_path} && wp rewrite flush 2>&1")
    
    print(f"\n✨ Menu fix complete!")

if __name__ == "__main__":
    main()

