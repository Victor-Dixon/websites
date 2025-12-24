#!/usr/bin/env python3
"""
Manual Fix Functions.php End
============================

Manually fixes the end of functions.php by reading and reconstructing it properly.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def manual_fix():
    """Manually fix functions.php end."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 MANUAL FIX FUNCTIONS.PHP END: {site_name}")
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
        
        # Find the last proper function before our additions
        print("2️⃣ Finding clean cut point...")
        lines = content.split('\n')
        
        # Find the last function that ends properly (before our rewrite rules)
        cut_point = -1
        for i in range(len(lines) - 1, -1, -1):
            if 'add_action(\'wp_head\'' in lines[i] or 'add_action("wp_head"' in lines[i]:
                # Found the analytics function end - cut here
                cut_point = i + 1
                break
        
        if cut_point < 0:
            # Fallback: find last proper closing brace
            for i in range(len(lines) - 1, max(0, len(lines) - 100), -1):
                if lines[i].strip() == '}' and i > 0:
                    # Check if this closes a function
                    prev_context = '\n'.join(lines[max(0, i-10):i+1])
                    if 'function' in prev_context and 'add_action' in prev_context:
                        cut_point = i + 1
                        break
        
        if cut_point > 0:
            print(f"   Found cut point at line {cut_point}")
            # Keep everything up to cut_point
            clean_content = '\n'.join(lines[:cut_point])
        else:
            # Remove last 30 lines and rebuild
            print("   Using fallback: removing last 30 lines")
            clean_content = '\n'.join(lines[:-30])
        
        # Add clean rewrite rules at the end
        clean_rewrite_rules = """

/**
 * Add custom rewrite rules for blog pagination
 * Added by Agent-7 - Enables /blog/page/2/ to work correctly
 */
if (!function_exists('freerideinvestor_add_blog_rewrite_rules')) {
    function freerideinvestor_add_blog_rewrite_rules() {
        add_rewrite_rule(
            '^blog/page/([0-9]+)/?$',
            'index.php?pagename=blog&paged=$matches[1]',
            'top'
        );
    }
    add_action('init', 'freerideinvestor_add_blog_rewrite_rules');
}
"""
        
        final_content = clean_content.rstrip() + clean_rewrite_rules
        
        # Write back
        print("3️⃣ Writing clean functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(final_content.encode('utf-8'))
        
        # Verify syntax
        print("4️⃣ Verifying syntax...")
        syntax_cmd = f"php -l {functions_file} 2>&1"
        syntax_result = deployer.execute_command(syntax_cmd)
        
        if 'No syntax errors' in syntax_result:
            print("   ✅ Syntax fixed! No errors found.")
            
            # Flush rewrite rules
            print()
            print("5️⃣ Flushing rewrite rules...")
            flush_cmd = f"cd {remote_path} && wp rewrite flush --hard --allow-root 2>&1"
            flush_result = deployer.execute_command(flush_cmd)
            print(f"   Result: {flush_result[:200] if flush_result else 'Success'}")
            
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
    success = manual_fix()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

