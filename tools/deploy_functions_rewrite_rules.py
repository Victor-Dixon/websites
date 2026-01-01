#!/usr/bin/env python3
"""
Deploy Functions.php Rewrite Rules
===================================

Adds rewrite rules to functions.php (appends if file exists).

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def deploy_rewrite_rules():
    """Add rewrite rules to functions.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 ADDING REWRITE RULES: {site_name}")
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
        
        # Read existing functions.php
        print("1️⃣ Reading existing functions.php...")
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        try:
            with deployer.sftp.open(functions_file, 'r') as f:
                existing_content = f.read().decode('utf-8')
        except FileNotFoundError:
            existing_content = "<?php\n"
        except Exception as e:
            print(f"⚠️  Could not read functions.php: {e}")
            existing_content = "<?php\n"
        
        # Check if rewrite rules already added
        if 'freerideinvestor_add_blog_rewrite_rules' in existing_content:
            print("✅ Rewrite rules already added")
            return True
        
        # Append rewrite rules
        rewrite_rules = """

/**
 * Add custom rewrite rules for blog pagination
 * Added by Agent-7 - Enables /blog/page/2/ to work correctly
 */
function freerideinvestor_add_blog_rewrite_rules() {
    // Add rewrite rule for blog pagination
    add_rewrite_rule(
        '^blog/page/([0-9]+)/?$',
        'index.php?pagename=blog&paged=$matches[1]',
        'top'
    );
}
add_action('init', 'freerideinvestor_add_blog_rewrite_rules');

/**
 * Flush rewrite rules on theme activation
 */
function freerideinvestor_flush_rewrite_rules() {
    freerideinvestor_add_blog_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'freerideinvestor_flush_rewrite_rules');
"""
        
        new_content = existing_content.rstrip() + rewrite_rules
        
        # Write back
        print("2️⃣ Writing updated functions.php...")
        with deployer.sftp.open(functions_file, 'w') as f:
            f.write(new_content.encode('utf-8'))
        
        print("✅ Rewrite rules added successfully")
        print()
        print("💡 Next step: Flush rewrite rules via WP-CLI")
        
        # Flush rewrite rules
        print("3️⃣ Flushing rewrite rules...")
        flush_cmd = f"cd {remote_path} && wp rewrite flush --allow-root 2>&1"
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
    success = deploy_rewrite_rules()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

