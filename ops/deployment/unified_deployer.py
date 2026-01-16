#!/usr/bin/env python3
"""
Unified Website Deployer
========================

Deploys files to all configured websites using SFTP or REST API.
Supports deploying themes, plugins, and other WordPress files.

Usage:
    python ops/deployment/unified_deployer.py --all              # Deploy all sites
    python ops/deployment/unified_deployer.py --site <domain>     # Deploy single site
    python ops/deployment/unified_deployer.py --all --dry-run    # Test without deploying

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-24
"""

import argparse
import json
import sys
from pathlib import Path
from typing import Dict, List, Optional

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False
    print("❌ SimpleWordPressDeployer not available")
    print("   Install dependencies: pip install paramiko python-dotenv")


def load_site_registry() -> Dict:
    """Load site registry from config/sites_registry.json."""
    registry_path = Path(__file__).parent.parent.parent / "config" / "sites_registry.json"
    if registry_path.exists():
        with open(registry_path, 'r') as f:
            return json.load(f)
    return {}


def get_ssot_paths(site_domain: str) -> Dict[str, List[Path]]:
    """Get canonical paths for a site according to SSOT map."""
    base_dir = Path(__file__).parent.parent.parent / "websites" / site_domain

    # SSOT canonical paths per domain
    ssot_paths = {
        'dadudekc.com': {
            'theme': [base_dir / "overlays" / "wp" / "theme" / "dadudekc"],
            'plugins': [],
            'content': [base_dir / "blog-posts"]
        },
        'freerideinvestor.com': {
            'theme': [base_dir / "wp" / "wp-content" / "themes" / "freerideinvestor-modern"],
            'plugins': [base_dir / "wp" / "wp-content" / "plugins"],
            'content': [base_dir / "blog"]
        },
        'tradingrobotplug.com': {
            'theme': [base_dir / "overlays" / "wp" / "theme" / "tradingrobotplug-theme"],
            'plugins': [base_dir / "overlays" / "wp" / "plugins"],
            'content': [base_dir / "blog"]
        },
        'crosbyultimateevents.com': {
            'theme': [base_dir / "overlays" / "wp" / "theme" / "crosbyultimateevents"],
            'plugins': [base_dir / "overlays" / "wp" / "plugins"],
            'content': [base_dir / "blog"]
        },
        # Domains needing consolidation (use current paths until migrated)
        'southwestsecret.com': {
            'theme': [
                base_dir / "wordpress-theme" / "southwestsecret",  # Preferred canonical
                base_dir / "wp" / "wp-content" / "themes" / "southwestsecret"  # Legacy
            ],
            'plugins': [base_dir / "wp" / "wp-content" / "plugins"],
            'content': []
        },
        'ariajet.site': {
            'theme': [
                base_dir / "wordpress-theme" / "ariajet",  # Preferred
                base_dir / "wp"  # Legacy mixed
            ],
            'plugins': [],
            'content': []
        },
        'weareswarm.site': {
            'theme': [base_dir / "wp" / "wp-content" / "themes" / "weareswarm"],
            'plugins': [base_dir / "wp" / "wp-content" / "plugins"],
            'content': []
        }
    }

    # Default fallback for unmapped domains
    if site_domain not in ssot_paths:
        ssot_paths[site_domain] = {
            'theme': [base_dir / "overlays" / "wp" / "theme"],
            'plugins': [base_dir / "overlays" / "wp" / "plugins"],
            'content': [base_dir / "content" / "posts"]
        }

    return ssot_paths[site_domain]


def find_theme_files(site_domain: str) -> List[Path]:
    """Find theme files for a site using SSOT canonical paths only."""
    ssot_paths = get_ssot_paths(site_domain)
    theme_dirs = ssot_paths.get('theme', [])

    theme_files = []
    for theme_dir in theme_dirs:
        if theme_dir.exists():
            # Find all PHP, CSS, JS files in canonical theme directories
            for ext in ['*.php', '*.css', '*.js', '*.scss']:
                theme_files.extend(theme_dir.rglob(ext))

    return theme_files


