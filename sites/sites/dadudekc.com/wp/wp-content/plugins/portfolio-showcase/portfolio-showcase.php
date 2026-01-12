<?php
/**
 * Plugin Name: Portfolio Showcase
 * Plugin URI: https://dadudekc.com
 * Description: Professional portfolio showcase and project management system
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 * License: GPL v2 or later
 * Text Domain: portfolio-showcase
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PORTFOLIO_SHOWCASE_VERSION', '1.0.0');
define('PORTFOLIO_SHOWCASE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PORTFOLIO_SHOWCASE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once PORTFOLIO_SHOWCASE_PLUGIN_DIR . 'includes/class-portfolio-showcase.php';
require_once PORTFOLIO_SHOWCASE_PLUGIN_DIR . 'includes/class-portfolio-post-type.php';
require_once PORTFOLIO_SHOWCASE_PLUGIN_DIR . 'admin/class-admin.php';
require_once PORTFOLIO_SHOWCASE_PLUGIN_DIR . 'public/class-public.php';

// Activation hook
register_activation_hook(__FILE__, 'portfolio_showcase_activate');

/**
 * Plugin activation function
 */
function portfolio_showcase_activate() {
    // Create necessary database tables
    portfolio_showcase_create_tables();

    // Set default options
    add_option('portfolio_showcase_version', PORTFOLIO_SHOWCASE_VERSION);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'portfolio_showcase_deactivate');

/**
 * Plugin deactivation function
 */
function portfolio_showcase_deactivate() {
    // Clean up if needed
}

/**
 * Create necessary database tables
 */
function portfolio_showcase_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    // Portfolio projects table
    $projects_table = $wpdb->prefix . 'portfolio_projects';
    $sql_projects = "CREATE TABLE $projects_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        project_name varchar(255) NOT NULL,
        project_description text,
        project_category varchar(100) NOT NULL,
        client_name varchar(255),
        completion_date date,
        project_url varchar(255),
        status varchar(20) DEFAULT 'active',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_projects);
}

// Initialize the plugin
function portfolio_showcase_init() {
    Portfolio_Showcase::get_instance();
    new Portfolio_Admin();
    new Portfolio_Public();
}
add_action('plugins_loaded', 'portfolio_showcase_init');