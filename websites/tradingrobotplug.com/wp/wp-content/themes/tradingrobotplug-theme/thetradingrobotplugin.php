<?php
/*
Plugin Name: The Trading Robot Plug Plugin
Plugin URI: https://TheTradingRobotPlug.com
Description: A plugin to integrate advanced trading algorithms with WordPress.
Version: 1.0.0
Author: Victor Dixon
Author URI: https://TheTradingRobotPlug.com
License: GPLv2 or later
Text Domain: thetradingrobotplugin
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define constants for the plugin
define('TRADINGROBOTPLUGIN_VERSION', '1.0.0');
define('TRADINGROBOTPLUGIN_PATH', plugin_dir_path(__FILE__));
define('TRADINGROBOTPLUGIN_URL', plugin_dir_url(__FILE__));

// Load plugin text domain for translations
function tradingrobotplugin_load_textdomain() {
    load_plugin_textdomain('thetradingrobotplugin', false, basename(TRADINGROBOTPLUGIN_PATH) . '/languages');
}
add_action('plugins_loaded', 'tradingrobotplugin_load_textdomain');

// Include necessary files
require_once TRADINGROBOTPLUGIN_PATH . 'includes/class-thetradingrobotplugin.php';

// Register activation and deactivation hooks
function activate_tradingrobotplugin() {
    require_once TRADINGROBOTPLUGIN_PATH . 'includes/class-thetradingrobotplugin-activator.php';
    TheTradingRobotPlugPlugin_Activator::activate();
}
function deactivate_tradingrobotplugin() {
    require_once TRADINGROBOTPLUGIN_PATH . 'includes/class-thetradingrobotplugin-deactivator.php';
    TheTradingRobotPlugPlugin_Deactivator::deactivate();
}
register_activation_hook(__FILE__, 'activate_tradingrobotplugin');
register_deactivation_hook(__FILE__, 'deactivate_tradingrobotplugin');

// Initialize the plugin
function run_tradingrobotplugin() {
    require_once TRADINGROBOTPLUGIN_PATH . 'includes/class-thetradingrobotplugin-runner.php';
    $plugin = new TheTradingRobotPlugPlugin();
    $plugin->run();
}
run_tradingrobotplugin();
