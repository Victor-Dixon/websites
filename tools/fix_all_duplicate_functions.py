#!/usr/bin/env python3
"""
Fix ALL Duplicate Functions in functions.php
=============================================

Finds and removes all duplicate function declarations.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_all_duplicates():
    """Fix all duplicate functions."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING ALL DUPLICATE FUNCTIONS: {site_name}")
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
        
        # Find all function declarations
        print("2️⃣ Finding all function declarations...")
        function_pattern = r'function\s+(\w+)\s*\('
        functions = {}
        
        for match in re.finditer(function_pattern, content):
            func_name = match.group(1)
            line_num = content[:match.start()].count('\n') + 1
            if func_name not in functions:
                functions[func_name] = []
            functions[func_name].append((match.start(), match.end(), line_num))
        
        # Find duplicates
        duplicates = {name: positions for name, positions in functions.items() if len(positions) > 1}
        
        if not duplicates:
            print("   ✅ No duplicate functions found")
            return True
        
        print(f"   Found {len(duplicates)} functions with duplicates:")
        for func_name, positions in duplicates.items():
            line_nums = [p[2] for p in positions]
            print(f"   - {func_name}: lines {line_nums}")
        
        # Remove duplicates - keep first, remove rest
        print()
        print("3️⃣ Removing duplicate functions...")
        lines = content.split('\n')
        lines_to_remove = set()
        
        for func_name, positions in duplicates.items():
            # Keep first occurrence, mark rest for removal
            for start_pos, end_pos, line_num in positions[1:]:  # Skip first
                print(f"   Removing duplicate {func_name} at line {line_num}")
                
                # Find the function definition line
                start_line = content[:start_pos].count('\n')
                
                # Find where this function ends by counting braces
                brace_count = 0
                in_function = False
                remove_start = start_line
                remove_end = start_line + 1
                
                for i in range(start_line, len(lines)):
                    line = lines[i]
                    
                    if f'function {func_name}(' in line:
                        in_function = True
                        brace_count = line.count('{') - line.count('}')
                    elif in_function:
                        brace_count += line.count('{') - line.count('}')
                        
                        if brace_count <= 0 and '}' in line:
                            remove_end = i + 1
                            break
                
                # Mark lines for removal
                for i in range(remove_start, remove_end):
                    lines_to_remove.add(i)
        
        # Remove marked lines
        new_lines = [line for i, line in enumerate(lines) if i not in lines_to_remove]
        content = '\n'.join(new_lines)
        
        # Write back
        print("4️⃣ Writing fixed functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        # Verify syntax
        print("5️⃣ Verifying syntax...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ Syntax fixed! No errors found.")
            return True
        else:
            print(f"   ⚠️  Still has errors:")
            print(syntax_result[:500])
            return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = fix_all_duplicates()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

