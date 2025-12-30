#!/usr/bin/env python3
"""
Deploy ICP Definition Custom Post Type to Revenue Engine Websites
BRAND-03 Fix - Tier 2 Foundation

Deploys Custom Post Type registration files to:
- freerideinvestor.com
- dadudekc.com
- crosbyultimateevents.com

Usage:
    python tools/deploy_icp_post_types.py --all
    python tools/deploy_icp_post_types.py --site freerideinvestor.com
"""

import argparse
import sys
from pathlib import Path

# Add ops to path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

REPO_ROOT = Path(__file__).parent.parent

SITE_CONFIGS = {
    'freerideinvestor.com': {
        'local_theme': 'websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern',
        'remote_theme': 'wp-content/themes/freerideinvestor-modern',
        'files': [
            ('inc/post-types/icp-definition.php', 'inc/post-types/icp-definition.php'),
            ('functions.php', 'functions.php'),  # Ensure functions.php includes it
        ]
    },
    'dadudekc.com': {
        'local_theme': 'sites/dadudekc.com/wp/theme/dadudekc',
        'remote_theme': 'wp-content/themes/dadudekc',
        'files': [
            ('inc/post-types/icp-definition.php', 'inc/post-types/icp-definition.php'),
            ('functions.php', 'functions.php'),
        ]
    },
    'crosbyultimateevents.com': {
        'local_theme': 'sites/crosbyultimateevents.com/wp/theme/crosbyultimateevents',
        'remote_theme': 'wp-content/themes/crosbyultimateevents',
        'files': [
            ('inc/post-types/icp-definition.php', 'inc/post-types/icp-definition.php'),
            ('functions.php', 'functions.php'),
        ]
    }
}

def deploy_site(site_domain: str) -> bool:
    """Deploy ICP Custom Post Type files for a specific site."""
    if site_domain not in SITE_CONFIGS:
        print(f"❌ Unknown site: {site_domain}")
        return False
    
    config = SITE_CONFIGS[site_domain]
    local_theme_path = REPO_ROOT / config['local_theme']
    remote_theme_path = config['remote_theme']
    
    print(f"\n📦 Deploying ICP Custom Post Type for {site_domain}...")
    print(f"   Local theme: {local_theme_path}")
    print(f"   Remote theme: {remote_theme_path}")
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print(f"❌ Failed to connect to {site_domain}")
        return False
    
    # Create remote directories first using SSH commands
    remote_dirs = set()
    for local_file, remote_file in config['files']:
        remote_dir = '/'.join(remote_file.split('/')[:-1])
        if remote_dir:
            remote_dirs.add(f"{remote_theme_path}/{remote_dir}")
    
    for remote_dir in remote_dirs:
        # Get remote base path from site config
        sftp_config = deployer.site_config.get('sftp', {})
        remote_base = sftp_config.get('remote_path', '')
        full_remote_dir = f"{remote_base}/{remote_dir}" if remote_base else remote_dir
        
        print(f"   📁 Creating directory: {full_remote_dir}")
        mkdir_cmd = f"mkdir -p {full_remote_dir}"
        result = deployer.execute_command(mkdir_cmd)
        if result:
            print(f"   ✅ Directory created or exists")
    
    success_count = 0
    for local_file, remote_file in config['files']:
        local_path = (local_theme_path / local_file).resolve()
        remote_path = f"{remote_theme_path}/{remote_file}"
        
        if not local_path.exists():
            print(f"⚠️  Local file not found: {local_path}")
            continue
        
        print(f"   📤 Deploying: {local_file} → {remote_path}")
        if deployer.deploy_file(local_path, remote_path):
            success_count += 1
            print(f"   ✅ Deployed: {remote_file}")
        else:
            print(f"   ❌ Failed to deploy: {remote_file}")
    
    deployer.disconnect()
    
    if success_count == len(config['files']):
        print(f"✅ Successfully deployed all files for {site_domain}")
        return True
    else:
        print(f"⚠️  Deployed {success_count}/{len(config['files'])} files for {site_domain}")
        return False

def main():
    """Main execution."""
    parser = argparse.ArgumentParser(
        description='Deploy ICP Definition Custom Post Type to WordPress sites'
    )
    parser.add_argument('--site', type=str, help='Site domain to deploy')
    parser.add_argument('--all', action='store_true', help='Deploy to all sites')
    
    args = parser.parse_args()
    
    if args.all:
        sites = list(SITE_CONFIGS.keys())
    elif args.site:
        sites = [args.site]
    else:
        parser.print_help()
        return 1
    
    print("="*60)
    print("📦 ICP DEFINITION CUSTOM POST TYPE DEPLOYMENT")
    print("="*60)
    
    results = {}
    for site in sites:
        results[site] = deploy_site(site)
    
    print("\n" + "="*60)
    print("📊 SUMMARY")
    print("="*60)
    for site, success in results.items():
        status = "✅ SUCCESS" if success else "❌ FAILED"
        print(f"{site}: {status}")
    
    if all(results.values()):
        print("\n✅ All deployments completed successfully!")
        print("   Next step: Run ICP content creation tool:")
        print("   python tools/create_icp_definitions.py --all")
        return 0
    else:
        print("\n⚠️  Some deployments failed. Check output above.")
        return 1

if __name__ == '__main__':
    exit(main())

