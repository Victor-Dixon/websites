
/**
 * Performance optimizations for southwestsecret.com
 * Add these functions to your theme's functions.php file
 */

// Disable emoji scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Disable embed scripts
function disable_embeds() {
    wp_deregister_script('wp-embed');
}
add_action('wp_footer', 'disable_embeds');

// Defer JavaScript loading
function defer_parsing_of_js($url) {
    if (is_admin()) return $url;
    if (FALSE === strpos($url, '.js')) return $url;
    if (strpos($url, 'jquery.js')) return $url;
    return str_replace(' src', ' defer src', $url);
}
add_filter('script_loader_tag', 'defer_parsing_of_js', 10);

// Remove unnecessary WordPress features
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head');

// Optimize database queries
function optimize_database_queries() {
    // Clean up expired transients
    $wpdb = $GLOBALS['wpdb'];
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%' AND option_value < UNIX_TIMESTAMP()");
}
add_action('wp_scheduled_delete', 'optimize_database_queries');
