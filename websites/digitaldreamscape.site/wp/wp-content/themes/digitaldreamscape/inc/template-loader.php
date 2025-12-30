<?php
/**
 * Template Loading Logic
 * 
 * Custom template loading and selection logic
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Use beautiful single post template for all single posts
 * Applied: 2025-12-23 - Blog post design improvements
 */
function digitaldreamscape_single_template($template) {
    if (is_single() && !is_admin() && !wp_doing_ajax()) {
        $beautiful_template = locate_template('single-beautiful.php');
        if ($beautiful_template) {
            return $beautiful_template;
        }
    }
    return $template;
}
add_filter('template_include', 'digitaldreamscape_single_template', 98); // Priority 98 - before page template filter

/**
 * Enhanced Template Loading Fix
 * Ensures page templates load correctly and handles cache clearing
 * Priority 999 ensures this runs before most other template filters
 * 
 * Applied: 2025-12-23
 */
add_filter('template_include', function ($template) {
    // Skip admin and AJAX requests
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return $template;
    }

    // Get the page slug from URL or post object
    $page_slug = null;

    if (is_page()) {
        global $post;
        if ($post && isset($post->post_name)) {
            $page_slug = $post->post_name;
        }
    }

    // Fallback: Check URL directly
    if (!$page_slug && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $request_parts = explode('/', $request_uri);
        $page_slug = end($request_parts);
    }

    // Map page slugs to templates (customize per site)
    $page_templates = array(
        'blog' => 'page-templates/page-blog-beautiful.php',  // Force blog page to use beautiful blog template
        'streaming' => 'page-templates/page-streaming-beautiful.php',  // Force streaming page to use beautiful streaming template
        'community' => 'page-templates/page-community-beautiful.php',  // Force community page to use beautiful community template
        'about' => 'page-templates/page-about-beautiful.php',  // Force about page to use beautiful about template
        // Add other site-specific page templates here
    );

    if ($page_slug && isset($page_templates[$page_slug])) {
        $custom_template = locate_template($page_templates[$page_slug]);

        if ($custom_template && file_exists($custom_template)) {
            // If page exists but template isn't set, update it
            if (is_page()) {
                global $post;
                $current_template = get_page_template_slug($post->ID);
                if ($current_template !== $page_templates[$page_slug]) {
                    update_post_meta($post->ID, '_wp_page_template', $page_templates[$page_slug]);
                }
            }

            return $custom_template;
        }
    }

    // Handle 404 cases (fallback for pages that don't exist yet)
    if (is_404() && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $request_parts = explode('/', $request_uri);
        $uri_slug = end($request_parts);

        if (isset($page_templates[$uri_slug])) {
            $new_template = locate_template($page_templates[$uri_slug]);
            if ($new_template && file_exists($new_template)) {
                // Set up WordPress query to treat this as a page
                global $wp_query;
                $wp_query->is_404 = false;
                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                $wp_query->queried_object = (object) array(
                    'post_type' => 'page',
                    'post_name' => $uri_slug,
                );
                return $new_template;
            }
        }
    }

    return $template;
}, 999);

/**
 * Clear cache when theme is activated or updated
 * This helps ensure template changes take effect immediately
 */
function clear_template_cache_on_theme_change()
{
    // Clear object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }

    // Clear LiteSpeed Cache if active
    if (class_exists('LiteSpeed_Cache') && method_exists('LiteSpeed_Cache', 'purge_all')) {
        LiteSpeed_Cache::purge_all();
    }

    // Clear rewrite rules to ensure permalinks work
    flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'clear_template_cache_on_theme_change');

