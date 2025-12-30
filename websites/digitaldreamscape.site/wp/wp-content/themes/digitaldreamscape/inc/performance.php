<?php
/**
 * Performance Optimizations
 * 
 * Functions to improve site speed and performance
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Lazy load images (native WordPress support for WordPress 5.5+)
 */
function digitaldreamscape_lazy_load_images($attr, $attachment, $size)
{
    if (!is_admin()) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'digitaldreamscape_lazy_load_images', 10, 3);

/**
 * Remove unnecessary WordPress features for better performance
 */
function digitaldreamscape_performance_cleanup()
{
    // Remove emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    // Remove unnecessary RSS feed links
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');

    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'digitaldreamscape_performance_cleanup');

/**
 * Optimize WordPress queries
 */
function digitaldreamscape_optimize_queries($query)
{
    if (!is_admin() && $query->is_main_query()) {
        // Limit post queries to improve performance
        if (is_home() || is_archive()) {
            $query->set('posts_per_page', 12);
        }
    }
}
add_action('pre_get_posts', 'digitaldreamscape_optimize_queries');

