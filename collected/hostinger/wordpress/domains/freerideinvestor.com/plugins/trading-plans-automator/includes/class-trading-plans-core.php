<?php
/**
 * Trading Plans Core - Main plugin functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class TradingPlansCore {

    private $api_client;
    private $plan_generator;
    private $post_scheduler;

    public function __construct() {
        $this->api_client = new TradingAPIClient();
        $this->plan_generator = new PlanGenerator();
        $this->post_scheduler = new PostScheduler();

        $this->init_hooks();
    }

    private function init_hooks() {
        // Register shortcodes
        add_shortcode('trading_dashboard', array($this, 'trading_dashboard_shortcode'));
        add_shortcode('latest_trading_plan', array($this, 'latest_plan_shortcode'));
        add_shortcode('performance_chart', array($this, 'performance_chart_shortcode'));

        // AJAX endpoints for frontend
        add_action('wp_ajax_nopriv_get_trading_status', array($this, 'ajax_get_trading_status'));
        add_action('wp_ajax_get_trading_status', array($this, 'ajax_get_trading_status'));

        // Admin bar integration
        add_action('admin_bar_menu', array($this, 'add_admin_bar_menu'), 100);

        // Health check endpoint
        add_action('wp_ajax_tpa_health_check', array($this, 'ajax_health_check'));
    }

    /**
     * Trading dashboard shortcode
     */
    public function trading_dashboard_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_balance' => 'true',
            'show_performance' => 'true',
            'show_risk' => 'true',
            'cache_timeout' => 300 // 5 minutes
        ), $atts);

        // Get data with caching
        $cache_key = 'tpa_dashboard_data_' . md5(serialize($atts));
        $data = get_transient($cache_key);

        if (false === $data) {
            $trading_data = $this->api_client->get_trading_data();
            $account_info = $this->api_client->get_account_info();
            $risk_status = $this->api_client->get_risk_status();
            $performance = $this->api_client->get_performance_metrics();

            $data = array(
                'trading_data' => $trading_data,
                'account_info' => $account_info,
                'risk_status' => $risk_status,
                'performance' => $performance,
                'last_updated' => current_time('mysql')
            );

            set_transient($cache_key, $data, $atts['cache_timeout']);
        }

        ob_start();
        ?>
        <div class="trading-dashboard" data-last-updated="<?php echo esc_attr($data['last_updated']); ?>">

            <?php if ($atts['show_balance'] === 'true' && $data['account_info']): ?>
            <div class="dashboard-section account-balance">
                <h3><?php _e('Account Balance', 'trading-plans-automator'); ?></h3>
                <div class="balance-amount">
                    $<?php echo number_format($data['account_info']['balance'] ?? 0, 2); ?>
                </div>
                <div class="balance-details">
                    <span>Buying Power: $<?php echo number_format($data['account_info']['buying_power'] ?? 0, 2); ?></span>
                    <span>Daily P&L: $<?php echo number_format($data['trading_data']['daily_pnl'] ?? 0, 2); ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($atts['show_risk'] === 'true' && $data['risk_status']): ?>
            <div class="dashboard-section risk-status">
                <h3><?php _e('Risk Status', 'trading-plans-automator'); ?></h3>
                <div class="risk-level <?php echo esc_attr(strtolower(str_replace('_', '-', $data['risk_status']['risk_level'] ?? 'unknown'))); ?>">
                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $data['risk_status']['risk_level'] ?? 'unknown'))); ?>
                </div>
                <div class="risk-metrics">
                    <span>Daily Loss Limit: <?php echo esc_html($data['risk_status']['limits']['daily_loss_limit_pct'] ?? 0); ?>%</span>
                    <span>Open Positions: <?php echo esc_html($data['risk_status']['open_positions'] ?? 0); ?></span>
                    <span>Emergency Stop: <?php echo $data['risk_status']['emergency_stop'] ? 'ACTIVE' : 'Inactive'; ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($atts['show_performance'] === 'true' && $data['performance']): ?>
            <div class="dashboard-section performance-metrics">
                <h3><?php _e('Performance Metrics', 'trading-plans-automator'); ?></h3>
                <div class="performance-grid">
                    <div class="metric">
                        <span class="label">Win Rate</span>
                        <span class="value"><?php echo esc_html($data['performance']['win_rate'] ?? 0); ?>%</span>
                    </div>
                    <div class="metric">
                        <span class="label">Total Trades</span>
                        <span class="value"><?php echo esc_html($data['performance']['total_trades'] ?? 0); ?></span>
                    </div>
                    <div class="metric">
                        <span class="label">Sharpe Ratio</span>
                        <span class="value"><?php echo number_format($data['performance']['sharpe_ratio'] ?? 0, 2); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="dashboard-footer">
                <small>Last updated: <?php echo esc_html(wp_date('M j, Y g:i A', strtotime($data['last_updated']))); ?>
                <a href="#" class="refresh-dashboard">Refresh</a></small>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Latest trading plan shortcode
     */
    public function latest_plan_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'daily', // daily, weekly, monthly
            'excerpt_length' => 150
        ), $atts);

        $args = array(
            'post_type' => $atts['type'] === 'daily' ? 'trading_plan' : 'strategy_performance',
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Filter by plan type for performance posts
        if ($atts['type'] !== 'daily') {
            $args['meta_query'] = array(
                array(
                    'key' => $atts['type'] === 'weekly' ? 'review_type' : 'report_type',
                    'value' => $atts['type'],
                    'compare' => '='
                )
            );
        }

        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            return '<p>' . __('No trading plans available.', 'trading-plans-automator') . '</p>';
        }

        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="latest-trading-plan">
                <h3><?php the_title(); ?></h3>
                <div class="plan-meta">
                    <span class="plan-date"><?php echo get_the_date(); ?></span>
                    <span class="plan-type"><?php echo ucfirst($atts['type']); ?> Plan</span>
                </div>
                <div class="plan-excerpt">
                    <?php echo wp_trim_words(get_the_excerpt(), $atts['excerpt_length']); ?>
                </div>
                <a href="<?php the_permalink(); ?>" class="read-more">
                    <?php _e('Read Full Plan', 'trading-plans-automator'); ?>
                </a>
            </div>
            <?php
        }
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Performance chart shortcode
     */
    public function performance_chart_shortcode($atts) {
        $atts = shortcode_atts(array(
            'period' => '30', // days
            'height' => '300',
            'show_pnl' => 'true',
            'show_trades' => 'true'
        ), $atts);

        // Get performance data
        $performance_data = $this->api_client->get_performance_metrics();

        if (!$performance_data) {
            return '<p>' . __('Performance data not available.', 'trading-plans-automator') . '</p>';
        }

        $chart_id = 'performance-chart-' . uniqid();

        ob_start();
        ?>
        <div class="performance-chart-container">
            <canvas id="<?php echo esc_attr($chart_id); ?>"
                    width="400" height="<?php echo esc_attr($atts['height']); ?>"></canvas>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var ctx = document.getElementById('<?php echo esc_attr($chart_id); ?>').getContext('2d');
            var performanceData = <?php echo wp_json_encode($performance_data); ?>;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: performanceData.dates || [],
                    datasets: [{
                        label: 'Portfolio Value',
                        data: performanceData.portfolio_values || [],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler for trading status
     */
    public function ajax_get_trading_status() {
        $trading_data = $this->api_client->get_trading_data();
        $risk_status = $this->api_client->get_risk_status();

        wp_send_json_success(array(
            'trading_status' => $trading_data,
            'risk_status' => $risk_status,
            'timestamp' => current_time('mysql')
        ));
    }

    /**
     * Add admin bar menu
     */
    public function add_admin_bar_menu($wp_admin_bar) {
        if (!current_user_can('manage_options')) {
            return;
        }

        $wp_admin_bar->add_node(array(
            'id' => 'tpa-status',
            'title' => __('Trading Status', 'trading-plans-automator'),
            'href' => admin_url('admin.php?page=trading-plans-automator')
        ));

        $wp_admin_bar->add_node(array(
            'id' => 'tpa-quick-generate',
            'parent' => 'tpa-status',
            'title' => __('Generate Plan Now', 'trading-plans-automator'),
            'href' => '#',
            'meta' => array(
                'onclick' => 'tpaGeneratePlan(); return false;'
            )
        ));
    }

    /**
     * AJAX health check
     */
    public function ajax_health_check() {
        check_ajax_referer('tpa_admin_nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $health = $this->post_scheduler->health_check();

        wp_send_json_success($health);
    }

    /**
     * Get plugin statistics
     */
    public function get_statistics() {
        return array(
            'total_plans' => wp_count_posts('trading_plan')->publish,
            'total_reports' => wp_count_posts('strategy_performance')->publish,
            'api_status' => $this->api_client->test_connection() ? 'connected' : 'disconnected',
            'last_sync' => get_option('tpa_last_sync', 'Never'),
            'next_scheduled' => $this->post_scheduler->get_schedule_info()
        );
    }
}