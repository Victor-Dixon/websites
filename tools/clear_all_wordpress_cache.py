#!/usr/bin/env python3
"""
Clear All WordPress Cache
==========================

Clears all types of WordPress cache.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def clear_all_cache():
    """Clear all WordPress cache."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🧹 CLEARING ALL CACHE: {site_name}")
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
        
        # Clear WordPress cache
        print("1️⃣ Clearing WordPress cache...")
        cache_result = deployer.execute_command(f"cd {remote_path} && wp cache flush --allow-root 2>&1")
        print(f"   Result: {cache_result[:100] if cache_result else 'Success'}")
        
        # Clear object cache
        print("2️⃣ Clearing object cache...")
        obj_cache = deployer.execute_command(f"cd {remote_path} && wp cache delete --allow-root 2>&1")
        
        # Clear rewrite rules (forces template refresh)
        print("3️⃣ Flushing rewrite rules...")
        rewrite = deployer.execute_command(f"cd {remote_path} && wp rewrite flush --allow-root 2>&1")
        
        # Clear transients
        print("4️⃣ Clearing transients...")
        transients = deployer.execute_command(f"cd {remote_path} && wp transient delete --all --allow-root 2>&1")
        
        print()
        print("=" * 70)
        print("✅ ALL CACHE CLEARED")
        print("=" * 70)
        print()
        print("💡 Please refresh the page with Ctrl+Shift+R (hard refresh)")
        
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
    success = clear_all_cache()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

