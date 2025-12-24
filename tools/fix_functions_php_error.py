#!/usr/bin/env python3
"""
Fix Functions.php Error
========================

Fixes the missing file error in functions.php.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_functions_php():
    """Fix functions.php error."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING FUNCTIONS.PHP ERROR: {site_name}")
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
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        functions_file = f"{theme_path}/functions.php"
        
        # Read functions.php
        print("1️⃣ Reading functions.php...")
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        with deployer.sftp.open(functions_file, 'r') as f:
            content = f.read().decode('utf-8')
        
        # Check for the problematic require_once
        if 'inc/plugin-testing.php' in content:
            print("   ⚠️  Found problematic require_once for inc/plugin-testing.php")
            
            # Comment out or remove the problematic line
            # Try to find and comment it out
            lines = content.split('\n')
            new_lines = []
            fixed = False
            
            for i, line in enumerate(lines):
                if 'plugin-testing.php' in line and ('require_once' in line or 'require' in line):
                    # Comment it out
                    if line.strip().startswith('//'):
                        new_lines.append(line)  # Already commented
                    else:
                        new_lines.append('// ' + line)  # Comment it out
                        fixed = True
                        print(f"   ✅ Commented out line {i+1}: {line.strip()[:60]}")
                else:
                    new_lines.append(line)
            
            if fixed:
                new_content = '\n'.join(new_lines)
                
                # Write back
                print("2️⃣ Writing fixed functions.php...")
                with deployer.sftp.open(functions_file, 'w') as f:
                    f.write(new_content.encode('utf-8'))
                
                print("✅ Functions.php fixed successfully")
                return True
            else:
                print("   ⚠️  Could not find problematic line to fix")
                return False
        else:
            print("   ✅ No problematic require_once found")
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
    success = fix_functions_php()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

