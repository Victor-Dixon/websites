#!/usr/bin/env python3
"""
Create freerideinvestor.com functions.php with menu fix
=======================================================

Creates a new functions.php file with the menu navigation fix included.
"""

import sys
from pathlib import Path

# Read the menu fix
fix_file = Path(__file__).parent.parent / "docs" / "freerideinvestor" / "freerideinvestor_menu_fix_FINAL.php"
with open(fix_file, 'r', encoding='utf-8') as f:
    menu_fix = f.read()
    # Remove opening <?php tag if present (we already have one)
    if menu_fix.strip().startswith('<?php'):
        menu_fix = menu_fix.replace('<?php', '', 1).lstrip()

# Base functions.php content
base_functions = """<?php
/**
 * Theme Functions
 * FreeRideInvestor Modern Theme
 */

/**
 * Add custom rewrite rules for blog pagination
 */
function freerideinvestor_add_blog_rewrite_rules() {
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

/**
 * Fix Text Rendering Issues
 */
function fix_text_rendering_issues($content) {
    if (empty($content)) {
        return $content;
    }
    
    $patterns = [
        '/ha\s{2,}been/i' => 'has been',
        '/thi\s{2,}web/i' => 'this web',
        '/trouble\s{2,}hooting/i' => 'troubleshooting',
        '/WordPre\s{2,}/i' => 'WordPress',
        '/web\s{2,}ite/i' => 'website',
        '/\s{3,}/' => ' ',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    return $content;
}
add_filter('the_content', 'fix_text_rendering_issues');
add_filter('the_excerpt', 'fix_text_rendering_issues');

"""

# Combine base + menu fix
full_functions = base_functions + "\n" + menu_fix

# Save
output_file = Path(__file__).parent.parent / "docs" / "freerideinvestor" / "functions.php"
with open(output_file, 'w', encoding='utf-8') as f:
    f.write(full_functions)

print(f"✅ Created functions.php: {output_file}")
print(f"   Total length: {len(full_functions)} characters")

