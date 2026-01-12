<?php
class Swarm_Intelligence_Core {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('init', array($this, 'register_swarm_post_types'));
        add_action('rest_api_init', array($this, 'register_api_endpoints'));
    }

    public function register_swarm_post_types() {
        // Swarm intelligence post types would be registered here
    }

    public function register_api_endpoints() {
        register_rest_route('swarm-intelligence/v1', '/agents', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_agents'),
            'permission_callback' => array($this, 'check_api_permissions'),
        ));

        register_rest_route('swarm-intelligence/v1', '/coordination', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_coordination'),
            'permission_callback' => array($this, 'check_api_permissions'),
        ));
    }

    public function get_agents() {
        global $wpdb;
        $agents = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}swarm_agents WHERE status = 'active'");
        return new WP_REST_Response($agents, 200);
    }

    public function create_coordination($request) {
        // Coordination logic would go here
        return new WP_REST_Response(array('status' => 'coordination_created'), 201);
    }

    public function check_api_permissions() {
        return current_user_can('manage_options');
    }
}