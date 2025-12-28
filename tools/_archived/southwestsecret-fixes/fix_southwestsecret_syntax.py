#!/usr/bin/env python3
"""
Fix southwestsecret.com Syntax Error
=====================================

Fixes the unmatched '}' syntax error in functions.php line 2159.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_syntax_error():
    """Fix the syntax error in southwestsecret theme functions.php."""
    print("=" * 70)
    print("🔧 FIXING SYNTAX ERROR: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        functions_file = f"{remote_path}/wp-content/themes/southwestsecret/functions.php"
        
        print("📖 Reading functions.php...")
        read_cmd = f"cat {functions_file}"
        functions_content = deployer.execute_command(read_cmd)
        
        if not functions_content:
            print("❌ Could not read functions.php")
            return False
        
        # Save backup
        print("💾 Creating backup...")
        backup_cmd = f"cp {functions_file} {functions_file}.backup.$(date +%Y%m%d_%H%M%S)"
        deployer.execute_command(backup_cmd)
        print("   ✅ Backup created")
        
        # Count braces to find the mismatch
        print("🔍 Analyzing brace balance...")
        lines = functions_content.split('\n')
        
        open_braces = 0
        close_braces = 0
        brace_stack = []
        
        for i, line in enumerate(lines, 1):
            line_open = line.count('{')
            line_close = line.count('}')
            
            open_braces += line_open
            close_braces += line_close
            
            # Track brace positions
            for char in line:
                if char == '{':
                    brace_stack.append(i)
                elif char == '}':
                    if brace_stack:
                        brace_stack.pop()
                    else:
                        print(f"   ⚠️  Extra closing brace found on line {i}")
        
        print(f"   Open braces: {open_braces}")
        print(f"   Close braces: {close_braces}")
        print(f"   Unmatched opens: {len(brace_stack)}")
        
        if open_braces > close_braces:
            print(f"   ⚠️  Missing {open_braces - close_braces} closing brace(s)")
            print(f"   Last unmatched open brace around line: {brace_stack[-1] if brace_stack else 'unknown'}")
        elif close_braces > open_braces:
            print(f"   ⚠️  Extra {close_braces - open_braces} closing brace(s)")
        
        # Check around line 2159 specifically
        print(f"\n🔍 Checking around line 2159...")
        start_line = max(0, 2150)
        end_line = min(len(lines), 2170)
        
        print(f"   Lines {start_line+1} to {end_line}:")
        for i in range(start_line, end_line):
            line_num = i + 1
            line = lines[i]
            open_count = line.count('{')
            close_count = line.count('}')
            
            if open_count > 0 or close_count > 0:
                print(f"      Line {line_num}: {open_count} open, {close_count} close - {line.strip()[:60]}")
        
        # Try to fix by removing the extra closing brace on line 2159
        print(f"\n🔧 Attempting to fix...")
        
        # Read the specific problematic area
        problem_line = lines[2158] if len(lines) > 2158 else ""  # Line 2159 (0-indexed)
        
        print(f"   Line 2159 content: {problem_line[:100]}")
        
        # Strategy: If there's an extra closing brace, remove it
        # But we need to be careful - let's check the context
        
        # Better approach: Use PHP's built-in syntax checker to find the exact issue
        print(f"\n🔍 Getting detailed PHP error...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        print(f"   PHP error: {syntax_result}")
        
        # Try a safer approach: read the file around the error and manually fix
        # For now, let's try to find and remove duplicate closing braces near line 2159
        
        fixed_lines = lines.copy()
        fixed = False
        
        # Check if line 2159 has an extra closing brace
        if len(fixed_lines) > 2158:
            line_2159 = fixed_lines[2158]
            
            # Count braces in this line
            if line_2159.count('}') > line_2159.count('{') + 1:
                # Likely has an extra closing brace
                # Remove one closing brace (but be careful)
                print(f"   ⚠️  Line 2159 appears to have extra closing brace")
                print(f"   Attempting to fix by removing one closing brace from line 2159...")
                
                # Find the last '}' and remove it (if safe)
                last_brace_pos = line_2159.rfind('}')
                if last_brace_pos > 0:
                    # Check if it's safe to remove (not part of a string)
                    before_brace = line_2159[:last_brace_pos]
                    after_brace = line_2159[last_brace_pos+1:]
                    
                    # Simple check: if there's no quote before it, it's probably safe
                    if "'" not in before_brace[-10:] and '"' not in before_brace[-10:]:
                        fixed_lines[2158] = before_brace + after_brace
                        fixed = True
                        print(f"   ✅ Removed extra closing brace from line 2159")
        
        if not fixed:
            # Alternative: try to add missing opening brace or remove extra closing brace
            # Let's try removing the line if it's just a closing brace
            if len(fixed_lines) > 2158:
                line_2159 = fixed_lines[2158].strip()
                if line_2159 == '}' or line_2159 == '};':
                    print(f"   ⚠️  Line 2159 is just a closing brace - checking if it's duplicate...")
                    # Check if previous line also ends with }
                    if len(fixed_lines) > 2157:
                        prev_line = fixed_lines[2157].strip()
                        if prev_line.endswith('}') or prev_line.endswith('};'):
                            print(f"   ✅ Removing duplicate closing brace on line 2159")
                            fixed_lines[2158] = ''
                            fixed = True
        
        if fixed:
            fixed_content = '\n'.join(fixed_lines)
            
            # Save locally
            local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_functions_fixed.php"
            local_file.parent.mkdir(parents=True, exist_ok=True)
            local_file.write_text(fixed_content, encoding='utf-8')
            
            # Deploy
            print(f"\n🚀 Deploying fixed functions.php...")
            success = deployer.deploy_file(local_file, functions_file)
            
            if success:
                print(f"   ✅ Fixed file deployed")
                
                # Verify syntax
                print(f"🔍 Verifying syntax...")
                syntax_cmd = f"php -l {functions_file} 2>&1"
                syntax_result = deployer.execute_command(syntax_cmd)
                
                if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                    print(f"   ✅ Syntax is now valid!")
                    print(f"\n✅ Fix successful! Site should now be accessible.")
                    return True
                else:
                    print(f"   ❌ Syntax error still present:")
                    print(f"   {syntax_result[:500]}")
                    return False
            else:
                print(f"   ❌ Failed to deploy fixed file")
                return False
        else:
            print(f"\n⚠️  Could not automatically fix the syntax error")
            print(f"   Manual intervention required")
            print(f"   Error location: Line 2159 in functions.php")
            print(f"   Issue: Unmatched closing brace")
            return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if fix_syntax_error() else 1)

