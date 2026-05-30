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
}
add_action('wp_enqueue_scripts', 'swarm_enqueue_assets');

/**
 * Agent Data
 */
function get_swarm_agents() {
    return array(
        array(
            'id' => 'agent-1',
            'name' => 'Agent-1',
            'role' => 'Integration & Core Systems',
            'description' => 'Specializes in system integration, runtime orchestration, and core infrastructure.',
            'status' => 'active',
            'points' => 8500,
            'coordinates' => '(-1269, 481)',
            'specialties' => array('Integration', 'Core Systems', 'Runtime Logic'),
        ),
        array(
            'id' => 'agent-2',
            'name' => 'Agent-2',
            'role' => 'Architecture & Design',
            'description' => 'Lead architect specializing in system design, V2 compliance, and strategic planning.',
            'status' => 'active',
            'points' => 10600,
            'coordinates' => '(-308, 480)',
            'specialties' => array('Architecture', 'V2 Compliance', 'Strategic Analysis'),
        ),
        array(
            'id' => 'agent-3',
            'name' => 'Agent-3',
            'role' => 'Infrastructure & DevOps',
            'description' => 'DevOps specialist focused on validation, tooling, and infrastructure management.',
            'status' => 'active',
            'points' => 7200,
            'coordinates' => '(-1269, 1001)',
            'specialties' => array('DevOps', 'Validation', 'Infrastructure'),
        ),
        array(
            'id' => 'agent-5',
            'name' => 'Agent-5',
            'role' => 'Business Intelligence',
            'description' => 'BI specialist handling analytics, decision logic, and pattern optimization.',
            'status' => 'active',
            'points' => 6800,
            'coordinates' => '(652, 421)',
            'specialties' => array('Analytics', 'Business Logic', 'Optimization'),
        ),
        array(
            'id' => 'agent-6',
            'name' => 'Agent-6',
            'role' => 'Coordination & Communication (Co-Captain)',
            'description' => 'Co-Captain specializing in swarm coordination, messaging, and team synchronization.',
            'status' => 'active',
            'points' => 9400,
            'coordinates' => '(1612, 419)',
            'specialties' => array('Coordination', 'Communication', 'Leadership'),
        ),
        array(
            'id' => 'agent-7',
            'name' => 'Agent-7',
            'role' => 'Web Development',
            'description' => 'Web specialist focused on front-end, WordPress, and web integrations.',
            'status' => 'active',
            'points' => 5900,
            'coordinates' => '(920, 851)',
            'specialties' => array('Web Development', 'WordPress', 'Front-End'),
        ),
        array(
            'id' => 'agent-8',
            'name' => 'Agent-8',
            'role' => 'SSOT & System Integration',
            'description' => 'Single Source of Truth specialist ensuring consistency across all systems.',
            'status' => 'active',
            'points' => 7100,
            'coordinates' => '(1611, 941)',
            'specialties' => array('SSOT', 'Integration', 'Consistency'),
        ),
        array(
            'id' => 'captain',
            'name' => 'Captain Agent-4',
            'role' => 'Mission Commander',
            'description' => 'Strategic mission commander overseeing all swarm operations and coordination.',
            'status' => 'active',
            'points' => 15000,
            'coordinates' => '(-308, 1000)',
            'specialties' => array('Strategy', 'Command', 'Coordination'),
        ),
    );
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



// Load Swarm Agent Status REST API
require_once get_template_directory() . '/swarm-api-enhanced.php';
