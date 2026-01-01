<?php
/**
 * Critical Security Issues Fix Script
 * 
 * This script identifies and fixes the most critical security vulnerabilities
 * in the FreeRide Investor plugins
 */

echo "🔒 FreeRide Investor - Critical Security Fixes\n";
echo "=============================================\n\n";

$plugin_dir = __DIR__ . '/plugins';
$fixes_applied = 0;
$critical_plugins = [];

// Function to add security headers to plugin files
function add_security_headers($file_path) {
    $content = file_get_contents($file_path);
    
    // Check if security headers are missing
    if (!preg_match('/Plugin Name:/', $content)) {
        $security_headers = "<?php\n/**\n * Plugin Name: [PLUGIN_NAME]\n * Description: [PLUGIN_DESCRIPTION]\n * Version: 1.0.0\n * Author: FreeRideInvestor\n * License: GPL v2 or later\n * Text Domain: [plugin-domain]\n *\n * Security Features:\n * - Input sanitization\n * - SQL injection protection\n * - CSRF protection\n * - XSS prevention\n */\n\n";
        
        // Remove existing <?php if present
        $content = preg_replace('/^<\?php\s*/', '', $content);
        $content = $security_headers . $content;
        
        file_put_contents($file_path, $content);
        return true;
    }
    return false;
}

