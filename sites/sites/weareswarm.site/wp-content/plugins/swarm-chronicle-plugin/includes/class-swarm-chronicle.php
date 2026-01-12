<?php
/**
 * Main Swarm Chronicle Plugin Class
 */

class Swarm_Chronicle {

    private $api;
    private $admin;

    public function __construct() {
        $this->api = new Chronicle_API();
        $this->admin = new Chronicle_Admin();
    }

    public function init() {
        // Initialize API endpoints
        $this->api->init();

        // Initialize admin interface
        $this->admin->init();

        // Register shortcodes
        add_shortcode('swarm_chronicle', array($this, 'chronicle_shortcode'));
        add_shortcode('swarm_missions', array($this, 'missions_shortcode'));
        add_shortcode('swarm_accomplishments', array($this, 'accomplishments_shortcode'));
        add_shortcode('swarm_project_state', array($this, 'project_state_shortcode'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Add AJAX handlers
        add_action('wp_ajax_swarm_sync_chronicle', array($this, 'ajax_sync_chronicle'));
        add_action('wp_ajax_nopriv_swarm_sync_chronicle', array($this, 'ajax_sync_chronicle'));
        add_action('wp_ajax_swarm_get_chronicle_data', array($this, 'ajax_get_chronicle_data'));
        add_action('wp_ajax_nopriv_swarm_get_chronicle_data', array($this, 'ajax_get_chronicle_data'));
        add_action('wp_ajax_swarm_get_dashboard_stats', array($this, 'ajax_get_dashboard_stats'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'swarm-chronicle',
            SWARM_CHRONICLE_PLUGIN_URL . 'assets/css/swarm-chronicle.css',
            array(),
            SWARM_CHRONICLE_VERSION
        );

        wp_enqueue_script(
            'swarm-chronicle',
            SWARM_CHRONICLE_PLUGIN_URL . 'assets/js/swarm-chronicle.js',
            array('jquery'),
            SWARM_CHRONICLE_VERSION,
            true
        );

        wp_localize_script('swarm-chronicle', 'swarmChronicleAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('swarm_chronicle_nonce')
        ));
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'swarm-chronicle') !== false) {
            wp_enqueue_style(
                'swarm-chronicle-admin',
                SWARM_CHRONICLE_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                SWARM_CHRONICLE_VERSION
            );

            wp_enqueue_script(
                'swarm-chronicle-admin',
                SWARM_CHRONICLE_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                SWARM_CHRONICLE_VERSION,
                true
            );
        }
    }

