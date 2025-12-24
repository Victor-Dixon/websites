<?php
/**
 * WordPress Performance Optimizations - Added by Agent-7
 * Date: 2025-12-22
 */

// Disable emoji scripts (reduces HTTP requests)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

// Remove unnecessary scripts
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_shortlink_wp_head');

// Defer JavaScript loading
function defer_parsing_of_js($url) {
    if (is_admin()) return $url;
    if (FALSE === strpos($url, '.js')) return $url;
    if (strpos($url, 'jquery.js')) return $url;
    return str_replace(' src', ' defer src', $url);
}
add_filter('script_loader_tag', 'defer_parsing_of_js', 10);

// Limit post revisions
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

// Optimize database queries
function optimize_database_queries() {
    // Clean up transients
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%' AND option_name NOT LIKE '_transient_timeout_%'");
}
add_action('wp_scheduled_delete', 'optimize_database_queries');
