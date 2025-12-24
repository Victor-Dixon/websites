#!/usr/bin/env python3
"""
Check Home.php Loop Code
=========================

Checks if home.php has the WordPress loop code.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_loop():
    """Check home.php loop code."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 CHECKING HOME.PHP LOOP CODE: {site_name}")
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
        home_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/home.php"
        
        print("1️⃣ Reading home.php (lines 250-330 for loop code)...")
        
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        # Use sed to get lines 250-330
        read_loop = f"sed -n '250,330p' {home_file}"
        loop_section = deployer.execute_command(read_loop)
        
        print("Loop section (lines 250-330):")
        print(loop_section)
        print()
        
        # Check for key elements
        if 'have_posts()' in loop_section:
            print("✅ Found have_posts()")
        else:
            print("❌ Missing have_posts()")
        
        if 'while (have_posts())' in loop_section or 'while(have_posts())' in loop_section:
            print("✅ Found while loop with have_posts()")
        else:
            print("❌ Missing while loop")
        
        if 'the_post()' in loop_section:
            print("✅ Found the_post()")
        else:
            print("❌ Missing the_post()")
        
        # Also check total file size
        file_size_cmd = f"wc -l {home_file}"
        total_lines = deployer.execute_command(file_size_cmd).strip().split()[0]
        print(f"\nTotal lines in home.php: {total_lines}")
        
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
    success = check_loop()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