    // Shortcode handlers
    public function chronicle_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'overview',
            'limit' => 50,
            'agent' => 'all'
        ), $atts);

        return $this->render_chronicle($atts);
    }

    public function missions_shortcode($atts) {
        $atts = shortcode_atts(array(
            'status' => 'all',
            'limit' => 20,
            'agent' => 'all'
        ), $atts);

        return $this->render_missions($atts);
    }

    public function accomplishments_shortcode($atts) {
        $atts = shortcode_atts(array(
            'period' => 'current',
            'limit' => 25,
            'agent' => 'all'
        ), $atts);

        return $this->render_accomplishments($atts);
    }

    public function project_state_shortcode($atts) {
        $atts = shortcode_atts(array(
            'detail' => 'summary',
            'metrics' => 'true'
        ), $atts);

        return $this->render_project_state($atts);
    }

    // AJAX handler for syncing chronicle data
    public function ajax_sync_chronicle() {
        check_ajax_referer('swarm_chronicle_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $result = $this->api->sync_chronicle_data();

        wp_send_json($result);
    }

    // AJAX handler for getting chronicle data
    public function ajax_get_chronicle_data() {
        check_ajax_referer('swarm_chronicle_nonce', 'nonce');

        $type = isset($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : 'chronicle';
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $agent = isset($_REQUEST['agent']) ? sanitize_text_field($_REQUEST['agent']) : 'all';
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

        $atts = array(
            'type' => $type,
            'limit' => $limit,
            'agent' => $agent,
            'page' => $page
        );

        switch ($type) {
            case 'missions':
                $data = $this->api->get_missions($atts);
                break;
            case 'accomplishments':
                $data = $this->api->get_accomplishments($atts);
                break;
            case 'project_state':
                $data = $this->api->get_project_state($atts);
                break;
            default:
                $data = $this->api->get_chronicle_data($atts);
                break;
        }

        wp_send_json_success($data);
    }

    // AJAX handler for getting dashboard stats
    public function ajax_get_dashboard_stats() {
        check_ajax_referer('swarm_chronicle_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $last_sync = get_option('swarm_chronicle_last_sync', 0);
        $api_endpoint = get_option('swarm_chronicle_api_endpoint', '');
        $auto_sync = get_option('swarm_chronicle_auto_sync', false);

        $master_tasks = get_option('swarm_chronicle_master_tasks', array());
        $accomplishments = get_option('swarm_chronicle_accomplishments', array());

        $stats = array(
            'api_connected' => !empty($api_endpoint),
            'auto_sync_enabled' => $auto_sync,
            'last_sync' => $last_sync,
            'has_data' => !empty($master_tasks) || !empty($accomplishments),
            'metrics' => array(
                'total_entries' => count($master_tasks) + count($accomplishments),
                'active_agents' => count(array_unique(array_merge(
                    array_column($master_tasks, 'agent'),
                    array_column($accomplishments, 'agent')
                ))),
                'pending_tasks' => count(array_filter($master_tasks, function($task) {
                    return isset($task['status']) && $task['status'] === 'pending';
                }))
            )
        );

        wp_send_json_success($stats);
    }

    // Render methods
    private function render_chronicle($atts) {
        ob_start();

        $data = $this->api->get_chronicle_data($atts);

        ?>
        <div class="swarm-chronicle-container">
            <div class="chronicle-header">
                <h2>🤖 Swarm Operating Chronicle</h2>
                <div class="chronicle-stats">
                    <span class="stat-item">📊 <?php echo esc_html($data['total_tasks']); ?> Total Missions</span>
                    <span class="stat-item">✅ <?php echo esc_html($data['completed_tasks']); ?> Completed</span>
                    <span class="stat-item">🚀 <?php echo esc_html($data['active_agents']); ?> Active Agents</span>
                </div>
            </div>

            <div class="chronicle-content">
                <?php foreach ($data['entries'] as $entry): ?>
                <div class="chronicle-entry <?php echo esc_attr($entry['type']); ?>">
                    <div class="entry-header">
                        <span class="entry-type"><?php echo esc_html($entry['icon']); ?></span>
                        <span class="entry-agent"><?php echo esc_html($entry['agent']); ?></span>
                        <span class="entry-date"><?php echo esc_html($entry['date']); ?></span>
                    </div>
                    <div class="entry-content">
                        <?php echo wp_kses_post($entry['content']); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($data['has_more']): ?>
            <div class="chronicle-pagination">
                <button class="load-more-btn" data-page="2">Load More Entries</button>
            </div>
            <?php endif; ?>
        </div>
        <?php

        return ob_get_clean();
    }

    private function render_missions($atts) {
        ob_start();

        $missions = $this->api->get_missions($atts);

        ?>
        <div class="swarm-missions-container">
            <h3>🎯 Active Missions</h3>

            <?php foreach ($missions as $mission): ?>
            <div class="mission-item <?php echo esc_attr($mission['status']); ?>">
                <div class="mission-header">
                    <span class="mission-priority <?php echo esc_attr($mission['priority']); ?>">
                        <?php echo esc_html($mission['priority']); ?>
                    </span>
                    <span class="mission-agent"><?php echo esc_html($mission['agent']); ?></span>
                </div>
                <div class="mission-content">
                    <h4><?php echo esc_html($mission['title']); ?></h4>
                    <p><?php echo wp_kses_post($mission['description']); ?></p>
                    <?php if ($mission['progress']): ?>
                    <div class="mission-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo esc_attr($mission['progress']); ?>%"></div>
                        </div>
                        <span class="progress-text"><?php echo esc_html($mission['progress']); ?>% Complete</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php

        return ob_get_clean();
    }

    private function render_accomplishments($atts) {
        ob_start();

        $accomplishments = $this->api->get_accomplishments($atts);

        ?>
        <div class="swarm-accomplishments-container">
            <h3>🏆 Recent Accomplishments</h3>

            <?php foreach ($accomplishments as $accomplishment): ?>
            <div class="accomplishment-item">
                <div class="accomplishment-header">
                    <span class="accomplishment-agent"><?php echo esc_html($accomplishment['agent']); ?></span>
                    <span class="accomplishment-date"><?php echo esc_html($accomplishment['date']); ?></span>
                </div>
                <div class="accomplishment-content">
                    <h4><?php echo esc_html($accomplishment['title']); ?></h4>
                    <p><?php echo wp_kses_post($accomplishment['description']); ?></p>
                    <div class="accomplishment-tags">
                        <?php foreach ($accomplishment['tags'] as $tag): ?>
                        <span class="tag"><?php echo esc_html($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php

        return ob_get_clean();
    }

    private function render_project_state($atts) {
        ob_start();

        $state = $this->api->get_project_state($atts);

        ?>
        <div class="swarm-project-state-container">
            <h3>📊 Project State Overview</h3>

            <div class="project-metrics">
                <div class="metric-item">
                    <span class="metric-label">Total Files:</span>
                    <span class="metric-value"><?php echo esc_html($state['total_files']); ?></span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Lines of Code:</span>
                    <span class="metric-value"><?php echo esc_html($state['total_loc']); ?></span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Active Components:</span>
                    <span class="metric-value"><?php echo esc_html($state['active_components']); ?></span>
                </div>
            </div>

            <div class="project-health">
                <h4>🏥 System Health</h4>
                <div class="health-indicators">
                    <?php foreach ($state['health_indicators'] as $indicator): ?>
                    <div class="health-item <?php echo esc_attr($indicator['status']); ?>">
                        <span class="indicator-label"><?php echo esc_html($indicator['label']); ?>:</span>
                        <span class="indicator-value"><?php echo esc_html($indicator['value']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }
}