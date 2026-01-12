<?php
/**
 * Main Beverage Menu Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Beverage_Menu {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('beverage-menu', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
}