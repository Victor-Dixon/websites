#!/usr/bin/env python3
"""
Deploy Fixed Home.php with Custom Query
========================================

Deploys the updated home.php that uses WP_Query to ensure posts display.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def deploy_fixed_home():
    """Deploy fixed home.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 DEPLOYING FIXED HOME.PHP: {site_name}")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        
        # Read local home.php (the updated one with WP_Query)
        local_home = Path(__file__).parent.parent / "websites" / site_name / "wp" / "wp-content" / "themes" / "freerideinvestor-modern" / "home.php"
        
        if not local_home.exists():
            print(f"❌ Local home.php not found at {local_home}")
            return False
        
        print("1️⃣ Reading local home.php (with WP_Query)...")
        with open(local_home, 'r', encoding='utf-8') as f:
            home_content = f.read()
        
        # Verify it has WP_Query
        if 'new WP_Query' in home_content:
            print("   ✅ Contains WP_Query (custom query)")
        else:
            print("   ⚠️  Missing WP_Query - may not work correctly")
        
        # Deploy
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        remote_home = f"{theme_path}/home.php"
        
        print(f"2️⃣ Deploying to {remote_home}...")
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        with deployer.sftp.open(remote_home, 'w') as f:
            f.write(home_content.encode('utf-8'))
        
        print("✅ home.php deployed")
        
        # Clear cache
        print()
        print("3️⃣ Clearing cache...")
        cache_cmd = f"cd {remote_path} && wp cache flush --allow-root 2>&1"
        deployer.execute_command(cache_cmd)
        print("✅ Cache cleared")
        
        print()
        print("=" * 70)
        print("✅ FIXED HOME.PHP DEPLOYED")
        print("=" * 70)
        print()
        print("💡 This version uses WP_Query to ensure posts display")
        print("   Visit https://freerideinvestor.com/blog/ to verify")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = deploy_fixed_home()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

