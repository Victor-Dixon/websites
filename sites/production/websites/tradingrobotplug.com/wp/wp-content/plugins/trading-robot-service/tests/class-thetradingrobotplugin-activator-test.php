<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\tests\class-thetradingrobotplugin-activator-test.php
*/

use PHPUnit\Framework\TestCase;

/**
 * @covers TheTradingRobotPlugPlugin_Activator
 */
class TheTradingRobotPlugPlugin_Activator_Test extends TestCase {

    protected function setUp(): void {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function test_activate_creates_table() {
        global $wpdb;

        // Set up the table name to check
        $table_name = esc_sql($wpdb->prefix) . 'trading_robot_settings';

        // Ensure the table does not exist before activation
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // Call the activation method
        TheTradingRobotPlugPlugin_Activator::activate();

        // Check that the table was created
        $this->assertEquals($table_name, $wpdb->get_var("SHOW TABLES LIKE '$table_name'"));
    }

    public function test_activate_sets_default_options() {
        // Define the expected default options
        $default_options = array(
            'default_algorithm' => 'simple_moving_average',
            'data_refresh_interval' => 15, // in minutes
            'enable_notifications' => true,
        );

        // Call the activation method
        TheTradingRobotPlugPlugin_Activator::activate();

        // Check that each default option was set
        foreach ($default_options as $key => $expected_value) {
            $this->assertEquals($expected_value, get_option($key));
        }
    }

    public function test_activate_fails_on_low_php_version() {
        // Mock the PHP version to be below the required version
        $original_php_version = PHP_VERSION;
        define('PHP_VERSION', '7.3');

        // Expect wp_die to be called
        $this->expectException(\WPDieException::class);

        // Call the activation method and catch the exception
        TheTradingRobotPlugPlugin_Activator::activate();

        // Restore the original PHP version
        define('PHP_VERSION', $original_php_version);
    }

    public function test_activate_logs_error_on_table_creation_failure() {
        global $wpdb;

        // Set up an invalid table name to force an error
        $table_name = esc_sql($wpdb->prefix) . 'trading_robot_settings';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // Mock dbDelta to simulate a failure
        $wpdb->query = function($sql) {
            return false; // Simulate failure
        };

        // Call the activation method
        TheTradingRobotPlugPlugin_Activator::activate();

        // Check if the log file was created and contains the error message
        $log_file = WP_CONTENT_DIR . '/tradingrobotplugin_error.log';
        $this->assertFileExists($log_file);
        $this->assertStringContainsString('Table creation failed', file_get_contents($log_file));
    }
}
