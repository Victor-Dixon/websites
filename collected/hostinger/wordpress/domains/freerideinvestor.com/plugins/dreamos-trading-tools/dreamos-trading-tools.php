<?php
/**
 * Plugin Name: DreamOS Trading Tools
 * Description: Trading research, stock data, shortcode, and dashboard utilities staged from DreamOS website repos.
 * Version: 0.1.0
 * Author: DreamOS.ai
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DREAMOS_TRADING_TOOLS_VERSION', '0.1.0');
define('DREAMOS_TRADING_TOOLS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DREAMOS_TRADING_TOOLS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once DREAMOS_TRADING_TOOLS_PLUGIN_DIR . 'includes/loader.php';

final class DreamosTradingTools {
    public static function init(): void {
        add_action('init', [__CLASS__, 'register']);
        add_shortcode('dreamos_trading_tools_status', [__CLASS__, 'status_shortcode']);
    }

    public static function register(): void {
        // TODO: wire reviewed includes from /includes after audit.
    }

    public static function status_shortcode(): string {
        return '<div class="dreamos-trading-tools-status">Plugin loaded: DreamOS Trading Tools</div>';
    }
}

DreamosTradingTools::init();
