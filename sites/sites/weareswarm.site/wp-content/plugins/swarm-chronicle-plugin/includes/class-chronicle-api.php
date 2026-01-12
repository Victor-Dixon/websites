<?php
/**
 * Chronicle API Class - Handles data synchronization
 */

class Chronicle_API {

    private $api_endpoint;
    private $api_key;

    public function __construct() {
        $this->api_endpoint = get_option('swarm_chronicle_api_endpoint', '');
        $this->api_key = get_option('swarm_chronicle_api_key', '');
    }

    public function init() {
        // Register REST API endpoints
        add_action('rest_api_init', array($this, 'register_endpoints'));
    }

    public function register_endpoints() {
        register_rest_route('swarm-chronicle/v1', '/sync', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_sync'),
            'permission_callback' => array($this, 'check_permissions'),
        ));

        register_rest_route('swarm-chronicle/v1', '/data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_data'),
            'permission_callback' => '__return_true', // Public read access
        ));
    }

    public function check_permissions($request) {
        return current_user_can('manage_options');
    }

    public function handle_sync($request) {
        $data = $request->get_json_params();

        if (!$data) {
            return new WP_Error('invalid_data', 'No data provided', array('status' => 400));
        }

        $result = $this->sync_chronicle_data($data);

        if ($result['success']) {
            return new WP_REST_Response(array('message' => 'Data synced successfully'), 200);
        } else {
            return new WP_Error('sync_failed', $result['error'], array('status' => 500));
        }
    }

    public function get_data($request) {
        $type = $request->get_param('type');
        $limit = $request->get_param('limit') ?: 50;
        $agent = $request->get_param('agent') ?: 'all';

        switch ($type) {
            case 'missions':
                return $this->get_missions(array('status' => 'all', 'limit' => $limit, 'agent' => $agent));
            case 'accomplishments':
                return $this->get_accomplishments(array('limit' => $limit, 'agent' => $agent));
            case 'project_state':
                return $this->get_project_state();
            default:
                return $this->get_chronicle_data(array('limit' => $limit, 'agent' => $agent));
        }
    }

    public function sync_chronicle_data($external_data = null) {
        try {
            if ($external_data) {
                // Sync from external API
                $result = $this->sync_from_external($external_data);
            } else {
                // Sync from local files (fallback)
                $result = $this->sync_from_local_files();
            }

            update_option('swarm_chronicle_last_sync', time());
            return array('success' => true, 'message' => 'Sync completed successfully');

        } catch (Exception $e) {
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    private function sync_from_external($data) {
        // Process external chronicle data
        if (isset($data['master_task_log'])) {
            update_option('swarm_chronicle_master_tasks', $data['master_task_log']);
        }

        if (isset($data['cycle_accomplishments'])) {
            update_option('swarm_chronicle_accomplishments', $data['cycle_accomplishments']);
        }

        if (isset($data['project_state'])) {
            update_option('swarm_chronicle_project_state', $data['project_state']);
        }

        return true;
    }

    private function sync_from_local_files() {
        // Fallback: try to read from local files if API not available
        // This would require the files to be accessible from the WordPress environment
        return true;
    }

    public function get_chronicle_data($atts = array()) {
        $limit = isset($atts['limit']) ? intval($atts['limit']) : 50;
        $agent = isset($atts['agent']) ? $atts['agent'] : 'all';

        // Get cached data or fetch from external source
        $master_tasks = get_option('swarm_chronicle_master_tasks', array());
        $accomplishments = get_option('swarm_chronicle_accomplishments', array());

        $entries = array();

        // Process master task log entries
        if (is_array($master_tasks)) {
            foreach ($master_tasks as $task) {
                if ($agent === 'all' || $task['agent'] === $agent) {
                    $entries[] = array(
                        'type' => 'task',
                        'icon' => '🎯',
                        'agent' => $task['agent'],
                        'date' => $task['date'],
                        'content' => $task['content']
                    );
                }

                if (count($entries) >= $limit) break;
            }
        }

        // Process accomplishments
        if (is_array($accomplishments)) {
            foreach ($accomplishments as $accomp) {
                if ($agent === 'all' || $accomp['agent'] === $agent) {
                    $entries[] = array(
                        'type' => 'accomplishment',
                        'icon' => '🏆',
                        'agent' => $accomp['agent'],
                        'date' => $accomp['date'],
                        'content' => $accomp['content']
                    );
                }

                if (count($entries) >= $limit) break;
            }
        }

        // Sort by date (most recent first)
        usort($entries, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array(
            'entries' => array_slice($entries, 0, $limit),
            'total_tasks' => count($master_tasks),
            'completed_tasks' => count(array_filter($master_tasks, function($task) {
                return isset($task['status']) && $task['status'] === 'completed';
            })),
            'active_agents' => count(array_unique(array_column($entries, 'agent'))),
            'has_more' => count($entries) >= $limit
        );
    }

    public function get_missions($atts = array()) {
        $status = isset($atts['status']) ? $atts['status'] : 'all';
        $limit = isset($atts['limit']) ? intval($atts['limit']) : 20;
        $agent = isset($atts['agent']) ? $atts['agent'] : 'all';

        $master_tasks = get_option('swarm_chronicle_master_tasks', array());
        $missions = array();

        foreach ($master_tasks as $task) {
            if ($agent !== 'all' && $task['agent'] !== $agent) continue;
            if ($status !== 'all' && $task['status'] !== $status) continue;

            $missions[] = array(
                'id' => $task['id'],
                'title' => $task['title'],
                'description' => $task['description'],
                'priority' => $task['priority'],
                'status' => $task['status'],
                'agent' => $task['agent'],
                'progress' => isset($task['progress']) ? $task['progress'] : null
            );

            if (count($missions) >= $limit) break;
        }

        return $missions;
    }

    public function get_accomplishments($atts = array()) {
        $limit = isset($atts['limit']) ? intval($atts['limit']) : 25;
        $agent = isset($atts['agent']) ? $atts['agent'] : 'all';

        $accomplishments = get_option('swarm_chronicle_accomplishments', array());
        $filtered = array();

        foreach ($accomplishments as $accomp) {
            if ($agent === 'all' || $accomp['agent'] === $agent) {
                $filtered[] = $accomp;
                if (count($filtered) >= $limit) break;
            }
        }

        return $filtered;
    }

    public function get_project_state($atts = array()) {
        $project_state = get_option('swarm_chronicle_project_state', array());

        // Default values if no data
        if (empty($project_state)) {
            $project_state = array(
                'total_files' => 0,
                'total_loc' => 0,
                'active_components' => 0,
                'health_indicators' => array(
                    array('label' => 'Syntax Errors', 'value' => '0', 'status' => 'good'),
                    array('label' => 'Test Coverage', 'value' => 'Unknown', 'status' => 'unknown'),
                    array('label' => 'Performance', 'value' => 'Unknown', 'status' => 'unknown')
                )
            );
        }

        return $project_state;
    }
}