<?php
/*
C:\TheTradingRobotPlugWeb\TheTradingRobotPlugin\class-thetradingrobotplugin.php
Plugin Name: The Trading Robot Plug Plugin
Plugin URI: https://TheTradingRobotPlug.com
Description: The core file of The Trading Robot Plug Plugin, responsible for defining constants and loading necessary files.
Version: 1.0.0
Author: Victor Dixon
Author URI: https://TheTradingRobotPlug.com
License: GPLv2 or later
Text Domain: thetradingrobotplugin
*/
/**
 * The core plugin class
 */
class TheTradingRobotPlugPlugin {

    /**
     * The loader responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var TheTradingRobotPlugPlugin_Loader
     */
    protected $loader;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-thetradingrobotplugin-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-thetradingrobotplugin-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-thetradingrobotplugin-public.php';

        $this->loader = new TheTradingRobotPlugPlugin_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     */
    private function define_admin_hooks() {
        $plugin_admin = new TheTradingRobotPlugPlugin_Admin();
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     */
    private function define_public_hooks() {
        $plugin_public = new TheTradingRobotPlugPlugin_Public();
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }
}
