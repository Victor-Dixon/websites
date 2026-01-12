<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\inc\customizer.php
Description: Customizer functionality for The Trading Robot Plug theme, allowing users to modify theme colors through the WordPress Customizer.
Version: 1.1.0
Author: Victor Dixon
*/

/**
 * Registers customizer settings and controls for modifying the theme colors.
 *
 * @param WP_Customize_Manager $wp_customize The Customizer object.
 */
function my_custom_theme_customize_register($wp_customize) {
    // Add a section
    $wp_customize->add_section('my_custom_theme_colors', array(
        'title' => __('Colors', 'my-custom-theme'),
        'description' => __('Modify the theme colors', 'my-custom-theme'),
        'priority' => 30,
    ));

    // Add a setting
    $wp_customize->add_setting('link_color', array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage', // Enables live preview
    ));

    // Add a control
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'link_color_control', array(
        'label' => __('Link Color', 'my-custom-theme'),
        'section' => 'my_custom_theme_colors',
        'settings' => 'link_color',
    )));
}

add_action('customize_register', 'my_custom_theme_customize_register');

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 */
function my_custom_theme_customize_preview_js() {
    wp_enqueue_script('my-custom-theme-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array('customize-preview'), '1.0.0', true);
}

add_action('customize_preview_init', 'my_custom_theme_customize_preview_js');
?>
