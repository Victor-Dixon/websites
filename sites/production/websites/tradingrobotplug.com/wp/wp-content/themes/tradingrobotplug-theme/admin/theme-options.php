<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\admin\theme-options.php
Description: Admin panel options for The Trading Robot Plug theme, allowing customization of theme settings through a custom options page.
Version: 1.0.0
Author: Victor Dixon
*/

// Add Theme Options Page
function trading_robot_plug_add_admin_page() {
    add_menu_page(
        'Trading Robot Plug Options',          // Page title
        'Trading Robot Plug',                  // Menu title
        'manage_options',                      // Capability
        'trading_robot_plug',                  // Menu slug
        'trading_robot_plug_create_page',      // Function to display the page content
        'dashicons-admin-generic',             // Icon
        110                                    // Position
    );
}
add_action('admin_menu', 'trading_robot_plug_add_admin_page');

// Create the Theme Options Page
function trading_robot_plug_create_page() {
    // Generate the HTML content for the theme options page
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Trading Robot Plug Theme Options', 'my-custom-theme'); ?></h1>
        <form method="post" action="options.php">
            <?php
            // Display necessary fields for the options page
            settings_fields('trading_robot_plug_options_group');
            do_settings_sections('trading_robot_plug');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register and Define the Theme Options Settings
function trading_robot_plug_settings() {
    // Register the settings
    register_setting('trading_robot_plug_options_group', 'trading_robot_plug_options');

    // Add a section for general settings
    add_settings_section(
        'trading_robot_plug_general_section',
        __('General Settings', 'my-custom-theme'),
        'trading_robot_plug_general_section_callback',
        'trading_robot_plug'
    );

    // Add a field for setting a custom logo URL
    add_settings_field(
        'custom_logo_url',
        __('Custom Logo URL', 'my-custom-theme'),
        'trading_robot_plug_custom_logo_url_render',
        'trading_robot_plug',
        'trading_robot_plug_general_section'
    );
}
add_action('admin_init', 'trading_robot_plug_settings');

// Section Callback
function trading_robot_plug_general_section_callback() {
    echo __('Customize the general settings of the Trading Robot Plug theme.', 'my-custom-theme');
}

// Field Rendering Function
function trading_robot_plug_custom_logo_url_render() {
    $options = get_option('trading_robot_plug_options');
    ?>
    <input type="text" name="trading_robot_plug_options[custom_logo_url]" value="<?php echo esc_attr($options['custom_logo_url']); ?>" />
    <?php
}
