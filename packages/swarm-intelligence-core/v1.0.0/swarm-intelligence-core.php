<?php
/**
 * Plugin Name: Swarm Intelligence Core
 * Plugin URI: https://weareswarm.site
 * Description: Core swarm intelligence functionality and agent coordination
 * Version: 1.0.0
 * Author: Swarm Intelligence Core Team
 * License: GPL v2 or later
 * Text Domain: swarm-intelligence-core
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SWARM_INTELLIGENCE_VERSION', '1.0.0');
define('SWARM_INTELLIGENCE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SWARM_INTELLIGENCE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SWARM_INTELLIGENCE_PLUGIN_DIR . 'includes/class-swarm-intelligence-core.php';
require_once SWARM_INTELLIGENCE_PLUGIN_DIR . 'includes/class-agent-coordinator.php';
require_once SWARM_INTELLIGENCE_PLUGIN_DIR . 'admin/class-admin.php';
require_once SWARM_INTELLIGENCE_PLUGIN_DIR . 'public/class-public.php';

// Activation hook
register_activation_hook(__FILE__, 'swarm_intelligence_activate');

/**
 * Plugin activation function
 */
function swarm_intelligence_activate() {
    // Create necessary database tables
    swarm_intelligence_create_tables();

    // Set default options
    add_option('swarm_intelligence_version', SWARM_INTELLIGENCE_VERSION);
    add_option('swarm_intelligence_max_agents', 50);
    add_option('swarm_intelligence_coordination_enabled', 'yes');
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'swarm_intelligence_deactivate');

/**
 * Plugin deactivation function
 */
function swarm_intelligence_deactivate() {
    // Clean up if needed
}

/**
 * Create necessary database tables
 */
function swarm_intelligence_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    // Agents table
    $agents_table = $wpdb->prefix . 'swarm_agents';
    $sql_agents = "CREATE TABLE $agents_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        agent_id varchar(100) NOT NULL,
        agent_name varchar(255) NOT NULL,
        agent_type varchar(50) NOT NULL,
        status varchar(20) DEFAULT 'inactive',
        capabilities text,
        last_seen datetime DEFAULT CURRENT_TIMESTAMP,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY agent_id (agent_id)
    ) $charset_collate;";

    // Coordination table
    $coordination_table = $wpdb->prefix . 'swarm_coordination';
    $sql_coordination = "CREATE TABLE $coordination_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        coordination_id varchar(100) NOT NULL,
        initiator_agent varchar(100) NOT NULL,
        target_agents text NOT NULL,
        task_type varchar(50) NOT NULL,
        status varchar(20) DEFAULT 'pending',
        priority varchar(10) DEFAULT 'normal',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY coordination_id (coordination_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_agents);
    dbDelta($sql_coordination);
}

// Initialize the plugin
function swarm_intelligence_init() {
    Swarm_Intelligence_Core::get_instance();
    new Swarm_Admin();
    new Swarm_Public();
}
add_action('plugins_loaded', 'swarm_intelligence_init');