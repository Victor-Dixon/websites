<?php
/**
 * Swarm Intelligence Theme Functions
 * 
 * @package Swarm_Theme
 * @version 1.0.0
 * @author Agent-2 (Architecture) & The Swarm
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

// Include enhanced API functionality
require_once get_template_directory() . '/swarm-api-enhanced.php';

/**
 * Theme Setup
 */
function swarm_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'swarm-theme'),
        'footer' => __('Footer Menu', 'swarm-theme'),
    ));
}
add_action('after_setup_theme', 'swarm_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function swarm_enqueue_assets() {
    // Main stylesheet
    wp_enqueue_style('swarm-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Google Fonts
    wp_enqueue_style('swarm-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
    
    // Main JavaScript
    wp_enqueue_script('swarm-main', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('swarm-main', 'swarmData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('swarm_nonce'),
    ));

    // ELS Suite assets (registered and loaded only on the template)
    wp_register_script(
        'swarm-els-suite',
        get_template_directory_uri() . '/js/els-suite.js',
        array(),
        '1.0.0',
        true
    );

    wp_localize_script('swarm-els-suite', 'swarmElsSuite', array(
        'restBase' => untrailingslashit( esc_url_raw( rest_url( 'weareswarm/v1' ) ) ),
        'siteUrl'  => esc_url( home_url( '/' ) ),
    ));

    if (is_page_template('page-els-suite.php')) {
        wp_enqueue_script('swarm-els-suite');
    }
}
add_action('wp_enqueue_scripts', 'swarm_enqueue_assets');

/**
 * Agent Data - Mode-Aware (4-Agent Mode)
 * Returns only active agents based on current mode configuration
 */
function get_swarm_agents() {
    // Current mode: 4-agent mode (Agent-1, Agent-2, Agent-3, Agent-4)
    // Agents 5-8 are paused but can be reactivated when switching modes
    
    $all_agents = array(
        'agent-1' => array(
            'id' => 'agent-1',
            'name' => 'Agent-1',
            'role' => 'Integration & Core Systems',
            'description' => 'Specializes in system integration, runtime orchestration, and core infrastructure.',
            'status' => 'active',
            'points' => 8500,
            'coordinates' => '(-1269, 481)',
            'specialties' => array('Integration', 'Core Systems', 'Runtime Logic'),
            'mode' => '4-agent',
        ),
        'agent-2' => array(
            'id' => 'agent-2',
            'name' => 'Agent-2',
            'role' => 'Architecture & Design',
            'description' => 'Lead architect specializing in system design, V2 compliance, and strategic planning.',
            'status' => 'active',
            'points' => 10600,
            'coordinates' => '(-308, 480)',
            'specialties' => array('Architecture', 'V2 Compliance', 'Strategic Analysis'),
            'mode' => '4-agent',
        ),
        'agent-3' => array(
            'id' => 'agent-3',
            'name' => 'Agent-3',
            'role' => 'Infrastructure & DevOps',
            'description' => 'DevOps specialist focused on validation, tooling, and infrastructure management.',
            'status' => 'active',
            'points' => 7200,
            'coordinates' => '(-1269, 1001)',
            'specialties' => array('DevOps', 'Validation', 'Infrastructure'),
            'mode' => '4-agent',
        ),
        'agent-4' => array(
            'id' => 'agent-4',
            'name' => 'Captain Agent-4',
            'role' => 'Strategic Oversight (Captain)',
            'description' => 'Strategic mission commander overseeing all swarm operations, coordination, gatekeeping, and force multiplier monitoring.',
            'status' => 'active',
            'points' => 15000,
            'coordinates' => '(-308, 1000)',
            'specialties' => array('Strategy', 'Command', 'Coordination', 'Gatekeeping'),
            'mode' => '4-agent',
        ),
        // Paused agents (can be reactivated in other modes)
        'agent-5' => array(
            'id' => 'agent-5',
            'name' => 'Agent-5',
            'role' => 'Business Intelligence',
            'description' => 'BI specialist handling analytics, decision logic, and pattern optimization. [Currently Paused - 5/6/8-Agent Mode]',
            'status' => 'paused',
            'points' => 6800,
            'coordinates' => '(652, 421)',
            'specialties' => array('Analytics', 'Business Logic', 'Optimization'),
            'mode' => '5-agent',
        ),
        'agent-6' => array(
            'id' => 'agent-6',
            'name' => 'Agent-6',
            'role' => 'Coordination & Communication',
            'description' => 'Specialist in swarm coordination, messaging, and team synchronization. [Currently Paused - 6/8-Agent Mode]',
            'status' => 'paused',
            'points' => 9400,
            'coordinates' => '(1612, 419)',
            'specialties' => array('Coordination', 'Communication', 'Leadership'),
            'mode' => '6-agent',
        ),
        'agent-7' => array(
            'id' => 'agent-7',
            'name' => 'Agent-7',
            'role' => 'Web Development',
            'description' => 'Web specialist focused on front-end, WordPress, and web integrations. [Currently Paused - 8-Agent Mode]',
            'status' => 'paused',
            'points' => 5900,
            'coordinates' => '(653, 940)',
            'specialties' => array('Web Development', 'WordPress', 'Front-End'),
            'mode' => '8-agent',
        ),
        'agent-8' => array(
            'id' => 'agent-8',
            'name' => 'Agent-8',
            'role' => 'SSOT & System Integration',
            'description' => 'Single Source of Truth specialist ensuring consistency across all systems. [Currently Paused - 8-Agent Mode]',
            'status' => 'paused',
            'points' => 7100,
            'coordinates' => '(1611, 941)',
            'specialties' => array('SSOT', 'Integration', 'Consistency'),
            'mode' => '8-agent',
        ),
    );
    
    // Filter to show only active agents in current mode (4-agent mode)
    // Optionally show paused agents with different styling
    $current_mode = '4-agent';
    $active_agent_ids = array('agent-1', 'agent-2', 'agent-3', 'agent-4');
    
    $active_agents = array();
    foreach ($active_agent_ids as $agent_id) {
        if (isset($all_agents[$agent_id])) {
            $active_agents[] = $all_agents[$agent_id];
        }
    }
    
    // Return active agents only (can modify to include paused agents for display)
    return $active_agents;
}

/**
 * REST API Endpoint for Agent Updates
 */
function swarm_register_api_routes() {
    register_rest_route('swarm/v1', '/agents/(?P<id>[a-z0-9\-]+)', array(
        'methods' => 'POST',
        'callback' => 'swarm_update_agent',
        'permission_callback' => 'swarm_check_api_permission',
    ));
    
    register_rest_route('swarm/v1', '/mission-log', array(
        'methods' => 'POST',
        'callback' => 'swarm_add_mission_log',
        'permission_callback' => 'swarm_check_api_permission',
    ));
}
add_action('rest_api_init', 'swarm_register_api_routes');

/**
 * Check API Permission (for agent updates)
 */
function swarm_check_api_permission() {
    // Verify nonce or application password
    $headers = getallheaders();
    $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    // Basic authentication check (enhance with proper application passwords)
    if (empty($auth_header)) {
        return false;
    }
    
    // TODO: Implement proper application password verification
    return true;
}

/**
 * Update Agent Status (via REST API)
 */
function swarm_update_agent($request) {
    $agent_id = $request['id'];
    $status = $request->get_param('status');
    $points = $request->get_param('points');
    $mission = $request->get_param('mission');
    
    // Update agent in database (transient for now, can move to custom table)
    $agents = get_transient('swarm_agents') ?: array();
    $agents[$agent_id] = array(
        'status' => sanitize_text_field($status),
        'points' => intval($points),
        'mission' => sanitize_text_field($mission),
        'updated' => current_time('timestamp'),
    );
    set_transient('swarm_agents', $agents, DAY_IN_SECONDS);
    
    return new WP_REST_Response(array(
        'success' => true,
        'agent' => $agent_id,
        'updated' => true,
    ), 200);
}

/**
 * Add Mission Log Entry (via REST API)
 */
function swarm_add_mission_log($request) {
    $agent = $request->get_param('agent');
    $message = $request->get_param('message');
    $priority = $request->get_param('priority') ?: 'normal';
    
    $logs = get_transient('swarm_mission_logs') ?: array();
    $logs[] = array(
        'agent' => sanitize_text_field($agent),
        'message' => sanitize_textarea_field($message),
        'priority' => sanitize_text_field($priority),
        'timestamp' => current_time('timestamp'),
    );
    
    // Keep last 100 logs
    $logs = array_slice($logs, -100);
    set_transient('swarm_mission_logs', $logs, WEEK_IN_SECONDS);
    
    return new WP_REST_Response(array(
        'success' => true,
        'log_added' => true,
    ), 200);
}

/**
 * Get Mission Logs
 */
function get_swarm_mission_logs($limit = 10) {
    $logs = get_transient('swarm_mission_logs') ?: array();
    return array_slice(array_reverse($logs), 0, $limit);
}

/**
 * Swarm Stats Widget
 */
function swarm_stats_widget() {
    register_sidebar(array(
        'name' => __('Swarm Stats Sidebar', 'swarm-theme'),
        'id' => 'swarm-stats',
        'description' => __('Sidebar for swarm statistics', 'swarm-theme'),
        'before_widget' => '<div class="swarm-stat-widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'swarm_stats_widget');

/**
 * Get Total Swarm Stats
 */
function get_swarm_stats() {
    $agents = get_swarm_agents();
    $total_points = array_sum(array_column($agents, 'points'));
    $active_agents = count(array_filter($agents, function($agent) {
        return $agent['status'] === 'active';
    }));
    
    return array(
        'total_agents' => count($agents),
        'active_agents' => $active_agents,
        'total_points' => $total_points,
        'avg_points' => round($total_points / count($agents)),
    );
}

/**
 * Remove Restaurant Menu Items
 * Filters out Flavio restaurant menu items and replaces with Swarm navigation
 */
function swarm_remove_restaurant_menu_items($items, $args) {
    if (!isset($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }
    
    // Restaurant menu items to remove
    $restaurant_items = array(
        'Our Menu',
        'Hi tory',
        'History',
        'Make Reservation',
        'Reservation',
        'Contact',
        'About Us',
        'Menu'
    );
    
    // Filter out restaurant items
    $filtered_items = array();
    foreach ($items as $item) {
        $title = strtolower($item->title);
        $url = strtolower($item->url);
        
        $is_restaurant_item = false;
        foreach ($restaurant_items as $restaurant_item) {
            if (stripos($title, strtolower($restaurant_item)) !== false ||
                stripos($url, 'menu') !== false ||
                stripos($url, 'reservation') !== false ||
                stripos($url, 'flavio') !== false) {
                $is_restaurant_item = true;
                break;
            }
        }
        
        if (!$is_restaurant_item) {
            $filtered_items[] = $item;
        }
    }
    
    // If menu is empty or only has restaurant items, use fallback Swarm menu
    if (empty($filtered_items)) {
        return array(); // Let fallback_cb handle it
    }
    
    return $filtered_items;
}
add_filter('wp_nav_menu_objects', 'swarm_remove_restaurant_menu_items', 999, 2);

/**
 * Create Default Swarm Menu
 * Creates a default menu if none exists
 */
function swarm_create_default_menu() {
    // Check if menu already exists
    $menu_name = 'Swarm Primary';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);
        
        if (!is_wp_error($menu_id)) {
            // Add menu items
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Capabilities',
                'menu-item-url' => home_url('/#capabilities'),
                'menu-item-status' => 'publish',
            ));
            
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Live Activity',
                'menu-item-url' => home_url('/#activity'),
                'menu-item-status' => 'publish',
            ));
            
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => 'Agents',
                'menu-item-url' => home_url('/#agents'),
                'menu-item-status' => 'publish',
            ));
            
            // Assign to primary location
            $locations = get_theme_mod('nav_menu_locations');
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}
add_action('after_setup_theme', 'swarm_create_default_menu', 20);


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
        // Add site-specific page templates here
        // Example: 'about' => 'page-templates/page-about.php',
        // Example: 'blog' => 'page-templates/page-blog.php',
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
function clear_template_cache_on_theme_change() {
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

