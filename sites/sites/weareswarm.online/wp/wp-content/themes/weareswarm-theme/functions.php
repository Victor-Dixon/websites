<?php
/**
 * WeAreSwarm Theme Functions
 *
 * @package WeAreSwarm
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup function
 */
function weareswarm_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Enable support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Enable support for custom background
    add_theme_support('custom-background');

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Enable support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Register navigation menu
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'weareswarm'),
        'footer'  => __('Footer Menu', 'weareswarm'),
    ));

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'weareswarm_setup');

/**
 * Enqueue scripts and styles
 */
function weareswarm_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('weareswarm-style', get_stylesheet_uri(), array(), '1.0.0');

    // Enqueue Google Fonts
    wp_enqueue_style('weareswarm-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);

    // Enqueue main JavaScript
    wp_enqueue_script('weareswarm-script', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.0.0', true);

    // Localize script for AJAX
    wp_localize_script('weareswarm-script', 'weareswarm_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('weareswarm_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'weareswarm_scripts');

/**
 * Register widget areas
 */
function weareswarm_widgets_init() {
    register_sidebar(array(
        'name'          => __('Main Sidebar', 'weareswarm'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'weareswarm'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'weareswarm'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'weareswarm'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'weareswarm_widgets_init');

/**
 * Custom excerpt length
 */
function weareswarm_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'weareswarm_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function weareswarm_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'weareswarm_excerpt_more');

/**
 * Add custom classes to body
 */
function weareswarm_body_classes($classes) {
    // Add class if it's the front page
    if (is_front_page()) {
        $classes[] = 'front-page';
    }

    // Add class for page templates
    if (is_page_template()) {
        $classes[] = 'page-template';
    }

    return $classes;
}
add_filter('body_class', 'weareswarm_body_classes');

/**
 * Custom pagination
 */
function weareswarm_pagination() {
    global $wp_query;

    if ($wp_query->max_num_pages <= 1) {
        return;
    }

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max   = intval($wp_query->max_num_pages);

    // Add current page to the array
    if ($paged >= 1) {
        $links[] = $paged;
    }

    // Add the pages around the current page to the array
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }

    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }

    echo '<nav class="pagination" role="navigation" aria-label="' . __('Posts Navigation', 'weareswarm') . '">' . "\n";
    echo '<h2 class="screen-reader-text">' . __('Posts navigation', 'weareswarm') . '</h2>' . "\n";
    echo '<ul class="pagination-list">' . "\n";

    // Previous Post Link
    if (get_previous_posts_link()) {
        printf('<li class="pagination-prev">%s</li>' . "\n", get_previous_posts_link(__('« Previous', 'weareswarm')));
    }

    // Link to first page, plus ellipses if necessary
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');

        if (!in_array(2, $links)) {
            echo '<li class="pagination-dots">…</li>' . "\n";
        }
    }

    // Link to current page, plus 2 pages in either direction if necessary
    sort($links);
    foreach ((array) $links as $link) {
        $class = $paged == $link ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
    }

    // Link to last page, plus ellipses if necessary
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links)) {
            echo '<li class="pagination-dots">…</li>' . "\n";
        }

        $class = $paged == $max ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
    }

    // Next Post Link
    if (get_next_posts_link()) {
        printf('<li class="pagination-next">%s</li>' . "\n", get_next_posts_link(__('Next »', 'weareswarm')));
    }

    echo '</ul>' . "\n" . '</nav>' . "\n";
}

/**
 * Add theme customizer options
 */
function weareswarm_customize_register($wp_customize) {
    // Add section for theme options
    $wp_customize->add_section('weareswarm_options', array(
        'title'    => __('WeAreSwarm Options', 'weareswarm'),
        'priority' => 30,
    ));

    // Add setting for hero background color
    $wp_customize->add_setting('hero_bg_color', array(
        'default'           => '#1a1a2e',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_bg_color', array(
        'label'    => __('Hero Background Color', 'weareswarm'),
        'section'  => 'weareswarm_options',
        'settings' => 'hero_bg_color',
    )));
}
add_action('customize_register', 'weareswarm_customize_register');

/**
 * Security: Remove WordPress version from head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Performance: Remove query strings from static resources
 */
function weareswarm_remove_query_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'weareswarm_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'weareswarm_remove_query_strings', 15, 1);

/**
 * Load Tailwind CSS for modern styling
 */
function weareswarm_enqueue_tailwind() {
    wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19');
}
add_action('wp_enqueue_scripts', 'weareswarm_enqueue_tailwind', 1);