<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FRI_Log_Viewer {

    /**
     * Log file path
     */
    private static $log_file = FRI_LOG_FILE; // Uses the defined log file path

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_log_viewer_menu']);
    }

    /**
     * Add Log Viewer menu to the WordPress admin Tools menu
     */
    public static function add_log_viewer_menu() {
        add_submenu_page(
            'tools.php',                            // Parent menu (Tools)
            __('Freeride Log Viewer', 'freeride-investor'), // Page title
            __('Log Viewer', 'freeride-investor'),          // Menu title
            'manage_options',                       // Capability
            'fri-log-viewer',                       // Menu slug
            [__CLASS__, 'render_log_viewer_page']   // Callback function
        );
    }

    /**
     * Render the Log Viewer admin page
     */
    public static function render_log_viewer_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle log clearing
        if (isset($_POST['clear_log_action']) && check_admin_referer('fri_clear_log_nonce')) {
            if (file_exists(self::$log_file)) {
                file_put_contents(self::$log_file, ''); // Clear the file content
                echo '<div class="notice notice-success"><p>' . __('Log cleared successfully.', 'freeride-investor') . '</p></div>';
            } else {
                echo '<div class="notice notice-warning"><p>' . __('Log file does not exist.', 'freeride-investor') . '</p></div>';
            }
        }

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Freeride Log Viewer', 'freeride-investor'); ?></h1>

            <form method="post">
                <?php wp_nonce_field('fri_clear_log_nonce'); ?>
                <input type="hidden" name="clear_log_action" value="1">
                <button type="submit" class="button button-secondary">
                    <?php esc_html_e('Clear Log', 'freeride-investor'); ?>
                </button>
            </form>

            <h2><?php esc_html_e('Log Contents', 'freeride-investor'); ?></h2>

            <div style="white-space: pre-wrap; background: #1e1e1e; color: #ffffff; padding: 15px; border-radius: 5px; max-height: 500px; overflow-y: scroll; font-family: monospace;">
                <?php
                if (file_exists(self::$log_file)) {
                    $log_contents = file_get_contents(self::$log_file);
                    if (!empty($log_contents)) {
                        echo esc_html($log_contents);
                    } else {
                        echo '<p>' . __('The log is empty.', 'freeride-investor') . '</p>';
                    }
                } else {
                    echo '<p>' . __('Log file not found.', 'freeride-investor') . '</p>';
                }
                ?>
            </div>
        </div>
        <?php
    }
}

// Initialize the log viewer
FRI_Log_Viewer::init();
