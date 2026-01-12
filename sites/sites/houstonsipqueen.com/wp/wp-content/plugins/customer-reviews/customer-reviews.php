<?php
/**
 * Plugin Name: Customer Reviews
 * Plugin URI: https://houstonsipqueen.com
 * Description: Customer review and rating system
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 */

if (!defined('ABSPATH')) exit;

define('CUSTOMER_REVIEWS_VERSION', '1.0.0');

require_once plugin_dir_path(__FILE__) . 'includes/class-review-post-type.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/class-public.php';

function customer_reviews_init() {
    new Review_Post_Type();
    new Review_Admin();
    new Review_Public();
}
add_action('plugins_loaded', 'customer_reviews_init');