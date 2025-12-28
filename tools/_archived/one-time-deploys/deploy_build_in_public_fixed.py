#!/usr/bin/env python3
"""Deploy BUILD-IN-PUBLIC Phase 0 - Fixed deployment script"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def deploy_theme_directory(site_key, local_theme_path, remote_theme_path):
    """Deploy entire theme directory."""
    site_configs = load_site_configs()
    
    # Find matching site config (try exact match, then partial)
    site_config = None
    for key, config in site_configs.items():
        if site_key in key or key.endswith(site_key.replace('.com', '').replace('.online', '')):
            site_config = config
            break
    
    if not site_config:
        print(f"❌ No config found for {site_key}")
        print(f"   Available: {list(site_configs.keys())}")
        return False
    
    try:
        deployer = SimpleWordPressDeployer(site_key, site_configs)
        if not deployer.connect():
            return False
        
        local_path = Path(local_theme_path).resolve()
        if not local_path.exists():
            print(f"❌ Local path not found: {local_path}")
            return False
        
        deployed = 0
        failed = 0
        
        # Deploy all PHP, CSS, JS files
        for file_path in local_path.rglob('*'):
            if file_path.is_file() and file_path.suffix in ['.php', '.css', '.js']:
                relative = file_path.relative_to(local_path)
                # Build remote path properly
                remote_file = f"{remote_theme_path}/{relative.as_posix()}".replace('\\', '/')
                
                print(f"📤 {relative}...", end=' ')
                
                # Use resolved absolute path
                abs_local = str(file_path.resolve())
                if deployer.deploy_file(Path(abs_local), remote_file):
                    deployed += 1
                    print("✅")
                else:
                    failed += 1
                    print("❌")
        
        deployer.disconnect()
        print(f"\n✅ Deployed {deployed} files, {failed} failed to {site_key}")
        return failed == 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False

# Deploy
print("=" * 60)
print("BUILD-IN-PUBLIC Phase 0 Deployment")
print("=" * 60)

# Add dadudekc.com to config if missing
site_configs = load_site_configs()
if 'dadudekc.com' not in site_configs:
    # Use weareswarm config as template
    if 'weareswarm.online' in site_configs:
        swarm_config = site_configs['weareswarm.online'].copy()
        swarm_config['sftp']['remote_path'] = 'domains/dadudekc.com/public_html'
        site_configs['dadudekc.com'] = swarm_config
        print("✅ Added dadudekc.com config")

print("\n1. Deploying dadudekc.com...")
deploy_theme_directory(
    "dadudekc.com",
    "D:/websites/sites/dadudekc.com/wp/theme/dadudekc",
    "domains/dadudekc.com/public_html/wp-content/themes/dadudekc"
)

print("\n2. Deploying weareswarm.online...")
deploy_theme_directory(
    "weareswarm.online",
    "D:/websites/sites/weareswarm.online/wp/theme/swarm",
    "domains/weareswarm.online/public_html/wp-content/themes/swarm"
)

print("\n" + "=" * 60)
print("Deployment complete!")


