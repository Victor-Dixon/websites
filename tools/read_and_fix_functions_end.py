#!/usr/bin/env python3
"""
Read and Fix Functions.php End Section
=======================================

Reads the end of functions.php and fixes the structure properly.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def read_and_fix():
    """Read and fix functions.php end section."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 READING AND FIXING FUNCTIONS.PHP END: {site_name}")
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
        
        print("1️⃣ Reading last 50 lines of functions.php...")
        tail_cmd = f"tail -50 {functions_file}"
        tail_result = deployer.execute_command(tail_cmd)
        print("Last 50 lines:")
        print(tail_result)
        print()
        
        # Read full file
        print("2️⃣ Reading full functions.php...")
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        with deployer.sftp.open(functions_file, 'r') as f:
            content = f.read().decode('utf-8')
        
        # Find where the rewrite rules section starts and ends
        lines = content.split('\n')
        
        # Find the last occurrence of our rewrite rules code
        rewrite_start = -1
        for i in range(len(lines) - 1, -1, -1):
            if 'freerideinvestor_add_blog_rewrite_rules' in lines[i] and 'function' in lines[i]:
                rewrite_start = i
                break
        
        if rewrite_start >= 0:
            print(f"   Found rewrite rules starting at line {rewrite_start + 1}")
            print(f"   Lines {rewrite_start + 1} to {len(lines)}:")
            print('\n'.join(lines[rewrite_start:]))
            print()
            
            # Count braces from rewrite_start to end
            brace_count = 0
            for i in range(rewrite_start, len(lines)):
                brace_count += lines[i].count('{') - lines[i].count('}')
            
            print(f"   Brace balance from rewrite section to end: {brace_count}")
            
            # Fix: ensure proper closing
            # The structure should be:
            # if (!function_exists(...)) {
            #     function ...() {
            #         ...
            #     }
            #     add_action(...);
            # }
            
            # Check if we need to add closing brace
            if brace_count > 0:
                print(f"   ⚠️  Missing {brace_count} closing brace(s)")
                # Add at the end (before PHP closing tag if exists)
                if content.strip().endswith('?>'):
                    content = content.replace('?>', '}' * brace_count + '\n?>')
                else:
                    content = content.rstrip() + '\n' + '}' * brace_count
            elif brace_count < 0:
                print(f"   ⚠️  Extra {abs(brace_count)} closing brace(s)")
        
        # Write back
        print("3️⃣ Writing fixed functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        # Verify
        print("4️⃣ Verifying syntax...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        print(syntax_result)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ Syntax fixed!")
        else:
            print("   ⚠️  Still has errors")
        
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
    success = read_and_fix()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

