<?php
/**
 * Theme Setup Functions
 *
 * Basic theme setup, support, and initialization
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup
 */
function digitaldreamscape_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in multiple locations
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'digitaldreamscape'),
        'footer' => __('Footer Menu', 'digitaldreamscape'),
    ));

    // Switch default core markup for search form, comment form, and comments
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');

    // Add support for responsive embeds
    add_theme_support('responsive-embeds');

    // Add support for custom background
    add_theme_support('custom-background', array(
        'default-color' => '0a0a0f',
    ));

    // Add support for custom header
    add_theme_support('custom-header', array(
        'default-image' => '',
        'width'         => 1200,
        'height'        => 400,
        'flex-width'    => true,
        'flex-height'   => true,
    ));
}
add_action('after_setup_theme', 'digitaldreamscape_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet
 */
function digitaldreamscape_content_width() {
    $GLOBALS['content_width'] = apply_filters('digitaldreamscape_content_width', 1200);
}
add_action('after_setup_theme', 'digitaldreamscape_content_width', 0);

/**
 * Register widget areas
 */
function digitaldreamscape_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'digitaldreamscape'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here.', 'digitaldreamscape'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'digitaldreamscape'),
        'id'            => 'footer-widgets',
        'description'   => __('Add footer widgets here.', 'digitaldreamscape'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'digitaldreamscape_widgets_init');

/**
 * Remove WordPress version from head
 */
function digitaldreamscape_remove_version() {
    return '';
}
add_filter('the_generator', 'digitaldreamscape_remove_version');

/**
 * Remove WordPress version from scripts and styles
 */
function digitaldreamscape_remove_wp_version_strings($src) {
    global $wp_version;
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    if (!empty($query['ver']) && $query['ver'] === $wp_version) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'digitaldreamscape_remove_wp_version_strings');
add_filter('style_loader_src', 'digitaldreamscape_remove_wp_version_strings');