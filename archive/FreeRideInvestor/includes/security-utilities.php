<?php
/**
 * FreeRide Investor - SSOT Security Utilities
 * 
 * Centralized security functions for consistent security across all plugins.
 * Created by: Agent-8 (SSOT & System Integration Specialist)
 * Mission: WP-SEC-003
 * 
 * Usage: Include this file in functions.php or plugin files:
 * require_once get_template_directory() . '/includes/security-utilities.php';
 * 
 * @package FreeRideInvestor
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SSOT Input Sanitization
 * 
 * Centralized input sanitization with type-specific handling.
 * 
 * @param mixed $value The value to sanitize
 * @param string $type The type of sanitization to apply
 * @return mixed Sanitized value
 */
function fri_sanitize_input($value, $type = 'text') {
    switch ($type) {
        case 'email':
            return sanitize_email($value);
            
        case 'url':
            return esc_url_raw($value);
            
        case 'textarea':
            return sanitize_textarea_field($value);
            
        case 'html':
            return wp_kses_post($value);
            
        case 'int':
            return absint($value);
            
        case 'float':
            return floatval($value);
            
        case 'boolean':
            return (bool) $value;
            
        case 'slug':
            return sanitize_title($value);
            
        case 'key':
            return sanitize_key($value);
            
        case 'filename':
            return sanitize_file_name($value);
            
        case 'user':
            return sanitize_user($value);
            
        default:
            return sanitize_text_field($value);
    }
}

/**
 * SSOT Output Escaping
 * 
 * Centralized output escaping for different contexts.
 * 
 * @param mixed $value The value to escape
 * @param string $context The output context
 * @return mixed Escaped value
 */
function fri_escape_output($value, $context = 'html') {
    switch ($context) {
        case 'html':
            return esc_html($value);
            
        case 'attr':
            return esc_attr($value);
            
        case 'url':
            return esc_url($value);
            
        case 'js':
            return esc_js($value);
            
        case 'textarea':
            return esc_textarea($value);
            
        case 'sql':
            return esc_sql($value);
            
        default:
            return esc_html($value);
    }
}

/**
 * SSOT Database Query Preparation
 * 
 * Secure database query preparation using wpdb::prepare().
 * 
 * @param string $query The SQL query with placeholders
 * @param mixed ...$args The values to bind to placeholders
 * @return string Prepared query
 */
function fri_prepare_query($query, ...$args) {
    global $wpdb;
    
    if (empty($args)) {
        return $query;
    }
    
    return $wpdb->prepare($query, ...$args);
}

/**
 * SSOT Nonce Verification
 * 
 * Verify WordPress nonce with automatic die on failure.
 * 
 * @param string $nonce_name The nonce field name
 * @param string $action The nonce action
 * @param bool $die_on_failure Whether to die on failure (default: true)
 * @return bool True if valid, false if invalid and $die_on_failure is false
 */
function fri_verify_nonce($nonce_name, $action, $die_on_failure = true) {
    $nonce_value = isset($_POST[$nonce_name]) ? $_POST[$nonce_name] : '';
    
    if (!$nonce_value || !wp_verify_nonce($nonce_value, $action)) {
        if ($die_on_failure) {
            wp_die('Security check failed. Please refresh the page and try again.', 'Security Error', array('response' => 403));
        }
        return false;
    }
    
    return true;
}

/**
 * SSOT AJAX Nonce Verification
 * 
 * Verify nonce for AJAX requests with JSON error response.
 * 
 * @param string $nonce_name The nonce field name
 * @param string $action The nonce action
 * @return void Dies with JSON error if invalid
 */
function fri_verify_ajax_nonce($nonce_name, $action) {
    $nonce_value = isset($_POST[$nonce_name]) ? $_POST[$nonce_name] : '';
    
    if (!$nonce_value || !wp_verify_nonce($nonce_value, $action)) {
        wp_send_json_error(array(
            'message' => 'Security check failed. Please refresh the page and try again.'
        ), 403);
    }
}

/**
 * SSOT User Capability Check
 * 
 * Check if current user has required capability.
 * 
 * @param string $capability The capability to check
 * @param bool $die_on_failure Whether to die on failure (default: true)
 * @return bool True if user has capability, false if not and $die_on_failure is false
 */
function fri_check_capability($capability = 'manage_options', $die_on_failure = true) {
    if (!current_user_can($capability)) {
        if ($die_on_failure) {
            wp_die('You do not have permission to perform this action.', 'Permission Denied', array('response' => 403));
        }
        return false;
    }
    
    return true;
}

/**
 * SSOT AJAX Capability Check
 * 
 * Check capability for AJAX requests with JSON error response.
 * 
 * @param string $capability The capability to check
 * @return void Dies with JSON error if user lacks capability
 */
function fri_check_ajax_capability($capability = 'manage_options') {
    if (!current_user_can($capability)) {
        wp_send_json_error(array(
            'message' => 'You do not have permission to perform this action.'
        ), 403);
    }
}

/**
 * SSOT File Upload Validation
 * 
 * Validate uploaded file for security.
 * 
 * @param array $file The uploaded file array from $_FILES
 * @param array $allowed_types Allowed MIME types (default: images)
 * @param int $max_size Maximum file size in bytes (default: 5MB)
 * @return array|WP_Error Validated file array or WP_Error on failure
 */