def find_plugin_files(site_domain: str) -> List[Path]:
    """Find plugin files for a site using SSOT canonical paths only."""
    ssot_paths = get_ssot_paths(site_domain)
    plugin_dirs = ssot_paths.get('plugins', [])

    plugin_files = []
    for plugin_dir in plugin_dirs:
        if plugin_dir.exists():
            for ext in ['*.php', '*.css', '*.js', '*.py']:
                plugin_files.extend(plugin_dir.rglob(ext))

    return plugin_files


def get_files_to_deploy(site_domain: str, site_config: Dict) -> List[Path]:
    """Get list of files to deploy for a site."""
    files = []
    
    # Add theme files
    theme_files = find_theme_files(site_domain)
    files.extend(theme_files)
    
    # Add plugin files
    plugin_files = find_plugin_files(site_domain)
    files.extend(plugin_files)
    
    # Add specific files from site config if defined
    if 'deploy_files' in site_config:
        base_path = Path(__file__).parent.parent.parent
        for file_path in site_config['deploy_files']:
            full_path = base_path / file_path
            if full_path.exists():
                files.append(full_path)
    
    return list(set(files))  # Remove duplicates


def deploy_site(site_domain: str, site_config: Dict, dry_run: bool = False) -> bool:
    """Deploy files to a single site."""
    print(f"\n{'='*60}")
    print(f"🌐 DEPLOYING: {site_domain}")
    print(f"{'='*60}\n")
    
    if dry_run:
        print("🔍 DRY RUN MODE - No files will be deployed\n")
    
    try:
        # Get files to deploy first (doesn't require connection)
        files_to_deploy = get_files_to_deploy(site_domain, site_config)
        
        if not files_to_deploy:
            print("⚠️  No files found to deploy for this site")
            print("   Check if theme/plugin files exist in websites/ directory")
            return True  # Not an error, just nothing to deploy
        
        print(f"📋 Found {len(files_to_deploy)} file(s) to deploy")
        
        if dry_run:
            print("\n📁 Files that would be deployed:")
            for file_path in files_to_deploy:
                rel_path = file_path.relative_to(Path(__file__).parent.parent.parent)
                print(f"   - {rel_path}")
            
            # Try to load configs to show deployment method
            try:
                site_configs = load_site_configs()
                if site_configs and site_domain in site_configs:
                    method = site_configs[site_domain].get('deployment_method', 'unknown')
                    print(f"\n📡 Deployment method: {method}")
                    if method == 'sftp':
                        remote_path = site_configs[site_domain].get('sftp', {}).get('remote_path', 'N/A')
                        print(f"   Remote path: {remote_path}")
            except Exception as e:
                print(f"\n⚠️  Could not load deployment config: {e}")
                print("   (This is OK for dry-run - files are ready to deploy)")
            
            return True
        
        # For actual deployment, need to load configs and connect
        site_configs = load_site_configs()
        if not site_configs:
            print("❌ No site configurations found")
            print("   Check config/site_configs.json or .env file for credentials")
            return False
        
        # Initialize deployer
        deployer = SimpleWordPressDeployer(site_domain, site_configs)
        
        # Connect to server
        print(f"📡 Connecting to {site_domain}...")
        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False
        print("✅ Connected!\n")
        
        # Deploy each file
        success_count = 0
        fail_count = 0
        
        for file_path in files_to_deploy:
            relative_path = file_path.relative_to(Path(__file__).parent.parent.parent)
            print(f"📤 Deploying: {relative_path}...", end=" ")
            
            # Calculate remote path
            # Try to determine remote path from local structure
            remote_path = None
            if 'wp/wp-content' in str(file_path):
                # Extract path after wp/wp-content
                parts = str(file_path).split('wp/wp-content/')
                if len(parts) > 1:
                    remote_base = site_config.get('sftp', {}).get('remote_path', '')
                    if remote_base:
                        remote_path = f"{remote_base}/wp-content/{parts[1]}"
            
            if deployer.deploy_file(file_path, remote_path):
                print("✅")
                success_count += 1
            else:
                print("❌")
                fail_count += 1
        
        # Disconnect
        deployer.disconnect()
        
        print(f"\n📊 Summary for {site_domain}:")
        print(f"   ✅ Succeeded: {success_count}")
        print(f"   ❌ Failed: {fail_count}")
        
        return fail_count == 0
        
    except ValueError as e:
        print(f"❌ Site configuration error: {e}")
        return False
    except Exception as e:
        print(f"❌ Error deploying {site_domain}: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution."""
    parser = argparse.ArgumentParser(description='Unified Website Deployer')
    parser.add_argument('--all', action='store_true', help='Deploy to all websites')
    parser.add_argument('--site', type=str, help='Deploy to specific site (domain name)')
    parser.add_argument('--dry-run', action='store_true', help='Test without deploying')
    
    args = parser.parse_args()
    
    if not DEPLOYER_AVAILABLE:
        print("❌ Deployment tools not available")
        return 1
    
    print("\n" + "="*60)
    print("🚀 UNIFIED WEBSITE DEPLOYER")
    print("="*60)
    
    # Load configurations
    site_configs_path = Path(__file__).parent.parent.parent / "config" / "site_configs.json"
    if not site_configs_path.exists():
        print(f"\n❌ Site configurations not found at: {site_configs_path}")
        print("   Please create config/site_configs.json with site configurations")
        return 1
    
    with open(site_configs_path, 'r') as f:
        site_configs = json.load(f)
    
    site_registry = load_site_registry()
    
    # Determine which sites to deploy
    sites_to_deploy = []
    
    if args.all:
        # Deploy all sites from registry
        if site_registry:
            sites_to_deploy = list(site_registry.keys())
        else:
            # Fallback to site_configs
            sites_to_deploy = list(site_configs.keys())
    elif args.site:
        if args.site in site_configs:
            sites_to_deploy = [args.site]
        else:
            print(f"\n❌ Site '{args.site}' not found in configurations")
            print(f"   Available sites: {', '.join(site_configs.keys())}")
            return 1
    else:
        parser.print_help()
        return 1
    
    if not sites_to_deploy:
        print("\n❌ No sites to deploy")
        return 1
    
    print(f"\n📋 Sites to deploy: {len(sites_to_deploy)}")
    for site in sites_to_deploy:
        mode = site_registry.get(site, {}).get('mode', 'UNKNOWN')
        print(f"   - {site} ({mode})")
    
    # Deploy each site
    results = {}
    for site_domain in sites_to_deploy:
        site_config = site_configs.get(site_domain, {})
        results[site_domain] = deploy_site(site_domain, site_config, args.dry_run)
    
    # Summary
    print("\n" + "="*60)
    print("📊 DEPLOYMENT SUMMARY")
    print("="*60)
    
    success_count = sum(1 for v in results.values() if v)
    fail_count = len(results) - success_count
    
    for site_domain, success in results.items():
        status = "✅ SUCCESS" if success else "❌ FAILED"
        print(f"   {status}: {site_domain}")
    
    print(f"\n✅ Successful: {success_count}/{len(results)}")
    print(f"❌ Failed: {fail_count}/{len(results)}")
    
    if args.dry_run:
        print("\n💡 This was a dry run. Use without --dry-run to actually deploy.")
    elif success_count == len(results):
        print("\n✅ All websites deployed successfully!")
        print("\n💡 Next Steps:")
        print("   1. Clear WordPress cache")
        print("   2. Clear browser cache")
        print("   3. Verify changes on live sites")
    else:
        print("\n⚠️  Some deployments failed. Check errors above.")
    
    return 0 if fail_count == 0 else 1


if __name__ == '__main__':
    exit(main())

