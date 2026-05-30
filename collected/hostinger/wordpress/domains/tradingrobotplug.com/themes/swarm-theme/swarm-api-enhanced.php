<?php
/**
 * Swarm Agent Status REST API
 * Enables autonomous agent status updates to WordPress
 */

// Register REST API endpoints
add_action('rest_api_init', function () {
    // Update agent status endpoint
    register_rest_route('swarm/v2', '/agents/(?P<id>[a-zA-Z0-9-]+)', array(
        'methods' => 'POST',
        'callback' => 'swarm_update_agent_status',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    // Get all agents endpoint
    register_rest_route('swarm/v2', '/agents', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_all_agents',
        'permission_callback' => '__return_true'
    ));
    
    // Health check endpoint
    register_rest_route('swarm/v2', '/health', array(
        'methods' => 'GET',
        'callback' => 'swarm_health_check',
        'permission_callback' => '__return_true'
    ));
    
    // Mission log endpoint
    register_rest_route('swarm/v2', '/mission-log', array(
        'methods' => 'POST',
        'callback' => 'swarm_post_mission_log',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
});

/**
 * Update agent status
 */
function swarm_update_agent_status($request) {
    $agent_id = sanitize_text_field($request['id']);
    $status = sanitize_text_field($request->get_param('status'));
    $points = intval($request->get_param('points'));
    $mission = sanitize_text_field($request->get_param('mission'));
    $updated = $request->get_param('updated');
    
    // Save to WordPress options
    $agent_data = array(
        'status' => $status,
        'points' => $points,
        'mission' => $mission,
        'updated' => $updated ? $updated : current_time('mysql'),
        'last_sync' => current_time('mysql')
    );
    
    update_option("swarm_agent_$agent_id", $agent_data);
    
    return array(
        'success' => true,
        'agent_id' => $agent_id,
        'message' => 'Agent status updated successfully',
        'data' => $agent_data
    );
}

/**
 * Get all agents
 */
function swarm_get_all_agents($request) {
    $agents = array();
    $agent_ids = array('agent-1', 'agent-2', 'agent-3', 'agent-4', 'agent-5', 'agent-6', 'agent-7', 'agent-8');
    
    foreach ($agent_ids as $agent_id) {
        $data = get_option("swarm_agent_$agent_id", null);
        if ($data) {
            $agents[$agent_id] = $data;
        }
    }
    
    return array(
        'success' => true,
        'agents' => $agents,
        'count' => count($agents)
    );
}

/**
 * Health check
 */
function swarm_health_check($request) {
    return array(
        'status' => 'ok',
        'swarm' => 'active',
        'timestamp' => current_time('mysql')
    );
}

/**
 * Post mission log
 */
function swarm_post_mission_log($request) {
    $agent = sanitize_text_field($request->get_param('agent'));
    $message = sanitize_text_field($request->get_param('message'));
    $priority = sanitize_text_field($request->get_param('priority'));
    
    // Save to options (or custom post type in future)
    $logs = get_option('swarm_mission_logs', array());
    $logs[] = array(
        'agent' => $agent,
        'message' => $message,
        'priority' => $priority,
        'timestamp' => current_time('mysql')
    );
    
    // Keep last 100 logs
    if (count($logs) > 100) {
        $logs = array_slice($logs, -100);
    }
    
    update_option('swarm_mission_logs', $logs);
    
    return array(
        'success' => true,
        'message' => 'Mission log posted successfully'
    );
}

