#!/usr/bin/env python3
"""Test with minimal functions.php"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / 'ops' / 'deployment'))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

# Create minimal functions.php
minimal_functions = """<?php
/**
 * Minimal functions.php for debugging
 */

// Basic theme support
function freerideinvestor_minimal_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'freerideinvestor_minimal_setup');

// Enqueue basic styles
function freerideinvestor_minimal_scripts() {
    wp_enqueue_style('main-css', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'freerideinvestor_minimal_scripts');
"""

site_configs = load_site_configs()
deployer = SimpleWordPressDeployer('freerideinvestor.com', site_configs)

if deployer.connect():
    # Backup current functions.php
    print("Backing up current functions.php...")
    deployer.execute_command(
        'cd domains/freerideinvestor.com/public_html && '
        'cp wp-content/themes/freerideinvestor-modern/functions.php '
        'wp-content/themes/freerideinvestor-modern/functions.php.backup_500_debug'
    )
    
    # Write minimal functions.php
    import tempfile
    import os
    with tempfile.NamedTemporaryFile(mode='w', delete=False, suffix='.php') as f:
        f.write(minimal_functions)
        temp_path = f.name
    
    from pathlib import Path as P
    print("Deploying minimal functions.php...")
    deployer.deploy_file(P(temp_path), 'wp-content/themes/freerideinvestor-modern/functions.php')
    os.unlink(temp_path)
    
    print("✅ Minimal functions.php deployed")
    print("Test the site now. If it works, the issue is in functions.php")
    print("To restore: cp functions.php.backup_500_debug functions.php")
    
    deployer.disconnect()

