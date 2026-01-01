#!/usr/bin/env python3
"""
Check freerideinvestor-modern Theme Syntax
==========================================

Reads and validates the current functions.php file for syntax errors.

Author: Agent-3
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_theme_syntax():
    """Check theme functions.php for syntax errors."""
    print("=" * 70)
    print("🔍 CHECKING FREERIDEINVESTOR-MODERN THEME SYNTAX")
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
        command = f"cat {functions_file}"
        functions_content = deployer.execute_command(command)
        
        if not functions_content:
            print("❌ Cannot read functions.php")
            return 1
        
        # Save locally for inspection
        local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_theme_functions_current.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(functions_content, encoding='utf-8')
        print(f"   ✅ File saved locally: {local_file}")
        
        # Check syntax with PHP
        print("🔍 Checking PHP syntax...")
        syntax_check = deployer.execute_command(f"php -l {functions_file} 2>&1")
        print(f"   Result: {syntax_check}")
        
        if "No syntax errors" in syntax_check or "syntax is OK" in syntax_check:
            print("   ✅ Syntax is valid!")
        else:
            print("   ❌ Syntax errors found!")
            # Try to extract line number
            if "on line" in syntax_check:
                import re
                match = re.search(r'on line (\d+)', syntax_check)
                if match:
                    line_num = int(match.group(1))
                    print(f"   📍 Error on line {line_num}")
                    lines = functions_content.split('\n')
                    if line_num <= len(lines):
                        print(f"   Line {line_num}: {lines[line_num - 1]}")
                        if line_num > 1:
                            print(f"   Line {line_num - 1}: {lines[line_num - 2]}")
                        if line_num < len(lines):
                            print(f"   Line {line_num + 1}: {lines[line_num]}")
        
        # Check for common issues
        print("\n🔍 Checking for common issues...")
        lines = functions_content.split('\n')
        issues = []
        
        for i, line in enumerate(lines, 1):
            # Check for hyphens in variable/function names
            if re.search(r'\$[a-zA-Z_][a-zA-Z0-9_-]*-[a-zA-Z0-9_-]*', line):
                issues.append(f"Line {i}: Variable with hyphen: {line.strip()[:80]}")
            # Check for broken object operators
            if '_>' in line and '->' not in line:
                issues.append(f"Line {i}: Possible broken object operator: {line.strip()[:80]}")
            # Check for unclosed strings
            if line.count("'") % 2 != 0 or line.count('"') % 2 != 0:
                if not line.strip().startswith('//') and not line.strip().startswith('#'):
                    issues.append(f"Line {i}: Possible unclosed string: {line.strip()[:80]}")
        
        if issues:
            print(f"   ⚠️  Found {len(issues)} potential issues:")
            for issue in issues[:10]:  # Show first 10
                print(f"      - {issue}")
        else:
            print("   ✅ No obvious issues found")
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(check_theme_syntax())

