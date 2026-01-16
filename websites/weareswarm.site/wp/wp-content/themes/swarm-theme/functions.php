<?php

// Theme setup
function weareswarm_theme_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));
    add_theme_support('custom-logo');
    add_theme_support('custom-background');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'weareswarm'),
        'footer' => __('Footer Menu', 'weareswarm'),
    ));

    // Add image sizes for performance
    add_image_size('hero-large', 1920, 1080, true);
    add_image_size('card-medium', 600, 400, true);
    add_image_size('avatar-small', 150, 150, true);
}
add_action('after_setup_theme', 'weareswarm_theme_setup');

// Enqueue scripts and styles
function weareswarm_enqueue_assets() {
    // Main stylesheet
    wp_enqueue_style('weareswarm-style', get_stylesheet_uri(), array(), '3.0.0');

    // Google Fonts
    wp_enqueue_style('weareswarm-fonts', 'https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap', array(), null);

    // Hero animations script (only on pages with hero)
    if (is_page_template('page-home.php')) {
        wp_enqueue_script('weareswarm-hero', get_template_directory_uri() . '/js/hero-animations.js', array('jquery'), '3.0.0', true);
    }

    // Main JavaScript
    wp_enqueue_script('weareswarm-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '3.0.0', true);

    // Localize script for AJAX
    wp_localize_script('weareswarm-main', 'weareswarmAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('weareswarm_nonce'),
        'theme_url' => get_template_directory_uri(),
    ));
}
add_action('wp_enqueue_scripts', 'weareswarm_enqueue_assets');

// Performance optimizations
function weareswarm_performance_optimizations() {
    // Remove query strings from static resources
    function remove_query_strings($src) {
        if (strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
    add_filter('script_loader_src', 'remove_query_strings');
    add_filter('style_loader_src', 'remove_query_strings');

    // Disable XML-RPC for security
    add_filter('xmlrpc_enabled', '__return_false');
    add_filter('xmlrpc_methods', '__return_empty_array');

    // Remove unnecessary scripts
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', false);
    }
}
add_action('init', 'weareswarm_performance_optimizations');

// Security headers
function weareswarm_security_headers() {
    if (!is_admin()) {
        // Remove WordPress version
        remove_action('wp_head', 'wp_generator');

        // Remove RSD link
        remove_action('wp_head', 'rsd_link');

        // Remove Windows Live Writer
        remove_action('wp_head', 'wlwmanifest_link');

        // Remove shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head');

        // Remove REST API link from head
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }
}
add_action('init', 'weareswarm_security_headers');

// Custom login page styling
function weareswarm_login_styles() {
    wp_enqueue_style('weareswarm-login', get_template_directory_uri() . '/css/login.css');
}
add_action('login_enqueue_scripts', 'weareswarm_login_styles');

// Custom admin styling
function weareswarm_admin_styles() {
    wp_enqueue_style('weareswarm-admin', get_template_directory_uri() . '/css/admin.css');
}
add_action('admin_enqueue_scripts', 'weareswarm_admin_styles');

// AJAX handlers
function weareswarm_ajax_handlers() {
    // Mission data handler
    add_action('wp_ajax_get_missions', 'weareswarm_get_missions');
    add_action('wp_ajax_nopriv_get_missions', 'weareswarm_get_missions');

    // Contact form handler
    add_action('wp_ajax_submit_contact', 'weareswarm_submit_contact');
    add_action('wp_ajax_nopriv_submit_contact', 'weareswarm_submit_contact');
}
add_action('init', 'weareswarm_ajax_handlers');

function weareswarm_get_missions() {
    check_ajax_referer('weareswarm_nonce', 'nonce');

    // Mock mission data - replace with real data source
    $missions = array(
        array(
            'id' => 'mission_1',
            'title' => 'Navigation Enhancement',
            'description' => 'Enhanced 11 core service files with comprehensive navigation references',
            'priority' => 'high',
            'status' => 'completed',
            'agent' => 'Agent-5'
        ),
        array(
            'id' => 'mission_2',
            'title' => 'Module Discovery',
            'description' => 'Create import path reference guide for complex module hierarchies',
            'priority' => 'medium',
            'status' => 'pending',
            'agent' => 'Agent-8'
        ),
        array(
            'id' => 'mission_3',
            'title' => 'Cycle Snapshot System',
            'description' => 'Design system architecture for cycle snapshot central hub',
            'priority' => 'high',
            'status' => 'pending',
            'agent' => 'Agent-2'
        )
    );

    wp_send_json_success($missions);
}

function weareswarm_submit_contact() {
    check_ajax_referer('weareswarm_nonce', 'nonce');

    // Get form data
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $company = sanitize_text_field($_POST['company']);
    $interest = sanitize_text_field($_POST['interest']);
    $message = sanitize_textarea_field($_POST['message']);

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error('Please fill in all required fields.');
        return;
    }

    // Send email (replace with your email configuration)
    $to = get_option('admin_email');
    $subject = 'New Contact Form Submission - We Are Swarm';
    $body = "
Name: $name
Email: $email
Company: $company
Interest: $interest

Message:
$message
    ";

    $headers = array('Content-Type: text/plain; charset=UTF-8');

    if (wp_mail($to, $subject, $body, $headers)) {
        wp_send_json_success('Message sent successfully!');
    } else {
        wp_send_json_error('Failed to send message. Please try again.');
    }
}

