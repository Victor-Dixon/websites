<?php
/**
 * Asset Enqueueing Functions
 *
 * Scripts and styles registration and enqueuing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue scripts and styles
 */
function digitaldreamscape_scripts() {
    // Theme stylesheet
    wp_enqueue_style('digitaldreamscape-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));

    // Main JavaScript
    wp_enqueue_script('digitaldreamscape-main', get_template_directory_uri() . '/js/main.js', array('jquery'), wp_get_theme()->get('Version'), true);

    // Comment reply script for threaded comments
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    // Localize script for AJAX and theme data
    wp_localize_script('digitaldreamscape-main', 'digitaldreamscape_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('digitaldreamscape_ajax_nonce'),
        'theme_url' => get_template_directory_uri(),
    ));
}
add_action('wp_enqueue_scripts', 'digitaldreamscape_scripts');

/**
 * Enqueue block editor styles
 */
function digitaldreamscape_block_editor_styles() {
    wp_enqueue_style('digitaldreamscape-block-editor-styles', get_template_directory_uri() . '/editor-style.css', array(), wp_get_theme()->get('Version'));
}
add_action('enqueue_block_editor_assets', 'digitaldreamscape_block_editor_styles');

/**
 * Add preload for critical assets
 */
function digitaldreamscape_preload_assets() {
    if (is_front_page()) {
        echo '<link rel="preload" href="' . get_template_directory_uri() . '/js/main.js" as="script">';
        echo '<link rel="preload" href="' . get_stylesheet_uri() . '" as="style">';
    }
}
add_action('wp_head', 'digitaldreamscape_preload_assets', 1);

/**
 * Dequeue unnecessary scripts and styles
 */
function digitaldreamscape_dequeue_scripts() {
    // Remove emoji scripts if not needed
    if (!is_admin()) {
        wp_dequeue_script('wp-embed');
    }
}
// add_action('wp_enqueue_scripts', 'digitaldreamscape_dequeue_scripts', 999);

/**
 * Add async/defer attributes to scripts
 */
function digitaldreamscape_script_attributes($tag, $handle, $src) {
    // Add async to non-critical scripts
    $async_scripts = array('digitaldreamscape-main');

    if (in_array($handle, $async_scripts)) {
        return str_replace('<script ', '<script defer ', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'digitaldreamscape_script_attributes', 10, 3);

/**
 * Add preload hints for critical resources
 */
function digitaldreamscape_resource_hints($hints, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $hints[] = '//fonts.googleapis.com';
        $hints[] = '//fonts.gstatic.com';
    }

    if ('preconnect' === $relation_type) {
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
    }

    return $hints;
}
add_filter('wp_resource_hints', 'digitaldreamscape_resource_hints', 10, 2);