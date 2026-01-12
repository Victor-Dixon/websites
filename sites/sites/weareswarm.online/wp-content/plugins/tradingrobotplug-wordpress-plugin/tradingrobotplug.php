<?php
/**
 * Plugin Name: Trading Robot Plug Platform
 * Plugin URI: https://tradingrobotplug.com
 * Description: Connects WordPress to the Trading Robot Service Platform. Handles user management, performance tracking, subscriptions, and dashboard integration.
 * Version: 1.0.0
 * Author: Trading Robot Plug
 * Author URI: https://tradingrobotplug.com
 * License: GPL v2 or later
 * Text Domain: tradingrobotplug
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRADINGROBOTPLUG_VERSION', '1.0.0');
define('TRADINGROBOTPLUG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRADINGROBOTPLUG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRADINGROBOTPLUG_API_URL', 'https://api.tradingrobotplug.com/v1'); // Placeholder for actual API

// Autoloader for classes (basic implementation)
spl_autoload_register(function ($class) {
    $prefix = 'TradingRobotPlug\\';
    $base_dir = TRADINGROBOTPLUG_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Include main class
require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'includes/class-trading-robot-plug.php';

// Initialize the plugin
function run_trading_robot_plug() {
    $plugin = new TradingRobotPlug\Trading_Robot_Plug();
    $plugin->run();
}
run_trading_robot_plug();
