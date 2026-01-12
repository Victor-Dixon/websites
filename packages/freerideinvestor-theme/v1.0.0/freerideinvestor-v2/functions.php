<?php
/**
 * FreeRideInvestor V2 Theme Functions
 *
 * @package FreeRideInvestor_V2
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function freerideinvestor_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Enable support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Register navigation menu
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'freerideinvestor'),
        'footer'  => __('Footer Menu', 'freerideinvestor'),
    ));

    // Enable support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Enable support for custom background
    add_theme_support('custom-background', array(
        'default-color' => 'f8f9fa',
    ));

    // Enable support for custom header
    add_theme_support('custom-header', array(
        'default-image' => '',
        'width'         => 1200,
        'height'        => 400,
        'flex-width'    => true,
        'flex-height'   => true,
    ));
}
add_action('after_setup_theme', 'freerideinvestor_setup');

/**
 * Enqueue scripts and styles
 */
function freerideinvestor_scripts() {
    // Theme stylesheet
    wp_enqueue_style('freerideinvestor-style', get_stylesheet_uri(), array(), '2.0.0');

    // Google Fonts
    wp_enqueue_style('freerideinvestor-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);

    // Theme JavaScript
    wp_enqueue_script('freerideinvestor-script', get_template_directory_uri() . '/js/theme.js', array('jquery'), '2.0.0', true);

    // Localize script for AJAX
    wp_localize_script('freerideinvestor-script', 'freerideinvestor_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('freerideinvestor_ajax_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'freerideinvestor_scripts');

/**
 * Register widget areas
 */
function freerideinvestor_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'freerideinvestor'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'freerideinvestor'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'freerideinvestor'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'freerideinvestor'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'freerideinvestor_widgets_init');

/**
 * Custom excerpt length
 */
function freerideinvestor_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'freerideinvestor_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function freerideinvestor_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'freerideinvestor_excerpt_more');

/**
 * Add custom classes to body
 */
function freerideinvestor_body_classes($classes) {
    // Add class if sidebar is active
    if (is_active_sidebar('sidebar-1')) {
        $classes[] = 'has-sidebar';
    }

    // Add class for page templates
    if (is_page_template()) {
        $classes[] = 'page-template';
    }

    return $classes;
}
add_filter('body_class', 'freerideinvestor_body_classes');

/**
 * Custom navigation menu walker to remove duplicate links
 */
class FreeRideInvestor_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * Remove duplicate navigation menu items (improved)
 */
function freerideinvestor_remove_duplicate_menu_items($items, $args) {
    if (empty($items)) {
        return $items;
    }

    $seen = array();
    $filtered = array();

    foreach ($items as $key => $item) {
        // Check by both URL and title to catch duplicates
        $identifier = $item->url . '|' . $item->title;
        
        if (!isset($seen[$identifier])) {
            $seen[$identifier] = true;
            $filtered[] = $item;
        }
    }

    return $filtered;
}
add_filter('wp_nav_menu_objects', 'freerideinvestor_remove_duplicate_menu_items', 10, 2);

/**
 * Create Daily Plans category on theme activation
 */
function freerideinvestor_create_daily_plans_category() {
    if (!term_exists('daily-plans', 'category')) {
        wp_insert_term(
            'Daily Plans',
            'category',
            array(
                'description' => 'Daily trading plans and journal entries',
                'slug' => 'daily-plans'
            )
        );
    }
}
add_action('after_setup_theme', 'freerideinvestor_create_daily_plans_category');

/**
 * Hide SEO drafting blocks from public view
 */
function freerideinvestor_hide_seo_blocks($content) {
    // Remove common SEO drafting patterns
    $patterns = array(
        '/Target Keywords:.*?(\n|$)/i',
        '/Meta Description:.*?(\n|$)/i',
        '/Schema:.*?(\n|$)/i',
        '/SEO Notes:.*?(\n|$)/i',
        '/<p>Target Keywords:.*?<\/p>/i',
        '/<p>Meta Description:.*?<\/p>/i',
    );
    
    foreach ($patterns as $pattern) {
        $content = preg_replace($pattern, '', $content);
    }
    
    return $content;
}
add_filter('the_content', 'freerideinvestor_hide_seo_blocks', 20);

/**
 * Custom comment form
 */
function freerideinvestor_comment_form_defaults($defaults) {
    $defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun') . '</label><br /><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>';
    $defaults['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />';
    $defaults['submit_field'] = '<p class="form-submit">%1$s %2$s</p>';

    return $defaults;
}
add_filter('comment_form_defaults', 'freerideinvestor_comment_form_defaults');

/**
 * Security enhancements
 */
function freerideinvestor_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'freerideinvestor_security_headers');

/**
 * Performance optimizations
 */
function freerideinvestor_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'freerideinvestor_disable_emojis');

/**
 * Remove query strings from static resources
 */
function freerideinvestor_remove_query_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'freerideinvestor_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'freerideinvestor_remove_query_strings', 15, 1);

/**
 * Enable shortcodes in widgets
 */
add_filter('widget_text', 'do_shortcode');

/**
 * Custom login logo
 */
function freerideinvestor_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/login-logo.png);
            height: 100px;
            width: 320px;
            background-size: contain;
            background-repeat: no-repeat;
            padding-bottom: 30px;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'freerideinvestor_login_logo');

