#!/usr/bin/env python3
"""
Create Music Page for ariajet.site
==================================

Creates the music page in WordPress and sets it up
"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

try:
    from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    from ops.deployment.wp_remote_utils import detect_wp_path
except ImportError:
    try:
        sys.path.insert(0, str(project_root / "ops" / "deployment"))
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except ImportError:
        print("❌ Could not import SimpleWordPressDeployer")
        sys.exit(1)

def main():
    site_domain = "ariajet.site"
    
    print(f"🎵 Creating Music Page for {site_domain}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    # Get WordPress path
    wp_path = detect_wp_path(deployer=deployer, site_domain=site_domain)
    if not wp_path:
        print("❌ Could not detect WordPress path on server.")
        deployer.disconnect()
        sys.exit(1)
    
    print(f"\n📂 WordPress path: {wp_path}")
    
    # Check if page already exists
    print(f"\n🔍 Checking if music page exists...")
    check_cmd = f"cd {wp_path} && wp post list --post_type=page --name=music --format=count 2>&1"
    result = deployer.execute_command(check_cmd)
    
    page_exists = result and result.strip().isdigit() and int(result.strip()) > 0
    
    if page_exists:
        print("   ℹ️  Music page already exists, updating it...")
        page_id_cmd = f"cd {wp_path} && wp post list --post_type=page --name=music --format=ids 2>&1"
        page_id_result = deployer.execute_command(page_id_cmd)
        if page_id_result and page_id_result.strip().isdigit():
            page_id = page_id_result.strip()
            update_cmd = f"cd {wp_path} && wp post update {page_id} --post_title='MUSIC' --post_excerpt='Discover Aria music collection from the cosmic universe' --post_status=publish --page_template='page-music.php' 2>&1"
        else:
            print("   ⚠️  Could not get page ID, skipping update")
            return
    else:
        print("   ✨ Creating new music page...")
        update_cmd = f"cd {wp_path} && wp post create --post_type=page --post_title='MUSIC' --post_name='music' --post_excerpt='Discover Aria music collection from the cosmic universe' --post_status=publish --page_template='page-music.php' 2>&1"
    
    result = deployer.execute_command(update_cmd)
    print(f"   ✅ {result.strip() if result else 'Page created/updated'}")
    
    # Get the page ID
    page_id_cmd = f"cd {wp_path} && wp post list --post_type=page --name=music --format=ids 2>&1"
    page_id = deployer.execute_command(page_id_cmd)
    
    if page_id and page_id.strip().isdigit():
        print(f"\n📄 Page ID: {page_id.strip()}")
        print(f"🔗 Page URL: https://{site_domain}/music")
    
    # Clear cache
    print(f"\n🧹 Clearing cache...")
    deployer.execute_command(f"cd {wp_path} && wp cache flush 2>&1")
    
    print(f"\n✨ Music page setup complete!")
    print(f"   Visit: https://{site_domain}/music")

if __name__ == "__main__":
    main()

