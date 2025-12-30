#!/usr/bin/env python3
"""
Fix Functions.php Syntax Error
================================

Fixes the unmatched brace error in functions.php.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_syntax_error():
    """Fix syntax error in functions.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING FUNCTIONS.PHP SYNTAX ERROR: {site_name}")
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
        
        # Check PHP syntax
        print("2️⃣ Checking PHP syntax...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ No syntax errors found")
            return True
        
        print(f"   ⚠️  Syntax error detected:")
        print(f"   {syntax_result[:300]}")
        
        # Count braces to find mismatch
        print()
        print("3️⃣ Analyzing brace balance...")
        open_braces = content.count('{')
        close_braces = content.count('}')
        print(f"   Open braces: {open_braces}")
        print(f"   Close braces: {close_braces}")
        print(f"   Difference: {open_braces - close_braces}")
        
        if open_braces > close_braces:
            missing = open_braces - close_braces
            print(f"   ⚠️  Missing {missing} closing brace(s)")
            # Add missing closing braces at the end (before PHP closing tag)
            if content.strip().endswith('?>'):
                content = content.replace('?>', '}' * missing + '\n?>')
            else:
                content = content + '\n' + '}' * missing
        elif close_braces > open_braces:
            extra = close_braces - open_braces
            print(f"   ⚠️  Extra {extra} closing brace(s)")
            # Remove extra closing braces from the end
            lines = content.split('\n')
            removed = 0
            new_lines = []
            for line in reversed(lines):
                if removed < extra and line.strip() == '}':
                    removed += 1
                    continue
                new_lines.insert(0, line)
            content = '\n'.join(new_lines)
        
        # Write back
        print("4️⃣ Writing fixed functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        # Verify syntax again
        print("5️⃣ Verifying syntax...")
        syntax_result = deployer.execute_command(syntax_cmd)
        if 'No syntax errors' in syntax_result:
            print("   ✅ Syntax fixed!")
        else:
            print(f"   ⚠️  Still has errors: {syntax_result[:300]}")
        
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
    success = fix_syntax_error()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())



