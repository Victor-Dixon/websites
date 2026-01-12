<?php
class Swarm_Public {
    public function __construct() {
        add_shortcode('swarm_status', array($this, 'swarm_status_shortcode'));
    }
    public function swarm_status_shortcode() {
        global $wpdb;
        $agent_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}swarm_agents WHERE status = 'active'");
        return '<div class="swarm-status"><h3>Swarm Status</h3><p>Active Agents: ' . $agent_count . '</p></div>';
    }
}