// Custom excerpt length
function weareswarm_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'weareswarm_excerpt_length');

// Custom excerpt more
function weareswarm_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'weareswarm_excerpt_more');

// Add theme customizer options
function weareswarm_customizer_settings($wp_customize) {
    // Hero Section
    $wp_customize->add_section('weareswarm_hero', array(
        'title' => __('Hero Section', 'weareswarm'),
        'priority' => 30,
    ));

    // Hero title
    $wp_customize->add_setting('hero_title', array(
        'default' => 'We Are Swarm',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'weareswarm'),
        'section' => 'weareswarm_hero',
        'type' => 'text',
    ));

    // Hero subtitle
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Revolutionary multi-agent coordination system',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('hero_subtitle', array(
        'label' => __('Hero Subtitle', 'weareswarm'),
        'section' => 'weareswarm_hero',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'weareswarm_customizer_settings');

// Theme update checker
function weareswarm_theme_update_checker() {
    // Check for theme updates (implement as needed)
}
add_action('admin_init', 'weareswarm_theme_update_checker');

/**
 * Performance and Security Optimizations
 */

/**
 * Add security headers
 */
function freerideinvestor_security_headers() {
    if (!is_admin()) {
        // Remove WordPress version from head
        remove_action('wp_head', 'wp_generator');

        // Remove RSD link
        remove_action('wp_head', 'rsd_link');

        // Remove Windows Live Writer
        remove_action('wp_head', 'wlwmanifest_link');

        // Remove shortlink
        remove_action('wp_head', 'wp_shortlink_wp_head');
    }
}
add_action('init', 'freerideinvestor_security_headers');

/**
 * Optimize asset loading - defer non-critical CSS
 */
function freerideinvestor_optimize_assets() {
    // Defer non-critical styles
    if (!is_admin()) {
        add_filter('style_loader_tag', 'freerideinvestor_defer_styles', 10, 4);
    }
}
add_action('wp_enqueue_scripts', 'freerideinvestor_optimize_assets', 999);

/**
 * Defer loading of non-critical styles
 */
function freerideinvestor_defer_styles($html, $handle, $href, $media) {
    // Defer Tailwind CSS as it's not critical for initial render
    if ($handle === 'tailwind-css') {
        return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
    }
    return $html;
}

/**
 * Optimize database queries
 */
function freerideinvestor_optimize_queries($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_home()) {
        // Limit posts per page for performance
        $query->set('posts_per_page', 12);
    }
    return $query;
}
add_action('pre_get_posts', 'freerideinvestor_optimize_queries');

/**
 * Add preconnect for external resources
 */
function freerideinvestor_resource_hints($hints, $relation_type) {
    if ($relation_type === 'preconnect') {
        // Preconnect to Google Fonts
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
    }
    return $hints;
}
add_filter('wp_resource_hints', 'freerideinvestor_resource_hints', 10, 2);

/**
 * Disable emojis for performance
 */
function freerideinvestor_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'freerideinvestor_disable_emojis');
?>