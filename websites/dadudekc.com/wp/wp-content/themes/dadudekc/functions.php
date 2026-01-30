<?php
/**
 * Dadu Dek C Theme Functions
 * Unified Positioning: "I build automation systems that save teams hours every week."
 */

// Positioning Functions (SSOT Pattern)
function dadudekc_get_positioning_line() {
    return "I build automation systems that save teams hours every week.";
}

function dadudekc_get_positioning_tagline() {
    return "Automation systems that save teams hours every week.";
}

function dadudekc_get_positioning_question() {
    return "Need automation systems that save teams hours every week?";
}

function dadudekc_get_positioning_action() {
    return "Let's build automation systems that save your team hours every week.";
}

// Enqueue styles and scripts
function dadudekc_enqueue_styles() {
    wp_enqueue_style('dadudekc-style', get_stylesheet_uri());

    // Tailwind CSS for Hero Animations
    wp_enqueue_style('tailwind-css', 'https://cdn.tailwindcss.com', array(), null);
}
add_action('wp_enqueue_scripts', 'dadudekc_enqueue_styles');

// Update site tagline programmatically
function dadudekc_update_site_tagline() {
    if (get_bloginfo('description') !== dadudekc_get_positioning_tagline()) {
        update_option('blogdescription', dadudekc_get_positioning_tagline());
    }
}
add_action('init', 'dadudekc_update_site_tagline');

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
    // Defer Tailwind CSS as it's not critical for initial render (disabled: had escaping issues)
    if ($handle === 'tailwind-css') {
        return $html;
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

/**
 * Avoid caching project archive so /projects/ always reflects current projects.
 */
function dadudekc_project_archive_no_cache() {
    if (!is_post_type_archive('project') || headers_sent()) {
        return;
    }
    if (function_exists('nocache_headers')) {
        nocache_headers();
    }
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}
add_action('template_redirect', 'dadudekc_project_archive_no_cache', 1);
?>