<?php
/**
 * Crosby Ultimate Events Theme Functions
 *
 * @package CrosbyUltimateEvents
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup function
 */
function crosbyultimateevents_setup() {
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
        'primary' => __('Primary Menu', 'crosbyultimateevents'),
        'footer'  => __('Footer Menu', 'crosbyultimateevents'),
    ));

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'crosbyultimateevents_setup');

/**
 * Enqueue scripts and styles
 */
function crosbyultimateevents_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('crosbyultimateevents-style', get_stylesheet_uri(), array(), '1.0.0');

    // Enqueue Google Fonts
    wp_enqueue_style('crosbyultimateevents-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);

    // Enqueue main JavaScript
    wp_enqueue_script('crosbyultimateevents-script', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.0.0', true);

    // Localize script for AJAX
    wp_localize_script('crosbyultimateevents-script', 'crosbyultimateevents_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('crosbyultimateevents_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'crosbyultimateevents_scripts');

/**
 * Register widget areas
 */
function crosbyultimateevents_widgets_init() {
    register_sidebar(array(
        'name'          => __('Main Sidebar', 'crosbyultimateevents'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'crosbyultimateevents'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'crosbyultimateevents'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'crosbyultimateevents'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'crosbyultimateevents_widgets_init');

/**
 * Custom excerpt length
 */
function crosbyultimateevents_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'crosbyultimateevents_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function crosbyultimateevents_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'crosbyultimateevents_excerpt_more');

/**
 * Add custom classes to body
 */
function crosbyultimateevents_body_classes($classes) {
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
add_filter('body_class', 'crosbyultimateevents_body_classes');

/**
 * Custom pagination for events
 */
function crosbyultimateevents_pagination() {
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

    echo '<nav class="pagination" role="navigation" aria-label="' . __('Events Navigation', 'crosbyultimateevents') . '">' . "\n";
    echo '<h2 class="screen-reader-text">' . __('Events navigation', 'crosbyultimateevents') . '</h2>' . "\n";
    echo '<ul class="pagination-list">' . "\n";

    // Previous Post Link
    if (get_previous_posts_link()) {
        printf('<li class="pagination-prev">%s</li>' . "\n", get_previous_posts_link(__('« Previous Events', 'crosbyultimateevents')));
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
        printf('<li class="pagination-next">%s</li>' . "\n", get_next_posts_link(__('Next Events »', 'crosbyultimateevents')));
    }

    echo '</ul>' . "\n" . '</nav>' . "\n";
}

/**
 * Add theme customizer options
 */
function crosbyultimateevents_customize_register($wp_customize) {
    // Add section for theme options
    $wp_customize->add_section('crosbyultimateevents_options', array(
        'title'    => __('Crosby Ultimate Events Options', 'crosbyultimateevents'),
        'priority' => 30,
    ));

    // Add setting for hero background color
    $wp_customize->add_setting('hero_bg_color', array(
        'default'           => '#1a472a',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_bg_color', array(
        'label'    => __('Hero Background Color', 'crosbyultimateevents'),
        'section'  => 'crosbyultimateevents_options',
        'settings' => 'hero_bg_color',
    )));
}
add_action('customize_register', 'crosbyultimateevents_customize_register');

/**
 * Security: Remove WordPress version from head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Performance: Remove query strings from static resources
 */
function crosbyultimateevents_remove_query_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'crosbyultimateevents_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'crosbyultimateevents_remove_query_strings', 15, 1);

/**
 * Load Tailwind CSS for modern styling
 */
function crosbyultimateevents_enqueue_tailwind() {
    wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19');
}
add_action('wp_enqueue_scripts', 'crosbyultimateevents_enqueue_tailwind', 1);

/**
 * Custom post type for Events
 */
function crosbyultimateevents_register_event_post_type() {
    $labels = array(
        'name'                  => _x('Events', 'Post type general name', 'crosbyultimateevents'),
        'singular_name'         => _x('Event', 'Post type singular name', 'crosbyultimateevents'),
        'menu_name'             => _x('Events', 'Admin Menu text', 'crosbyultimateevents'),
        'name_admin_bar'        => _x('Event', 'Add New on Toolbar', 'crosbyultimateevents'),
        'add_new'               => __('Add New', 'crosbyultimateevents'),
        'add_new_item'          => __('Add New Event', 'crosbyultimateevents'),
        'new_item'              => __('New Event', 'crosbyultimateevents'),
        'edit_item'             => __('Edit Event', 'crosbyultimateevents'),
        'view_item'             => __('View Event', 'crosbyultimateevents'),
        'all_items'             => __('All Events', 'crosbyultimateevents'),
        'search_items'          => __('Search Events', 'crosbyultimateevents'),
        'parent_item_colon'     => __('Parent Events:', 'crosbyultimateevents'),
        'not_found'             => __('No events found.', 'crosbyultimateevents'),
        'not_found_in_trash'    => __('No events found in Trash.', 'crosbyultimateevents'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'events'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest'       => true,
    );

    register_post_type('event', $args);
}
add_action('init', 'crosbyultimateevents_register_event_post_type');

/**
 * Add event meta boxes
 */
function crosbyultimateevents_add_event_meta_boxes() {
    add_meta_box(
        'event_details',
        __('Event Details', 'crosbyultimateevents'),
        'crosbyultimateevents_event_details_callback',
        'event',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'crosbyultimateevents_add_event_meta_boxes');

/**
 * Event details meta box callback
 */
function crosbyultimateevents_event_details_callback($post) {
    wp_nonce_field('crosbyultimateevents_event_details', 'crosbyultimateevents_event_details_nonce');

    $event_date = get_post_meta($post->ID, '_event_date', true);
    $event_location = get_post_meta($post->ID, '_event_location', true);
    $event_capacity = get_post_meta($post->ID, '_event_capacity', true);

    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="event_date">' . __('Event Date', 'crosbyultimateevents') . '</label></th>';
    echo '<td><input type="date" id="event_date" name="event_date" value="' . esc_attr($event_date) . '" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="event_location">' . __('Event Location', 'crosbyultimateevents') . '</label></th>';
    echo '<td><input type="text" id="event_location" name="event_location" value="' . esc_attr($event_location) . '" style="width: 100%;" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="event_capacity">' . __('Max Capacity', 'crosbyultimateevents') . '</label></th>';
    echo '<td><input type="number" id="event_capacity" name="event_capacity" value="' . esc_attr($event_capacity) . '" /></td>';
    echo '</tr>';
    echo '</table>';
}

/**
 * Save event meta data
 */
function crosbyultimateevents_save_event_meta($post_id) {
    if (!isset($_POST['crosbyultimateevents_event_details_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['crosbyultimateevents_event_details_nonce'], 'crosbyultimateevents_event_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['event_date'])) {
        update_post_meta($post_id, '_event_date', sanitize_text_field($_POST['event_date']));
    }

    if (isset($_POST['event_location'])) {
        update_post_meta($post_id, '_event_location', sanitize_text_field($_POST['event_location']));
    }

    if (isset($_POST['event_capacity'])) {
        update_post_meta($post_id, '_event_capacity', sanitize_text_field($_POST['event_capacity']));
    }
}
add_action('save_post', 'crosbyultimateevents_save_event_meta');