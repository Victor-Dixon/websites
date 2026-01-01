#!/usr/bin/env python3
"""
Completely Fix Functions.php
=============================

Reads functions.php, removes broken rewrite rules, and adds them correctly.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import re

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_functions_completely():
    """Completely fix functions.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 COMPLETELY FIXING FUNCTIONS.PHP: {site_name}")
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
        
        # Remove all instances of the broken rewrite rules
        print("2️⃣ Removing broken rewrite rules...")
        
        # Remove the add_action calls that reference the function
        lines = content.split('\n')
        new_lines = []
        skip_until_end = False
        in_function = False
        brace_count = 0
        
        for i, line in enumerate(lines):
            # Skip lines that are part of the broken rewrite rules function
            if 'freerideinvestor_add_blog_rewrite_rules' in line:
                if 'function' in line and 'freerideinvestor_add_blog_rewrite_rules' in line:
                    # Start of function - skip it
                    in_function = True
                    brace_count = line.count('{') - line.count('}')
                    continue
                elif 'add_action' in line and 'freerideinvestor_add_blog_rewrite_rules' in line:
                    # Skip add_action lines
                    continue
                elif in_function:
                    # Count braces to know when function ends
                    brace_count += line.count('{') - line.count('}')
                    if brace_count <= 0:
                        in_function = False
                    continue
            
            new_lines.append(line)
        
        content = '\n'.join(new_lines)
        
        # Add correct rewrite rules at the end
        print("3️⃣ Adding correct rewrite rules...")
        rewrite_code = """

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
        
        # Append before closing PHP tag or at end
        if content.strip().endswith('?>'):
            content = content.replace('?>', rewrite_code + '\n?>')
        else:
            content = content.rstrip() + rewrite_code
        
        # Write back
        print("4️⃣ Writing fixed functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(content.encode('utf-8'))
        
        print("✅ Functions.php fixed successfully")
        
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
    success = fix_functions_completely()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

