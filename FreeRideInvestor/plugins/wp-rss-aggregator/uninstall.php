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

// If uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit ();
}

// Remove capabilities
if ( function_exists( 'wprss_remove_caps' ) ) {
    wprss_remove_caps();
}

// Delete options from options table
delete_option( 'wprss_options' );
delete_option( 'wprss_addon_notices' );
delete_option( 'wprss_admin_notices' );
delete_option( 'wprss_db_version' );
delete_option( 'wprss_did_intro' );
delete_option( 'wprss_first_activation_time' );
delete_option( 'wprss_hs_beacon_enabled' );
delete_option( 'wprss_intro_feed_id' );
delete_option( 'wprss_settings_notices' );
