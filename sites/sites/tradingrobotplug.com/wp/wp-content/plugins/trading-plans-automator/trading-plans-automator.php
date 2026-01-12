<?php
/**
 * Plugin Name: Trading Plans Automator
 * Plugin URI: https://freerideinvestor.com
 * Description: Automatically posts trading plans and strategies from the trading robot system
 * Version: 2.0.0
 * Author: Agent-7 & Agent-2
 * License: GPL v2 or later
 * Text Domain: trading-plans-automator
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TPA_VERSION', '2.0.0');
define('TPA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TPA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include core classes
require_once TPA_PLUGIN_DIR . 'includes/class-trading-plans-core.php';
require_once TPA_PLUGIN_DIR . 'includes/class-trading-api-client.php';
require_once TPA_PLUGIN_DIR . 'includes/class-plan-generator.php';
require_once TPA_PLUGIN_DIR . 'includes/class-post-scheduler.php';

/**
 * Main Plugin Class
 */
class TradingPlansAutomator {

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

        // Scheduled posting hooks
        add_action('tpa_daily_trading_plan', array($this, 'generate_daily_plan'));
        add_action('tpa_weekly_strategy_review', array($this, 'generate_weekly_review'));
        add_action('tpa_monthly_performance_report', array($this, 'generate_monthly_report'));

