<?php
/**
 * Plugin Name: Freeride Investor
 * Plugin URI: https://freerideinvestor.com
 * Description: A plugin for stock research, AI-generated trade plans, historical data visualization, and customizable alerts.
 * Version: 1.8.1
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: freeride-investor
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Define plugin constants
define('FRI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FRI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once FRI_PLUGIN_DIR . 'includes/admin/class-clear-cache.php';
require_once FRI_PLUGIN_DIR . 'includes/admin/class-settings.php';
require_once FRI_PLUGIN_DIR . 'includes/admin/class-log-viewer.php';
require_once FRI_PLUGIN_DIR . 'includes/api/class-api-requests.php';
require_once FRI_PLUGIN_DIR . 'includes/api/class-alpha-vantage.php';
require_once FRI_PLUGIN_DIR . 'includes/api/class-finnhub.php';
require_once FRI_PLUGIN_DIR . 'includes/api/class-openai.php';
require_once FRI_PLUGIN_DIR . 'includes/cache/class-cache-manager.php';
require_once FRI_PLUGIN_DIR . 'includes/alerts/class-alerts-handler.php';
require_once FRI_PLUGIN_DIR . 'includes/alerts/class-alerts-cron.php';

/**
 * Initialize the plugin
 */
function fri_init() {
    // Initialize modules
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
function fri_enqueue_assets() {
    if (!is_admin()) {
        wp_enqueue_style('freeride-investor-dashboard', FRI_PLUGIN_URL . 'assets/css/dashboard.css', [], '1.0.0');
        wp_enqueue_script('freeride-investor-dashboard', FRI_PLUGIN_URL . 'assets/js/dashboard.js', ['jquery', 'chart-js'], '1.0.0', true);

        // Localize script with AJAX URL and nonce
        wp_localize_script('freeride-investor-dashboard', 'freerideAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('fri_stock_research_nonce'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'fri_enqueue_assets');

/**
 * Plugin activation hook
 */
function fri_activate_plugin() {
    // Create database tables
    FRI_Alerts_Handler::create_alerts_table();
    FRI_Cache_Manager::create_cache_table();

    // Schedule cron jobs
    FRI_Alerts_Cron::schedule_cron();
}
register_activation_hook(__FILE__, 'fri_activate_plugin');

/**
 * Plugin deactivation hook
 */
function fri_deactivate_plugin() {
    // Unschedule cron jobs
    FRI_Alerts_Cron::unschedule_cron();

    // Optionally remove tables or cleanup if needed
}
register_deactivation_hook(__FILE__, 'fri_deactivate_plugin');

/**
 * Plugin uninstall hook
 */
function fri_uninstall_plugin() {
    // Clean up database tables
    FRI_Alerts_Handler::delete_alerts_table();

    // Clear all cached data
    FRI_Cache_Manager::clear();
}
register_uninstall_hook(__FILE__, 'fri_uninstall_plugin');
