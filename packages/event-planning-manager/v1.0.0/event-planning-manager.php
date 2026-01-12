<?php
/**
 * Plugin Name: Event Planning Manager
 * Plugin URI: https://crosbyultimateevents.com
 * Description: Professional event planning and management system
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 * License: GPL v2 or later
 * Text Domain: event-planning-manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EVENT_PLANNING_VERSION', '1.0.0');
define('EVENT_PLANNING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EVENT_PLANNING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once EVENT_PLANNING_PLUGIN_DIR . 'includes/class-event-planning-manager.php';
require_once EVENT_PLANNING_PLUGIN_DIR . 'includes/class-service-post-type.php';
require_once EVENT_PLANNING_PLUGIN_DIR . 'admin/class-admin.php';
require_once EVENT_PLANNING_PLUGIN_DIR . 'public/class-public.php';

// Activation hook
register_activation_hook(__FILE__, 'event_planning_activate');

/**
 * Plugin activation function
 */
function event_planning_activate() {
    // Create necessary database tables
    event_planning_create_tables();

    // Set default options
    add_option('event_planning_version', EVENT_PLANNING_VERSION);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'event_planning_deactivate');

/**
 * Plugin deactivation function
 */
function event_planning_deactivate() {
    // Clean up if needed
}

/**
 * Create necessary database tables
 */
function event_planning_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    // Services table
    $services_table = $wpdb->prefix . 'event_services';
    $sql_services = "CREATE TABLE $services_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        service_name varchar(255) NOT NULL,
        service_description text,
        service_category varchar(100) NOT NULL,
        base_price decimal(10,2),
        status varchar(20) DEFAULT 'active',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_services);
}

// Initialize the plugin
function event_planning_init() {
    Event_Planning_Manager::get_instance();
    new Event_Service_Admin();
    new Event_Service_Public();
}
add_action('plugins_loaded', 'event_planning_init');