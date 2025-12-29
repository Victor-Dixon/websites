#!/usr/bin/env python3
"""
Deploy All Updated Websites
===========================

Deploys all theme files for websites that have been updated recently.
Uses SimpleWordPressDeployer to deploy via SFTP.

Author: Agent-2
Date: 2025-12-27
V2 Compliant: Yes (<300 lines)
"""

import sys
from pathlib import Path

# Add deployment directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

# Sites to deploy (based on recent commits)
SITES_TO_DEPLOY = [
    "freerideinvestor.com",
    "dadudekc.com",
    "crosbyultimateevents.com",
    "digitaldreamscape.site",
    "tradingrobotplug.com"
]

# Theme path mappings
THEME_PATHS = {
    "freerideinvestor.com": "websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern",
    "dadudekc.com": "sites/dadudekc.com/wp/theme/dadudekc",
    "crosbyultimateevents.com": "sites/crosbyultimateevents.com/wp/theme/crosbyultimateevents",
    "digitaldreamscape.site": "websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape",
    "tradingrobotplug.com": "websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme"
}


def deploy_site_theme(site_key: str, site_configs: dict) -> bool:
    """Deploy all theme files for a site."""
    print(f"\n{'='*70}")
    print(f"🚀 DEPLOYING: {site_key}")
    print(f"{'='*70}")
    
    try:
        deployer = SimpleWordPressDeployer(site_key, site_configs)
        
        if not deployer.connect():
            print(f"❌ Failed to connect to {site_key}")
            return False
        
        # Get theme path
        theme_path = Path(THEME_PATHS.get(site_key, ""))
        if not theme_path.exists():
            print(f"❌ Theme directory not found: {theme_path}")
            deployer.disconnect()
            return False
        
        print(f"📂 Theme directory: {theme_path}")
        
        # Get remote base path from config
        remote_base = "wp-content/themes"
        if 'sftp' in site_configs.get(site_key, {}):
            remote_path = site_configs[site_key]['sftp'].get('remote_path', '')
            if remote_path:
                remote_base = f"{remote_path}/{remote_base}"
        
        # Find theme name (directory name)
        theme_name = theme_path.name
        
        # Deploy all files
        uploaded_count = 0
        failed_count = 0
        skipped_count = 0
        
        for file_path in theme_path.rglob('*'):
            if file_path.is_file():
                # Skip if file doesn't exist (broken symlinks, etc.)
                try:
                    if not file_path.exists() or not file_path.stat().st_size:
                        skipped_count += 1
                        continue
                except (OSError, FileNotFoundError):
                    skipped_count += 1
                    continue
                
                # Get relative path from theme directory
                relative_path = file_path.relative_to(theme_path)
                
                # Construct remote path
                remote_file_path = f"{remote_base}/{theme_name}/{relative_path.as_posix()}"
                
                print(f"📤 Uploading: {relative_path}...", end=" ")
                
                try:
                    if deployer.deploy_file(file_path, remote_file_path):
                        print("✅")
                        uploaded_count += 1
                    else:
                        print("❌")
                        failed_count += 1
                except FileNotFoundError:
                    print("⚠️  (file not found, skipped)")
                    skipped_count += 1
                except Exception as e:
                    print(f"❌ ({type(e).__name__})")
                    failed_count += 1
        
        deployer.disconnect()
        
        print(f"\n📊 Summary for {site_key}:")
        print(f"   ✅ Uploaded: {uploaded_count}")
        print(f"   ❌ Failed: {failed_count}")
        if skipped_count > 0:
            print(f"   ⚠️  Skipped: {skipped_count}")
        
        # Consider successful if no failures (skipped files are OK)
        return failed_count == 0
        
    except Exception as e:
        print(f"❌ Error deploying {site_key}: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main deployment function."""
    print("=" * 70)
    print("🚀 DEPLOY ALL UPDATED WEBSITES")
    print("=" * 70)
    print()
    
    # Load site configurations
    try:
        site_configs = load_site_configs()
        print(f"✅ Loaded configurations for {len(site_configs)} sites")
    except Exception as e:
        print(f"❌ Failed to load site configurations: {e}")
        return False
    
    # Deploy each site
    results = {}
    for site_key in SITES_TO_DEPLOY:
        if site_key not in site_configs:
            print(f"⚠️  Skipping {site_key}: Configuration not found")
            continue
        
        success = deploy_site_theme(site_key, site_configs)
        results[site_key] = success
    
    # Summary
    print("\n" + "=" * 70)
    print("📊 DEPLOYMENT SUMMARY")
    print("=" * 70)
    
    successful = [k for k, v in results.items() if v]
    failed = [k for k, v in results.items() if not v]
    
    print(f"✅ Successful: {len(successful)}")
    for site in successful:
        print(f"   - {site}")
    
    if failed:
        print(f"\n❌ Failed: {len(failed)}")
        for site in failed:
            print(f"   - {site}")
    
    return len(failed) == 0


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)

