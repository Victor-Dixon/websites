<?php
/**
 * Plugin Name: Event Booking
 * Plugin URI: https://houstonsipqueen.com
 * Description: Event booking and reservation system for Houston Sip Queen
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 * License: GPL v2 or later
 * Text Domain: event-booking
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EVENT_BOOKING_VERSION', '1.0.0');
define('EVENT_BOOKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EVENT_BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once EVENT_BOOKING_PLUGIN_DIR . 'includes/class-event-booking.php';
require_once EVENT_BOOKING_PLUGIN_DIR . 'includes/class-event-post-type.php';
require_once EVENT_BOOKING_PLUGIN_DIR . 'admin/class-admin.php';
require_once EVENT_BOOKING_PLUGIN_DIR . 'public/class-public.php';

// Activation hook
register_activation_hook(__FILE__, 'event_booking_activate');

/**
 * Plugin activation function
 */
function event_booking_activate() {
    add_option('event_booking_version', EVENT_BOOKING_VERSION);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'event_booking_deactivate');

/**
 * Plugin deactivation function
 */
function event_booking_deactivate() {
    // Clean up if needed
}

// Initialize the plugin
function event_booking_init() {
    new Event_Post_Type();
    new Event_Admin();
    new Event_Public();
}
add_action('plugins_loaded', 'event_booking_init');