#!/usr/bin/env python3
"""Deploy BUILD-IN-PUBLIC Phase 0 - Immediate execution"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def deploy_theme_files(site_key, local_theme_path, remote_theme_path):
    """Deploy theme files for a site."""
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_key, site_configs)
        if not deployer.connect():
            return False
        
        local_path = Path(local_theme_path)
        if not local_path.exists():
            print(f"❌ Local path not found: {local_path}")
            return False
        
        deployed = 0
        for file_path in local_path.rglob('*'):
            if file_path.is_file() and file_path.suffix in ['.php', '.css', '.js']:
                relative = file_path.relative_to(local_path)
                remote_file = f"{remote_theme_path}/{relative.as_posix()}".replace('\\', '/')
                print(f"📤 {relative}...")
                if deployer.deploy_file(file_path, remote_file):
                    deployed += 1
                    print(f"   ✅")
                else:
                    print(f"   ❌")
        
        deployer.disconnect()
        print(f"✅ Deployed {deployed} files to {site_key}")
        return True
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False

# Deploy dadudekc.com
print("=" * 60)
print("Deploying dadudekc.com...")
deploy_theme_files(
    "dadudekc.com",
    "D:/websites/sites/dadudekc.com/wp/theme/dadudekc",
    "domains/dadudekc.com/public_html/wp-content/themes/dadudekc"
)

# Deploy weareswarm.online  
print("\n" + "=" * 60)
print("Deploying weareswarm.online...")
deploy_theme_files(
    "weareswarm.online",
    "D:/websites/sites/weareswarm.online/wp/theme/swarm",
    "domains/weareswarm.online/public_html/wp-content/themes/swarm"
)

print("\n" + "=" * 60)
print("Deployment complete!")