        // AJAX handlers
        add_action('wp_ajax_tpa_generate_plan', array($this, 'ajax_generate_plan'));
        add_action('wp_ajax_tpa_get_trading_data', array($this, 'ajax_get_trading_data'));
    }

    private function load_dependencies() {
        // Load core classes
        new TradingPlansCore();
        new TradingAPIClient();
        new PlanGenerator();
        new PostScheduler();
    }

    public function init() {
        // Register custom post type for trading plans
        $this->register_post_types();

        // Schedule recurring events
        $this->schedule_events();
    }

    private function register_post_types() {
        register_post_type('trading_plan', array(
            'labels' => array(
                'name' => __('Trading Plans', 'trading-plans-automator'),
                'singular_name' => __('Trading Plan', 'trading-plans-automator'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'menu_icon' => 'dashicons-chart-line',
            'rewrite' => array('slug' => 'trading-plans'),
        ));

        register_post_type('strategy_performance', array(
            'labels' => array(
                'name' => __('Strategy Performance', 'trading-plans-automator'),
                'singular_name' => __('Strategy Performance', 'trading-plans-automator'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-performance',
            'rewrite' => array('slug' => 'strategy-performance'),
        ));
    }

    private function schedule_events() {
        if (!wp_next_scheduled('tpa_daily_trading_plan')) {
            wp_schedule_event(strtotime('09:00:00'), 'daily', 'tpa_daily_trading_plan');
        }

        if (!wp_next_scheduled('tpa_weekly_strategy_review')) {
            wp_schedule_event(strtotime('next monday 10:00:00'), 'weekly', 'tpa_weekly_strategy_review');
        }

        if (!wp_next_scheduled('tpa_monthly_performance_report')) {
            wp_schedule_event(strtotime('first day of next month 11:00:00'), 'monthly', 'tpa_monthly_performance_report');
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Trading Plans Automator', 'trading-plans-automator'),
            __('Trading Plans', 'trading-plans-automator'),
            'manage_options',
            'trading-plans-automator',
            array($this, 'admin_page'),
            'dashicons-chart-line',
            30
        );

        add_submenu_page(
            'trading-plans-automator',
            __('Settings', 'trading-plans-automator'),
            __('Settings', 'trading-plans-automator'),
            'manage_options',
            'trading-plans-settings',
            array($this, 'settings_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'trading-plans') === false) {
            return;
        }

        wp_enqueue_script(
            'tpa-admin-js',
            TPA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            TPA_VERSION,
            true
        );

        wp_enqueue_style(
            'tpa-admin-css',
            TPA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            TPA_VERSION
        );
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Trading Plans Automator', 'trading-plans-automator'); ?></h1>

            <div class="tpa-dashboard">
                <div class="tpa-stats-grid">
                    <div class="tpa-stat-card">
                        <h3><?php _e('Daily Plans Posted', 'trading-plans-automator'); ?></h3>
                        <div class="tpa-stat-number"><?php echo $this->get_daily_plans_count(); ?></div>
                    </div>

                    <div class="tpa-stat-card">
                        <h3><?php _e('Active Strategies', 'trading-plans-automator'); ?></h3>
                        <div class="tpa-stat-number"><?php echo $this->get_active_strategies_count(); ?></div>
                    </div>

                    <div class="tpa-stat-card">
                        <h3><?php _e('Performance Score', 'trading-plans-automator'); ?></h3>
                        <div class="tpa-stat-number"><?php echo $this->get_performance_score(); ?>%</div>
                    </div>
                </div>

                <div class="tpa-controls">
                    <button id="tpa-generate-plan" class="button button-primary">
                        <?php _e('Generate Trading Plan Now', 'trading-plans-automator'); ?>
                    </button>

                    <button id="tpa-sync-data" class="button">
                        <?php _e('Sync Trading Data', 'trading-plans-automator'); ?>
                    </button>
                </div>

                <div id="tpa-results"></div>
            </div>
        </div>
        <?php
    }

    public function settings_page() {
        if (isset($_POST['tpa_save_settings'])) {
            $this->save_settings();
            echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Trading Plans Settings', 'trading-plans-automator'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('tpa_settings_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="tpa_api_endpoint"><?php _e('Trading API Endpoint', 'trading-plans-automator'); ?></label>
                        </th>
                        <td>
                            <input type="url" id="tpa_api_endpoint" name="tpa_api_endpoint"
                                   value="<?php echo esc_attr(get_option('tpa_api_endpoint', $this->get_default_api_endpoint())); ?>"
                                   class="regular-text" />
                            <p class="description"><?php _e('URL of the trading robot API', 'trading-plans-automator'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="tpa_api_key"><?php _e('API Key', 'trading-plans-automator'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="tpa_api_key" name="tpa_api_key"
                                   value="<?php echo esc_attr(get_option('tpa_api_key', '')); ?>"
                                   class="regular-text" />
                            <p class="description"><?php _e('API key for trading data access', 'trading-plans-automator'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="tpa_auto_post"><?php _e('Auto-Post Plans', 'trading-plans-automator'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="tpa_auto_post" name="tpa_auto_post"
                                   value="1" <?php checked(get_option('tpa_auto_post', '1')); ?> />
                            <label for="tpa_auto_post"><?php _e('Automatically post trading plans', 'trading-plans-automator'); ?></label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="tpa_risk_level"><?php _e('Risk Level', 'trading-plans-automator'); ?></label>
                        </th>
                        <td>
                            <select id="tpa_risk_level" name="tpa_risk_level">
                                <option value="ultra_conservative" <?php selected(get_option('tpa_risk_level', 'ultra_conservative'), 'ultra_conservative'); ?>>
                                    <?php _e('Ultra Conservative', 'trading-plans-automator'); ?>
                                </option>
                                <option value="conservative" <?php selected(get_option('tpa_risk_level', 'conservative'), 'conservative'); ?>>
                                    <?php _e('Conservative', 'trading-plans-automator'); ?>
                                </option>
                                <option value="moderate" <?php selected(get_option('tpa_risk_level', 'moderate'), 'moderate'); ?>>
                                    <?php _e('Moderate', 'trading-plans-automator'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="tpa_save_settings" class="button button-primary"
                           value="<?php _e('Save Settings', 'trading-plans-automator'); ?>" />
                </p>
            </form>
        </div>
        <?php
    }

    private function save_settings() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'tpa_settings_nonce')) {
            return;
        }

        update_option('tpa_api_endpoint', sanitize_url($_POST['tpa_api_endpoint']));
        update_option('tpa_api_key', sanitize_text_field($_POST['tpa_api_key']));
        update_option('tpa_auto_post', isset($_POST['tpa_auto_post']) ? '1' : '0');
        update_option('tpa_risk_level', sanitize_text_field($_POST['tpa_risk_level']));
    }

    // Core functionality methods
    public function generate_daily_plan() {
        $plan_generator = new PlanGenerator();
        $plan_data = $plan_generator->generate_daily_plan();

        if ($plan_data) {
            $this->create_trading_plan_post($plan_data);
        }
    }

    public function generate_weekly_review() {
        $plan_generator = new PlanGenerator();
        $review_data = $plan_generator->generate_weekly_review();

        if ($review_data) {
            $this->create_strategy_performance_post($review_data);
        }
    }

    public function generate_monthly_report() {
        $plan_generator = new PlanGenerator();
        $report_data = $plan_generator->generate_monthly_report();

        if ($report_data) {
            $this->create_strategy_performance_post($report_data);
        }
    }

    private function create_trading_plan_post($plan_data) {
        $post_data = array(
            'post_title' => $plan_data['title'],
            'post_content' => $plan_data['content'],
            'post_status' => 'publish',
            'post_type' => 'trading_plan',
            'meta_input' => $plan_data['meta']
        );

        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            // Set categories/tags
            if (isset($plan_data['categories'])) {
                wp_set_post_categories($post_id, $plan_data['categories']);
            }

            if (isset($plan_data['tags'])) {
                wp_set_post_tags($post_id, $plan_data['tags']);
            }

            // Log successful posting
            error_log("Trading plan posted: {$plan_data['title']} (ID: {$post_id})");
        }
    }

    private function create_strategy_performance_post($performance_data) {
        $post_data = array(
            'post_title' => $performance_data['title'],
            'post_content' => $performance_data['content'],
            'post_status' => 'publish',
            'post_type' => 'strategy_performance',
            'meta_input' => $performance_data['meta']
        );

        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            error_log("Performance report posted: {$performance_data['title']} (ID: {$post_id})");
        }
    }

    // AJAX handlers
    public function ajax_generate_plan() {
        check_ajax_referer('tpa_admin_nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $plan_generator = new PlanGenerator();
        $plan_data = $plan_generator->generate_daily_plan();

        if ($plan_data) {
            $post_id = $this->create_trading_plan_post($plan_data);
            wp_send_json_success(array(
                'message' => 'Trading plan generated and posted successfully',
                'post_id' => $post_id,
                'title' => $plan_data['title']
            ));
        } else {
            wp_send_json_error('Failed to generate trading plan');
        }
    }

    public function ajax_get_trading_data() {
        check_ajax_referer('tpa_admin_nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $api_client = new TradingAPIClient();
        $trading_data = $api_client->get_trading_data();

        if ($trading_data) {
            wp_send_json_success($trading_data);
        } else {
            wp_send_json_error('Failed to fetch trading data');
        }
    }

    // Statistics methods
    private function get_daily_plans_count() {
        $args = array(
            'post_type' => 'trading_plan',
            'date_query' => array(
                array('after' => 'today'),
            ),
            'posts_per_page' => -1,
        );

        $query = new WP_Query($args);
        return $query->found_posts;
    }

    private function get_active_strategies_count() {
        // Count active trading strategies from API
        $api_client = new TradingAPIClient();
        $strategies = $api_client->get_active_strategies();
        return count($strategies);
    }

    private function get_performance_score() {
        // Calculate performance score from recent trades
        $api_client = new TradingAPIClient();
        $performance = $api_client->get_performance_metrics();
        return isset($performance['win_rate']) ? round($performance['win_rate']) : 0;
    }

    private function get_default_api_endpoint() {
        // Environment-based defaults - NO localhost in production
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false || strpos($host, '.local') !== false) {
                return 'http://localhost:8000'; // Development only
            } elseif (strpos($host, 'staging') !== false || strpos($host, 'dev.') !== false) {
                return 'https://staging-api.freerideinvestor.com'; // Staging API URL
            }
        }
        return 'https://api.freerideinvestor.com'; // Production API URL (safe default)
    }
}

// Initialize the plugin
function tpa_init() {
    TradingPlansAutomator::get_instance();
}
add_action('plugins_loaded', 'tpa_init');

// Activation hook
register_activation_hook(__FILE__, 'tpa_activate');
function tpa_activate() {
    // Create necessary database tables or options
    add_option('tpa_version', TPA_VERSION);

    // Set environment-appropriate API endpoint (NO localhost in production)
    $default_endpoint = 'https://api.freerideinvestor.com'; // Safe production default
    if (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
        if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false || strpos($host, '.local') !== false) {
            $default_endpoint = 'http://localhost:8000'; // Development only
        }
    }
    add_option('tpa_api_endpoint', $default_endpoint);

    add_option('tpa_risk_level', 'ultra_conservative');
    add_option('tpa_auto_post', '1');

    // Flush rewrite rules for custom post types
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'tpa_deactivate');
function tpa_deactivate() {
    // Clear scheduled events
    wp_clear_scheduled_hook('tpa_daily_trading_plan');
    wp_clear_scheduled_hook('tpa_weekly_strategy_review');
    wp_clear_scheduled_hook('tpa_monthly_performance_report');

    // Flush rewrite rules
    flush_rewrite_rules();
}