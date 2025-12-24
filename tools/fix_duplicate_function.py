#!/usr/bin/env python3
"""
Fix Duplicate Function Declaration
===================================

Fixes the duplicate freerideinvestor_menu_css_styled() function in functions.php.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_duplicate():
    """Fix duplicate function declaration."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING DUPLICATE FUNCTION: {site_name}")
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
        
        print("1️⃣ Reading functions.php...")
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        with deployer.sftp.open(functions_file, 'r') as f:
            content = f.read().decode('utf-8')
        
        # Find all occurrences of the function
        print("2️⃣ Finding duplicate function declarations...")
        function_name = 'freerideinvestor_menu_css_styled'
        
        lines = content.split('\n')
        function_indices = []
        
        for i, line in enumerate(lines):
            if f'function {function_name}(' in line:
                function_indices.append(i)
        
        print(f"   Found function declarations at lines: {[i+1 for i in function_indices]}")
        
        if len(function_indices) <= 1:
            print("   ✅ No duplicates found")
            return True
        
        # Remove duplicates, keep only the first one
        print("3️⃣ Removing duplicate declarations...")
        
        # Find the full function definitions (from function to closing brace)
        functions_to_remove = []
        
        for idx in function_indices[1:]:  # Skip first one, remove the rest
            # Find where this function ends
            start_idx = idx
            brace_count = 0
            in_function = False
            
            for i in range(start_idx, len(lines)):
                line = lines[i]
                
                # Count braces to find function end
                if 'function ' + function_name in line:
                    in_function = True
                    brace_count = line.count('{') - line.count('}')
                elif in_function:
                    brace_count += line.count('{') - line.count('}')
                    
                    if brace_count <= 0 and '}' in line:
                        # Function ends here
                        functions_to_remove.append((start_idx, i + 1))
                        break
        
        # Remove duplicates in reverse order to maintain indices
        for start, end in reversed(functions_to_remove):
            print(f"   Removing duplicate function at lines {start+1}-{end}")
            del lines[start:end]
        
        content = '\n'.join(lines)
        
        # Write back
        print("4️⃣ Writing fixed functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        # Verify syntax
        print("5️⃣ Verifying syntax...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ Syntax fixed!")
        else:
            print(f"   ⚠️  Still has errors:")
            print(syntax_result[:500])
        
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
    success = fix_duplicate()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

