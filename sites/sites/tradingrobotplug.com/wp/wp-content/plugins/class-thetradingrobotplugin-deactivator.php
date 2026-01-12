<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\class-thetradingrobotplugin-runner.php
Plugin Name: The Trading Robot Plug Plugin
Plugin URI: https://TheTradingRobotPlug.com
Description: Handles the deactivation process for The Trading Robot Plug Plugin.
Version: 1.0.0
Author: Victor Dixon
Author URI: https://TheTradingRobotPlug.com
License: GPLv2 or later
Text Domain: thetradingrobotplugin
*/
/**
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class TheTradingRobotPlugPlugin_Deactivator {

    /**
     * Deactivate the plugin.
     *
     * This method runs when the plugin is deactivated. It handles the cleanup
     * of options, scheduled tasks, and any other temporary data that should be
     * removed or reset when the plugin is no longer active.
     */
    public static function deactivate() {
        // Clear scheduled tasks or cron jobs associated with the plugin.
        wp_clear_scheduled_hook('trading_robot_data_refresh_event');

        // Remove custom options or settings.
        delete_option('default_algorithm');
        delete_option('data_refresh_interval');
        delete_option('enable_notifications');

        // Perform any additional cleanup tasks.
        self::cleanup_temp_data();

        // Optionally, you can deactivate shortcodes, widgets, or other plugin components.
        // Example: unregister_widget('TradingRobotWidget');
    }

    /**
     * Cleanup temporary data.
     *
     * This method handles the removal of any temporary data or transients that
     * should not persist after the plugin is deactivated.
     */
    private static function cleanup_temp_data() {
        global $wpdb;

        // Example: Delete any transients related to the plugin.
        $result = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_trading_robot_%'");
        if ($result === false) {
            error_log('Failed to delete transients during deactivation.');
        }

        // Example: Remove temporary database tables if they exist.
        $temp_table_name = $wpdb->prefix . 'trading_robot_temp_data';
        $result = $wpdb->query("DROP TABLE IF EXISTS $temp_table_name");
        if ($result === false) {
            error_log("Failed to drop temporary table $temp_table_name during deactivation.");
        }

        // Add any other necessary cleanup tasks here.
    }
}
