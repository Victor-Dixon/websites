#!/usr/bin/env python3
"""
Fix Missing Closing Brace
==========================

Fixes the missing closing brace in functions.php.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_missing_brace():
    """Fix missing closing brace."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING MISSING CLOSING BRACE: {site_name}")
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
        
        # Check if the if statement is closed
        print("2️⃣ Checking for missing closing brace...")
        if 'add_action(\'init\', \'freerideinvestor_add_blog_rewrite_rules\');' in content:
            # Find where this line is and check if there's a closing brace after it
            lines = content.split('\n')
            for i, line in enumerate(lines):
                if 'add_action(\'init\', \'freerideinvestor_add_blog_rewrite_rules\');' in line:
                    # Check next few lines for closing brace
                    next_lines = '\n'.join(lines[i+1:i+5])
                    if '}' not in next_lines:
                        print(f"   ⚠️  Missing closing brace after line {i+1}")
                        # Add closing brace after add_action
                        lines.insert(i+1, '}')
                        content = '\n'.join(lines)
                        break
        
        # Write back
        print("3️⃣ Writing fixed functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        # Verify syntax
        print("4️⃣ Verifying syntax...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ Syntax fixed! No errors found.")
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
    success = fix_missing_brace()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

