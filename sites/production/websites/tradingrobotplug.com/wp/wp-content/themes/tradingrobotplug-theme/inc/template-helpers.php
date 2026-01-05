<?php
/**
 * Template Helpers Module
 * Template loading fixes and 404 handling
 * 
 * @package TradingRobotPlug
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced template loading with 404 fallback
 */
function trp_template_include($template)
{
    // Skip admin and AJAX requests
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return $template;
    }
    
    // Allow WordPress template hierarchy to handle front page and blog index
    // front-page.php has highest priority for static front page
    // home.php is used for blog posts index
    if (is_front_page() || is_home()) {
        return $template;
    }
    
    // Get page slug from URL or post object
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
    
    // Map page slugs to templates
    $page_templates = array(
        'products' => 'page-products.php',
        'features' => 'page-features.php',
        'pricing' => 'page-pricing.php',
        'blog' => 'page-blog.php',
        'ai-swarm' => 'page-ai-swarm.php',
        'contact' => 'page-contact.php',
        'waitlist' => 'page-waitlist.php',
        'thank-you' => 'page-thank-you.php',
        // P0 Compliance - Legal pages
        'privacy' => 'page-privacy.php',
        'terms-of-service' => 'page-terms-of-service.php',
        'product-terms' => 'page-product-terms.php',
    );
    
    // Check if custom template exists
    if ($page_slug && isset($page_templates[$page_slug])) {
        $custom_template = locate_template($page_templates[$page_slug]);
        
        if ($custom_template && file_exists($custom_template)) {
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
    
    // Handle 404 cases
    if (is_404() && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $request_parts = explode('/', $request_uri);
        $uri_slug = end($request_parts);
        
        if (isset($page_templates[$uri_slug])) {
            $new_template = locate_template($page_templates[$uri_slug]);
            if ($new_template && file_exists($new_template)) {
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
}

add_filter('template_include', 'trp_template_include', 999);

/**
 * Clear cache on theme activation
 */
function trp_clear_template_cache()
{
    // Clear object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear LiteSpeed Cache if active
    if (class_exists('LiteSpeed_Cache') && method_exists('LiteSpeed_Cache', 'purge_all')) {
        LiteSpeed_Cache::purge_all();
    }
    
    // Clear rewrite rules
    flush_rewrite_rules(false);
}

add_action('after_switch_theme', 'trp_clear_template_cache');

