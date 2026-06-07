<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FRI_Clear_Cache {

    /**
     * Initialize the class and set up actions
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_clear_cache_menu']);
    }

    /**
     * Add Clear Cache menu to the WordPress admin Tools menu
     */
    public static function add_clear_cache_menu() {
        add_submenu_page(
            'tools.php',                            // Parent menu (Tools)
            __('Clear Cache', 'freeride-investor'), // Page title
            __('Clear Cache', 'freeride-investor'), // Menu title
            'manage_options',                       // Capability
            'fri-clear-cache',                      // Menu slug
            [__CLASS__, 'render_clear_cache_page']  // Callback function
        );
    }

    /**
     * Render the Clear Cache admin page
     */
    public static function render_clear_cache_page() {
        if (isset($_POST['clear_cache_action']) && check_admin_referer('fri_clear_cache_nonce')) {
            FRI_Cache_Manager::clear();
            echo '<div class="notice notice-success"><p>' . __('Cache cleared successfully.', 'freeride-investor') . '</p></div>';
        }

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Clear Cache', 'freeride-investor'); ?></h1>
            <p><?php esc_html_e('Use this tool to clear all cached data for the FreerideInvestor plugin.', 'freeride-investor'); ?></p>
            <form method="post">
                <?php wp_nonce_field('fri_clear_cache_nonce'); ?>
                <input type="hidden" name="clear_cache_action" value="1">
                <button type="submit" class="button button-primary">
                    <?php esc_html_e('Clear Cache', 'freeride-investor'); ?>
                </button>
            </form>
        </div>
        <?php
    }
}

// Initialize the class
FRI_Clear_Cache::init();
