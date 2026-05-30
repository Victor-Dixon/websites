<?php
/**
 * Runtime loader for FreeRideInvestor Content Engine.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once FREERIDEINVESTOR_CONTENT_ENGINE_PLUGIN_DIR . 'includes/post-types/cheat-sheet.php';
require_once FREERIDEINVESTOR_CONTENT_ENGINE_PLUGIN_DIR . 'includes/post-types/free-investor.php';
require_once FREERIDEINVESTOR_CONTENT_ENGINE_PLUGIN_DIR . 'includes/post-types/tbow-tactics.php';
require_once FREERIDEINVESTOR_CONTENT_ENGINE_PLUGIN_DIR . 'includes/custom-shortcodes.php';
