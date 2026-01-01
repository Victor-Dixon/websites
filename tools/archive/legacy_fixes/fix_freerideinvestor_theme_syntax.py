#!/usr/bin/env python3
"""
Fix freerideinvestor-modern Theme Syntax Error
==============================================

Fixes syntax error in functions.php line 209.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_theme_syntax_error():
    """Fix syntax error in theme functions.php."""
    print("=" * 70)
    print("🔧 FIXING FREERIDEINVESTOR-MODERN THEME SYNTAX ERROR")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return 1
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return 1
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        functions_file = f"{theme_path}/functions.php"
        
        print("📖 Reading functions.php...")
        # Read functions.php
        command = f"cat {functions_file}"
        functions_content = deployer.execute_command(command)
        
        if not functions_content:
            print("❌ Cannot read functions.php")
            return 1
        
        # Create backup
        print("💾 Creating backup...")
        backup_result = deployer.execute_command(f"cp {functions_file} {functions_file}.backup.$(date +%Y%m%d_%H%M%S)")
        print("   ✅ Backup created")
        
        # Check syntax
        print("🔍 Checking syntax...")
        syntax_check = deployer.execute_command(f"php -l {functions_file} 2>&1")
        print(f"   Syntax check: {syntax_check[:300]}")
        
        # Read around line 209
        lines = functions_content.split('\n')
        print(f"\n📝 Lines around 209:")
        start_line = max(0, 204)  # Show lines 205-215
        end_line = min(len(lines), 215)
        for i in range(start_line, end_line):
            marker = ">>> " if i == 208 else "    "  # Line 209 is index 208
            print(f"{marker}{i+1:4d}: {lines[i]}")
        
        # Common fix: Check for invalid function name with hyphen
        # PHP function names cannot have hyphens, they must use underscores
        print("\n🔧 Attempting to fix syntax error...")
        print("   Error: 'unexpected token \"-\", expecting \"(\"'")
        print("   Likely cause: Function name with hyphen (e.g., function-name())")
        print("   Fix: Replace hyphen with underscore in function name")
        
        # Try to find and fix the issue
        fixed_lines = []
        fixed = False
        import re
        
        for i, line in enumerate(lines):
            original_line = line
            
            # Fix function names and variables with hyphens (lines 209-213)
            if 208 <= i <= 212:  # Lines 209-213
                # Fix function name
                if 'function' in line and '-' in line:
                    # Pattern: function name-with-hyphen(
                    pattern = r'function\s+([a-zA-Z_][a-zA-Z0-9_-]*)\s*\('
                    match = re.search(pattern, line)
                    if match:
                        func_name = match.group(1)
                        if '-' in func_name:
                            fixed_func_name = func_name.replace('-', '_')
                            line = line.replace(f'function {func_name}(', f'function {fixed_func_name}(')
                            print(f"   ✅ Fixed function name: {func_name} → {fixed_func_name}")
                            fixed = True
                
                # Fix variable names with hyphens ($var-name)
                # Pattern: $variable-name
                var_pattern = r'\$([a-zA-Z_][a-zA-Z0-9_-]*)'
                matches = re.finditer(var_pattern, line)
                for match in matches:
                    var_name = match.group(1)
                    if '-' in var_name:
                        fixed_var_name = var_name.replace('-', '_')
                        line = line.replace(f'${var_name}', f'${fixed_var_name}')
                        print(f"   ✅ Fixed variable name: ${var_name} → ${fixed_var_name}")
                        fixed = True
            
            fixed_lines.append(line)
        
        if not fixed:
            # Try alternative: might be a method call or variable with hyphen
            for i, line in enumerate(lines):
                if i == 208:
                    # Check for variable or method calls with hyphens
                    if '->' in line or '::' in line:
                        # Might be a method call issue
                        print("   ⚠️  Line 209 contains method call - manual review needed")
                        print(f"   Line content: {line}")
                        # Save for manual review
                        local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_theme_functions_around_209.php"
                        local_file.parent.mkdir(parents=True, exist_ok=True)
                        context_lines = '\n'.join(lines[max(0, i-5):min(len(lines), i+6)])
                        local_file.write_text(context_lines, encoding='utf-8')
                        print(f"   📄 Context saved to: {local_file}")
                        print("   💡 Review file and fix manually, then deploy")
                        return 1
        
        if fixed:
            fixed_content = '\n'.join(fixed_lines)
            
            # Save fixed file locally
            local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_theme_functions_fixed.php"
            local_file.parent.mkdir(parents=True, exist_ok=True)
            local_file.write_text(fixed_content, encoding='utf-8')
            print(f"   ✅ Fixed file saved to: {local_file}")
            
            # Deploy fixed file
            print("🚀 Deploying fixed functions.php...")
            success = deployer.deploy_file(local_file, functions_file)
            
            if success:
                print("   ✅ Fixed functions.php deployed")
                
                # Verify syntax
                print("🔍 Verifying syntax...")
                syntax_check = deployer.execute_command(f"php -l {functions_file} 2>&1")
                if "No syntax errors" in syntax_check or "syntax is OK" in syntax_check:
                    print("   ✅ functions.php syntax is now valid!")
                    print()
                    print("🌐 Testing site...")
                    import requests
                    try:
                        response = requests.get("https://freerideinvestor.com", timeout=10)
                        if response.status_code == 200:
                            print("   ✅ Site is now accessible (HTTP 200)")
                            print("   🎉 Fix successful!")
                        else:
                            print(f"   ⚠️  Site returned HTTP {response.status_code}")
                            print("   📝 Check site manually")
                    except Exception as e:
                        print(f"   ⚠️  Could not test site: {e}")
                else:
                    print(f"   ⚠️  Syntax check result: {syntax_check[:200]}")
                    print("   📝 Review fixed file and deploy manually if needed")
            else:
                print("   ❌ Failed to deploy fixed config")
                return 1
        else:
            print("   ⚠️  Could not automatically fix - manual intervention needed")
            return 1
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(fix_theme_syntax_error())

