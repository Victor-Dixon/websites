<?php
/**
 * Theme Setup
 */
function southwestsecretcom_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'southwestsecretcom'),
    ));
}
add_action('after_setup_theme', 'southwestsecretcom_setup');

function southwestsecretcom_enqueue_styles() {
    wp_enqueue_style('southwestsecretcom-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'southwestsecretcom_enqueue_styles');

/**
 * Performance and Security Optimizations
 */

/**
 * Add security headers
 */
function southwestsecret_security_headers() {
    if (!is_admin()) {
        // Remove WordPress version from head
        remove_action('wp_head', 'wp_generator');

        // Remove RSD link
        remove_action('wp_head', 'rsd_link');

        // Remove Windows Live Writer
        remove_action('wp_head', 'wlwmanifest_link');

        // Remove shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head');
    }
}
add_action('init', 'southwestsecret_security_headers');

/**
 * Optimize asset loading - defer non-critical CSS
 */
function southwestsecret_optimize_assets() {
    // Defer non-critical styles
    if (!is_admin()) {
        add_filter('style_loader_tag', 'southwestsecret_defer_styles', 10, 4);
    }
}
add_action('wp_enqueue_scripts', 'southwestsecret_optimize_assets', 999);

/**
 * Defer loading of non-critical styles
 */
function southwestsecret_defer_styles($html, $handle, $href, $media) {
    // Defer Tailwind CSS as it's not critical for initial render
    if ($handle === 'tailwind-css') {
        return str_replace("rel='stylesheet'", "rel='preload' as='style' onload="this.onload=null;this.rel='stylesheet'"", $html);
    }
    return $html;
}

/**
 * Optimize database queries
 */
function southwestsecret_optimize_queries($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_home()) {
        // Limit posts per page for performance
        $query->set('posts_per_page', 12);
    }
    return $query;
}
add_action('pre_get_posts', 'southwestsecret_optimize_queries');

/**
 * Add preconnect for external resources
 */
function southwestsecret_resource_hints($hints, $relation_type) {
    if ($relation_type === 'preconnect') {
        // Preconnect to Google Fonts
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
    }
    return $hints;
}
add_filter('wp_resource_hints', 'southwestsecret_resource_hints', 10, 2);

/**
 * Disable emojis for performance
 */
function southwestsecret_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'southwestsecret_disable_emojis');
?>