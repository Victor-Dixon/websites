#!/usr/bin/env python3
"""
Deploy Trading Robot Plug Quality Fixes
========================================

Deploys quality fixes to WordPress:
1. Updated footer.php (copyright fix)
2. Updated functions.php (page title fix)

Note: Navigation menu typo must be fixed in WordPress Admin → Appearance → Menus
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "tradingrobotplug.com"
THEME_NAME = "tradingrobotplug-theme"

def deploy_quality_fixes():
    """Deploy quality fixes to WordPress."""
    print(f"🚀 Deploying Quality Fixes to {SITE_NAME}...\n")
    
    project_root = Path(__file__).parent.parent
    
    # Try multiple possible paths
    possible_paths = [
        project_root / "websites" / SITE_NAME / "wp" / "wp-content" / "themes" / THEME_NAME,
        project_root / "sites" / SITE_NAME / "wp" / "wp-content" / "themes" / THEME_NAME,
        project_root / SITE_NAME / "wp" / "wp-content" / "themes" / THEME_NAME,
    ]
    
    theme_path = None
    for path in possible_paths:
        if path.exists():
            theme_path = path
            break
    
    if not theme_path:
        print(f"❌ Theme directory not found. Tried:")
        for path in possible_paths:
            print(f"   - {path}")
        return False
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_base = deployer.remote_path or f"/home/u996867598/domains/{SITE_NAME}/public_html"
        theme_remote_path = f"{remote_base}/wp-content/themes/{THEME_NAME}"
        
        deployed_count = 0
        
        # Deploy footer.php
        footer_file = theme_path / "footer.php"
        if footer_file.exists():
            remote_footer = f"{theme_remote_path}/footer.php"
            print(f"📤 Deploying footer.php...")
            if deployer.deploy_file(footer_file, remote_footer):
                print(f"   ✅ Deployed successfully")
                deployed_count += 1
            else:
                print(f"   ❌ Deployment failed")
        
        # Deploy functions.php
        functions_file = theme_path / "functions.php"
        if functions_file.exists():
            remote_functions = f"{theme_remote_path}/functions.php"
            print(f"📤 Deploying functions.php...")
            if deployer.deploy_file(functions_file, remote_functions):
                print(f"   ✅ Deployed successfully")
                deployed_count += 1
            else:
                print(f"   ❌ Deployment failed")
        
        print(f"\n✅ Deployment complete!")
        print(f"📊 Files deployed: {deployed_count}/2")
        
        print(f"\n⚠️  IMPORTANT: Navigation Menu Typo Fix Required")
        print(f"   The 'Capabilitie' → 'Capabilities' fix must be done manually:")
        print(f"   1. Log into WordPress Admin")
        print(f"   2. Go to Appearance → Menus")
        print(f"   3. Find 'Capabilitie' menu item")
        print(f"   4. Edit to 'Capabilities'")
        print(f"   5. Save menu")
        
        return deployed_count > 0
    
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    success = deploy_quality_fixes()
    sys.exit(0 if success else 1)

