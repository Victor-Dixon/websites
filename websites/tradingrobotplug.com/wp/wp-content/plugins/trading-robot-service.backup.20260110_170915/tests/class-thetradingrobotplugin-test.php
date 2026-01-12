<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\tests\class-thetradingrobotplugin-test.php
*/

use PHPUnit\Framework\TestCase;

class TheTradingRobotPlugPlugin_Test extends TestCase {

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
     * Test that the plugin class is instantiated correctly and dependencies are loaded.
     */
    public function test_plugin_instantiation_and_dependencies() {
        // Mock the loader
        $loader = $this->createMock('TheTradingRobotPlugPlugin_Loader');
        $loader->expects($this->once())
               ->method('add_action');

        // Mock the admin and public classes
        $admin = $this->createMock('TheTradingRobotPlugPlugin_Admin');
        $public = $this->createMock('TheTradingRobotPlugPlugin_Public');

        // Ensure the dependencies are loaded and the loader is assigned
        $plugin = $this->getMockBuilder('TheTradingRobotPlugPlugin')
                       ->setMethods(['load_dependencies', 'define_admin_hooks', 'define_public_hooks'])
                       ->getMock();

        $plugin->expects($this->once())
               ->method('load_dependencies')
               ->willReturnSelf();

        $plugin->expects($this->once())
               ->method('define_admin_hooks')
               ->willReturnSelf();

        $plugin->expects($this->once())
               ->method('define_public_hooks')
               ->willReturnSelf();

        $plugin->__construct();
    }

    /**
     * Test that admin hooks are defined correctly.
     */
    public function test_define_admin_hooks() {
        // Mock the loader
        $loader = $this->createMock('TheTradingRobotPlugPlugin_Loader');
        $loader->expects($this->once())
               ->method('add_action')
               ->with('admin_menu', $this->isInstanceOf('TheTradingRobotPlugPlugin_Admin'), 'add_plugin_admin_menu');

        // Create the plugin instance
        $plugin = $this->getMockBuilder('TheTradingRobotPlugPlugin')
                       ->disableOriginalConstructor()
                       ->getMock();

        // Assign the mock loader to the plugin
        $plugin->loader = $loader;

        // Call the method to define admin hooks
        $plugin->define_admin_hooks();
    }

    /**
     * Test that public hooks are defined correctly.
     */
    public function test_define_public_hooks() {
        // Mock the loader
        $loader = $this->createMock('TheTradingRobotPlugPlugin_Loader');
        $loader->expects($this->exactly(2))
               ->method('add_action')
               ->withConsecutive(
                   ['wp_enqueue_scripts', $this->isInstanceOf('TheTradingRobotPlugPlugin_Public'), 'enqueue_styles'],
                   ['wp_enqueue_scripts', $this->isInstanceOf('TheTradingRobotPlugPlugin_Public'), 'enqueue_scripts']
               );

        // Create the plugin instance
        $plugin = $this->getMockBuilder('TheTradingRobotPlugPlugin')
                       ->disableOriginalConstructor()
                       ->getMock();

        // Assign the mock loader to the plugin
        $plugin->loader = $loader;

        // Call the method to define public hooks
        $plugin->define_public_hooks();
    }

    /**
     * Test that the loader's run method is called.
     */
    public function test_run_loader() {
        // Mock the loader
        $loader = $this->createMock('TheTradingRobotPlugPlugin_Loader');
        $loader->expects($this->once())
               ->method('run');

        // Create the plugin instance
        $plugin = $this->getMockBuilder('TheTradingRobotPlugPlugin')
                       ->disableOriginalConstructor()
                       ->getMock();

        // Assign the mock loader to the plugin
        $plugin->loader = $loader;

        // Call the run method
        $plugin->run();
    }
}
