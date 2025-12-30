<?php
/**
 * Swarm Theme Functions
 *
 * @package Swarm
 * @since 1.0.0
 */

/**
 * Theme Setup
 */
function swarm_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'swarm'),
        'footer' => __('Footer Menu', 'swarm'),
    ));
}
add_action('after_setup_theme', 'swarm_setup');

/**
 * Enqueue Styles and Scripts
 */
function swarm_scripts() {
    // Load Google Fonts - Inter for body text
    wp_enqueue_style(
        'swarm-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );
    
    wp_enqueue_style('swarm-style', get_stylesheet_uri(), array('swarm-google-fonts'), '1.0.1');
}
add_action('wp_enqueue_scripts', 'swarm_scripts');


