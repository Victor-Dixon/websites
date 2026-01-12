<?php
/**
 * Plugin Name: Client Testimonials
 * Description: Client testimonial management and display system
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 */
if (!defined('ABSPATH')) exit;
function client_testimonials_init() {
    add_shortcode('client_testimonials', 'client_testimonials_shortcode');
}
function client_testimonials_shortcode() {
    return '<div class="client-testimonials"><h3>Client Testimonials</h3><p>What our clients say about our work.</p></div>';
}
add_action('plugins_loaded', 'client_testimonials_init');