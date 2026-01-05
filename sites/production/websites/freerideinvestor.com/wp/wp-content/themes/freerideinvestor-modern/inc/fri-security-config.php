<?php
/**
 * FreeRide Investor Security Configuration
 * 
 * This file contains security settings and constants
 * for the FreeRide Investor plugins
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Security Constants
define('FRI_SECURITY_ENABLED', true);
define('FRI_RATE_LIMIT_ENABLED', true);
define('FRI_SQL_INJECTION_PROTECTION', true);
define('FRI_XSS_PROTECTION', true);
define('FRI_CSRF_PROTECTION', true);

// Rate Limiting Settings
define('FRI_MAX_REQUESTS_PER_MINUTE', 60);
define('FRI_MAX_API_REQUESTS_PER_HOUR', 1000);

// Input Validation Settings
define('FRI_MAX_INPUT_LENGTH', 1000);
define('FRI_ALLOWED_HTML_TAGS', '<p><br><strong><em><ul><li><a>');

// Security Headers
add_action('send_headers', 'fri_security_headers');
function fri_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}

// Input Sanitization Helper
function fri_sanitize_input($input, $type = 'text') {
    switch ($type) {
        case 'email':
            return sanitize_email($input);
        case 'url':
            return esc_url_raw($input);
        case 'int':
            return intval($input);
        case 'float':
            return floatval($input);
        case 'html':
            return wp_kses_post($input);
        default:
            return sanitize_text_field($input);
    }
}

// SQL Injection Protection Helper
function fri_safe_query($query, ...$args) {
    global $wpdb;
    
    if (empty($args)) {
        return $wpdb->query($query);
    }
    
    return $wpdb->query($wpdb->prepare($query, ...$args));
}

// Rate Limiting Helper
function fri_check_rate_limit($action, $user_id = null) {
    if (!FRI_RATE_LIMIT_ENABLED) {
        return true;
    }
    
    $user_id = $user_id ?: get_current_user_id();
    $key = 'fri_rate_limit_' . $action . '_' . $user_id;
    $count = get_transient($key) ?: 0;
    
    if ($count >= FRI_MAX_REQUESTS_PER_MINUTE) {
        return false;
    }
    
    set_transient($key, $count + 1, 60);
    return true;
}

// CSRF Protection Helper
function fri_verify_nonce($action, $nonce_field = 'fri_nonce') {
    if (!isset($_POST[$nonce_field])) {
        return false;
    }
    
    return wp_verify_nonce($_POST[$nonce_field], $action);
}

// XSS Protection Helper
function fri_escape_output($output, $context = 'display') {
    switch ($context) {
        case 'attribute':
            return esc_attr($output);
        case 'url':
            return esc_url($output);
        case 'html':
            return esc_html($output);
        case 'js':
            return esc_js($output);
        default:
            return esc_html($output);
    }
}
?>