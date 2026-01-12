<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\tests\class-thetradingrobotplugin-deactivator-test.php
*/

use PHPUnit\Framework\TestCase;

class TheTradingRobotPlugPlugin_Deactivator_Test extends TestCase {

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
     * Test that scheduled tasks are cleared on deactivation.
     */
    public function test_scheduled_tasks_cleared() {
        // Set a scheduled hook to simulate the plugin's cron job.
        wp_schedule_event(time(), 'hourly', 'trading_robot_data_refresh_event');

        // Ensure the event is scheduled.
        $this->assertNotFalse(wp_next_scheduled('trading_robot_data_refresh_event'));

        // Deactivate the plugin.
        TheTradingRobotPlugPlugin_Deactivator::deactivate();

        // Ensure the event is cleared.
        $this->assertFalse(wp_next_scheduled('trading_robot_data_refresh_event'));
    }

    /**
     * Test that plugin options are deleted on deactivation.
     */
    public function test_options_deleted_on_deactivation() {
        // Add some options to simulate plugin settings.
        add_option('default_algorithm', 'simple_moving_average');
        add_option('data_refresh_interval', 15);
        add_option('enable_notifications', true);

        // Ensure the options are set.
        $this->assertEquals('simple_moving_average', get_option('default_algorithm'));
        $this->assertEquals(15, get_option('data_refresh_interval'));
        $this->assertTrue(get_option('enable_notifications'));

        // Deactivate the plugin.
        TheTradingRobotPlugPlugin_Deactivator::deactivate();

        // Ensure the options are deleted.
        $this->assertFalse(get_option('default_algorithm'));
        $this->assertFalse(get_option('data_refresh_interval'));
        $this->assertFalse(get_option('enable_notifications'));
    }

    /**
     * Test that temporary data is cleaned up on deactivation.
     */
    public function test_temp_data_cleanup() {
        global $wpdb;

        // Set up a transient and a temporary table to simulate plugin data.
        set_transient('trading_robot_sample_transient', 'sample_data', 60);
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}trading_robot_temp_data (id INT)");

        // Ensure the transient and table exist.
        $this->assertNotFalse(get_transient('trading_robot_sample_transient'));
        $this->assertTrue($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}trading_robot_temp_data'") !== null);

        // Deactivate the plugin.
        TheTradingRobotPlugPlugin_Deactivator::deactivate();

        // Ensure the transient is deleted and the table is dropped.
        $this->assertFalse(get_transient('trading_robot_sample_transient'));
        $this->assertFalse($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}trading_robot_temp_data'"));
    }

    /**
     * Test that cleanup errors are logged correctly.
     */
    public function test_cleanup_errors_logged() {
        global $wpdb;

        // Mock the query method to return false to simulate a cleanup failure.
        $wpdb = $this->getMockBuilder('wpdb')
            ->disableOriginalConstructor()
            ->getMock();

        $wpdb->method('query')
            ->will($this->returnValue(false));

        // Capture the logs.
        $log_file = WP_CONTENT_DIR . '/debug.log';
        @unlink($log_file); // Ensure the log file is empty before testing.

        // Deactivate the plugin and check for logged errors.
        TheTradingRobotPlugPlugin_Deactivator::deactivate();

        $this->assertFileExists($log_file);
        $log_content = file_get_contents($log_file);
        $this->assertStringContainsString('Failed to delete transients during deactivation.', $log_content);
        $this->assertStringContainsString('Failed to drop temporary table', $log_content);
    }
}
