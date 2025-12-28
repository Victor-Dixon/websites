#!/usr/bin/env python3
"""
Test Home.php PHP Syntax
=========================

Tests home.php for PHP syntax errors.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def test_syntax():
    """Test home.php syntax."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🧪 TESTING HOME.PHP SYNTAX: {site_name}")
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
        
        print("1️⃣ Checking PHP syntax...")
        syntax_cmd = f"php -l {home_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        print(syntax_result)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ No syntax errors")
        else:
            print("   ❌ Syntax errors found")
            return False
        
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
    success = test_syntax()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

