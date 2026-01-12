<?php
/**
 * WeAreSwarm Theme Functions
 * Swarm Intelligence & Collective AI Agent Coordination
 */

// Enqueue theme styles and scripts
function weareswarmonline_enqueue_styles() {
    wp_enqueue_style('weareswarmonline-style', get_stylesheet_uri());
    wp_enqueue_style('swarm-intelligence-css', get_template_directory_uri() . '/css/swarm-intelligence.css', array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'weareswarmonline_enqueue_styles');

function weareswarmonline_enqueue_scripts() {
    wp_enqueue_script('swarm-intelligence-js', get_template_directory_uri() . '/js/swarm-intelligence.js', array('jquery'), '1.0.0', true);

    // Swarm coordination data
    wp_localize_script('swarm-intelligence-js', 'swarmIntelligence', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('swarm_intelligence_nonce'),
        'agents' => array(
            array('id' => 'Agent-1', 'role' => 'Integration & Core', 'status' => 'active'),
            array('id' => 'Agent-2', 'role' => 'Architecture & Design', 'status' => 'active'),
            array('id' => 'Agent-3', 'role' => 'Infrastructure & DevOps', 'status' => 'active'),
            array('id' => 'Agent-4', 'role' => 'Captain & Strategy', 'status' => 'active'),
            array('id' => 'Agent-5', 'role' => 'Business Intelligence', 'status' => 'active'),
            array('id' => 'Agent-6', 'role' => 'Coordination & Communication', 'status' => 'active'),
            array('id' => 'Agent-7', 'role' => 'Web Development', 'status' => 'active'),
            array('id' => 'Agent-8', 'role' => 'SSOT & System Integration', 'status' => 'active')
        )
    ));
}
add_action('wp_enqueue_scripts', 'weareswarmonline_enqueue_scripts');

// Register navigation menus
function weareswarmonline_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'weareswarmonline'),
        'footer' => __('Footer Menu', 'weareswarmonline'),
        'agent-navigation' => __('Agent Navigation', 'weareswarmonline')
    ));
}
add_action('init', 'weareswarmonline_register_menus');

// Add theme support
function weareswarmonline_theme_support() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));
}
add_action('after_setup_theme', 'weareswarmonline_theme_support');

// Swarm coordination hooks
function swarm_intelligence_init() {
    // Initialize swarm coordination protocols
    do_action('swarm_intelligence_initialized');
}
add_action('init', 'swarm_intelligence_init');

function swarm_agent_status_update($agent_id, $status) {
    // Update agent status in swarm coordination
    update_option("swarm_agent_{$agent_id}_status", $status);
    do_action('swarm_agent_status_changed', $agent_id, $status);
}

// AJAX handlers for swarm coordination
function swarm_get_agent_status() {
    check_ajax_referer('swarm_intelligence_nonce', 'nonce');

    $agents = get_option('swarm_agents_status', array());
    wp_send_json_success($agents);
}
add_action('wp_ajax_swarm_get_agent_status', 'swarm_get_agent_status');
add_action('wp_ajax_nopriv_swarm_get_agent_status', 'swarm_get_agent_status');

// Swarm intelligence page templates
function swarm_page_templates($templates) {
    $templates['page-swarm-dashboard.php'] = 'Swarm Dashboard';
    $templates['page-agent-showcase.php'] = 'Agent Showcase';
    $templates['page-coordination-center.php'] = 'Coordination Center';
    return $templates;
}
add_filter('theme_page_templates', 'swarm_page_templates');
?>
