<?php
/**
 * Houston Sip Queen Theme Functions
 *
 * @package HoustonSipQueen
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup function
 */
function houstonsipqueen_setup() {
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
        'primary' => __('Primary Menu', 'houstonsipqueen'),
        'footer'  => __('Footer Menu', 'houstonsipqueen'),
    ));

    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('editor-style.css');
}
add_action('after_setup_theme', 'houstonsipqueen_setup');

/**
 * Enqueue scripts and styles
 */
function houstonsipqueen_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('houstonsipqueen-style', get_stylesheet_uri(), array(), '1.0.0');

    // Enqueue Google Fonts - Luxury Typography
    wp_enqueue_style('houstonsipqueen-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap', array(), null);

    // Enqueue main JavaScript
    wp_enqueue_script('houstonsipqueen-script', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.0.0', true);

    // Localize script for AJAX
    wp_localize_script('houstonsipqueen-script', 'houstonsipqueen_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('houstonsipqueen_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'houstonsipqueen_scripts');

/**
 * Register widget areas
 */
function houstonsipqueen_widgets_init() {
    register_sidebar(array(
        'name'          => __('Main Sidebar', 'houstonsipqueen'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'houstonsipqueen'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'houstonsipqueen'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'houstonsipqueen'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'houstonsipqueen_widgets_init');

/**
 * Custom excerpt length
 */
function houstonsipqueen_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'houstonsipqueen_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function houstonsipqueen_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'houstonsipqueen_excerpt_more');

/**
 * Add custom classes to body
 */
function houstonsipqueen_body_classes($classes) {
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
add_filter('body_class', 'houstonsipqueen_body_classes');

/**
 * Custom pagination for luxury theme
 */
function houstonsipqueen_pagination() {
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

    echo '<nav class="pagination" role="navigation" aria-label="' . __('Cocktail Posts Navigation', 'houstonsipqueen') . '">' . "\n";
    echo '<h2 class="screen-reader-text">' . __('Cocktail posts navigation', 'houstonsipqueen') . '</h2>' . "\n";
    echo '<ul class="pagination-list">' . "\n";

    // Previous Post Link
    if (get_previous_posts_link()) {
        printf('<li class="pagination-prev">%s</li>' . "\n", get_previous_posts_link(__('« Previous Cocktails', 'houstonsipqueen')));
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
        printf('<li class="pagination-next">%s</li>' . "\n", get_next_posts_link(__('Next Cocktails »', 'houstonsipqueen')));
    }

    echo '</ul>' . "\n" . '</nav>' . "\n";
}

/**
 * Add theme customizer options
 */
function houstonsipqueen_customize_register($wp_customize) {
    // Add section for luxury theme options
    $wp_customize->add_section('houstonsipqueen_options', array(
        'title'    => __('Houston Sip Queen Options', 'houstonsipqueen'),
        'priority' => 30,
    ));

    // Add setting for hero background gradient
    $wp_customize->add_setting('hero_gradient_start', array(
        'default'           => '#0B0B0F',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_gradient_start', array(
        'label'    => __('Hero Gradient Start Color', 'houstonsipqueen'),
        'section'  => 'houstonsipqueen_options',
        'settings' => 'hero_gradient_start',
    )));

    // Contact information settings
    $wp_customize->add_setting('business_phone', array(
        'default'           => '(281) 555-SIPQ',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('business_phone', array(
        'label'    => __('Business Phone', 'houstonsipqueen'),
        'section'  => 'houstonsipqueen_options',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('service_area', array(
        'default'           => 'Houston, TX and surrounding areas',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('service_area', array(
        'label'    => __('Service Area', 'houstonsipqueen'),
        'section'  => 'houstonsipqueen_options',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'houstonsipqueen_customize_register');

/**
 * Security: Remove WordPress version from head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Performance: Remove query strings from static resources
 */
function houstonsipqueen_remove_query_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'houstonsipqueen_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'houstonsipqueen_remove_query_strings', 15, 1);

/**
 * Load Tailwind CSS for modern styling
 */
function houstonsipqueen_enqueue_tailwind() {
    wp_enqueue_style('tailwindcss', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css', array(), '2.2.19');
}
add_action('wp_enqueue_scripts', 'houstonsipqueen_enqueue_tailwind', 1);

/**
 * Custom post type for Events/Bookings
 */
function houstonsipqueen_register_event_post_type() {
    $labels = array(
        'name'                  => _x('Events', 'Post type general name', 'houstonsipqueen'),
        'singular_name'         => _x('Event', 'Post type singular name', 'houstonsipqueen'),
        'menu_name'             => _x('Events', 'Admin Menu text', 'houstonsipqueen'),
        'name_admin_bar'        => _x('Event', 'Add New on Toolbar', 'houstonsipqueen'),
        'add_new'               => __('Add New', 'houstonsipqueen'),
        'add_new_item'          => __('Add New Event', 'houstonsipqueen'),
        'new_item'              => __('New Event', 'houstonsipqueen'),
        'edit_item'             => __('Edit Event', 'houstonsipqueen'),
        'view_item'             => __('View Event', 'houstonsipqueen'),
        'all_items'             => __('All Events', 'houstonsipqueen'),
        'search_items'          => __('Search Events', 'houstonsipqueen'),
        'parent_item_colon'     => __('Parent Events:', 'houstonsipqueen'),
        'not_found'             => __('No events found.', 'houstonsipqueen'),
        'not_found_in_trash'    => __('No events found in Trash.', 'houstonsipqueen'),
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
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('event', $args);
}
add_action('init', 'houstonsipqueen_register_event_post_type');

/**
 * Add event meta boxes
 */
function houstonsipqueen_add_event_meta_boxes() {
    add_meta_box(
        'event_details',
        __('Event Details', 'houstonsipqueen'),
        'houstonsipqueen_event_details_callback',
        'event',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'houstonsipqueen_add_event_meta_boxes');

/**
 * Event details meta box callback
 */
function houstonsipqueen_event_details_callback($post) {
    wp_nonce_field('houstonsipqueen_event_details', 'houstonsipqueen_event_details_nonce');

    $event_date = get_post_meta($post->ID, '_event_date', true);
    $event_time = get_post_meta($post->ID, '_event_time', true);
    $event_location = get_post_meta($post->ID, '_event_location', true);
    $event_guests = get_post_meta($post->ID, '_event_guests', true);
    $event_package = get_post_meta($post->ID, '_event_package', true);

    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="event_date">' . __('Event Date', 'houstonsipqueen') . '</label></th>';
    echo '<td><input type="date" id="event_date" name="event_date" value="' . esc_attr($event_date) . '" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="event_time">' . __('Event Time', 'houstonsipqueen') . '</label></th>';
    echo '<td><input type="time" id="event_time" name="event_time" value="' . esc_attr($event_time) . '" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="event_location">' . __('Event Location', 'houstonsipqueen') . '</label></th>';
    echo '<td><input type="text" id="event_location" name="event_location" value="' . esc_attr($event_location) . '" style="width: 100%;" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="event_guests">' . __('Number of Guests', 'houstonsipqueen') . '</label></th>';
    echo '<td><input type="number" id="event_guests" name="event_guests" value="' . esc_attr($event_guests) . '" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="event_package">' . __('Package Selected', 'houstonsipqueen') . '</label></th>';
    echo '<td>';
    echo '<select id="event_package" name="event_package" style="width: 100%;">';
    echo '<option value="">Select Package</option>';
    echo '<option value="signature" ' . selected($event_package, 'signature', false) . '>Signature Experience</option>';
    echo '<option value="premium" ' . selected($event_package, 'premium', false) . '>Premium Package</option>';
    echo '<option value="luxury" ' . selected($event_package, 'luxury', false) . '>Luxury Affair</option>';
    echo '<option value="custom" ' . selected($event_package, 'custom', false) . '>Custom Package</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
}

/**
 * Save event meta data
 */
function houstonsipqueen_save_event_meta($post_id) {
    if (!isset($_POST['houstonsipqueen_event_details_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['houstonsipqueen_event_details_nonce'], 'houstonsipqueen_event_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('event_date', 'event_time', 'event_location', 'event_guests', 'event_package');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'houstonsipqueen_save_event_meta');

/**
 * Custom post type for Cocktails
 */
function houstonsipqueen_register_cocktail_post_type() {
    $labels = array(
        'name'                  => _x('Cocktails', 'Post type general name', 'houstonsipqueen'),
        'singular_name'         => _x('Cocktail', 'Post type singular name', 'houstonsipqueen'),
        'menu_name'             => _x('Cocktails', 'Admin Menu text', 'houstonsipqueen'),
        'name_admin_bar'        => _x('Cocktail', 'Add New on Toolbar', 'houstonsipqueen'),
        'add_new'               => __('Add New', 'houstonsipqueen'),
        'add_new_item'          => __('Add New Cocktail', 'houstonsipqueen'),
        'new_item'              => __('New Cocktail', 'houstonsipqueen'),
        'edit_item'             => __('Edit Cocktail', 'houstonsipqueen'),
        'view_item'             => __('View Cocktail', 'houstonsipqueen'),
        'all_items'             => __('All Cocktails', 'houstonsipqueen'),
        'search_items'          => __('Search Cocktails', 'houstonsipqueen'),
        'parent_item_colon'     => __('Parent Cocktails:', 'houstonsipqueen'),
        'not_found'             => __('No cocktails found.', 'houstonsipqueen'),
        'not_found_in_trash'    => __('No cocktails found in Trash.', 'houstonsipqueen'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'cocktails'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
    );

    register_post_type('cocktail', $args);
}
add_action('init', 'houstonsipqueen_register_cocktail_post_type');

/**
 * Add cocktail meta boxes
 */
function houstonsipqueen_add_cocktail_meta_boxes() {
    add_meta_box(
        'cocktail_details',
        __('Cocktail Details', 'houstonsipqueen'),
        'houstonsipqueen_cocktail_details_callback',
        'cocktail',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'houstonsipqueen_add_cocktail_meta_boxes');

/**
 * Cocktail details meta box callback
 */
function houstonsipqueen_cocktail_details_callback($post) {
    wp_nonce_field('houstonsipqueen_cocktail_details', 'houstonsipqueen_cocktail_details_nonce');

    $cocktail_spirit = get_post_meta($post->ID, '_cocktail_spirit', true);
    $cocktail_sweetness = get_post_meta($post->ID, '_cocktail_sweetness', true);
    $cocktail_complexity = get_post_meta($post->ID, '_cocktail_complexity', true);

    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="cocktail_spirit">' . __('Primary Spirit', 'houstonsipqueen') . '</label></th>';
    echo '<td><input type="text" id="cocktail_spirit" name="cocktail_spirit" value="' . esc_attr($cocktail_spirit) . '" style="width: 100%;" placeholder="e.g., Vodka, Gin, Whiskey" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="cocktail_sweetness">' . __('Sweetness Level', 'houstonsipqueen') . '</label></th>';
    echo '<td>';
    echo '<select id="cocktail_sweetness" name="cocktail_sweetness" style="width: 100%;">';
    echo '<option value="">Select Level</option>';
    echo '<option value="dry" ' . selected($cocktail_sweetness, 'dry', false) . '>Dry</option>';
    echo '<option value="semi-dry" ' . selected($cocktail_sweetness, 'semi-dry', false) . '>Semi-Dry</option>';
    echo '<option value="balanced" ' . selected($cocktail_sweetness, 'balanced', false) . '>Balanced</option>';
    echo '<option value="semi-sweet" ' . selected($cocktail_sweetness, 'semi-sweet', false) . '>Semi-Sweet</option>';
    echo '<option value="sweet" ' . selected($cocktail_sweetness, 'sweet', false) . '>Sweet</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th><label for="cocktail_complexity">' . __('Complexity Level', 'houstonsipqueen') . '</label></th>';
    echo '<td>';
    echo '<select id="cocktail_complexity" name="cocktail_complexity" style="width: 100%;">';
    echo '<option value="">Select Level</option>';
    echo '<option value="simple" ' . selected($cocktail_complexity, 'simple', false) . '>Simple (3 ingredients)</option>';
    echo '<option value="intermediate" ' . selected($cocktail_complexity, 'intermediate', false) . '>Intermediate (4-5 ingredients)</option>';
    echo '<option value="complex" ' . selected($cocktail_complexity, 'complex', false) . '>Complex (6+ ingredients)</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
}

/**
 * Save cocktail meta data
 */
function houstonsipqueen_save_cocktail_meta($post_id) {
    if (!isset($_POST['houstonsipqueen_cocktail_details_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['houstonsipqueen_cocktail_details_nonce'], 'houstonsipqueen_cocktail_details')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('cocktail_spirit', 'cocktail_sweetness', 'cocktail_complexity');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'houstonsipqueen_save_cocktail_meta');