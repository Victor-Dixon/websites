<?php
/**
 * Plugin Name: [PLUGIN_NAME]
 * Description: [PLUGIN_DESCRIPTION]
 * Version: 1.0.0
 * Author: FreeRideInvestor
 * License: GPL v2 or later
 * Text Domain: [plugin-domain]
 *
 * Security Features:
 * - Input sanitization
 * - SQL injection protection
 * - CSRF protection
 * - XSS prevention
 */

/**
 * Uninstall FreerideInvestor Plugin
 *
 * @package FreerideInvestor
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Exit if accessed directly
}

// Example: If you stored any options, delete them here.
// delete_option('freerideinvestor_option_name');

// If you created custom database tables, drop them.
global $wpdb;
$table1 = $wpdb->prefix . 'freerideinvestor_portfolio';
$table2 = $wpdb->prefix . 'freerideinvestor_settings';

// Table names cannot be parameterized in prepare()
$wpdb->query("DROP TABLE IF EXISTS $table1");
$wpdb->query("DROP TABLE IF EXISTS $table2");

// Add any other cleanup tasks here.
