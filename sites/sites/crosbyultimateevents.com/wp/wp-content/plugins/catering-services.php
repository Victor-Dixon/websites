<?php
/**
 * Plugin Name: Catering Services
 * Description: Catering menu and service management
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 */
if (!defined('ABSPATH')) exit;
function catering_services_init() {
    add_shortcode('catering_menu', 'catering_menu_shortcode');
}
function catering_menu_shortcode() {
    return '<div class="catering-menu"><h3>Catering Services</h3><p>Professional catering menu and services.</p></div>';
}
add_action('plugins_loaded', 'catering_services_init');