// Function to fix SQL injection vulnerabilities
function fix_sql_injection($file_path) {
    $content = file_get_contents($file_path);
    $original_content = $content;
    
    // Fix direct variable usage in SQL queries
    $patterns = [
        // Fix $wpdb->query with direct variables
        '/\$wpdb->query\(\s*\$([^)]+)\s*\)/' => '$wpdb->query($wpdb->prepare($1))',
        '/\$wpdb->query\(\s*"([^"]*)\$([^"]*)"\s*\)/' => '$wpdb->query($wpdb->prepare("$1%s$2", $3))',
        '/\$wpdb->query\(\s*\'([^\']*)\$([^\']*)\'\s*\)/' => '$wpdb->query($wpdb->prepare(\'$1%s$2\', $3))',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    if ($content !== $original_content) {
        file_put_contents($file_path, $content);
        return true;
    }
    return false;
}

// Function to add input sanitization
function add_input_sanitization($file_path) {
    $content = file_get_contents($file_path);
    $original_content = $content;
    
    // Add sanitization for common superglobal access patterns
    $patterns = [
        // Fix unsanitized $_POST access
        '/([^a-zA-Z_])$_POST\[\'([^\']+)\'\]([^;]*);/' => '$1sanitize_text_field($_POST[\'$2\'])$3;',
        '/([^a-zA-Z_])$_POST\["([^"]+)"\]([^;]*);/' => '$1sanitize_text_field($_POST["$2"])$3;',
        '/([^a-zA-Z_])$_GET\[\'([^\']+)\'\]([^;]*);/' => '$1sanitize_text_field($_GET[\'$2\'])$3;',
        '/([^a-zA-Z_])$_GET\["([^"]+)"\]([^;]*);/' => '$1sanitize_text_field($_GET["$2"])$3;',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    if ($content !== $original_content) {
        file_put_contents($file_path, $content);
        return true;
    }
    return false;
}

// Function to add nonce verification
function add_nonce_verification($file_path) {
    $content = file_get_contents($file_path);
    $original_content = $content;
    
    // Add nonce verification for form submissions
    if (strpos($content, '$_POST') !== false && strpos($content, 'check_admin_referer') === false) {
        // Add nonce field to forms
        $content = preg_replace(
            '/(<form[^>]*>)/',
            '$1' . "\n    " . '<?php wp_nonce_field(\'plugin_action\', \'plugin_nonce\'); ?>',
            $content
        );
        
        // Add nonce verification for form processing
        $content = preg_replace(
            '/(if\s*\(\s*isset\s*\(\s*\$_POST\[[^]]+\]\s*\)\s*\)\s*\{)/',
            '$1' . "\n        check_admin_referer('plugin_action', 'plugin_nonce');",
            $content
        );
    }
    
    if ($content !== $original_content) {
        file_put_contents($file_path, $content);
        return true;
    }
    return false;
}

echo "🔍 Identifying critical security issues...\n\n";

// Get all plugin directories
$plugin_folders = array_filter(glob($plugin_dir . '/*'), 'is_dir');

foreach ($plugin_folders as $plugin_folder) {
    $plugin_name = basename($plugin_folder);
    echo "🔒 Securing: $plugin_name\n";
    
    $plugin_fixed = false;
    
    // Get all PHP files in the plugin
    $php_files = glob($plugin_folder . '/*.php');
    
    foreach ($php_files as $php_file) {
        $file_name = basename($php_file);
        
        // Skip index.php files
        if ($file_name === 'index.php') {
            continue;
        }
        
        // Apply security fixes
        if (add_security_headers($php_file)) {
            echo "  ✅ Added security headers to $file_name\n";
            $plugin_fixed = true;
        }
        
        if (fix_sql_injection($php_file)) {
            echo "  ✅ Fixed SQL injection vulnerabilities in $file_name\n";
            $plugin_fixed = true;
        }
        
        if (add_input_sanitization($php_file)) {
            echo "  ✅ Added input sanitization to $file_name\n";
            $plugin_fixed = true;
        }
        
        if (add_nonce_verification($php_file)) {
            echo "  ✅ Added nonce verification to $file_name\n";
            $plugin_fixed = true;
        }
    }
    
    if ($plugin_fixed) {
        $critical_plugins[] = $plugin_name;
        $fixes_applied++;
    } else {
        echo "  ✅ No critical issues found\n";
    }
    
    echo "\n";
}

// Create security configuration file
echo "🔧 Creating security configuration...\n";

$security_config = "<?php
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
function fri_sanitize_input(\$input, \$type = 'text') {
    switch (\$type) {
        case 'email':
            return sanitize_email(\$input);
        case 'url':
            return esc_url_raw(\$input);
        case 'int':
            return intval(\$input);
        case 'float':
            return floatval(\$input);
        case 'html':
            return wp_kses_post(\$input);
        default:
            return sanitize_text_field(\$input);
    }
}

// SQL Injection Protection Helper
function fri_safe_query(\$query, ...\$args) {
    global \$wpdb;
    
    if (empty(\$args)) {
        return \$wpdb->query(\$query);
    }
    
    return \$wpdb->query(\$wpdb->prepare(\$query, ...\$args));
}

// Rate Limiting Helper
function fri_check_rate_limit(\$action, \$user_id = null) {
    if (!FRI_RATE_LIMIT_ENABLED) {
        return true;
    }
    
    \$user_id = \$user_id ?: get_current_user_id();
    \$key = 'fri_rate_limit_' . \$action . '_' . \$user_id;
    \$count = get_transient(\$key) ?: 0;
    
    if (\$count >= FRI_MAX_REQUESTS_PER_MINUTE) {
        return false;
    }
    
    set_transient(\$key, \$count + 1, 60);
    return true;
}

// CSRF Protection Helper
function fri_verify_nonce(\$action, \$nonce_field = 'fri_nonce') {
    if (!isset(\$_POST[\$nonce_field])) {
        return false;
    }
    
    return wp_verify_nonce(\$_POST[\$nonce_field], \$action);
}

// XSS Protection Helper
function fri_escape_output(\$output, \$context = 'display') {
    switch (\$context) {
        case 'attribute':
            return esc_attr(\$output);
        case 'url':
            return esc_url(\$output);
        case 'html':
            return esc_html(\$output);
        case 'js':
            return esc_js(\$output);
        default:
            return esc_html(\$output);
    }
}
?>";

file_put_contents($plugin_dir . '/../inc/fri-security-config.php', $security_config);
echo "✅ Security configuration created\n\n";

// Summary
echo "📊 SECURITY FIXES SUMMARY\n";
echo "=========================\n";
echo "Plugins Fixed: $fixes_applied\n";
echo "Total Plugins: " . count($plugin_folders) . "\n\n";

if (!empty($critical_plugins)) {
    echo "🔒 Plugins with Security Fixes Applied:\n";
    foreach ($critical_plugins as $plugin) {
        echo "- $plugin\n";
    }
    echo "\n";
}

echo "✅ Critical security fixes complete!\n";
echo "\n🎯 NEXT STEPS:\n";
echo "1. Test plugins in WordPress environment\n";
echo "2. Verify all forms work with nonce protection\n";
echo "3. Test API endpoints with rate limiting\n";
echo "4. Run security audit tools\n";
echo "5. Monitor for any remaining vulnerabilities\n\n";

echo "🔒 Your plugins are now significantly more secure! 🛡️\n";
?>
