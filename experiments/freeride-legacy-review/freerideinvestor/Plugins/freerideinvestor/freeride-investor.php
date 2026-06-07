<?php
/**
 * Plugin Name: Freeride Investor
 * Plugin URI: https://freerideinvestor.com
 * Description: A plugin for stock research, AI-generated trade plans, historical data visualization, and customizable alerts.
 * Version: 1.8.2
 * Author: Victor Dixon
 * Author URI: https://freerideinvestor.com
 * Text Domain: freeride-investor
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Define plugin constants
define('FRI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FRI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FRI_LOG_FILE', FRI_PLUGIN_DIR . 'debug.log'); // Log file path

// Ensure the log file exists
if (!file_exists(FRI_LOG_FILE)) {
    file_put_contents(FRI_LOG_FILE, ''); // Create an empty log file
}

// Include necessary files with fallback logging
$includes = [
    'includes/admin/class-clear-cache.php',
    'includes/admin/class-settings.php',
    'includes/admin/class-log-viewer.php',
    'includes/api/class-api-requests.php',
    'includes/api/class-alpha-vantage.php',
    'includes/api/class-finnhub.php',
    'includes/api/class-openai.php',
    'includes/cache/class-cache-manager.php',
    'includes/alerts/class-alerts-handler.php',
    'includes/alerts/class-alerts-cron.php'
];

foreach ($includes as $file) {
    $file_path = FRI_PLUGIN_DIR . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log("Missing file: $file_path", 3, FRI_LOG_FILE);
    }
}

/**
 * Initialize the plugin
 */
function fri_init() {
    FRI_Clear_Cache::init();
    FRI_Settings::init();
    FRI_Log_Viewer::init();
    FRI_Alerts_Cron::init();
}
add_action('init', 'fri_init');

/**
 * Register shortcodes
 */
function fri_register_shortcodes() {
    add_shortcode('stock_research', 'fri_render_stock_research');
    add_shortcode('alert_setup', 'fri_render_alert_setup');
}
add_action('init', 'fri_register_shortcodes');

/**
 * Render the stock research shortcode
 */
function fri_render_stock_research() {
    ob_start();
    include FRI_PLUGIN_DIR . 'templates/shortcode-stock-research.php';
    return ob_get_clean();
}

/**
 * Render the alert setup shortcode
 */
function fri_render_alert_setup() {
    ob_start();
    include FRI_PLUGIN_DIR . 'templates/shortcode-alert-setup.php';
    return ob_get_clean();
}

/**
 * Enqueue assets
 */
if (!function_exists('fri_enqueue_assets')) {
    function fri_enqueue_assets() {
        if (!is_admin()) {
            wp_enqueue_style('freeride-investor-dashboard', FRI_PLUGIN_URL . 'assets/css/dashboard.css', [], '1.0.0');
            wp_enqueue_script('freeride-investor-dashboard', FRI_PLUGIN_URL . 'assets/js/dashboard.js', ['jquery', 'chart-js'], '1.0.0', true);

            // Localize script with sanitized AJAX URL and nonce
            wp_localize_script('freeride-investor-dashboard', 'freerideAjax', [
                'ajax_url' => esc_url(admin_url('admin-ajax.php')),
                'nonce'    => wp_create_nonce('fri_stock_research_nonce'),
            ]);
        }
    }
}
add_action('wp_enqueue_scripts', 'fri_enqueue_assets');

/**
 * Plugin activation hook
 */
function fri_activate_plugin() {
    try {
        // Create database tables
        FRI_Alerts_Handler::create_alerts_table();
        FRI_Cache_Manager::create_cache_table();

        // Ensure log file exists
        if (!file_exists(FRI_LOG_FILE)) {
            file_put_contents(FRI_LOG_FILE, ''); // Create log file
        }

        // Schedule cron jobs
        FRI_Alerts_Cron::schedule_cron();
    } catch (Exception $e) {
        error_log('Plugin activation error: ' . $e->getMessage(), 3, FRI_LOG_FILE);
    }
}
register_activation_hook(__FILE__, 'fri_activate_plugin');

/**
 * Plugin deactivation hook
 */
function fri_deactivate_plugin() {
    try {
        // Unschedule cron jobs
        FRI_Alerts_Cron::unschedule_cron();
    } catch (Exception $e) {
        error_log('Plugin deactivation error: ' . $e->getMessage(), 3, FRI_LOG_FILE);
    }
}
register_deactivation_hook(__FILE__, 'fri_deactivate_plugin');

/**
 * Plugin uninstall hook
 */
function fri_uninstall_plugin() {
    try {
        // Clean up database tables
        FRI_Alerts_Handler::delete_alerts_table();

        // Clear all cached data
        FRI_Cache_Manager::clear();
    } catch (Exception $e) {
        error_log('Plugin uninstall error: ' . $e->getMessage(), 3, FRI_LOG_FILE);
    }
}
register_uninstall_hook(__FILE__, 'fri_uninstall_plugin');

/**
 * Test shortcode for debugging
 */
function fri_test_debug_shortcode() {
    $output = '';

    try {
        // Example 1: Log a test message
        error_log('Testing Freeride Investor Plugin Debugging', 3, FRI_LOG_FILE);

        // Example 2: Fetch table info (replace `wp_fri_cache` with your table name)
        global $wpdb;
        $table_name = $wpdb->prefix . 'fri_cache';
        $output .= '<h3>Table Exists: ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name ? 'Yes' : 'No') . '</h3>';

        // Example 3: Log table schema
        $output .= '<pre>' . print_r($wpdb->get_results("SHOW COLUMNS FROM $table_name", ARRAY_A), true) . '</pre>';

        // Example 4: Log file permissions
        $output .= '<h3>Log File Writable: ' . (is_writable(FRI_LOG_FILE) ? 'Yes' : 'No') . '</h3>';

    } catch (Exception $e) {
        error_log('Error during test: ' . $e->getMessage(), 3, FRI_LOG_FILE);
        $output .= '<h3>Error: ' . esc_html($e->getMessage()) . '</h3>';
    }

    return $output;
}
add_shortcode('fri_test_debug', 'fri_test_debug_shortcode');
