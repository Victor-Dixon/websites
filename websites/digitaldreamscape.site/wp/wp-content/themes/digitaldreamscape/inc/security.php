<?php
/**
 * Security Functions
 *
 * Security-related functions and hardening
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove WordPress version from RSS feeds
 */
function digitaldreamscape_remove_wp_version_rss() {
    return '';
}
add_filter('the_generator', 'digitaldreamscape_remove_wp_version_rss');

/**
 * Disable XML-RPC if not needed
 * Uncomment if you want to disable XML-RPC
 */
// add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove unnecessary header information
 */
function digitaldreamscape_remove_header_info() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'digitaldreamscape_remove_header_info');

/**
 * Disable file editing from admin
 */
function digitaldreamscape_disable_file_editing() {
    if (!defined('DISALLOW_FILE_EDIT')) {
        define('DISALLOW_FILE_EDIT', true);
    }
}
add_action('init', 'digitaldreamscape_disable_file_editing');

/**
 * Secure login error messages
 */
function digitaldreamscape_login_errors() {
    return 'Invalid login credentials.';
}
add_filter('login_errors', 'digitaldreamscape_login_errors');

/**
 * Remove admin bar for non-admins on frontend
 */
function digitaldreamscape_remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'digitaldreamscape_remove_admin_bar');

/**
 * Sanitize file uploads
 */
function digitaldreamscape_upload_mimes($mimes) {
    // Remove potentially dangerous file types
    unset($mimes['exe']);
    unset($mimes['bat']);
    unset($mimes['com']);
    unset($mimes['pif']);

    // Add safe alternatives
    $mimes['svg'] = 'image/svg+xml';

    return $mimes;
}
add_filter('upload_mimes', 'digitaldreamscape_upload_mimes');

/**
 * Limit login attempts (basic implementation)
 */
function digitaldreamscape_limit_login_attempts() {
    $max_attempts = 5;
    $time_window = 3600; // 1 hour

    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'login_attempts_' . md5($ip);

    $attempts = get_transient($transient_key);

    if ($attempts >= $max_attempts) {
        wp_die('Too many login attempts. Please try again later.');
    }

    if (isset($_POST['wp-submit'])) {
        $attempts = $attempts ? $attempts + 1 : 1;
        set_transient($transient_key, $attempts, $time_window);
    }
}
// add_action('wp_login_failed', 'digitaldreamscape_limit_login_attempts');

/**
 * Add security headers
 */
function digitaldreamscape_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'digitaldreamscape_security_headers');

/**
 * Clean up wp_head
 */
function digitaldreamscape_cleanup_head() {
    // Remove emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');

    // Remove unnecessary scripts
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
}
add_action('init', 'digitaldreamscape_cleanup_head');