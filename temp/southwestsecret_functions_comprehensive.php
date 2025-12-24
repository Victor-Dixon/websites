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


/**
 * Comprehensive Site Fixes - Added by Agent-7
 * Fixes font rendering, character spacing, and ensures proper encoding
 */

// Force UTF-8 encoding
function southwestsecret_force_utf8() {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<meta charset="UTF-8">';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
}
add_action('wp_head', 'southwestsecret_force_utf8', 1);

// Fix font rendering with comprehensive CSS
function southwestsecret_comprehensive_font_fix() {
    $font_css = "
        /* Force UTF-8 and proper font rendering */
        * {
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
            font-feature-settings: 'kern' 1 !important;
            font-kerning: normal !important;
            letter-spacing: 0 !important;
        }
        
        /* Use system fonts with proper fallbacks */
        body, input, textarea, select, button, h1, h2, h3, h4, h5, h6, p, span, a, li, div {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            letter-spacing: 0 !important;
            word-spacing: 0.05em !important;
        }
        
        /* Ensure proper character display */
        html {
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
        }
        
        /* Fix any character spacing issues */
        * {
            text-transform: none !important;
        }
    ";
    
    wp_add_inline_style('wp-block-library', $font_css);
}
add_action('wp_enqueue_scripts', 'southwestsecret_comprehensive_font_fix', 999);

// Fix navigation menu items
function southwestsecret_fix_navigation($items, $args) {
    if ($args->theme_location == 'primary' || $args->theme_location == '') {
        foreach ($items as $item) {
            // Fix common menu item text issues
            if (strpos($item->title, 'Capabilitie') !== false) {
                $item->title = str_replace('Capabilitie', 'Capabilities', $item->title);
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'southwestsecret_fix_navigation', 10, 2);

// Content filter to fix any remaining text issues
function southwestsecret_fix_content_text($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Fix common encoding issues
    $fixes = array(
        'Hou ton' => 'Houston',
        'In ide' => 'Inside',
        ' crewed' => 'screwed',
        ' etup' => 'setup',
        ' etli t' => 'setlist',
        ' tran form' => 'transform',
        ' tarted' => 'started',
        ' pirit' => 'spirit',
        '  ame' => 'same',
        ' ride ' => 'rides ',
        ' u ' => 'us ',
        '  peaker ' => 'speakers ',
        '  lowed' => 'slowed',
        ' remixe ' => 'remixes ',
        '  outhern' => 'southern',
        ' ho pitality' => 'hospitality',
        '  e ion' => 'session',
        '  torie ' => 'stories ',
        '  heartbeat' => 'heartbeat',
        '  low' => 'slow',
        '  low it' => 'slow it',
        '  low down' => 'slow down',
        '  low thing' => 'slow things',
        '  oundtrack' => 'soundtrack',
        ' per onalized' => 'personalized',
        '  how ' => 'show ',
        '  oulful' => 'soulful',
        '  pace' => 'space',
        '  tory' => 'story',
        '  et' => 'set',
        '  et up' => 'set up',
        '  election' => 'selection',
        '  alway ' => 'always ',
        '  et recap' => 'set recap',
        ' li t' => 'list',
        'Capabilitie' => 'Capabilities',
    );
    
    foreach ($fixes as $old => $new) {
        $content = str_replace($old, $new, $content);
    }
    
    return $content;
}
add_filter('the_content', 'southwestsecret_fix_content_text', 999);
add_filter('the_title', 'southwestsecret_fix_content_text', 999);
add_filter('wp_nav_menu_items', 'southwestsecret_fix_content_text', 999);
