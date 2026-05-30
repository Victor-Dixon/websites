<?php
/**
 * Add custom rewrite rules for blog pagination
 * 
 * This ensures /blog/page/2/ works correctly with page templates
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

/**
 * Create default navigation menu for FreeRideInvestor
 */
/**
 * Theme setup for FreeRideInvestor
 */
function freerideinvestor_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'freerideinvestor'),
    ));
}
add_action('after_setup_theme', 'freerideinvestor_theme_setup');

/**
 * Create default navigation menu for FreeRideInvestor
 */
function freerideinvestor_create_default_menu() {
    // Check if menu already exists
    $menu_name = 'FreeRideInvestor Primary';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);

        if (!is_wp_error($menu_id)) {
            // Add menu items
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Home',
                'menu-item-url' => home_url('/'),
                'menu-item-status' => 'publish',
            ));

            // Add links to custom post type archives
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Free Investors',
                'menu-item-url' => get_post_type_archive_link('free_investor'),
                'menu-item-status' => 'publish',
            ));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Cheat Sheets',
                'menu-item-url' => get_post_type_archive_link('cheat_sheet'),
                'menu-item-status' => 'publish',
            ));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'TBOW Tactics',
                'menu-item-url' => get_post_type_archive_link('tbow_tactics'),
                'menu-item-status' => 'publish',
            ));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Trading Strategies',
                'menu-item-url' => home_url('/trading-strategies/'),
                'menu-item-status' => 'publish',
            ));

            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Contact',
                'menu-item-url' => home_url('/contact/'),
                'menu-item-status' => 'publish',
            ));

            // Assign to primary location
            $locations = get_theme_mod('nav_menu_locations');
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}
add_action('after_setup_theme', 'freerideinvestor_create_default_menu', 20);


/**
 * Fix Text Rendering Issues
 * Fixes broken text patterns (missing spaces, corrupted characters)
 */
function fix_text_rendering_issues($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Common broken patterns: "ha  been" -> "has been", "thi  web" -> "this web", etc.
    $patterns = [
        // Fix double spaces in common words
        '/ha\s{2,}been/i' => 'has been',
        '/thi\s{2,}web/i' => 'this web',
        '/trouble\s{2,}hooting/i' => 'troubleshooting',
        '/WordPre\s{2,}/i' => 'WordPress',
        '/web\s{2,}ite/i' => 'website',
        
        // Fix corrupted spacing in common phrases
        '/\s{3,}/' => ' ', // Replace 3+ spaces with single space
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    return $content;
}
add_filter('the_content', 'fix_text_rendering_issues');
add_filter('widget_text', 'fix_text_rendering_issues');
add_filter('get_the_excerpt', 'fix_text_rendering_issues');

/**
 * Enqueue inline CSS for text rendering fixes
 */
function freerideinvestor_text_rendering_styles() {
    $css = '
        body, p, h1, h2, h3, h4, h5, h6, a, span, div, li, td, th {
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            font-variant-ligatures: none !important;
            font-feature-settings: normal !important;
        }
    ';
    wp_add_inline_style('freerideinvestor-modern-style', $css);
}
add_action('wp_enqueue_scripts', 'freerideinvestor_text_rendering_styles', 999);
