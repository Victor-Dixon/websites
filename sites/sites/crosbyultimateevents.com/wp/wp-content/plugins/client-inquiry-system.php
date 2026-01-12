<?php
/**
 * Plugin Name: Client Inquiry System
 * Description: Client inquiry and contact form management
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 */
if (!defined('ABSPATH')) exit;
function client_inquiry_init() {
    add_shortcode('inquiry_form', 'inquiry_form_shortcode');
}
function inquiry_form_shortcode() {
    return '<div class="inquiry-form"><h3>Event Inquiry</h3><p>Contact us for your event planning needs.</p></div>';
}
add_action('plugins_loaded', 'client_inquiry_init');