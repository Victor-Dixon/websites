#!/usr/bin/env python3
"""Deploy page-contact.php to freerideinvestor.com"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_domain = "freerideinvestor.com"
    local_file = Path(f"websites/{site_domain}/wp/wp-content/themes/freerideinvestor-modern/page-contact.php")
    remote_file = "wp-content/themes/freerideinvestor-modern/page-contact.php"
    
    print(f"📤 Deploying page-contact.php to {site_domain}")
    print("=" * 60)
    
    if not local_file.exists():
        print(f"❌ Local file not found: {local_file}")
        sys.exit(1)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    full_remote_path = f"{deployer.remote_path}/{remote_file}"
    
    if deployer.deploy_file(local_file, full_remote_path):
        print("✅ File deployed successfully")
    else:
        print("❌ Failed to deploy file")
        sys.exit(1)
    
    # Clear cache
    wp_path = f"/home/u996867598/{deployer.remote_path}"
    deployer.execute_command(f"cd {wp_path} && wp cache flush 2>&1")
    deployer.execute_command(f"cd {wp_path} && wp rewrite flush 2>&1")
    
    deployer.disconnect()
    print("✨ Deployment complete!")

if __name__ == "__main__":
    main()

