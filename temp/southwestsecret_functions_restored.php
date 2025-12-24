<?php
/**
 * Theme Functions - Restored by Agent-7
 * Minimal working version to restore site functionality
 */

// Enqueue styles and scripts
function southwestsecret_enqueue_scripts() {
    wp_enqueue_style('southwestsecret-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'southwestsecret_enqueue_scripts');

// Theme support
function southwestsecret_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
}
add_action('after_setup_theme', 'southwestsecret_theme_setup');

// Register navigation menus
function southwestsecret_register_menus() {
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu',
    ));
}
add_action('init', 'southwestsecret_register_menus');

// Add meta description support
function southwestsecret_add_meta_description() {
    if (is_front_page() || is_home()) {
        echo '<meta name="description" content="Southwest Secret is your guide to hidden gems, unique experiences, and untold stories of the American Southwest." />';
    }
}
add_action('wp_head', 'southwestsecret_add_meta_description');

// Custom title tag
function southwestsecret_custom_title_tag($title) {
    if (is_front_page() || is_home()) {
        $title = 'Southwest Secret - Hidden Gems & Unique Experiences of the American Southwest';
    } elseif (is_singular()) {
        $title = get_the_title() . ' | Southwest Secret';
    }
    return $title;
}
add_filter('pre_get_document_title', 'southwestsecret_custom_title_tag', 999);

// Add HSTS header
if (!function_exists('southwestsecret_add_hsts_header')) {
    function southwestsecret_add_hsts_header() {
        if (is_ssl() && !headers_sent()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
    add_action('send_headers', 'southwestsecret_add_hsts_header');
}
