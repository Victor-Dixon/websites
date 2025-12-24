<?php
/**
 * FreeRideInvestor Modern Theme Functions - MINIMAL WORKING VERSION
 *
 * @package FreeRideInvestor_Modern
 * @since 3.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function freerideinvestor_modern_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');
}
add_action('after_setup_theme', 'freerideinvestor_modern_setup');

/**
 * Enqueue Scripts and Styles
 */
function freerideinvestor_modern_scripts() {
    wp_enqueue_style('freerideinvestor-modern-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'freerideinvestor_modern_scripts');