function fri_validate_file_upload($file, $allowed_types = array('image/jpeg', 'image/png', 'image/gif'), $max_size = 5242880) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('upload_error', 'File upload failed.');
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return new WP_Error('file_too_large', sprintf('File size exceeds maximum of %s.', size_format($max_size)));
    }
    
    // Verify MIME type
    $file_type = wp_check_filetype($file['name']);
    if (!in_array($file_type['type'], $allowed_types)) {
        return new WP_Error('invalid_file_type', 'File type not allowed.');
    }
    
    // Additional security check
    $real_mime = mime_content_type($file['tmp_name']);
    if (!in_array($real_mime, $allowed_types)) {
        return new WP_Error('mime_mismatch', 'File MIME type does not match extension.');
    }
    
    return $file;
}

/**
 * SSOT URL Parameter Validation
 * 
 * Safely get and validate URL parameter.
 * 
 * @param string $param_name The parameter name
 * @param string $type The sanitization type
 * @param mixed $default Default value if parameter not set
 * @return mixed Sanitized parameter value or default
 */
function fri_get_param($param_name, $type = 'text', $default = '') {
    $value = isset($_GET[$param_name]) ? $_GET[$param_name] : $default;
    return fri_sanitize_input($value, $type);
}

/**
 * SSOT POST Data Validation
 * 
 * Safely get and validate POST data.
 * 
 * @param string $field_name The field name
 * @param string $type The sanitization type
 * @param mixed $default Default value if field not set
 * @return mixed Sanitized field value or default
 */
function fri_get_post_field($field_name, $type = 'text', $default = '') {
    $value = isset($_POST[$field_name]) ? $_POST[$field_name] : $default;
    return fri_sanitize_input($value, $type);
}

/**
 * SSOT Array Data Sanitization
 * 
 * Recursively sanitize array data.
 * 
 * @param array $array The array to sanitize
 * @param string $type The sanitization type for all values
 * @return array Sanitized array
 */
function fri_sanitize_array($array, $type = 'text') {
    if (!is_array($array)) {
        return fri_sanitize_input($array, $type);
    }
    
    $sanitized = array();
    foreach ($array as $key => $value) {
        $sanitized_key = sanitize_key($key);
        $sanitized[$sanitized_key] = is_array($value) 
            ? fri_sanitize_array($value, $type)
            : fri_sanitize_input($value, $type);
    }
    
    return $sanitized;
}

/**
 * SSOT SQL Injection Prevention Check
 * 
 * Check if query uses prepare() for dynamic values.
 * 
 * @param string $query The SQL query to check
 * @return bool True if query appears safe, false if potentially unsafe
 */
function fri_is_query_safe($query) {
    // Check for common SQL injection patterns
    $unsafe_patterns = array(
        '/\$_(GET|POST|REQUEST|COOKIE)\[/',
        '/concat\s*\(/i',
        '/union\s+select/i',
        '/\'\s*or\s*\'/i',
        '/"\s*or\s*"/i',
    );
    
    foreach ($unsafe_patterns as $pattern) {
        if (preg_match($pattern, $query)) {
            return false;
        }
    }
    
    // Check if query contains %s, %d, or %f (prepare() placeholders)
    if (strpos($query, '$') !== false && !preg_match('/%[sdf]/', $query)) {
        return false;
    }
    
    return true;
}

/**
 * SSOT XSS Prevention Check
 * 
 * Validate that output is properly escaped.
 * 
 * @param string $output The output to check
 * @param string $context The output context
 * @return bool True if output appears safe, false if potentially unsafe
 */
function fri_is_output_safe($output, $context = 'html') {
    // Check for unescaped special characters based on context
    switch ($context) {
        case 'html':
            return !preg_match('/<script|javascript:|onerror=/i', $output);
            
        case 'js':
            return !preg_match('/[\'"]?\s*on\w+\s*=/i', $output);
            
        case 'url':
            return filter_var($output, FILTER_VALIDATE_URL) !== false;
            
        default:
            return true;
    }
}

/**
 * SSOT Security Audit Log
 * 
 * Log security-related events for auditing.
 * 
 * @param string $event_type The type of security event
 * @param string $message The event message
 * @param array $context Additional context data
 * @return bool True on success
 */
function fri_log_security_event($event_type, $message, $context = array()) {
    $log_entry = array(
        'timestamp' => current_time('mysql'),
        'event_type' => $event_type,
        'message' => $message,
        'user_id' => get_current_user_id(),
        'user_ip' => $_SERVER['REMOTE_ADDR'],
        'context' => $context
    );
    
    // Log to WordPress error log
    error_log(sprintf(
        '[FRI Security] %s: %s (User: %d, IP: %s)',
        $event_type,
        $message,
        $log_entry['user_id'],
        $log_entry['user_ip']
    ));
    
    // Store in database option for review
    $security_log = get_option('fri_security_log', array());
    $security_log[] = $log_entry;
    
    // Keep only last 100 entries
    if (count($security_log) > 100) {
        $security_log = array_slice($security_log, -100);
    }
    
    update_option('fri_security_log', $security_log, false);
    
    return true;
}

/**
 * SSOT Complete Security Check
 * 
 * Perform full security validation (nonce + capability).
 * 
 * @param string $nonce_name The nonce field name
 * @param string $action The nonce action
 * @param string $capability The required capability
 * @return bool True if all checks pass
 */
function fri_security_check($nonce_name, $action, $capability = 'manage_options') {
    fri_verify_nonce($nonce_name, $action, true);
    fri_check_capability($capability, true);
    return true;
}

/**
 * SSOT AJAX Security Check
 * 
 * Perform full security validation for AJAX (nonce + capability).
 * 
 * @param string $nonce_name The nonce field name
 * @param string $action The nonce action
 * @param string $capability The required capability
 * @return void Dies with JSON error if checks fail
 */
function fri_ajax_security_check($nonce_name, $action, $capability = 'manage_options') {
    fri_verify_ajax_nonce($nonce_name, $action);
    fri_check_ajax_capability($capability);
}

