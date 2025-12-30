<?php

/**
 * DaDudeKC Theme Functions
 * 
 * @package DaDudeKC
 * @since 1.0.0
 */

/**
 * Theme Setup
 */
function dadudekc_setup()
{
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'dadudekc'),
        'footer' => __('Footer Menu', 'dadudekc'),
    ));

    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'dadudekc_setup');

/**
 * Enqueue Styles and Scripts
 */
function dadudekc_scripts()
{
    // Enqueue theme stylesheet
    wp_enqueue_style('dadudekc-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'dadudekc_scripts');

/**
 * Register Widget Areas
 */
function dadudekc_widgets_init()
{
    register_sidebar(array(
        'name' => __('Sidebar', 'dadudekc'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'dadudekc'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'dadudekc_widgets_init');

/**
 * Handle Contact Form Submission (Tier 1 Quick Win WEB-04)
 */
function dadudekc_handle_contact_form()
{
    // Verify nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'contact_form')) {
        wp_die('Security check failed');
    }
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_die('Invalid email address');
    }
    
    // Process email (add to mailing list, send notification, etc.)
    // TODO: Integrate with email marketing platform (MailChimp, ConvertKit, etc.)
    $admin_email = get_option('admin_email');
    wp_mail($admin_email, 'New Contact Form Submission', 'Email: ' . $email);
    
    // Redirect to thank you page
    wp_redirect(home_url('/thank-you'));
    exit;
}

add_action('admin_post_handle_contact_form', 'dadudekc_handle_contact_form');
add_action('admin_post_nopriv_handle_contact_form', 'dadudekc_handle_contact_form');

/**
 * Force front-page.php to be used for homepage (even when showing posts)
 */
function dadudekc_template_include($template) {
    // Only process on front page
    if (!is_front_page()) {
        return $template;
    }
    
    // Check if we should use front-page.php
    $front_page_template = locate_template('front-page.php');
    if ($front_page_template) {
        // Always use front-page.php for front page
        return $front_page_template;
    }
    
    return $template;
}
add_filter('template_include', 'dadudekc_template_include', 99);

/**
 * Include Custom Post Types
 */
require_once get_template_directory() . '/inc/post-types/icp-definition.php';
require_once get_template_directory() . '/inc/post-types/offer-ladder.php';

