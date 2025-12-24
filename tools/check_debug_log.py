#!/usr/bin/env python3
"""
Check Debug Log
================

Checks WordPress debug log for errors.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_log():
    """Check debug log."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"📋 CHECKING DEBUG LOG: {site_name}")
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
        debug_log = f"{remote_path}/wp-content/debug.log"
        
        # Check last 50 lines of debug log
        print("1️⃣ Checking last 50 lines of debug.log...")
        tail_cmd = f"tail -50 {debug_log} 2>&1"
        log_output = deployer.execute_command(tail_cmd)
        
        if log_output and 'No such file' not in log_output:
            print(log_output[-1000:])  # Last 1000 chars
        else:
            print("   No debug.log found or empty")
        
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
    success = check_log()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

