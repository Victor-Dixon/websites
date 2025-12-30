<?php
/**
 * TradingRobotPlug Theme - Modular Functions Loader
 * 
 * Professional dark theme for automated trading tools platform
 * Modular architecture following V2 compliance principles
 * 
 * @package TradingRobotPlug
 * @version 2.0.0
 * @since 2025-12-25
 * @author Agent-7 (Web Development) + Swarm Coordination
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme version for cache busting
define('TRP_THEME_VERSION', '2.0.0');
define('TRP_THEME_DIR', get_template_directory());
define('TRP_THEME_URI', get_template_directory_uri());

/**
 * Load modular theme components
 * Following V2 compliance: files < 300 lines, functions < 30 lines
 */
$inc_dir = TRP_THEME_DIR . '/inc';

// Core theme setup
require_once $inc_dir . '/theme-setup.php';

// Asset enqueuing (styles, scripts, dark theme)
require_once $inc_dir . '/asset-enqueue.php';

// REST API endpoints for trading data
require_once $inc_dir . '/rest-api.php';

// Dashboard REST API endpoints
require_once $inc_dir . '/dashboard-api.php';

// Charts REST API endpoints
require_once $inc_dir . '/charts-api.php';

// Analytics integration (GA4, Facebook Pixel)
require_once $inc_dir . '/analytics.php';

// Form handlers (waitlist, contact)
require_once $inc_dir . '/forms.php';

// Template helpers and 404 fixes
require_once $inc_dir . '/template-helpers.php';

// Admin theme options (existing)
if (file_exists(TRP_THEME_DIR . '/admin/theme-options.php')) {
    require_once TRP_THEME_DIR . '/admin/theme-options.php';
}

// Custom shortcodes (existing)
if (file_exists(TRP_THEME_DIR . '/admin/shortcodes.php')) {
    require_once TRP_THEME_DIR . '/admin/shortcodes.php';
}
