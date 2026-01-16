<?php
/**
 * Performance Validation Plugin
 * Tracks user engagement, validates strategy performance, and provides analytics
 * Version: 1.0.0
 * Author: Agent-7
 */

if (!defined('ABSPATH')) {
    exit;
}

class PerformanceValidation {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_track_user_action', array($this, 'ajax_track_user_action'));
        add_action('wp_ajax_get_performance_metrics', array($this, 'ajax_get_performance_metrics'));
        add_shortcode('performance_dashboard', array($this, 'render_dashboard'));
        add_shortcode('user_testing_panel', array($this, 'render_testing_panel'));

        // Hook into strategy deployment for tracking
        add_action('strategy_deployed', array($this, 'track_strategy_deployment'), 10, 2);
    }

    public function init() {
        $this->create_tables();
        $this->schedule_cleanup();
    }

    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // User actions table
        $table_name = $wpdb->prefix . 'performance_user_actions';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            session_id varchar(64) NOT NULL,
            action_type varchar(50) NOT NULL,
            action_data longtext,
            page_url varchar(500),
            user_agent text,
            ip_address varchar(45),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_session (user_id, session_id),
            KEY action_type (action_type),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Performance metrics table
        $metrics_table = $wpdb->prefix . 'performance_metrics';
        $metrics_sql = "CREATE TABLE $metrics_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            metric_type varchar(50) NOT NULL,
            metric_key varchar(100) NOT NULL,
            metric_value decimal(10,4),
            metadata longtext,
            recorded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY metric_type_key (metric_type, metric_key),
            KEY recorded_at (recorded_at)
        ) $charset_collate;";

        // A/B testing table
        $ab_table = $wpdb->prefix . 'performance_ab_tests';
        $ab_sql = "CREATE TABLE $ab_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            test_name varchar(100) NOT NULL,
            test_group varchar(50) NOT NULL,
            user_id bigint(20) unsigned,
            session_id varchar(64),
            conversion_type varchar(50),
            converted tinyint(1) DEFAULT 0,
            conversion_value decimal(10,2),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            converted_at datetime,
            PRIMARY KEY (id),
            KEY test_group (test_name, test_group),
            KEY user_conversion (user_id, conversion_type)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        dbDelta($metrics_sql);
        dbDelta($ab_sql);
    }

    public function enqueue_scripts() {
        if (!is_admin()) {
            wp_enqueue_script(
                'performance-validation-js',
                plugin_dir_url(__FILE__) . 'assets/js/performance-validation.js',
                array('jquery'),
                '1.0.0',
                true
            );

            wp_localize_script('performance-validation-js', 'performanceValidationAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('performance_validation_nonce'),
                'session_id' => $this->get_session_id(),
                'user_id' => get_current_user_id()
            ));
        }
    }

    private function get_session_id() {
        if (!session_id()) {
            session_start();
        }
        if (!isset($_SESSION['performance_session_id'])) {
            $_SESSION['performance_session_id'] = uniqid('perf_', true);
        }
        return $_SESSION['performance_session_id'];
    }

    public function ajax_track_user_action() {
        check_ajax_referer('performance_validation_nonce', 'nonce');

        $action_type = sanitize_text_field($_POST['action_type']);
        $action_data = wp_json_encode($_POST['action_data']);
        $page_url = sanitize_text_field($_POST['page_url']);
        $session_id = sanitize_text_field($_POST['session_id']);
        $user_id = intval($_POST['user_id']);

        // If no user_id, try to get from session
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // If still no user_id, create anonymous user tracking
        if (!$user_id) {
            $user_id = 0; // Anonymous user
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'performance_user_actions';

        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'session_id' => $session_id,
                'action_type' => $action_type,
                'action_data' => $action_data,
                'page_url' => $page_url,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip_address' => $this->get_client_ip()
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result) {
            // Update performance metrics
            $this->update_metrics($action_type, $action_data);

            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to track action');
        }
    }

    private function update_metrics($action_type, $action_data) {
        global $wpdb;
        $metrics_table = $wpdb->prefix . 'performance_metrics';

        // Decode action data if it's JSON
        $data = json_decode($action_data, true);
        if (!$data) {
            $data = array('value' => 1);
        }

        // Insert metric record
        $wpdb->insert(
            $metrics_table,
            array(
                'metric_type' => $action_type,
                'metric_key' => isset($data['key']) ? $data['key'] : 'default',
                'metric_value' => isset($data['value']) ? floatval($data['value']) : 1,
                'metadata' => wp_json_encode($data)
            ),
            array('%s', '%s', '%f', '%s')
        );
    }

    public function ajax_get_performance_metrics() {
        check_ajax_referer('performance_validation_nonce', 'nonce');

        $timeframe = isset($_POST['timeframe']) ? sanitize_text_field($_POST['timeframe']) : '7 days';
        $metric_types = isset($_POST['metric_types']) ? $_POST['metric_types'] : array();

        $metrics = $this->get_metrics_data($timeframe, $metric_types);

        wp_send_json_success($metrics);
    }

    private function get_metrics_data($timeframe, $metric_types) {
        global $wpdb;
        $metrics_table = $wpdb->prefix . 'performance_metrics';

        $where_clause = "WHERE recorded_at >= DATE_SUB(NOW(), INTERVAL $timeframe)";

        if (!empty($metric_types)) {
            $types_placeholder = str_repeat('%s,', count($metric_types) - 1) . '%s';
            $where_clause .= $wpdb->prepare(" AND metric_type IN ($types_placeholder)", $metric_types);
        }

        $query = "SELECT
            metric_type,
            metric_key,
            AVG(metric_value) as avg_value,
            SUM(metric_value) as total_value,
            COUNT(*) as count,
            MAX(recorded_at) as latest_record
        FROM $metrics_table
        $where_clause
        GROUP BY metric_type, metric_key
        ORDER BY metric_type, total_value DESC";

        $results = $wpdb->get_results($query, ARRAY_A);

        return array(
            'metrics' => $results,
            'timeframe' => $timeframe,
            'total_records' => count($results)
        );
    }

    public function render_dashboard($atts) {
        if (!current_user_can('manage_options')) {
            return '<p>You do not have permission to view this dashboard.</p>';
        }

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/dashboard.php';
        return ob_get_clean();
    }

    public function render_testing_panel($atts) {
        $test_groups = isset($atts['groups']) ? explode(',', $atts['groups']) : array('control', 'variant_a');

        // Assign user to test group
        $user_id = get_current_user_id();
        $test_name = isset($atts['test']) ? $atts['test'] : 'default_test';
        $user_group = $this->assign_test_group($user_id, $test_name, $test_groups);

        // Store test assignment
        $this->record_test_assignment($test_name, $user_group, $user_id);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/testing-panel.php';
        return ob_get_clean();
    }

    private function assign_test_group($user_id, $test_name, $groups) {
        // Use user ID for consistent group assignment
        $hash = crc32($user_id . $test_name);
        $group_index = abs($hash) % count($groups);
        return $groups[$group_index];
    }

    private function record_test_assignment($test_name, $group, $user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'performance_ab_tests';

        $wpdb->insert(
            $table_name,
            array(
                'test_name' => $test_name,
                'test_group' => $group,
                'user_id' => $user_id,
                'session_id' => $this->get_session_id()
            ),
            array('%s', '%s', '%d', '%s')
        );
    }

    public function track_strategy_deployment($strategy_id, $user_id) {
        // Track strategy deployment as a conversion event
        $this->track_conversion('strategy_deployment', $strategy_id, $user_id);

        // Update user engagement metrics
        $this->update_user_engagement($user_id, 'strategy_deployment');
    }

    private function track_conversion($conversion_type, $value, $user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'performance_ab_tests';

        // Update existing test records for this user
        $wpdb->update(
            $table_name,
            array(
                'converted' => 1,
                'conversion_type' => $conversion_type,
                'conversion_value' => $value,
                'converted_at' => current_time('mysql')
            ),
            array('user_id' => $user_id),
            array('%d', '%s', '%f', '%s'),
            array('%d')
        );
    }

    private function update_user_engagement($user_id, $engagement_type) {
        // Update user engagement score
        $current_score = get_user_meta($user_id, 'engagement_score', true);
        $current_score = $current_score ? intval($current_score) : 0;

        $engagement_points = array(
            'page_view' => 1,
            'strategy_view' => 5,
            'strategy_deployment' => 20,
            'waitlist_signup' => 50
        );

        $points = isset($engagement_points[$engagement_type]) ? $engagement_points[$engagement_type] : 1;
        update_user_meta($user_id, 'engagement_score', $current_score + $points);
    }

    private function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    private function schedule_cleanup() {
        if (!wp_next_scheduled('performance_cleanup_old_data')) {
            wp_schedule_event(time(), 'daily', 'performance_cleanup_old_data');
        }

        add_action('performance_cleanup_old_data', array($this, 'cleanup_old_data'));
    }

    public function cleanup_old_data() {
        global $wpdb;

        // Delete data older than 90 days
        $tables = array(
            $wpdb->prefix . 'performance_user_actions',
            $wpdb->prefix . 'performance_metrics'
        );

        foreach ($tables as $table) {
            $wpdb->query($wpdb->prepare(
                "DELETE FROM $table WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)"
            ));
        }

        // Clean up old A/B test data (older than 1 year)
        $ab_table = $wpdb->prefix . 'performance_ab_tests';
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $ab_table WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)"
        ));
    }
}

// Initialize the plugin
new PerformanceValidation();

// Activation hook
register_activation_hook(__FILE__, 'performance_validation_activate');
function performance_validation_activate() {
    // The table creation will happen in the constructor
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'performance_validation_deactivate');
function performance_validation_deactivate() {
    wp_clear_scheduled_hook('performance_cleanup_old_data');
}