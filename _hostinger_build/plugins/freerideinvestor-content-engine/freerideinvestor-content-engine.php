<?php
/**
 * Plugin Name: FreeRideInvestor Content Engine
 * Description: Content types, shortcodes, archive tools, and publishing utilities for the FreeRideInvestor domain model.
 * Version: 0.1.0
 * Author: DreamOS.ai
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FREERIDEINVESTOR_CONTENT_ENGINE_VERSION', '0.1.0');
define('FREERIDEINVESTOR_CONTENT_ENGINE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FREERIDEINVESTOR_CONTENT_ENGINE_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once FREERIDEINVESTOR_CONTENT_ENGINE_PLUGIN_DIR . 'includes/loader.php';

final class FreerideinvestorContentEngine {
    public static function init(): void {
        add_action('init', [__CLASS__, 'register']);
        add_shortcode('freerideinvestor_content_engine_status', [__CLASS__, 'status_shortcode']);
    }

    public static function register(): void {
        // TODO: wire reviewed includes from /includes after audit.
    }

    public static function status_shortcode(): string {
        return '<div class="freerideinvestor-content-engine-status">Plugin loaded: FreeRideInvestor Content Engine</div>';
    }
}

FreerideinvestorContentEngine::init();
