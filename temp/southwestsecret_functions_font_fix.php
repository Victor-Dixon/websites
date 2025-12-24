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


/**
 * Fix Font Rendering Issues - Added by Agent-7
 * Ensures proper character spacing and font loading
 */

// Enqueue proper fonts with font-display swap
function southwestsecret_fix_fonts() {
    // Remove any problematic font enqueues
    wp_dequeue_style('google-fonts');
    wp_deregister_style('google-fonts');
    
    // Add proper system fonts with fallbacks
    $font_css = "
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: 'kern' 1;
            font-kerning: normal;
        }
        
        body, input, textarea, select, button {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
        }
        
        /* Fix character spacing issues */
        h1, h2, h3, h4, h5, h6, p, span, a, li {
            letter-spacing: 0 !important;
            word-spacing: 0.05em !important;
        }
        
        /* Ensure proper encoding */
        html {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
    ";
    
    wp_add_inline_style('wp-block-library', $font_css);
}
add_action('wp_enqueue_scripts', 'southwestsecret_fix_fonts', 999);

// Fix character encoding in head
function southwestsecret_fix_encoding() {
    echo '<meta charset="UTF-8">';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
}
add_action('wp_head', 'southwestsecret_fix_encoding', 1);
