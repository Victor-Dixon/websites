<?php
/**
 * Debug script to identify register_post_type issues
 */

echo "=== FREERIDEINVESTOR.COM DEBUG ===\n\n";

// Check if we can access WordPress
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

require_once ABSPATH . 'wp-load.php';

echo "WordPress loaded successfully\n\n";

// Hook into register_post_type to catch the problematic call
function debug_register_post_type($post_type, $args) {
    $len = strlen($post_type);
    echo "REGISTER_POST_TYPE CALLED: '$post_type' (length: $len)\n";

    if ($len < 1 || $len > 20) {
        echo "❌ INVALID LENGTH: Post type name must be 1-20 characters\n";
        echo "Args: " . json_encode($args, JSON_PRETTY_PRINT) . "\n";
        echo "Backtrace:\n";
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo "\n";
    } else {
        echo "✅ Valid length\n";
    }
}

// Add filter to catch all register_post_type calls
add_filter('register_post_type_args', function($args, $post_type) {
    debug_register_post_type($post_type, $args);
    return $args;
}, 1, 2);

// Try to initialize plugins to trigger the error
echo "Loading plugins...\n";
wp_load_alloptions();
wp_cache_flush();

echo "\n=== ACTIVE PLUGINS ===\n";
$active_plugins = get_option('active_plugins', array());
foreach ($active_plugins as $plugin) {
    echo "- $plugin\n";
}

echo "\n=== MUST-USE PLUGINS ===\n";
$mu_plugins = get_mu_plugins();
foreach ($mu_plugins as $plugin => $data) {
    echo "- $plugin\n";
}

echo "\n=== THEME ===\n";
echo "Current theme: " . get_template() . "\n";

echo "\nDebug complete.\n";
?>