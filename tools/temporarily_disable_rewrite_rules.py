#!/usr/bin/env python3
"""
Temporarily Disable Rewrite Rules
==================================

Comments out all rewrite rules to get site working, then we'll add them back properly.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def disable_rewrite_rules():
    """Temporarily disable rewrite rules."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 TEMPORARILY DISABLING REWRITE RULES: {site_name}")
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
        
        # Find and comment out ALL rewrite rules sections
        print("2️⃣ Commenting out all rewrite rules sections...")
        
        lines = content.split('\n')
        new_lines = []
        comment_block = False
        
        for i, line in enumerate(lines):
            # Detect rewrite rules sections
            if 'freerideinvestor_add_blog_rewrite_rules' in line or 'freerideinvestor_flush_rewrite_rules' in line:
                if 'Add custom rewrite rules' in line or 'Flush rewrite rules' in line:
                    # Start of section - comment it
                    comment_block = True
                    new_lines.append('/* ' + line)
                elif comment_block:
                    # Inside comment block
                    if line.strip() == '}' and i < len(lines) - 1:
                        # End of function - close comment
                        new_lines.append(line + ' */')
                        comment_block = False
                    else:
                        new_lines.append(line)
                else:
                    # Just comment this line
                    new_lines.append('// ' + line)
            elif comment_block:
                # Continue comment block
                if line.strip() == '}' and 'add_action' not in '\n'.join(lines[max(0, i-3):i+1]):
                    new_lines.append(line + ' */')
                    comment_block = False
                else:
                    new_lines.append(line)
            else:
                new_lines.append(line)
        
        content = '\n'.join(new_lines)
        
        # Write back
        print("3️⃣ Writing functions.php with rewrite rules disabled...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        # Verify syntax
        print("4️⃣ Verifying syntax...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ Syntax fixed! Site should work now.")
            print()
            print("💡 Rewrite rules are temporarily disabled.")
            print("   Blog pagination may not work, but site should load.")
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
    success = disable_rewrite_rules()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

