<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\class-thetradingrobotplugin-activator.php
Plugin Name: The Trading Robot Plug Plugin
Plugin URI: https://TheTradingRobotPlug.com
Description: Handles the activation process for The Trading Robot Plug Plugin.
Version: 1.0.0
Author: Victor Dixon
Author URI: https://TheTradingRobotPlug.com
License: GPLv2 or later
Text Domain: thetradingrobotplugin
*/

/**
 * This class defines all code necessary to run during the plugin's activation.
 */
class TheTradingRobotPlugPlugin_Activator {

    /**
     * Activate the plugin.
     */
    public static function activate() {
        global $wpdb;

        // Ensure minimum PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            wp_die(__('The Trading Robot Plugin requires PHP 7.4 or higher.', 'thetradingrobotplugin'));
        }

        // Set charset and collate for the table
        $charset_collate = $wpdb->get_charset_collate();
        
        // Sanitize the table prefix just in case
        $table_name = esc_sql($wpdb->prefix) . 'trading_robot_settings';

        // SQL to create the custom table
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            option_name varchar(191) NOT NULL,
            option_value longtext NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY option_name (option_name)
        ) $charset_collate;";

        // Include the upgrade.php file to use dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Execute the SQL statement
        dbDelta($sql);

        // Check for any errors during table creation
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Define the log file path
            $log_file = WP_CONTENT_DIR . '/tradingrobotplugin_error.log';
            if (!is_writable($log_file)) {
                wp_die(__('The Trading Robot Plugin could not write to the log file.', 'thetradingrobotplugin'));
            }
            // Log the error to a custom log file
            error_log('The Trading Robot Plugin: Table creation failed.', 3, $log_file);
            wp_die(__('The Trading Robot Plugin could not create the required database table.', 'thetradingrobotplugin'));
        }

        // Set default options if not already present
        $default_options = array(
            'default_algorithm' => 'simple_moving_average',
            'data_refresh_interval' => 15, // in minutes
            'enable_notifications' => true,
        );

        foreach ($default_options as $key => $value) {
            add_option($key, $value);
        }

        // Add any additional setup tasks here, such as scheduling cron jobs
    }
}
