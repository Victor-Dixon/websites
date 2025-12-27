#!/usr/bin/env python3
"""
Deploy High-Impact Improvements
===============================

Deploys changes to:
1. Crosby Ultimate Events (Portfolio, Contact, Home)
2. Digital Dreamscape (Streaming, Community, About)
3. Free Ride Investor (Fixes)

Author: Agent-7
Date: 2025-12-27
"""

import sys
import os
from pathlib import Path

# Add current directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

# Configuration for deployment
DEPLOY_CONFIGS = {
    "crosbyultimateevents.com": {
        "site_key": "crosbyultimateevents.com",
        "theme_path": "sites/crosbyultimateevents.com/wp/theme",
        "remote_base": "wp-content/themes"
    },
    "digitaldreamscape.site": {
        "site_key": "digitaldreamscape.site",
        "theme_path": "websites/digitaldreamscape.site/wp/wp-content/themes",
        "remote_base": "wp-content/themes"
    },
    "freerideinvestor.com": {
        "site_key": "freerideinvestor.com",
        "theme_path": "websites/freerideinvestor.com/wp/wp-content/themes",
        "remote_base": "wp-content/themes"
    }
}

def deploy_site(site_domain, config, site_configs):
    print(f"\n{'='*60}")
    print(f"🚀 DEPLOYING: {site_domain}")
    print(f"{'='*60}")
    
    try:
        deployer = SimpleWordPressDeployer(config["site_key"], site_configs)
        
        print(f"📡 Connecting to {site_domain}...")
        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False
        
        # Local base path (workspace root)
        workspace_root = Path("/workspace")
        local_themes_dir = workspace_root / config["theme_path"]
        
        if not local_themes_dir.exists():
            print(f"❌ Local theme directory not found: {local_themes_dir}")
            return False
            
        print(f"📂 Local themes dir: {local_themes_dir}")
        
        # Walk through all files in the theme directory
        uploaded_count = 0
        failed_count = 0
        
        for file_path in local_themes_dir.rglob('*'):
            if file_path.is_file():
                # Get relative path from themes dir (e.g. "theme-name/style.css")
                relative_path = file_path.relative_to(local_themes_dir)
                
                # Construct remote path relative to public_html
                # e.g. wp-content/themes/theme-name/style.css
                remote_file_path = f"{config['remote_base']}/{relative_path.as_posix()}"
                
                print(f"📤 Uploading: {relative_path}...")
                
                if deployer.deploy_file(file_path, remote_file_path):
                    print(f"   ✅ Success")
                    uploaded_count += 1
                else:
                    print(f"   ❌ Failed")
                    failed_count += 1
                    
        deployer.disconnect()
        
        print(f"\n📊 Summary for {site_domain}:")
        print(f"   ✅ Uploaded: {uploaded_count}")
        print(f"   ❌ Failed: {failed_count}")
        
        return failed_count == 0
        
    except Exception as e:
        print(f"❌ Error deploying {site_domain}: {e}")
        import traceback
        traceback.print_exc()
        return False

def main():
    # Load site configs
    os.environ["SITE_CONFIGS_PATH"] = "/workspace/configs/site_configs.json"
    site_configs = load_site_configs()
    
    if not site_configs:
        print("❌ Could not load site configurations!")
        return 1
        
    results = {}
    
    for domain, config in DEPLOY_CONFIGS.items():
        success = deploy_site(domain, config, site_configs)
        results[domain] = "✅ Success" if success else "❌ Failed"
        
    print("\n" + "="*60)
    print("🏁 FINAL DEPLOYMENT STATUS")
    print("="*60)
    for domain, status in results.items():
        print(f"{domain}: {status}")
        
    return 0 if all("Success" in s for s in results.values()) else 1

if __name__ == "__main__":
    sys.exit(main())
