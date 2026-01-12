<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\tests\class-thetradingrobotplugin-runner-test.php
*/

use PHPUnit\Framework\TestCase;

class TheTradingRobotPlugPlugin_Runner_Test extends TestCase {

    protected function setUp(): void {
        // Set up the WordPress environment for testing.
        if (!defined('ABSPATH')) {
            define('ABSPATH', true);
        }
        if (!defined('WPINC')) {
            define('WPINC', true);
        }
    }

    /**
     * Test that the run method initializes the plugin components.
     */
    public function test_plugin_run_initializes_components() {
        $runner = $this->getMockBuilder('TheTradingRobotPlugPlugin_Runner')
            ->setMethods(['initialize_trading_algorithms', 'enqueue_scripts_and_styles', 'register_shortcodes', 'register_hooks'])
            ->getMock();

        $runner->expects($this->once())->method('initialize_trading_algorithms');
        $runner->expects($this->once())->method('enqueue_scripts_and_styles');
        $runner->expects($this->once())->method('register_shortcodes');
        $runner->expects($this->once())->method('register_hooks');

        // Run the plugin
        $runner->run();
    }

    /**
     * Test that trading algorithms are initialized based on the default option.
     */
    public function test_initialize_trading_algorithms() {
        update_option('default_algorithm', 'simple_moving_average');

        $runner = $this->getMockBuilder('TheTradingRobotPlugPlugin_Runner')
            ->setMethods(['initialize_trading_algorithms'])
            ->getMock();

        // Ensure the correct algorithm is initialized
        $runner->expects($this->once())
            ->method('initialize_trading_algorithms')
            ->will($this->returnCallback(function () {
                $default_algorithm = get_option('default_algorithm', 'simple_moving_average');
                $this->assertEquals('simple_moving_average', $default_algorithm);
            }));

        $runner->run();
    }

    /**
     * Test that scripts and styles are enqueued.
     */
    public function test_enqueue_scripts_and_styles() {
        $runner = $this->getMockBuilder('TheTradingRobotPlugPlugin_Runner')
            ->setMethods(['enqueue_scripts_and_styles'])
            ->getMock();

        // Expect that scripts and styles are enqueued
        $runner->expects($this->once())->method('enqueue_scripts_and_styles');

        $runner->run();
    }

    /**
     * Test that shortcodes are registered.
     */
    public function test_register_shortcodes() {
        $runner = $this->getMockBuilder('TheTradingRobotPlugPlugin_Runner')
            ->setMethods(['register_shortcodes'])
            ->getMock();

        // Expect that shortcodes are registered
        $runner->expects($this->once())->method('register_shortcodes');

        $runner->run();

        // Check that the shortcode is registered
        $this->assertTrue(shortcode_exists('trading_robot_data'));
    }

    /**
     * Test that hooks are registered.
     */
    public function test_register_hooks() {
        $runner = $this->getMockBuilder('TheTradingRobotPlugPlugin_Runner')
            ->setMethods(['register_hooks', 'initialize_plugin'])
            ->getMock();

        // Expect hooks to be registered
        $runner->expects($this->once())->method('register_hooks');
        $runner->expects($this->once())->method('initialize_plugin');

        $runner->run();

        // Ensure the hooks were added
        $this->assertGreaterThan(0, has_action('init', [$runner, 'initialize_plugin']));
        $this->assertGreaterThan(0, has_action('wp_enqueue_scripts', [$runner, 'enqueue_scripts_and_styles']));
    }
}
