<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\tests\class-thetradingrobotplugin-admin-test.php
*/

use PHPUnit\Framework\TestCase;

class TheTradingRobotPlugPlugin_Admin_Test extends TestCase {

    protected function setUp(): void {
        // Set up the WordPress environment for testing.
        if (!defined('ABSPATH')) {
            define('ABSPATH', true);
        }
        if (!defined('WP_CONTENT_DIR')) {
            define('WP_CONTENT_DIR', dirname(__FILE__) . '/wp-content');
        }
    }

    /**
     * Test that the menu page is added to the admin menu.
     */
    public function test_menu_page_added() {
        // Call the function to add the menu
        tradingrobotplugin_menu();

        // Check that the menu page is registered
        global $submenu;
        $this->assertArrayHasKey('tradingrobotplugin', $submenu['options-general.php']);
        $this->assertEquals('Trading Robot Plugin Settings', $submenu['options-general.php']['tradingrobotplugin'][0]);
    }

    /**
     * Test that the settings page content is displayed.
     */
    public function test_settings_page_content() {
        // Start output buffering
        ob_start();

        // Call the settings page function
        tradingrobotplugin_settings_page();

        // Get the output
        $output = ob_get_clean();

        // Check that the output contains expected content
        $this->assertStringContainsString('<h1>Trading Robot Plug Settings</h1>', $output);
        $this->assertStringContainsString('<form method="post" action="options.php">', $output);
    }

    /**
     * Test that the plugin options are sanitized correctly.
     */
    public function test_options_sanitization() {
        $input = array(
            'default_algorithm' => '<script>alert("xss");</script>simple_moving_average',
            'data_refresh_interval' => '30 minutes',
        );

        $sanitized = tradingrobotplugin_options_sanitize($input);

        // Check that the input was sanitized correctly
        $this->assertEquals('simple_moving_average', $sanitized['default_algorithm']);
        $this->assertEquals(30, $sanitized['data_refresh_interval']);
    }

    /**
     * Test that the default options are set on activation.
     */
    public function test_default_options_set_on_activation() {
        // Ensure no options exist before running the function
        delete_option('tradingrobotplugin_options');

        // Call the function to set default options
        tradingrobotplugin_default_options();

        // Check that the default options were set
        $options = get_option('tradingrobotplugin_options');
        $this->assertEquals('simple_moving_average', $options['default_algorithm']);
        $this->assertEquals(15, $options['data_refresh_interval']);
        $this->assertTrue($options['enable_notifications']);
    }

    /**
     * Test the settings initialization.
     */
    public function test_settings_initialization() {
        global $wp_settings_fields;

        // Initialize the settings
        tradingrobotplugin_settings_init();

        // Check that the settings were registered correctly
        $this->assertArrayHasKey('tradingrobotplugin', $wp_settings_fields);
        $this->assertArrayHasKey('tradingrobotplugin_settings_section', $wp_settings_fields['tradingrobotplugin']);
    }
}
