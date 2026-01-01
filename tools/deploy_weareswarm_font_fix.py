#!/usr/bin/env python3
"""
Deploy weareswarm.online font fix
Deploys functions.php and header.php with Google Fonts Inter loading
"""

import sys
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_key = "weareswarm.online"
    
    # Load site configurations
    site_configs = load_site_configs()
    
    # Check if weareswarm.online is in configs, if not try adding it with default Hostinger creds
    if site_key not in site_configs:
        print(f"⚠️  {site_key} not found in site configs")
        print("   Attempting to use default Hostinger credentials...")
        # Try with a generic config that will use env vars
        site_configs[site_key] = {}
    
    deployer = SimpleWordPressDeployer(site_key=site_key, site_configs=site_configs)
    
    print(f"🚀 Deploying font fix to {site_key}...")
    
    if not deployer.connect():
        print(f"❌ Failed to connect to {site_key}")
        print("   Check SFTP credentials in:")
        print("   - .deploy_credentials/sites.json")
        print("   - config/site_configs.json")
        print("   - Environment variables (HOSTINGER_*)")
        return 1
    
    print(f"✅ Connected to {site_key}")
    
    # Local file paths
    repo_root = Path(__file__).parent.parent
    functions_local = repo_root / "sites" / site_key / "wp" / "theme" / "swarm" / "functions.php"
    header_local = repo_root / "sites" / site_key / "wp" / "theme" / "swarm" / "header.php"
    
    # Remote file paths
    functions_remote = "wp-content/themes/swarm/functions.php"
    header_remote = "wp-content/themes/swarm/header.php"
    
    files_to_deploy = [
        (functions_local, functions_remote),
        (header_local, header_remote)
    ]
    
    deployed = []
    failed = []
    
    for local_path, remote_path in files_to_deploy:
        if not local_path.exists():
            print(f"❌ Local file not found: {local_path}")
            failed.append((local_path.name, "File not found"))
            continue
        
        print(f"📤 Deploying {local_path.name}...")
        success = deployer.deploy_file(str(local_path), remote_path)
        
        if success:
            print(f"   ✅ Deployed to {remote_path}")
            deployed.append(local_path.name)
            
            # Validate PHP syntax
            print(f"   🔍 Validating PHP syntax...")
            syntax_result = deployer.check_php_syntax(remote_path)
            if syntax_result.get('valid'):
                print(f"   ✅ PHP syntax is valid")
            else:
                print(f"   ⚠️  PHP syntax error: {syntax_result.get('error_message', 'Unknown error')}")
        else:
            print(f"   ❌ Deployment failed")
            failed.append((local_path.name, "Deployment failed"))
    
    deployer.disconnect()
    
    print(f"\n📊 Deployment Summary:")
    print(f"   ✅ Deployed: {len(deployed)}/{len(files_to_deploy)}")
    print(f"   ❌ Failed: {len(failed)}/{len(files_to_deploy)}")
    
    if deployed:
        print(f"\n✅ Successfully deployed files:")
        for file in deployed:
            print(f"   - {file}")
    
    if failed:
        print(f"\n❌ Failed files:")
        for file, error in failed:
            print(f"   - {file}: {error}")
    
    return 0 if len(failed) == 0 else 1

if __name__ == "__main__":
    sys.exit(main())

