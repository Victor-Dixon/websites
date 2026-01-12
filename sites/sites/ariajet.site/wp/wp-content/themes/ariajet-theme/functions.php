<?php
/**
 * AriaJet Gaming Theme functions and definitions
 *
 * @package AriaJet_Theme
 * @since 1.0.0
 */

function ariajet_theme_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // This theme uses wp_nav_menu() in two locations.
    register_nav_menus(array(
        'menu-1' => esc_html__('Primary', 'ariajet-theme'),
        'footer' => esc_html__('Footer Menu', 'ariajet-theme'),
    ));

    // Switch default core markup for search form, comment form, and comments
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for custom logo.
    add_theme_support('custom-logo', array(
        'width'       => 250,
        'height'      => 250,
        'flex-width'  => true,
    ));

    // Add support for custom background.
    add_theme_support('custom-background', array(
        'default-color' => '0f0f23',
    ));

    // Add editor styles
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'ariajet_theme_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function ariajet_theme_content_width() {
    $GLOBALS['content_width'] = apply_filters('ariajet_theme_content_width', 640);
}
add_action('after_setup_theme', 'ariajet_theme_content_width', 0);

/**
 * Register widget area.
 */
function ariajet_theme_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'ariajet-theme'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'ariajet-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'ariajet_theme_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function ariajet_theme_scripts() {
    wp_enqueue_style('ariajet-theme-style', get_stylesheet_uri(), array(), '1.0.0');

    wp_enqueue_script('ariajet-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0.0', true);

    wp_enqueue_script('ariajet-theme-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '1.0.0', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'ariajet_theme_scripts');

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}