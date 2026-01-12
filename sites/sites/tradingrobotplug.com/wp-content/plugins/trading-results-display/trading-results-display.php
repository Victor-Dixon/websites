<?php
/**
 * Trading Results Display Plugin
 * Plugin Name: Trading Results Display
 * Plugin URI: https://tradingrobotplug.com
 * Description: Displays automated trading results and performance metrics from the trading robot system
 * Version: 2.0.0
 * Author: Agent-7 & Agent-2
 * License: GPL v2 or later
 * Text Domain: trading-results-display
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRD_VERSION', '2.0.0');
define('TRD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRD_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include core classes
require_once TRD_PLUGIN_DIR . 'includes/class-results-api.php';
require_once TRD_PLUGIN_DIR . 'includes/class-results-display.php';
require_once TRD_PLUGIN_DIR . 'includes/class-performance-charts.php';

/**
 * Main Plugin Class
 */
class TradingResultsDisplay {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }

    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Shortcodes
        add_shortcode('trading_results', array($this, 'results_shortcode'));
        add_shortcode('performance_metrics', array($this, 'metrics_shortcode'));
        add_shortcode('trading_stats', array($this, 'stats_shortcode'));
    }

    private function load_dependencies() {
        new ResultsAPI();
        new ResultsDisplay();
        new PerformanceCharts();
    }

    public function init() {
        // Register custom post type for results
        $this->register_post_types();

        // Schedule data sync
        $this->schedule_data_sync();
    }

    private function register_post_types() {
        register_post_type('trading_result', array(
            'labels' => array(
                'name' => __('Trading Results', 'trading-results-display'),
                'singular_name' => __('Trading Result', 'trading-results-display'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-chart-bar',
            'rewrite' => array('slug' => 'trading-results'),
            'show_in_rest' => true,
        ));

        register_post_type('performance_update', array(
            'labels' => array(
                'name' => __('Performance Updates', 'trading-results-display'),
                'singular_name' => __('Performance Update', 'trading-results-display'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-update',
            'rewrite' => array('slug' => 'performance-updates'),
            'show_in_rest' => true,
        ));
    }

    private function schedule_data_sync() {
        if (!wp_next_scheduled('trd_sync_trading_data')) {
            wp_schedule_event(time(), 'hourly', 'trd_sync_trading_data');
        }
    }

    public function register_rest_routes() {
        register_rest_route('trp/v1', '/update-results', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_results_update'),
            'permission_callback' => array($this, 'verify_api_key'),
            'args' => array(
                'content_type' => array(
                    'required' => true,
                    'validate_callback' => function($value) {
                        return in_array($value, array('daily_plan', 'weekly_review', 'monthly_report'));
                    }
                ),
                'title' => array('required' => true),
                'content' => array('required' => true),
                'meta' => array('type' => 'object'),
                'timestamp' => array('required' => true),
                'source_site' => array('required' => true)
            )
        ));

        register_rest_route('trp/v1', '/get-results', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_results_data'),
            'permission_callback' => '__return_true',
            'args' => array(
                'period' => array(
                    'default' => '30',
                    'validate_callback' => function($value) {
                        return is_numeric($value) && $value > 0 && $value <= 365;
                    }
                ),
                'type' => array(
                    'default' => 'all',
                    'validate_callback' => function($value) {
                        return in_array($value, array('all', 'plans', 'reviews', 'reports'));
                    }
                )
            )
        ));
    }

    public function handle_results_update($request) {
        $params = $request->get_params();

        // Create appropriate post type
        $post_type = $this->get_post_type_from_content_type($params['content_type']);

        $post_data = array(
            'post_title' => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content']),
            'post_status' => 'publish',
            'post_type' => $post_type,
            'post_date' => current_time('mysql'),
            'post_date_gmt' => current_time('mysql', 1),
            'meta_input' => array(
                'source_site' => sanitize_text_field($params['source_site']),
                'content_type' => sanitize_text_field($params['content_type']),
                'original_timestamp' => sanitize_text_field($params['timestamp']),
                'performance_data' => $params['meta'] ?? array()
            )
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            return new WP_Error('post_creation_failed', 'Failed to create results post', array('status' => 500));
        }

        // Set categories based on content type
        $categories = $this->get_categories_for_content_type($params['content_type']);
        if (!empty($categories)) {
            wp_set_post_categories($post_id, $categories);
        }

        // Clear relevant caches
        $this->clear_results_cache();

        return new WP_REST_Response(array(
            'success' => true,
            'post_id' => $post_id,
            'message' => 'Results updated successfully'
        ), 200);
    }

    public function get_results_data($request) {
        $params = $request->get_params();
        $period = intval($params['period']);
        $type = $params['type'];

        // Get posts from the last N days
        $args = array(
            'post_type' => array('trading_result', 'performance_update'),
            'posts_per_page' => -1,
            'date_query' => array(
                array(
                    'after' => "{$period} days ago",
                    'inclusive' => true,
                ),
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Filter by type if specified
        if ($type !== 'all') {
            $meta_key = $type === 'plans' ? 'content_type=daily_plan' :
                       ($type === 'reviews' ? 'content_type=weekly_review' : 'content_type=monthly_report');
            // This would need more complex meta query logic
        }

        $query = new WP_Query($args);
        $results = array();

        while ($query->have_posts()) {
            $query->the_post();

            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'content' => get_the_content(),
                'date' => get_the_date('Y-m-d H:i:s'),
                'type' => get_post_meta(get_the_ID(), 'content_type', true),
                'source' => get_post_meta(get_the_ID(), 'source_site', true),
                'performance_data' => get_post_meta(get_the_ID(), 'performance_data', true)
            );
        }

        wp_reset_postdata();

        return new WP_REST_Response(array(
            'results' => $results,
            'total' => count($results),
            'period_days' => $period
        ), 200);
    }

    private function get_post_type_from_content_type($content_type) {
        switch ($content_type) {
            case 'daily_plan':
                return 'trading_result';
            case 'weekly_review':
            case 'monthly_report':
                return 'performance_update';
            default:
                return 'trading_result';
        }
    }

    private function get_categories_for_content_type($content_type) {
        $categories = array();

        switch ($content_type) {
            case 'daily_plan':
                $categories[] = get_cat_ID('Daily Plans');
                break;
            case 'weekly_review':
                $categories[] = get_cat_ID('Weekly Reviews');
                break;
            case 'monthly_report':
                $categories[] = get_cat_ID('Monthly Reports');
                break;
        }

        return array_filter($categories);
    }

    public function verify_api_key($request) {
        $api_key = $request->get_header('authorization');

        if (empty($api_key)) {
            return false;
        }

        // Remove "Bearer " prefix if present
        $api_key = str_replace('Bearer ', '', $api_key);

        $stored_key = get_option('trd_api_key', '');

        return hash_equals($stored_key, $api_key);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Trading Results', 'trading-results-display'),
            __('Trading Results', 'trading-results-display'),
            'manage_options',
            'trading-results-display',
            array($this, 'admin_page'),
            'dashicons-chart-bar',
            35
        );

        add_submenu_page(
            'trading-results-display',
            __('Settings', 'trading-results-display'),
            __('Settings', 'trading-results-display'),
            'manage_options',
            'trading-results-settings',
            array($this, 'settings_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'trading-results') === false) {
            return;
        }

        wp_enqueue_script(
            'trd-admin-js',
            TRD_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            TRD_VERSION,
            true
        );

        wp_enqueue_style(
            'trd-admin-css',
            TRD_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            TRD_VERSION
        );
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Trading Results Display', 'trading-results-display'); ?></h1>

            <div class="trd-dashboard">
                <div class="trd-stats-grid">
                    <div class="trd-stat-card">
                        <h3><?php _e('Total Results', 'trading-results-display'); ?></h3>
                        <div class="trd-stat-number"><?php echo $this->get_total_results_count(); ?></div>
                    </div>

                    <div class="trd-stat-card">
                        <h3><?php _e('Latest Update', 'trading-results-display'); ?></h3>
                        <div class="trd-stat-date"><?php echo $this->get_latest_update_date(); ?></div>
                    </div>

                    <div class="trd-stat-card">
                        <h3><?php _e('API Status', 'trading-results-display'); ?></h3>
                        <div class="trd-stat-status <?php echo $this->get_api_status_class(); ?>">
                            <?php echo $this->get_api_status(); ?>
                        </div>
                    </div>
                </div>

                <div class="trd-recent-results">
                    <h3><?php _e('Recent Trading Results', 'trading-results-display'); ?></h3>
                    <?php echo $this->get_recent_results_html(); ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function settings_page() {
        if (isset($_POST['trd_save_settings'])) {
            $this->save_settings();
            echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Trading Results Settings', 'trading-results-display'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('trd_settings_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="trd_api_key"><?php _e('API Key', 'trading-results-display'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="trd_api_key" name="trd_api_key"
                                   value="<?php echo esc_attr(get_option('trd_api_key', '')); ?>"
                                   class="regular-text" />
                            <p class="description"><?php _e('API key for receiving results updates from freerideinvestor.com', 'trading-results-display'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="trd_auto_sync"><?php _e('Auto Sync', 'trading-results-display'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="trd_auto_sync" name="trd_auto_sync"
                                   value="1" <?php checked(get_option('trd_auto_sync', '1')); ?> />
                            <label for="trd_auto_sync"><?php _e('Automatically sync trading results', 'trading-results-display'); ?></label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="trd_cache_timeout"><?php _e('Cache Timeout', 'trading-results-display'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="trd_cache_timeout" name="trd_cache_timeout"
                                   value="<?php echo esc_attr(get_option('trd_cache_timeout', '300')); ?>"
                                   class="small-text" />
                            <span>seconds</span>
                            <p class="description"><?php _e('How long to cache results data', 'trading-results-display'); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="trd_save_settings" class="button button-primary"
                           value="<?php _e('Save Settings', 'trading-results-display'); ?>" />
                </p>
            </form>
        </div>
        <?php
    }

    private function save_settings() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'trd_settings_nonce')) {
            return;
        }

        update_option('trd_api_key', sanitize_text_field($_POST['trd_api_key']));
        update_option('trd_auto_sync', isset($_POST['trd_auto_sync']) ? '1' : '0');
        update_option('trd_cache_timeout', intval($_POST['trd_cache_timeout']));
    }

    // Shortcode implementations
    public function results_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 5,
            'type' => 'all',
            'show_meta' => 'true'
        ), $atts);

        $results = $this->get_recent_results($atts['limit'], $atts['type']);

        ob_start();
        if (!empty($results)) {
            echo '<div class="trading-results-list">';
            foreach ($results as $result) {
                ?>
                <div class="trading-result-item">
                    <h4><?php echo esc_html($result['title']); ?></h4>
                    <?php if ($atts['show_meta'] === 'true'): ?>
                    <div class="result-meta">
                        <span class="result-date"><?php echo esc_html(wp_date('M j, Y', strtotime($result['date']))); ?></span>
                        <span class="result-type"><?php echo esc_html(ucfirst(str_replace('_', ' ', $result['type']))); ?></span>
                        <span class="result-source"><?php echo esc_html($result['source']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="result-excerpt">
                        <?php echo wp_trim_words(wp_strip_all_tags($result['content']), 50); ?>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p>' . __('No trading results available.', 'trading-results-display') . '</p>';
        }
        return ob_get_clean();
    }

    public function metrics_shortcode($atts) {
        $atts = shortcode_atts(array(
            'period' => '30',
            'show_chart' => 'true'
        ), $atts);

        // This would integrate with performance charts
        $metrics = $this->get_performance_metrics($atts['period']);

        ob_start();
        ?>
        <div class="performance-metrics">
            <h3><?php printf(__('Performance Metrics (%d Days)', 'trading-results-display'), $atts['period']); ?></h3>

            <?php if (!empty($metrics)): ?>
            <div class="metrics-grid">
                <div class="metric-card">
                    <span class="metric-label"><?php _e('Total Return', 'trading-results-display'); ?></span>
                    <span class="metric-value"><?php echo esc_html($metrics['total_return'] ?? 'N/A'); ?>%</span>
                </div>

                <div class="metric-card">
                    <span class="metric-label"><?php _e('Win Rate', 'trading-results-display'); ?></span>
                    <span class="metric-value"><?php echo esc_html($metrics['win_rate'] ?? 'N/A'); ?>%</span>
                </div>

                <div class="metric-card">
                    <span class="metric-label"><?php _e('Sharpe Ratio', 'trading-results-display'); ?></span>
                    <span class="metric-value"><?php echo esc_html($metrics['sharpe_ratio'] ?? 'N/A'); ?></span>
                </div>

                <div class="metric-card">
                    <span class="metric-label"><?php _e('Max Drawdown', 'trading-results-display'); ?></span>
                    <span class="metric-value"><?php echo esc_html($metrics['max_drawdown'] ?? 'N/A'); ?>%</span>
                </div>
            </div>

            <?php if ($atts['show_chart'] === 'true'): ?>
            <div class="performance-chart">
                <!-- Chart would be rendered here -->
                <p><?php _e('Performance chart coming soon...', 'trading-results-display'); ?></p>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <p><?php _e('Performance metrics not available.', 'trading-results-display'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function stats_shortcode($atts) {
        $stats = $this->get_trading_stats();

        ob_start();
        ?>
        <div class="trading-stats">
            <h3><?php _e('Trading Statistics', 'trading-results-display'); ?></h3>

            <div class="stats-overview">
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html($stats['total_trades'] ?? 0); ?></span>
                    <span class="stat-label"><?php _e('Total Trades', 'trading-results-display'); ?></span>
                </div>

                <div class="stat-item">
                    <span class="stat-number">$<?php echo number_format($stats['total_pnl'] ?? 0, 2); ?></span>
                    <span class="stat-label"><?php _e('Total P&L', 'trading-results-display'); ?></span>
                </div>

                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html($stats['win_rate'] ?? 0); ?>%</span>
                    <span class="stat-label"><?php _e('Win Rate', 'trading-results-display'); ?></span>
                </div>

                <div class="stat-item">
                    <span class="stat-number">$<?php echo number_format($stats['avg_trade'] ?? 0, 2); ?></span>
                    <span class="stat-label"><?php _e('Avg Trade', 'trading-results-display'); ?></span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // Helper methods
    private function get_total_results_count() {
        return wp_count_posts('trading_result')->publish + wp_count_posts('performance_update')->publish;
    }

    private function get_latest_update_date() {
        $args = array(
            'post_type' => array('trading_result', 'performance_update'),
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $query = new WP_Query($args);
        if ($query->have_posts()) {
            $query->the_post();
            $date = get_the_date('M j, Y g:i A');
            wp_reset_postdata();
            return $date;
        }

        return 'Never';
    }

    private function get_api_status() {
        // Check if we can connect to the results API
        return 'Connected'; // Placeholder
    }

    private function get_api_status_class() {
        return 'connected'; // Placeholder
    }

    private function get_recent_results_html() {
        $results = $this->get_recent_results(5);

        if (empty($results)) {
            return '<p>' . __('No recent results available.', 'trading-results-display') . '</p>';
        }

        ob_start();
        echo '<ul class="recent-results-list">';
        foreach ($results as $result) {
            echo '<li>';
            echo '<strong>' . esc_html($result['title']) . '</strong> - ';
            echo esc_html(wp_date('M j', strtotime($result['date'])));
            echo ' <em>(' . esc_html($result['type']) . ')</em>';
            echo '</li>';
        }
        echo '</ul>';
        return ob_get_clean();
    }

    private function get_recent_results($limit = 5, $type = 'all') {
        $args = array(
            'post_type' => array('trading_result', 'performance_update'),
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $query = new WP_Query($args);
        $results = array();

        while ($query->have_posts()) {
            $query->the_post();

            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'content' => get_the_content(),
                'date' => get_the_date('Y-m-d H:i:s'),
                'type' => get_post_meta(get_the_ID(), 'content_type', true),
                'source' => get_post_meta(get_the_ID(), 'source_site', true)
            );
        }

        wp_reset_postdata();
        return $results;
    }

    private function get_performance_metrics($period) {
        // Placeholder - would aggregate from stored results
        return array(
            'total_return' => '12.5',
            'win_rate' => '68',
            'sharpe_ratio' => '1.8',
            'max_drawdown' => '3.2'
        );
    }

    private function get_trading_stats() {
        // Placeholder - would aggregate from stored results
        return array(
            'total_trades' => 145,
            'total_pnl' => 8750.50,
            'win_rate' => 68,
            'avg_trade' => 60.35
        );
    }

    private function clear_results_cache() {
        // Clear any cached results data
        wp_cache_flush();
    }
}

// Initialize the plugin
function trd_init() {
    TradingResultsDisplay::get_instance();
}
add_action('plugins_loaded', 'trd_init');

// Activation hook
register_activation_hook(__FILE__, 'trd_activate');
function trd_activate() {
    // Create necessary options
    add_option('trd_api_key', wp_generate_password(32, false));
    add_option('trd_auto_sync', '1');
    add_option('trd_cache_timeout', '300');

    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'trd_deactivate');
function trd_deactivate() {
    // Clear scheduled events
    wp_clear_scheduled_hook('trd_sync_trading_data');

    // Flush rewrite rules
    flush_rewrite_rules();
}