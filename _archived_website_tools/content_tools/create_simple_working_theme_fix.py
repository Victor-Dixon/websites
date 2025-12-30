#!/usr/bin/env python3
"""
Create Simple Working Theme Fix for freerideinvestor.com
========================================================

Since the theme has complex issues, creates a minimal working version
by copying structure from working default theme.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def create_minimal_functions_php(deployer):
    """Create a minimal functions.php based on default theme structure."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("=" * 70)
    print("CREATING MINIMAL FUNCTIONS.PHP")
    print("=" * 70)
    
    # Get current functions.php for backup
    current_content = deployer.execute_command(f"cat {functions_php}")
    backup_path = f"{functions_php}.backup.full"
    deployer.execute_command(f"cp {functions_php} {backup_path}")
    print(f"✅ Full backup created: {backup_path}")
    
    # Create minimal functions.php based on default theme structure
    minimal_functions = """<?php
/**
 * FreeRideInvestor Modern Theme Functions - MINIMAL WORKING VERSION
 *
 * @package FreeRideInvestor_Modern
 * @since 3.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function freerideinvestor_modern_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');
}
add_action('after_setup_theme', 'freerideinvestor_modern_setup');

/**
 * Enqueue Scripts and Styles
 */
function freerideinvestor_modern_scripts() {
    wp_enqueue_style('freerideinvestor-modern-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'freerideinvestor_modern_scripts');
"""
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_functions_minimal.php"
    local_file.write_text(minimal_functions, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, functions_php)
    
    if success:
        print("✅ Minimal functions.php deployed")
        # Verify syntax
        syntax_result = deployer.check_php_syntax(functions_php)
        if syntax_result.get('valid'):
            print("✅ Syntax is valid")
            return True
        else:
            print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
            # Restore backup
            deployer.execute_command(f"cp {backup_path} {functions_php}")
            print("⚠️  Restored backup")
            return False
    else:
        print("❌ Failed to deploy")
        return False


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        print("⚠️  WARNING: This will replace functions.php with a minimal version")
        print("   Full backup will be created")
        print()
        
        if create_minimal_functions_php(deployer):
            print("\n✅ Minimal functions.php deployed")
            print("\n📋 Testing site in 3 seconds...")
            import time
            import requests
            from bs4 import BeautifulSoup
            
            time.sleep(3)
            r = requests.get("https://freerideinvestor.com", timeout=10)
            soup = BeautifulSoup(r.text, 'html.parser')
            main = soup.find('main')
            
            if main:
                print("🎉 SUCCESS! Main tag found with minimal functions.php!")
                print(f"   Body text: {len(soup.find('body').get_text()) if soup.find('body') else 0} chars")
                print(f"   Articles: {len(soup.find_all('article'))}")
                print("\n✅ Issue was in functions.php - can now gradually add features back")
            else:
                print("❌ Main tag still missing")
                print("   Issue is not in functions.php")
                print("   May need to restore full functions.php")
                print(f"   Backup available at: functions.php.backup.full")
        
        return 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(main())






