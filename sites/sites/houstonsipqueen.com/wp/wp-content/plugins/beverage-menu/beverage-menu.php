<?php
/**
 * Plugin Name: Beverage Menu
 * Plugin URI: https://houstonsipqueen.com
 * Description: Premium beverage menu management for Houston Sip Queen
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 * License: GPL v2 or later
 * Text Domain: beverage-menu
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BEVERAGE_MENU_VERSION', '1.0.0');
define('BEVERAGE_MENU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BEVERAGE_MENU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once BEVERAGE_MENU_PLUGIN_DIR . 'includes/class-beverage-menu.php';
require_once BEVERAGE_MENU_PLUGIN_DIR . 'includes/class-beverage-post-type.php';
require_once BEVERAGE_MENU_PLUGIN_DIR . 'admin/class-admin.php';
require_once BEVERAGE_MENU_PLUGIN_DIR . 'public/class-public.php';

// Activation hook
register_activation_hook(__FILE__, 'beverage_menu_activate');

/**
 * Plugin activation function
 */
function beverage_menu_activate() {
    // Create database tables if needed
    // Set default options
    add_option('beverage_menu_version', BEVERAGE_MENU_VERSION);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'beverage_menu_deactivate');

/**
 * Plugin deactivation function
 */
function beverage_menu_deactivate() {
    // Clean up if needed
}

// Initialize the plugin
function beverage_menu_init() {
    Beverage_Menu::get_instance();
    new Beverage_Post_Type();
    new Beverage_Admin();
    new Beverage_Public();
}
add_action('plugins_loaded', 'beverage_menu_init');