<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\class-thetradingrobotplugin-admin.php
Plugin Name: The Trading Robot Plug Plugin
Plugin URI: https://TheTradingRobotPlug.com
Description: Admin settings and menu for The Trading Robot Plug Plugin, allowing users to configure plugin settings within the WordPress admin dashboard.
Version: 1.0.0
Author: Victor Dixon
Author URI: https://TheTradingRobotPlug.com
License: GPLv2 or later
Text Domain: thetradingrobotplugin
*/

/**
 * Add a settings page to the WordPress admin menu.
 */
function tradingrobotplugin_menu() {
    add_options_page(
        'Trading Robot Plugin Settings',
        'Trading Robot Plug',
        'manage_options',
        'tradingrobotplugin',
        'tradingrobotplugin_settings_page'
    );
}
add_action('admin_menu', 'tradingrobotplugin_menu');

/**
 * Display the settings page content.
 */
function tradingrobotplugin_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Trading Robot Plug Settings', 'thetradingrobotplugin'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('tradingrobotplugin_settings');
            do_settings_sections('tradingrobotplugin');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Initialize settings for the plugin.
 */
function tradingrobotplugin_settings_init() {
    register_setting('tradingrobotplugin_settings', 'tradingrobotplugin_options', 'tradingrobotplugin_options_sanitize');

    add_settings_section(
        'tradingrobotplugin_settings_section',
        __('General Settings', 'tradingrobotplugin'),
        'tradingrobotplugin_settings_section_callback',
        'tradingrobotplugin'
    );

    add_settings_field(
        'default_algorithm',
        __('Default Algorithm', 'tradingrobotplugin'),
        'tradingrobotplugin_default_algorithm_render',
        'tradingrobotplugin',
        'tradingrobotplugin_settings_section'
    );

    add_settings_field(
        'data_refresh_interval',
        __('Data Refresh Interval (minutes)', 'tradingrobotplugin'),
        'tradingrobotplugin_data_refresh_interval_render',
        'tradingrobotplugin',
        'tradingrobotplugin_settings_section'
    );
}
add_action('admin_init', 'tradingrobotplugin_settings_init');

/**
 * Sanitize and validate plugin options.
 */
function tradingrobotplugin_options_sanitize($input) {
    $sanitized_input = array();
    $sanitized_input['default_algorithm'] = sanitize_text_field($input['default_algorithm']);
    $sanitized_input['data_refresh_interval'] = intval($input['data_refresh_interval']);
    return $sanitized_input;
}

/**
 * Callback for the settings section description.
 */
function tradingrobotplugin_settings_section_callback() {
    echo __('Configure the settings for Trading Robot Plug Plugin.', 'tradingrobotplugin');
}

/**
 * Render the input field for the 'Default Algorithm' setting.
 */
function tradingrobotplugin_default_algorithm_render() {
    $options = get_option('tradingrobotplugin_options');
    ?>
    <input type="text" name="tradingrobotplugin_options[default_algorithm]" value="<?php echo esc_attr($options['default_algorithm']); ?>">
    <?php
}

/**
 * Render the input field for the 'Data Refresh Interval' setting.
 */
function tradingrobotplugin_data_refresh_interval_render() {
    $options = get_option('tradingrobotplugin_options');
    ?>
    <input type="number" name="tradingrobotplugin_options[data_refresh_interval]" value="<?php echo esc_attr($options['data_refresh_interval']); ?>">
    <?php
}

/**
 * Set default options on plugin activation.
 */
function tradingrobotplugin_default_options() {
    $default_options = array(
        'default_algorithm' => 'simple_moving_average',
        'data_refresh_interval' => 15, // in minutes
        'enable_notifications' => true,
    );

    if (!get_option('tradingrobotplugin_options')) {
        add_option('tradingrobotplugin_options', $default_options);
    }
}
add_action('admin_init', 'tradingrobotplugin_default_options');
