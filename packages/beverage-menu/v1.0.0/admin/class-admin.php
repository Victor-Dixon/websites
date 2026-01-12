<?php
/**
 * Admin Class for Beverage Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class Beverage_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=beverage',
            __('Beverage Menu Settings', 'beverage-menu'),
            __('Settings', 'beverage-menu'),
            'manage_options',
            'beverage-menu-settings',
            array($this, 'settings_page')
        );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Beverage Menu Settings', 'beverage-menu'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('beverage_menu_settings');
                do_settings_sections('beverage_menu_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_admin_scripts($hook) {
        if ('beverage_page_beverage-menu-settings' !== $hook) {
            return;
        }

        wp_enqueue_script('beverage-admin-js', BEVERAGE_MENU_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), BEVERAGE_MENU_VERSION, true);
        wp_enqueue_style('beverage-admin-css', BEVERAGE_MENU_PLUGIN_URL . 'admin/css/admin.css', array(), BEVERAGE_MENU_VERSION);
    }
}