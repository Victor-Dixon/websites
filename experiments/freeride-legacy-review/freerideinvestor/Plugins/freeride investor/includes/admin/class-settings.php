<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FRI_Settings {

    /**
     * Initialize the settings functionality
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_settings_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    /**
     * Add the Settings page to the WordPress admin menu
     */
    public static function add_settings_menu() {
        add_options_page(
            __('Freeride Investor Settings', 'freeride-investor'), // Page title
            __('Freeride Investor', 'freeride-investor'),          // Menu title
            'manage_options',                                     // Capability
            'fri-settings',                                       // Menu slug
            [__CLASS__, 'render_settings_page']                   // Callback function
        );
    }

    /**
     * Register plugin settings
     */
    public static function register_settings() {
        register_setting('fri_settings_group', 'fri_alpha_vantage_key');
        register_setting('fri_settings_group', 'fri_finnhub_key');
        register_setting('fri_settings_group', 'fri_openai_key');

        add_settings_section(
            'fri_api_keys_section',                               // Section ID
            __('API Keys', 'freeride-investor'),                  // Title
            [__CLASS__, 'render_api_keys_section'],               // Callback
            'fri-settings'                                       // Page slug
        );

        add_settings_field(
            'alpha_vantage_key',                                  // Field ID
            __('Alpha Vantage API Key', 'freeride-investor'),     // Label
            [__CLASS__, 'render_text_field'],                     // Callback
            'fri-settings',                                      // Page slug
            'fri_api_keys_section',                              // Section ID
            ['id' => 'fri_alpha_vantage_key', 'option' => 'fri_alpha_vantage_key']
        );

        add_settings_field(
            'finnhub_key',                                        // Field ID
            __('Finnhub API Key', 'freeride-investor'),           // Label
            [__CLASS__, 'render_text_field'],                     // Callback
            'fri-settings',                                      // Page slug
            'fri_api_keys_section',                              // Section ID
            ['id' => 'fri_finnhub_key', 'option' => 'fri_finnhub_key']
        );

        add_settings_field(
            'openai_key',                                         // Field ID
            __('OpenAI API Key', 'freeride-investor'),            // Label
            [__CLASS__, 'render_text_field'],                     // Callback
            'fri-settings',                                      // Page slug
            'fri_api_keys_section',                              // Section ID
            ['id' => 'fri_openai_key', 'option' => 'fri_openai_key']
        );
    }

    /**
     * Render the Settings page
     */
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Freeride Investor Settings', 'freeride-investor'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('fri_settings_group'); // Security fields
                do_settings_sections('fri-settings');  // Render sections and fields
                submit_button();                       // Save button
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render the API Keys section description
     */
    public static function render_api_keys_section() {
        echo '<p>' . esc_html__('Enter your API keys for Alpha Vantage, Finnhub, and OpenAI. These are required for the plugin to function.', 'freeride-investor') . '</p>';
    }

    /**
     * Render a generic text input field
     *
     * @param array $args Field arguments
     */
    public static function render_text_field($args) {
        $value = get_option($args['option'], '');
        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="regular-text" />',
            esc_attr($args['id']),
            esc_attr($args['option']),
            esc_attr($value)
        );
    }
}

// Initialize the settings
FRI_Settings::init();
