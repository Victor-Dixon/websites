#!/usr/bin/env python3
"""
Deploy BUILD-IN-PUBLIC Phase 0 Theme Files
===========================================

Deploys Phase 0 theme files for dadudekc.com and weareswarm.online.

Author: Agent-7
Date: 2025-12-26
"""

import sys
import os
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False
    print("⚠️  SimpleWordPressDeployer not available")

# Theme deployment configs
THEME_DEPLOYMENTS = {
    "dadudekc.com": {
        "local_theme_path": Path("D:/websites/sites/dadudekc.com/wp/theme/dadudekc"),
        "remote_theme_path": "wp-content/themes/dadudekc",
        "files_to_deploy": [
            "front-page.php",
            "style.css",
            "functions.php",
            "header.php",
            "footer.php",
            "index.php",
            "page-contact.php"
        ]
    },
    "weareswarm.online": {
        "local_theme_path": Path("D:/websites/sites/weareswarm.online/wp/theme/swarm"),
        "remote_theme_path": "wp-content/themes/swarm",
        "files_to_deploy": [
            "front-page.php",
            "style.css",
            "functions.php",
            "header.php",
            "footer.php",
            "index.php",
            "page-swarm-manifesto.php",
            "page-how-the-swarm-works.php"
        ]
    }
}


def deploy_theme_files(site_domain: str) -> bool:
    """Deploy theme files for a site."""
    if site_domain not in THEME_DEPLOYMENTS:
        print(f"❌ No deployment config for {site_domain}")
        return False
    
    config = THEME_DEPLOYMENTS[site_domain]
    local_path = config["local_theme_path"]
    
    if not local_path.exists():
        print(f"❌ Local theme path not found: {local_path}")
        return False
    
    print(f"\n🚀 Deploying {site_domain} theme files...")
    print(f"   Local: {local_path}")
    print(f"   Remote: {config['remote_theme_path']}")
    
    if not DEPLOYER_AVAILABLE:
        print("⚠️  Deployer not available - files ready for manual deployment")
        print(f"   Files to deploy: {', '.join(config['files_to_deploy'])}")
        return False
    
    # Load site configs
    try:
        site_configs_path = Path("D:/websites/configs/site_configs.json")
        if site_configs_path.exists():
            import json
            with open(site_configs_path, 'r') as f:
                site_configs = json.load(f)
        else:
            site_configs = {}
    except Exception as e:
        print(f"⚠️  Could not load site configs: {e}")
        site_configs = {}
    
    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)
        
        if not deployer.connect():
            print(f"❌ Could not connect to {site_domain}")
            return False
        
        deployed_count = 0
        for file_name in config["files_to_deploy"]:
            local_file = local_path / file_name
            if local_file.exists():
                remote_file = f"{config['remote_theme_path']}/{file_name}"
                print(f"   Deploying {file_name}...")
                if deployer.deploy_file(local_file, remote_file):
                    deployed_count += 1
                    print(f"   ✅ {file_name}")
                else:
                    print(f"   ❌ Failed: {file_name}")
            else:
                print(f"   ⚠️  File not found: {local_file}")
        
        deployer.disconnect()
        
        print(f"\n✅ Deployed {deployed_count}/{len(config['files_to_deploy'])} files to {site_domain}")
        return deployed_count == len(config['files_to_deploy'])
        
    except Exception as e:
        print(f"❌ Deployment error: {e}")
        return False


def main():
    """Main deployment function."""
    print("=" * 60)
    print("BUILD-IN-PUBLIC Phase 0 Theme Deployment")
    print("=" * 60)
    
    sites = ["dadudekc.com", "weareswarm.online"]
    results = {}
    
    for site in sites:
        results[site] = deploy_theme_files(site)
        print()
    
    print("=" * 60)
    print("Deployment Summary:")
    for site, success in results.items():
        status = "✅ SUCCESS" if success else "❌ FAILED"
        print(f"  {site}: {status}")
    print("=" * 60)
    
    if all(results.values()):
        print("\n✅ All deployments successful!")
        return 0
    else:
        print("\n⚠️  Some deployments failed - check output above")
        return 1


if __name__ == "__main__":
    sys.exit(main())


