#!/usr/bin/env python3
"""
Fix Crosby Ultimate Events Text Rendering Issue
================================================

Deploys CSS and PHP fixes to resolve text rendering issues.

Author: Agent-2 (Architecture & Design Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def deploy_fixes():
    """Deploy CSS and PHP fixes to the live site."""
    print("=" * 60)
    print("Deploying Text Rendering Fixes to crosbyultimateevents.com")
    print("=" * 60)
    
    site_configs = load_site_configs()
    if 'crosbyultimateevents.com' not in site_configs:
        print("❌ Site config not found")
        return False
    
    deployer = SimpleWordPressDeployer('crosbyultimateevents.com', site_configs)
    if not deployer.connect():
        print("❌ Could not connect to server")
        return False
    
    try:
        remote_path = deployer.remote_path or "domains/crosbyultimateevents.com/public_html"
        if not remote_path.startswith('/'):
            username = site_configs['crosbyultimateevents.com'].get('username') or site_configs['crosbyultimateevents.com'].get('sftp', {}).get('username', '')
            if username:
                remote_path = f"/home/{username}/{remote_path}"
        
        theme_path = f"{remote_path}/wp-content/themes/crosbyultimateevents"
        
        # Deploy CSS fixes
        print("\n📤 Deploying CSS fixes...")
        css_local = Path(__file__).parent.parent / "sites" / "crosbyultimateevents.com" / "wp" / "theme" / "crosbyultimateevents" / "style.css"
        
        if deployer.deploy_file(css_local, f"{theme_path}/style.css"):
            print("   ✅ CSS file deployed")
        else:
            print("   ❌ Failed to deploy CSS file")
            return False
        
        # Deploy PHP fixes
        print("\n📤 Deploying PHP fixes...")
        php_local = Path(__file__).parent.parent / "sites" / "crosbyultimateevents.com" / "wp" / "theme" / "crosbyultimateevents" / "functions.php"
        
        if deployer.deploy_file(php_local, f"{theme_path}/functions.php"):
            print("   ✅ Functions.php file deployed")
        else:
            print("   ❌ Failed to deploy functions.php file")
            return False
        
        # Clear WordPress cache
        print("\n🧹 Clearing WordPress cache...")
        cache_clear = deployer.execute_command(f"wp cache flush --path={remote_path}")
        if cache_clear:
            print("   ✅ Cache cleared")
        else:
            print("   ⚠️  Cache clear command may have failed (this is OK if cache plugin not active)")
        
        print("\n✅ Deployment complete!")
        print("\n📋 Next Steps:")
        print("   1. Clear browser cache (Ctrl+F5)")
        print("   2. Test site: https://crosbyultimateevents.com")
        print("   3. Verify text renders correctly")
        
        return True
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    success = deploy_fixes()
    sys.exit(0 if success else 1)

