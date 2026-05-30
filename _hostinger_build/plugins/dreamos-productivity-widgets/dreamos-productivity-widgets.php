<?php
/**
 * Plugin Name: DreamOS Productivity Widgets
 * Description: Checklist, pomodoro, and dashboard widgets for WordPress sites.
 * Version: 0.1.0
 * Author: DreamOS.ai
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DREAMOS_PRODUCTIVITY_WIDGETS_VERSION', '0.1.0');
define('DREAMOS_PRODUCTIVITY_WIDGETS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DREAMOS_PRODUCTIVITY_WIDGETS_PLUGIN_URL', plugin_dir_url(__FILE__));

final class DreamosProductivityWidgets {
    public static function init(): void {
        add_action('init', [__CLASS__, 'register']);
        add_shortcode('dreamos_productivity_widgets_status', [__CLASS__, 'status_shortcode']);
    }

    public static function register(): void {
        // TODO: wire reviewed includes from /includes after audit.
    }

    public static function status_shortcode(): string {
        return '<div class="dreamos-productivity-widgets-status">Plugin loaded: DreamOS Productivity Widgets</div>';
    }
}

DreamosProductivityWidgets::init();
