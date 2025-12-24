#!/usr/bin/env python3
"""
Get Detailed PHP Syntax Error
==============================

Gets the exact PHP syntax error with line numbers.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def get_detailed_error():
    """Get detailed PHP error."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 GETTING DETAILED PHP ERROR: {site_name}")
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
        functions_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/functions.php"
        
        # Get detailed PHP error
        print("1️⃣ Getting detailed PHP syntax error...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        print(syntax_result)
        
        # Read around line 1905 if error mentions it
        if '1905' in syntax_result:
            print()
            print("2️⃣ Reading around line 1905...")
            read_cmd = f"sed -n '1900,1910p' {functions_file}"
            context = deployer.execute_command(read_cmd)
            print("Lines 1900-1910:")
            print(context)
        
        # Also check the end of the file
        print()
        print("3️⃣ Reading last 20 lines of functions.php...")
        tail_cmd = f"tail -20 {functions_file}"
        tail_result = deployer.execute_command(tail_cmd)
        print("Last 20 lines:")
        print(tail_result)
        
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
    success = get_detailed_error()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

