#!/usr/bin/env python3
"""
Fix Functions.php Rewrite Rules
================================

Properly adds rewrite rules to functions.php by reading existing file first.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_rewrite_rules():
    """Fix rewrite rules in functions.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 FIXING FUNCTIONS.PHP REWRITE RULES: {site_name}")
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
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        functions_file = f"{theme_path}/functions.php"
        
        # Read functions.php
        print("1️⃣ Reading functions.php...")
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        try:
            with deployer.sftp.open(functions_file, 'r') as f:
                content = f.read().decode('utf-8')
        except Exception as e:
            print(f"❌ Failed to read functions.php: {e}")
            return False
        
        # Check if function already exists
        if 'function freerideinvestor_add_blog_rewrite_rules' in content:
            print("   ✅ Rewrite rules function already exists")
            # Just remove the duplicate add_action if it exists multiple times
            return True
        
        # Remove any broken add_action calls for the function
        lines = content.split('\n')
        new_lines = []
        skip_next_add_action = False
        
        for line in lines:
            if 'freerideinvestor_add_blog_rewrite_rules' in line and 'add_action' in line:
                # Skip this add_action if function doesn't exist
                if 'function freerideinvestor_add_blog_rewrite_rules' not in content:
                    continue
            new_lines.append(line)
        
        content = '\n'.join(new_lines)
        
        # Add rewrite rules function at the end (before closing PHP tag if exists)
        rewrite_code = """

/**
 * Add custom rewrite rules for blog pagination
 * Added by Agent-7 - Enables /blog/page/2/ to work correctly
 */
if (!function_exists('freerideinvestor_add_blog_rewrite_rules')) {
    function freerideinvestor_add_blog_rewrite_rules() {
        // Add rewrite rule for blog pagination
        add_rewrite_rule(
            '^blog/page/([0-9]+)/?$',
            'index.php?pagename=blog&paged=$matches[1]',
            'top'
        );
    }
    add_action('init', 'freerideinvestor_add_blog_rewrite_rules');
}
"""
        
        # Append before closing PHP tag or at end
        if content.strip().endswith('?>'):
            content = content.replace('?>', rewrite_code + '\n?>')
        else:
            content = content.rstrip() + rewrite_code
        
        # Write back
        print("2️⃣ Writing updated functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        print("✅ Rewrite rules added successfully")
        
        # Flush rewrite rules
        print("3️⃣ Flushing rewrite rules...")
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
    success = fix_rewrite_rules()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

