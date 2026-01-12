<?php
/**
 * Plugin Name: Swarm Chronicle
 * Plugin URI: https://weareswarm.online
 * Description: Displays the complete Swarm operating chronicle including cycle accomplishments, project state, and mission logs
 * Version: 1.0.0
 * Author: Swarm Intelligence
 * License: MIT
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SWARM_CHRONICLE_VERSION', '1.0.0');
define('SWARM_CHRONICLE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SWARM_CHRONICLE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SWARM_CHRONICLE_PLUGIN_DIR . 'includes/class-swarm-chronicle.php';
require_once SWARM_CHRONICLE_PLUGIN_DIR . 'includes/class-chronicle-api.php';
require_once SWARM_CHRONICLE_PLUGIN_DIR . 'includes/class-chronicle-admin.php';

// Initialize the plugin
function swarm_chronicle_init() {
    $chronicle = new Swarm_Chronicle();
    $chronicle->init();
}
add_action('plugins_loaded', 'swarm_chronicle_init');

// Activation hook
function swarm_chronicle_activate() {
    // Create necessary database tables or options
    add_option('swarm_chronicle_version', SWARM_CHRONICLE_VERSION);
    add_option('swarm_chronicle_last_sync', time());

    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'swarm_chronicle_activate');

// Deactivation hook
function swarm_chronicle_deactivate() {
    // Clean up if needed
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'swarm_chronicle_deactivate');