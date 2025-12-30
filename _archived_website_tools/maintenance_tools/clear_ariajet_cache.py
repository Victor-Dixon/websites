#!/usr/bin/env python3
"""
Clear WordPress Cache for ariajet.site
======================================

Clears WordPress cache to ensure menu changes take effect
"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

try:
    from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    from ops.deployment.wp_remote_utils import detect_wp_path
except ImportError:
    try:
        sys.path.insert(0, str(project_root / "ops" / "deployment"))
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except ImportError:
        print("❌ Could not import SimpleWordPressDeployer")
        sys.exit(1)

def main():
    site_domain = "ariajet.site"
    
    print(f"🧹 Clearing cache for {site_domain}")
    print("=" * 60)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    # Get WordPress path
    wp_path = detect_wp_path(deployer=deployer, site_domain=site_domain)
    if not wp_path:
        print("❌ Could not detect WordPress path on server.")
        deployer.disconnect()
        sys.exit(1)
    
    print(f"\n📂 WordPress path: {wp_path}")
    
    # Clear WordPress cache via WP-CLI
    commands = [
        f"cd {wp_path} && wp cache flush 2>&1",
        f"cd {wp_path} && wp rewrite flush 2>&1",
        f"cd {wp_path} && wp transient delete --all 2>&1",
    ]
    
    for cmd in commands:
        print(f"\n🔄 Running: {cmd.split('&&')[1].strip()}")
        result = deployer.execute_command(cmd)
        if result:
            print(f"   ✅ {result.strip()}")
        else:
            print(f"   ⚠️  No output (may be normal)")
    
    print(f"\n✨ Cache cleared!")
    print(f"   The menu should update on the next page load")

if __name__ == "__main__":
    main()

