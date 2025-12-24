#!/usr/bin/env python3
"""
Remove Duplicate Rewrite Rules
===============================

Removes the broken duplicate rewrite rules section.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def remove_duplicates():
    """Remove duplicate rewrite rules."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 REMOVING DUPLICATE REWRITE RULES: {site_name}")
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
        
        lines = content.split('\n')
        new_lines = []
        skip_until_end = False
        in_broken_section = False
        brace_count = 0
        
        print("2️⃣ Removing broken duplicate section...")
        
        for i, line in enumerate(lines):
            # Detect start of broken section (has add_rewrite_rule but no function wrapper before it)
            if 'Add custom rewrite rules for blog pagination' in line and i > 0:
                # Check if previous lines have the function definition
                prev_context = '\n'.join(lines[max(0, i-5):i])
                if 'function freerideinvestor_add_blog_rewrite_rules' not in prev_context and 'if (!function_exists' not in prev_context:
                    # This is the broken section - skip it
                    in_broken_section = True
                    brace_count = 0
                    print(f"   Found broken section starting at line {i+1}")
                    continue
            
            if in_broken_section:
                # Count braces to know when section ends
                brace_count += line.count('{') - line.count('}')
                
                # Skip until we find the flush_rewrite_rules function (end of broken section)
                if 'freerideinvestor_flush_rewrite_rules' in line and 'function' in line:
                    # End of broken section, but keep this function
                    in_broken_section = False
                    new_lines.append(line)
                elif brace_count < 0 and '}' in line:
                    # Extra closing brace from broken section
                    in_broken_section = False
                    # Don't add this line
                    continue
                else:
                    # Skip lines in broken section
                    continue
            
            new_lines.append(line)
        
        content = '\n'.join(new_lines)
        
        # Write back
        print("3️⃣ Writing cleaned functions.php...")
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
            return False
        
        # Flush rewrite rules
        print()
        print("5️⃣ Flushing rewrite rules...")
        flush_cmd = f"cd {remote_path} && wp rewrite flush --hard --allow-root 2>&1"
        flush_result = deployer.execute_command(flush_cmd)
        print(f"   Result: {flush_result[:200] if flush_result else 'Success'}")
        
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
    success = remove_duplicates()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

