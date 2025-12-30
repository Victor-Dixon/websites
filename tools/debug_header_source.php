<?php
/**
 * Debug script to identify header source
 * Add this to functions.php temporarily to debug
 */

// Add this to see what template is being used
add_filter('template_include', function($template) {
    if (is_admin()) return $template;
    
    error_log('=== TEMPLATE DEBUG ===');
    error_log('Template file: ' . $template);
    error_log('Is page: ' . (is_page() ? 'yes' : 'no'));
    error_log('Is home: ' . (is_home() ? 'yes' : 'no'));
    error_log('Is front page: ' . (is_front_page() ? 'yes' : 'no'));
    
    if (is_page()) {
        global $post;
        error_log('Page ID: ' . $post->ID);
        error_log('Page slug: ' . $post->post_name);
        error_log('Page template: ' . get_page_template_slug($post->ID));
    }
    
    return $template;
}, 999);

// Check what get_header() is loading
add_action('get_header', function($name) {
    error_log('=== HEADER DEBUG ===');
    error_log('Header name: ' . ($name ? $name : 'default'));
    error_log('Header template: ' . locate_template("header{$name}.php"));
}, 1);

