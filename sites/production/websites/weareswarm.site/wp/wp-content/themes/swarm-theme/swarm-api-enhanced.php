<?php
/**
 * Swarm Intelligence Theme - Enhanced API Functions
 * 
 * Extended REST API for real-time agent updates with better authentication
 * and data persistence.
 * 
 * @package Swarm_Theme
 * @version 2.0.0
 * @author Agent Architecture Team
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Register Enhanced REST API Routes
 */
function swarm_register_enhanced_api_routes() {
    // Agent status endpoints
    register_rest_route('swarm/v2', '/agents', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_all_agents',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route('swarm/v2', '/agents/(?P<id>[a-z0-9\-]+)', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_single_agent',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route('swarm/v2', '/agents/(?P<id>[a-z0-9\-]+)', array(
        'methods' => 'POST',
        'callback' => 'swarm_update_agent_enhanced',
        'permission_callback' => 'swarm_check_api_permission_enhanced',
    ));
    
    // Mission log endpoints
    register_rest_route('swarm/v2', '/mission-log', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_mission_logs',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route('swarm/v2', '/mission-log', array(
        'methods' => 'POST',
        'callback' => 'swarm_add_mission_log_enhanced',
        'permission_callback' => 'swarm_check_api_permission_enhanced',
    ));
    
    // Missions page endpoint
    register_rest_route('swarm/v2', '/missions', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_missions_page',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route('swarm/v2', '/missions', array(
        'methods' => 'POST',
        'callback' => 'swarm_update_missions_page',
        'permission_callback' => 'swarm_check_api_permission_enhanced',
    ));
    
    // Leaderboard endpoint
    register_rest_route('swarm/v2', '/leaderboard', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_leaderboard',
        'permission_callback' => '__return_true',
    ));
    
    // Stats endpoint
    register_rest_route('swarm/v2', '/stats', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_stats',
        'permission_callback' => '__return_true',
    ));
    
    // Health check endpoint
    register_rest_route('swarm/v2', '/health', array(
        'methods' => 'GET',
        'callback' => 'swarm_health_check',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'swarm_register_enhanced_api_routes');

/**
 * Enhanced API Permission Check
 * Supports both Application Passwords and API Keys
 */
function swarm_check_api_permission_enhanced($request) {
    // Check for Application Password authentication
    $current_user = wp_get_current_user();
    if ($current_user->ID > 0) {
        return true;
    }
    
    // Check for custom API key in header
    $api_key = $request->get_header('X-Swarm-API-Key');
    if (!empty($api_key)) {
        $stored_key = get_option('swarm_api_key');
        if ($stored_key && hash_equals($stored_key, $api_key)) {
            return true;
        }
    }
    
    return new WP_Error(
        'rest_forbidden',
        'Authentication required',
        array('status' => 401)
    );
}

/**
 * Get All Agents
 */
function swarm_get_all_agents($request) {
    $agents = get_option('swarm_agents_data', array());
    
    if (empty($agents)) {
        // Return default agent structure
        $agents = get_swarm_default_agents();
    }
    
    return new WP_REST_Response($agents, 200);
}

/**
 * Get Single Agent
 */
function swarm_get_single_agent($request) {
    $agent_id = $request['id'];
    $agents = get_option('swarm_agents_data', array());
    
    if (isset($agents[$agent_id])) {
        return new WP_REST_Response($agents[$agent_id], 200);
    }
    
    return new WP_Error(
        'agent_not_found',
        'Agent not found',
        array('status' => 404)
    );
}

/**
 * Update Agent Status (Enhanced)
 */
function swarm_update_agent_enhanced($request) {
    $agent_id = $request['id'];
    
    // Get current agents
    $agents = get_option('swarm_agents_data', array());
    
    // Prepare update data
    $update_data = array(
        'status' => sanitize_text_field($request->get_param('status')),
        'points' => intval($request->get_param('points')),
        'mission' => sanitize_textarea_field($request->get_param('mission')),
        'updated_at' => current_time('mysql'),
        'timestamp' => time(),
    );
    
    // Optional fields
    $optional_fields = array(
        'last_updated',
        'current_phase',
        'cycle_count',
        'energy_level',
        'current_task',
        'progress_percentage',
        'eta_hours'
    );
    
    foreach ($optional_fields as $field) {
        $value = $request->get_param($field);
        if ($value !== null) {
            $update_data[$field] = sanitize_text_field($value);
        }
    }
    
    // Merge with existing data or create new
    if (isset($agents[$agent_id])) {
        $agents[$agent_id] = array_merge($agents[$agent_id], $update_data);
    } else {
        $agents[$agent_id] = $update_data;
        $agents[$agent_id]['agent_id'] = $agent_id;
    }
    
    // Save to database
    update_option('swarm_agents_data', $agents);
    
    // Also save history for analytics
    swarm_save_agent_history($agent_id, $update_data);
    
    return new WP_REST_Response(array(
        'success' => true,
        'agent' => $agent_id,
        'data' => $agents[$agent_id],
        'message' => 'Agent status updated successfully',
    ), 200);
}

/**
 * Add Mission Log (Enhanced)
 */
function swarm_add_mission_log_enhanced($request) {
    $logs = get_option('swarm_mission_logs', array());
    
    $log_entry = array(
        'id' => uniqid('log_'),
        'agent' => sanitize_text_field($request->get_param('agent')),
        'message' => sanitize_textarea_field($request->get_param('message')),
        'priority' => sanitize_text_field($request->get_param('priority')) ?: 'normal',
        'timestamp' => current_time('mysql'),
        'unix_timestamp' => time(),
    );
    
    // Optional tags
    $tags = $request->get_param('tags');
    if (is_array($tags)) {
        $log_entry['tags'] = array_map('sanitize_text_field', $tags);
    }
    
    // Add to beginning of array (newest first)
    array_unshift($logs, $log_entry);
    
    // Keep last 200 logs
    $logs = array_slice($logs, 0, 200);
    
    update_option('swarm_mission_logs', $logs);
    
    return new WP_REST_Response(array(
        'success' => true,
        'log_id' => $log_entry['id'],
        'message' => 'Mission log added successfully',
    ), 200);
}

/**
 * Get Mission Logs
 */
function swarm_get_mission_logs($request) {
    $limit = intval($request->get_param('limit')) ?: 20;
    $limit = min($limit, 100); // Max 100
    
    $logs = get_option('swarm_mission_logs', array());
    $logs = array_slice($logs, 0, $limit);
    
    return new WP_REST_Response($logs, 200);
}

/**
 * Get Missions Page Content
 */
function swarm_get_missions_page($request) {
    $missions_content = get_option('swarm_missions_content', '');
    $missions_updated = get_option('swarm_missions_updated', '');
    
    return new WP_REST_Response(array(
        'content' => $missions_content,
        'updated_at' => $missions_updated,
    ), 200);
}

/**
 * Update Missions Page Content
 */
function swarm_update_missions_page($request) {
    $content = $request->get_param('content');
    
    if (empty($content)) {
        return new WP_Error(
            'missing_content',
            'Mission content is required',
            array('status' => 400)
        );
    }
    
    // Sanitize HTML content (allow safe HTML tags)
    $content = wp_kses_post($content);
    
    // Save to database
    update_option('swarm_missions_content', $content);
    update_option('swarm_missions_updated', current_time('mysql'));
    
    return new WP_REST_Response(array(
        'success' => true,
        'message' => 'Missions page updated successfully',
        'updated_at' => current_time('mysql'),
    ), 200);
}

/**
 * Get Leaderboard
 */
function swarm_get_leaderboard($request) {
    $agents = get_option('swarm_agents_data', array());
    
    // Sort by points descending
    usort($agents, function($a, $b) {
        return ($b['points'] ?? 0) - ($a['points'] ?? 0);
    });
    
    // Add ranks
    foreach ($agents as $index => &$agent) {
        $agent['rank'] = $index + 1;
    }
    
    return new WP_REST_Response($agents, 200);
}

/**
 * Get Swarm Stats
 */
function swarm_get_stats($request) {
    $agents = get_option('swarm_agents_data', array());
    
    $total_agents = count($agents);
    $active_agents = 0;
    $total_points = 0;
    
    foreach ($agents as $agent) {
        if (isset($agent['status']) && $agent['status'] === 'active') {
            $active_agents++;
        }
        $total_points += $agent['points'] ?? 0;
    }
    
    $stats = array(
        'total_agents' => $total_agents,
        'active_agents' => $active_agents,
        'total_points' => $total_points,
        'avg_points' => $total_agents > 0 ? round($total_points / $total_agents) : 0,
        'last_updated' => current_time('mysql'),
    );
    
    return new WP_REST_Response($stats, 200);
}

/**
 * Health Check Endpoint
 */
function swarm_health_check($request) {
    return new WP_REST_Response(array(
        'status' => 'healthy',
        'version' => '2.0.0',
        'timestamp' => current_time('mysql'),
    ), 200);
}

/**
 * Save Agent History for Analytics
 */
function swarm_save_agent_history($agent_id, $data) {
    $history = get_option('swarm_agent_history', array());
    
    if (!isset($history[$agent_id])) {
        $history[$agent_id] = array();
    }
    
    $history[$agent_id][] = array_merge($data, array(
        'recorded_at' => time(),
    ));
    
    // Keep last 100 entries per agent
    $history[$agent_id] = array_slice($history[$agent_id], -100);
    
    update_option('swarm_agent_history', $history);
}

/**
 * Get Default Agents Structure
 */
function get_swarm_default_agents() {
    return array(
        'agent-1' => array(
            'agent_id' => 'agent-1',
            'name' => 'Agent-1',
            'role' => 'Integration & Core Systems',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Awaiting assignment',
        ),
        'agent-2' => array(
            'agent_id' => 'agent-2',
            'name' => 'Agent-2',
            'role' => 'Architecture & Design',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Awaiting assignment',
        ),
        'agent-3' => array(
            'agent_id' => 'agent-3',
            'name' => 'Agent-3',
            'role' => 'Infrastructure & DevOps',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Awaiting assignment',
        ),
        'agent-5' => array(
            'agent_id' => 'agent-5',
            'name' => 'Agent-5',
            'role' => 'Business Intelligence',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Awaiting assignment',
        ),
        'agent-6' => array(
            'agent_id' => 'agent-6',
            'name' => 'Agent-6',
            'role' => 'Coordination & Communication',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Awaiting assignment',
        ),
        'agent-7' => array(
            'agent_id' => 'agent-7',
            'name' => 'Agent-7',
            'role' => 'Web Development',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Awaiting assignment',
        ),
        'agent-8' => array(
            'agent_id' => 'agent-8',
            'name' => 'Agent-8',
            'role' => 'SSOT & System Integration',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Awaiting assignment',
        ),
        'captain' => array(
            'agent_id' => 'captain',
            'name' => 'Captain Agent-4',
            'role' => 'Mission Commander',
            'status' => 'idle',
            'points' => 0,
            'mission' => 'Strategic oversight',
        ),
    );
}

/**
 * Admin Settings Page
 */
function swarm_api_settings_page() {
    add_options_page(
        'Swarm API Settings',
        'Swarm API',
        'manage_options',
        'swarm-api-settings',
        'swarm_api_settings_render'
    );
}
add_action('admin_menu', 'swarm_api_settings_page');

/**
 * Render Settings Page
 */
function swarm_api_settings_render() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Handle API key generation
    if (isset($_POST['generate_api_key'])) {
        $new_key = bin2hex(random_bytes(32));
        update_option('swarm_api_key', $new_key);
        echo '<div class="notice notice-success"><p>New API key generated!</p></div>';
    }
    
    $api_key = get_option('swarm_api_key');
    
    ?>
    <div class="wrap">
        <h1>Swarm API Settings</h1>
        
        <h2>API Key</h2>
        <p>Use this API key in the <code>X-Swarm-API-Key</code> header for authentication.</p>
        
        <?php if ($api_key): ?>
            <p><code><?php echo esc_html($api_key); ?></code></p>
        <?php else: ?>
            <p>No API key generated yet.</p>
        <?php endif; ?>
        
        <form method="post">
            <input type="submit" name="generate_api_key" class="button button-primary" 
                   value="<?php echo $api_key ? 'Regenerate API Key' : 'Generate API Key'; ?>">
        </form>
        
        <h2>API Endpoints</h2>
        <ul>
            <li><code>GET /wp-json/swarm/v2/agents</code> - Get all agents</li>
            <li><code>GET /wp-json/swarm/v2/agents/{id}</code> - Get specific agent</li>
            <li><code>POST /wp-json/swarm/v2/agents/{id}</code> - Update agent status</li>
            <li><code>GET /wp-json/swarm/v2/mission-log</code> - Get mission logs</li>
            <li><code>POST /wp-json/swarm/v2/mission-log</code> - Add mission log</li>
            <li><code>GET /wp-json/swarm/v2/leaderboard</code> - Get leaderboard</li>
            <li><code>GET /wp-json/swarm/v2/stats</code> - Get swarm stats</li>
            <li><code>GET /wp-json/swarm/v2/health</code> - Health check</li>
        </ul>
    </div>
    <?php
}

?>

