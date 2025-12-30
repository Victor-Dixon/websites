#!/usr/bin/env python3
"""
Deploy freerideinvestor.com Text Rendering Fixes
================================================

Deploys CSS and PHP fixes to resolve text rendering issues.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "freerideinvestor.com"
THEME_NAME = "freerideinvestor-modern"

def deploy_fixes():
    """Deploy CSS and PHP fixes to the live site."""
    print("=" * 60)
    print(f"Deploying Text Rendering Fixes to {SITE_NAME}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    if SITE_NAME not in site_configs:
        print("❌ Site config not found")
        return False
    
    deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    if not deployer.connect():
        print("❌ Could not connect to server")
        return False
    
    try:
        project_root = Path(__file__).parent.parent
        theme_path_local = project_root / "websites" / SITE_NAME / "wp" / "wp-content" / "themes" / THEME_NAME
        
        remote_base = deployer.remote_path or f"/home/u996867598/domains/{SITE_NAME}/public_html"
        if not remote_base.startswith('/'):
            username = site_configs[SITE_NAME].get('username') or site_configs[SITE_NAME].get('sftp', {}).get('username', '')
            if username:
                remote_base = f"/home/{username}/{remote_base}"
        
        theme_path_remote = f"{remote_base}/wp-content/themes/{THEME_NAME}"
        
        # Deploy CSS fixes
        print("\n📤 Deploying CSS fixes...")
        css_local = theme_path_local / "style.css"
        if css_local.exists():
            if deployer.deploy_file(css_local, f"{theme_path_remote}/style.css"):
                print("   ✅ CSS file deployed")
            else:
                print("   ❌ Failed to deploy CSS file")
                return False
        else:
            print(f"   ⚠️  CSS file not found locally: {css_local}")
        
        # Deploy PHP fixes
        print("\n📤 Deploying PHP fixes...")
        functions_local = theme_path_local / "functions.php"
        if functions_local.exists():
            if deployer.deploy_file(functions_local, f"{theme_path_remote}/functions.php"):
                print("   ✅ Functions.php file deployed")
            else:
                print("   ❌ Failed to deploy functions.php file")
                return False
        else:
            print(f"   ⚠️  Functions.php not found locally: {functions_local}")
        
        # Clear WordPress cache (if possible)
        print("\n🧹 Clearing WordPress cache...")
        try:
            cache_clear = deployer.execute_command(f"wp cache flush --path={remote_base} 2>/dev/null || true")
            print("   ✅ Cache clear attempted")
        except Exception as e:
            print(f"   ⚠️  Cache clear may have failed (this is OK if WP-CLI not available): {e}")
        
        print("\n✅ Deployment complete!")
        print("\n📋 Next Steps:")
        print("   1. Clear browser cache (Ctrl+F5)")
        print(f"   2. Test site: https://{SITE_NAME}")
        print("   3. Verify text renders correctly")
        print("   4. If critical error persists, check WordPress debug.log")
        
        return True
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    success = deploy_fixes()
    sys.exit(0 if success else 1)

