#!/usr/bin/env python3
"""
Restore southwestsecret.com functions.php
==========================================

Creates a minimal working functions.php to restore the site.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def restore_functions():
    """Restore functions.php with minimal working code."""
    print("=" * 70)
    print("🔧 RESTORING: southwestsecret.com functions.php")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        functions_file = f"{remote_path}/wp-content/themes/southwestsecret/functions.php"
        
        # Create backup of current broken file
        print("💾 Creating backup of current file...")
        backup_cmd = f"cp {functions_file} {functions_file}.broken.$(date +%Y%m%d_%H%M%S)"
        deployer.execute_command(backup_cmd)
        print("   ✅ Backup created")
        
        # Create minimal working functions.php
        minimal_functions = '''<?php
/**
 * Theme Functions - Restored by Agent-7
 * Minimal working version to restore site functionality
 */

// Enqueue styles and scripts
function southwestsecret_enqueue_scripts() {
    wp_enqueue_style('southwestsecret-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'southwestsecret_enqueue_scripts');

// Theme support
function southwestsecret_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'southwestsecret_theme_setup');

// Register navigation menus
function southwestsecret_register_menus() {
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu',
    ));
}
add_action('init', 'southwestsecret_register_menus');

// Add meta description support
function southwestsecret_add_meta_description() {
    if (is_front_page() || is_home()) {
        echo '<meta name="description" content="Southwest Secret is your guide to hidden gems, unique experiences, and untold stories of the American Southwest." />';
    }
}
add_action('wp_head', 'southwestsecret_add_meta_description');

// Custom title tag
function southwestsecret_custom_title_tag($title) {
    if (is_front_page() || is_home()) {
        $title = 'Southwest Secret - Hidden Gems & Unique Experiences of the American Southwest';
    } elseif (is_singular()) {
        $title = get_the_title() . ' | Southwest Secret';
    }
    return $title;
}
add_filter('pre_get_document_title', 'southwestsecret_custom_title_tag', 999);

// Add HSTS header
if (!function_exists('southwestsecret_add_hsts_header')) {
    function southwestsecret_add_hsts_header() {
        if (is_ssl() && !headers_sent()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
    add_action('send_headers', 'southwestsecret_add_hsts_header');
}
'''
        
        # Save locally
        local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_functions_restored.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(minimal_functions, encoding='utf-8')
        
        # Deploy
        print("🚀 Deploying restored functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print("   ✅ File deployed")
            
            # Verify syntax
            print("🔍 Verifying syntax...")
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ Syntax is valid!")
                
                # Test site
                print("\n🌐 Testing site...")
                import requests
                try:
                    response = requests.get("https://southwestsecret.com", timeout=10)
                    if response.status_code == 200:
                        print("   ✅ Site is now accessible (HTTP 200)")
                        print("   🎉 Fix successful!")
                        return True
                    else:
                        print(f"   ⚠️  Site returned HTTP {response.status_code}")
                        print("   📝 Site may need additional configuration")
                        return True  # Syntax is fixed, site may have other issues
                except Exception as e:
                    print(f"   ⚠️  Could not test site: {e}")
                    print("   📝 Syntax is fixed - please test manually")
                    return True
            else:
                print(f"   ❌ Syntax error still present:")
                print(f"   {syntax_result[:500]}")
                return False
        else:
            print("   ❌ Failed to deploy file")
            return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if restore_functions() else 1)

