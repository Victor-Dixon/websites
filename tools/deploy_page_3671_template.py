#!/usr/bin/env python3
"""Deploy page-3671.php template"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_domain = "ariajet.site"
    local_template = Path(f"websites/{site_domain}/wp/wp-content/themes/ariajet/page-3671.php")
    remote_template = "wp-content/themes/ariajet/page-3671.php"
    
    print(f"📤 Deploying page-3671.php template to {site_domain}")
    print("=" * 60)
    
    if not local_template.exists():
        print(f"❌ Local template not found: {local_template}")
        sys.exit(1)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    full_remote_path = f"{deployer.remote_path}/{remote_template}"
    
    if deployer.deploy_file(local_template, full_remote_path):
        print("✅ Template deployed successfully")
    else:
        print("❌ Failed to deploy template")
        sys.exit(1)
    
    deployer.disconnect()
    print("✨ Deployment complete!")

if __name__ == "__main__":
    main()

