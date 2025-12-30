<?php
/**
 * Enqueue Scripts and Styles
 * 
 * Handles loading of CSS and JavaScript files with performance optimizations
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Styles and Scripts with Performance Optimizations
 */
function digitaldreamscape_scripts()
{
    // Enqueue theme stylesheet with cache busting - unified brand header v3.0.1
    wp_enqueue_style('digitaldreamscape-style', get_stylesheet_uri(), array(), '3.0.1');

    // Enqueue beautiful blog template styles (conditionally on blog pages)
    if (is_page('blog') || is_home() || is_archive()) {
        wp_enqueue_style('digitaldreamscape-beautiful-blog', get_template_directory_uri() . '/assets/css/beautiful-blog.css', array('digitaldreamscape-style'), '1.0.0');
    }
    
    // Enqueue beautiful single post styles (conditionally on single posts)
    if (is_single()) {
        wp_enqueue_style('digitaldreamscape-beautiful-single', get_template_directory_uri() . '/assets/css/beautiful-single.css', array('digitaldreamscape-style'), '1.0.0');
    }
    
    // Enqueue beautiful streaming template styles (conditionally on streaming page)
    // Use multiple detection methods for reliability
    $is_streaming = is_page('streaming') 
        || is_page_template('page-templates/page-streaming-beautiful.php')
        || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/streaming') !== false);
    if ($is_streaming) {
        wp_enqueue_style('digitaldreamscape-beautiful-streaming', get_template_directory_uri() . '/assets/css/beautiful-streaming.css', array('digitaldreamscape-style'), '1.0.1');
    }
    
    // Enqueue beautiful community template styles (conditionally on community page)
    // Use multiple detection methods for reliability
    $is_community = is_page('community') 
        || is_page_template('page-templates/page-community-beautiful.php')
        || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/community') !== false);
    if ($is_community) {
        wp_enqueue_style('digitaldreamscape-beautiful-community', get_template_directory_uri() . '/assets/css/beautiful-community.css', array('digitaldreamscape-style'), '1.0.1');
    }
    
    // Enqueue beautiful about template styles (conditionally on about page)
    // Use multiple detection methods for reliability
    $is_about = is_page('about') 
        || is_page_template('page-templates/page-about-beautiful.php')
        || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/about') !== false);
    if ($is_about) {
        wp_enqueue_style('digitaldreamscape-beautiful-about', get_template_directory_uri() . '/assets/css/beautiful-about.css', array('digitaldreamscape-style'), '1.0.0');
    }

    // Enqueue theme JavaScript (load in footer for better performance) - unified brand header v3.0.1
    wp_enqueue_script('digitaldreamscape-script', get_template_directory_uri() . '/js/main.js', array(), '3.0.1', true);

    // Add Digital Dreamscape context to page
    wp_localize_script('digitaldreamscape-script', 'dreamscapeContext', array(
        'isEpisode' => is_single(),
        'isArchive' => is_archive(),
        'narrativeMode' => true,
    ));
}
add_action('wp_enqueue_scripts', 'digitaldreamscape_scripts');

/**
 * Fix Text Rendering Issues - Ensure proper word spacing across all pages
 * Applied: 2025-12-23 - Fixes character spacing issues causing missing spaces
 */
function digitaldreamscape_fix_text_rendering() {
    $text_rendering_fix = "
    <style id='digitaldreamscape-text-rendering-fix'>
        /* Global text rendering fix - ensure spaces render correctly */
        html, body, body * {
            word-spacing: normal !important;
            font-feature-settings: 'liga' 0, 'kern' 1 !important;
            font-variant-ligatures: none !important;
        }
        
        /* Body text - no letter-spacing adjustments */
        body,
        p,
        div,
        span,
        li,
        td,
        th,
        label,
        input,
        textarea,
        button,
        .entry-content,
        .page-content,
        .hero-subtitle,
        .card-content,
        .card-excerpt,
        nav a,
        .nav-menu a,
        .menu a {
            letter-spacing: normal !important;
            word-spacing: normal !important;
        }
        
        /* Only headings and badges can have custom letter-spacing */
        h1, h2, h3, h4, h5, h6 {
            letter-spacing: -0.02em !important;
            word-spacing: normal !important;
        }
        
        .badge,
        .page-badge,
        .card-label,
        [class*='badge'] {
            word-spacing: normal !important;
        }
        
        /* Ensure text rendering is optimized */
        body {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    ";
    echo $text_rendering_fix;
}
add_action('wp_head', 'digitaldreamscape_fix_text_rendering', 999);

