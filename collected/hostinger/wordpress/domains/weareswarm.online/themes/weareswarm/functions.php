<?php
function weareswarmonline_enqueue_styles() {
    wp_enqueue_style('weareswarmonline-style', get_stylesheet_uri());

    // Tailwind's CDN endpoint is JavaScript, not a stylesheet.
    wp_enqueue_script('tailwind-css', 'https://cdn.tailwindcss.com', array(), null, false);
}
add_action('wp_enqueue_scripts', 'weareswarmonline_enqueue_styles');

/**
 * Performance and Security Optimizations
 */

/**
 * Add security headers
 */
function freerideinvestor_security_headers() {
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
add_action('init', 'freerideinvestor_security_headers');

/**
 * Optimize asset loading - defer non-critical CSS
 */
function freerideinvestor_optimize_assets() {
    // Defer non-critical styles
    if (!is_admin()) {
        add_filter('style_loader_tag', 'freerideinvestor_defer_styles', 10, 4);
    }
}
add_action('wp_enqueue_scripts', 'freerideinvestor_optimize_assets', 999);

/**
 * Defer loading of non-critical styles
 */
function freerideinvestor_defer_styles($html, $handle, $href, $media) {
    return $html;
}

/**
 * Optimize database queries
 */
function freerideinvestor_optimize_queries($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_home()) {
        // Limit posts per page for performance
        $query->set('posts_per_page', 12);
    }
    return $query;
}
add_action('pre_get_posts', 'freerideinvestor_optimize_queries');

/**
 * Add preconnect for external resources
 */
function freerideinvestor_resource_hints($hints, $relation_type) {
    if ($relation_type === 'preconnect') {
        // Preconnect to Google Fonts
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
    }
    return $hints;
}
add_filter('wp_resource_hints', 'freerideinvestor_resource_hints', 10, 2);

/**
 * Disable emojis for performance
 */
function freerideinvestor_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'freerideinvestor_disable_emojis');

/**
 * Font Corruption Fix - Enqueue CSS Override
 * Added automatically by deploy_font_fix.py
 * Date: 2026-01-24
 */
function enqueue_font_corruption_fix() {
    $css_path = get_template_directory() . '/font-corruption-fix.css';
    if (file_exists($css_path)) {
        wp_enqueue_style(
            'font-corruption-fix',
            get_template_directory_uri() . '/font-corruption-fix.css',
            array(),
            '20260124' // Version for cache busting
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_font_corruption_fix', 999); // High priority to override theme styles


/**
 * SEO Meta Tags Fix
 * Added automatically by deploy_seo_fix.py
 * Date: 2026-01-24
 */
$weareswarmonline_seo_fix = get_template_directory() . '/seo-meta-fix.php';
if (file_exists($weareswarmonline_seo_fix)) {
    require_once $weareswarmonline_seo_fix;
}
