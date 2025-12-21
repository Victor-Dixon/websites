<?php
/**
 * PREVIEW TEMPLATES PAGE
 * 
 * Access this page to preview Free vs Premium views
 * 
 * URL: yoursite.com/wp-content/plugins/freeride-automated-trading-plan/preview-templates.php
 * 
 * Or create a WordPress page and include this content
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('You must be an administrator to view this preview.');
}

// Load plugin
define('FRATP_PLUGIN_DIR', __DIR__ . '/');
define('FRATP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load required classes
require_once(__DIR__ . '/includes/class-fratp-membership.php');
require_once(__DIR__ . '/includes/class-fratp-strategy-calculator.php');
require_once(__DIR__ . '/includes/class-fratp-market-data.php');
require_once(__DIR__ . '/includes/class-fratp-plan-generator.php');

// Include preview template
include(__DIR__ . '/templates/frontend/preview-demo.php');



