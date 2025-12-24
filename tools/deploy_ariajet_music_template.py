#!/usr/bin/env python3
"""
Deploy Music Page Template for ariajet.site
===========================================
"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

try:
    from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
except ImportError:
    try:
        sys.path.insert(0, str(project_root / "ops" / "deployment"))
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except ImportError:
        print("❌ Could not import SimpleWordPressDeployer")
        sys.exit(1)

def main():
    site_domain = "ariajet.site"
    
    print(f"🎵 Deploying Music Page Template to {site_domain}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    # Local template path
    local_template = project_root / "websites" / "ariajet.site" / "wp" / "wp-content" / "themes" / "ariajet" / "page-music.php"
    
    if not local_template.exists():
        print(f"❌ Local template not found at: {local_template}")
        sys.exit(1)
    
    # Remote template path
    remote_template = "wp-content/themes/ariajet/page-music.php"
    
    print(f"\n📄 Local file: {local_template}")
    print(f"📤 Remote file: {remote_template}")
    
    # Deploy the template
    print(f"\n📤 Deploying music page template...")
    try:
        success = deployer.deploy_file(
            local_path=local_template,
            remote_path=remote_template
        )
        if success:
            print("   ✅ Template deployed successfully")
        else:
            print("   ❌ Deployment failed")
            sys.exit(1)
    except Exception as e:
        print(f"   ❌ Deployment failed: {e}")
        sys.exit(1)
    
    print(f"\n✨ Template deployment complete!")

if __name__ == "__main__":
    main()

