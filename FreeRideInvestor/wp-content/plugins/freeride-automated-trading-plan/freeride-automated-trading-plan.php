<?php
/**
 * Plugin Name: FreeRide Automated Trading Plan
 * Plugin URI: https://freerideinvestor.com
 * Description: Automates daily trading plans based on TradingView strategies with MA, RSI, and risk management. Generates actionable daily plans for TSLA and other stocks.
 * Version: 1.0.0
 * Author: FreeRideInvestor
 * Author URI: https://freerideinvestor.com
 * Text Domain: freeride-automated-trading-plan
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('FRATP_VERSION', '1.0.0');
define('FRATP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FRATP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FRATP_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class FRATP_Plugin {
    
    /**
     * Instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        // Load dependencies after WordPress is fully loaded
        add_action('plugins_loaded', array($this, 'load_dependencies'), 5);
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Shortcodes
        add_shortcode('fratp_daily_plan', array($this, 'render_daily_plan'));
        add_shortcode('fratp_strategy_status', array($this, 'render_strategy_status'));
        add_shortcode('fratp_premium_signup', array($this, 'render_premium_signup'));
        add_shortcode('fratp_membership_status', array($this, 'render_membership_status'));
        add_shortcode('fratp_plans_list', array($this, 'render_plans_list'));
        
        // AJAX handlers
        add_action('wp_ajax_fratp_generate_plan', array($this, 'ajax_generate_plan'));
        add_action('wp_ajax_fratp_get_latest_plan', array($this, 'ajax_get_latest_plan'));
        add_action('wp_ajax_fratp_upgrade_premium', array($this, 'ajax_upgrade_premium'));
        add_action('wp_ajax_nopriv_fratp_upgrade_premium', array($this, 'ajax_upgrade_premium'));
        
        // Cron job for daily plan generation
        add_action('fratp_daily_plan_generation', array($this, 'generate_daily_plans'));
        
        // Schedule cron on activation (runs daily at market open - 9:30 AM EST)
        if (!wp_next_scheduled('fratp_daily_plan_generation')) {
            // Schedule for 9:30 AM EST (adjust timezone as needed)
            $schedule_time = strtotime('today 9:30 AM');
            if ($schedule_time < time()) {
                $schedule_time = strtotime('tomorrow 9:30 AM');
            }
            wp_schedule_event($schedule_time, 'daily', 'fratp_daily_plan_generation');
        }
    }
    
    /**
     * Load plugin dependencies
     */
    public function load_dependencies() {
        require_once FRATP_PLUGIN_DIR . 'includes/class-fratp-strategy-calculator.php';
        require_once FRATP_PLUGIN_DIR . 'includes/class-fratp-market-data.php';
        require_once FRATP_PLUGIN_DIR . 'includes/class-fratp-plan-generator.php';
        require_once FRATP_PLUGIN_DIR . 'includes/class-fratp-database.php';
        require_once FRATP_PLUGIN_DIR . 'includes/class-fratp-tbow-generator.php';
        require_once FRATP_PLUGIN_DIR . 'includes/class-fratp-membership.php';
        
        // Initialize membership system
        FRATP_Membership::init();
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'fratp-style',
            FRATP_PLUGIN_URL . 'assets/css/style.css',
            array(),
            FRATP_VERSION
        );
        
        wp_enqueue_script(
            'fratp-script',
            FRATP_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            FRATP_VERSION,
            true
        );
        
        wp_localize_script('fratp-script', 'fratp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fratp_nonce'),
        ));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Automated Trading Plan', 'freeride-automated-trading-plan'),
            __('Trading Plans', 'freeride-automated-trading-plan'),
            'manage_options',
            'fratp-settings',
            array($this, 'render_settings_page'),
            'dashicons-chart-line',
            30
        );
        
        add_submenu_page(
            'fratp-settings',
            __('Settings', 'freeride-automated-trading-plan'),
            __('Settings', 'freeride-automated-trading-plan'),
            'manage_options',
            'fratp-settings',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'fratp-settings',
            __('Daily Plans', 'freeride-automated-trading-plan'),
            __('Daily Plans', 'freeride-automated-trading-plan'),
            'read',
            'fratp-daily-plans',
            array($this, 'render_daily_plans_page')
        );
        
        add_submenu_page(
            'fratp-settings',
            __('Preview Templates', 'freeride-automated-trading-plan'),
            __('Preview', 'freeride-automated-trading-plan'),
            'manage_options',
            'fratp-preview',
            array($this, 'render_preview_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Strategy settings
        register_setting('fratp_settings', 'fratp_ma_short_length', array('default' => 50));
        register_setting('fratp_settings', 'fratp_ma_long_length', array('default' => 200));
        register_setting('fratp_settings', 'fratp_rsi_length', array('default' => 14));
        register_setting('fratp_settings', 'fratp_rsi_overbought', array('default' => 60));
        register_setting('fratp_settings', 'fratp_rsi_oversold', array('default' => 40));
        
        // Risk settings
        register_setting('fratp_settings', 'fratp_risk_pct_equity', array('default' => 0.5));
        register_setting('fratp_settings', 'fratp_stop_pct_price', array('default' => 1.0));
        register_setting('fratp_settings', 'fratp_target_pct_price', array('default' => 15.0));
        
        // Trailing stop settings
        register_setting('fratp_settings', 'fratp_use_trailing_stop', array('default' => true));
        register_setting('fratp_settings', 'fratp_trail_offset_pct', array('default' => 0.5));
        register_setting('fratp_settings', 'fratp_trail_trigger_pct', array('default' => 5.0));
        
        // Stock symbols
        register_setting('fratp_settings', 'fratp_stock_symbols', array('default' => 'TSLA'));
        register_setting('fratp_settings', 'fratp_initial_capital', array('default' => 1000000));
        
        // TBOW Integration
        register_setting('fratp_settings', 'fratp_create_tbow_posts', array('default' => true));
        register_setting('fratp_settings', 'fratp_tbow_category', array('default' => 'tbow-tactic'));
        
        // Membership & Sales Funnel
        register_setting('fratp_settings', 'fratp_premium_price', array('default' => '29.99'));
        register_setting('fratp_settings', 'fratp_premium_signup_page');
        register_setting('fratp_settings', 'fratp_login_page');
        register_setting('fratp_settings', 'fratp_premium_features', array('default' => 'Daily trading plans, Real-time signals, Risk management tools, Options strategies'));
        
        // API settings
        register_setting('fratp_settings', 'fratp_alpha_vantage_key');
        register_setting('fratp_settings', 'fratp_finnhub_key');
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        include FRATP_PLUGIN_DIR . 'templates/admin/settings.php';
    }
    
    /**
     * Render daily plans page
     */
    public function render_daily_plans_page() {
        include FRATP_PLUGIN_DIR . 'templates/admin/daily-plans.php';
    }
    
    /**
     * Render preview page
     */
    public function render_preview_page() {
        include FRATP_PLUGIN_DIR . 'templates/frontend/preview-demo.php';
    }
    
    /**
     * Render daily plan shortcode
     */
    public function render_daily_plan($atts) {
        $atts = shortcode_atts(array(
            'symbol' => 'TSLA',
            'date' => 'today',
        ), $atts);
        
        $plan_generator = new FRATP_Plan_Generator();
        $plan = $plan_generator->get_plan($atts['symbol'], $atts['date']);
        
        if (!$plan) {
            return '<p>' . __('No trading plan available for this date.', 'freeride-automated-trading-plan') . '</p>';
        }
        
        // Check access
        $can_view = apply_filters('fratp_can_view_plan', true, $plan);
        if (!$can_view) {
            return $this->render_access_denied($plan);
        }
        
        ob_start();
        include FRATP_PLUGIN_DIR . 'templates/frontend/daily-plan.php';
        return ob_get_clean();
    }
    
    /**
     * Render access denied message
     */
    private function render_access_denied($plan = null) {
        ob_start();
        include FRATP_PLUGIN_DIR . 'templates/frontend/access-denied.php';
        return ob_get_clean();
    }
    
    /**
     * Render premium signup shortcode
     */
    public function render_premium_signup($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Upgrade to Premium', 'freeride-automated-trading-plan'),
            'show_features' => 'true',
        ), $atts);
        
        ob_start();
        include FRATP_PLUGIN_DIR . 'templates/frontend/premium-signup.php';
        return ob_get_clean();
    }
    
    /**
     * Render membership status shortcode
     */
    public function render_membership_status($atts) {
        $status = FRATP_Membership::get_membership_status();
        
        ob_start();
        include FRATP_PLUGIN_DIR . 'templates/frontend/membership-status.php';
        return ob_get_clean();
    }
    
    /**
     * Render plans list shortcode
     */
    public function render_plans_list($atts) {
        $atts = shortcode_atts(array(
            'symbol' => '',
            'limit' => 10,
        ), $atts);
        
        $db = new FRATP_Database();
        $symbols = explode(',', get_option('fratp_stock_symbols', 'TSLA'));
        $symbols = array_map('trim', $symbols);
        
        $plans = array();
        foreach ($symbols as $symbol) {
            if (!empty($atts['symbol']) && $symbol !== $atts['symbol']) {
                continue;
            }
            $plan = $db->get_latest_plan($symbol);
            if ($plan) {
                $plans[] = $plan;
            }
        }
        
        ob_start();
        include FRATP_PLUGIN_DIR . 'templates/frontend/plans-list.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX: Upgrade to premium
     */
    public function ajax_upgrade_premium() {
        check_ajax_referer('fratp_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Please log in first.', 'freeride-automated-trading-plan')));
        }
        
        $user_id = get_current_user_id();
        
        // Here you would integrate with payment gateway
        // For now, we'll just upgrade the user
        // In production, verify payment first
        
        FRATP_Membership::upgrade_to_premium($user_id);
        
        wp_send_json_success(array(
            'message' => __('Successfully upgraded to premium!', 'freeride-automated-trading-plan'),
            'redirect' => home_url('/dashboard'),
        ));
    }
    
    /**
     * Render strategy status shortcode
     */
    public function render_strategy_status($atts) {
        $atts = shortcode_atts(array(
            'symbol' => 'TSLA',
        ), $atts);
        
        $calculator = new FRATP_Strategy_Calculator();
        $status = $calculator->get_current_status($atts['symbol']);
        
        ob_start();
        include FRATP_PLUGIN_DIR . 'templates/frontend/strategy-status.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX: Generate plan
     */
    public function ajax_generate_plan() {
        check_ajax_referer('fratp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'freeride-automated-trading-plan')));
        }
        
        $symbol = isset($_POST['symbol']) ? sanitize_text_field($_POST['symbol']) : 'TSLA';
        $create_tbow = isset($_POST['create_tbow']) ? (bool)$_POST['create_tbow'] : false;
        
        $plan_generator = new FRATP_Plan_Generator();
        $plan = $plan_generator->generate_plan($symbol);
        
        if (is_wp_error($plan)) {
            wp_send_json_error(array('message' => $plan->get_error_message()));
        }
        
        $response = array('plan' => $plan);
        
        // Create TBOW post if requested
        if ($create_tbow) {
            $post_id = $this->create_tbow_post($plan);
            if (!is_wp_error($post_id)) {
                $response['tbow_post_id'] = $post_id;
                $response['tbow_post_url'] = get_permalink($post_id);
            }
        }
        
        wp_send_json_success($response);
    }
    
    /**
     * AJAX: Get latest plan
     */
    public function ajax_get_latest_plan() {
        check_ajax_referer('fratp_nonce', 'nonce');
        
        $symbol = isset($_POST['symbol']) ? sanitize_text_field($_POST['symbol']) : 'TSLA';
        
        $plan_generator = new FRATP_Plan_Generator();
        $plan = $plan_generator->get_latest_plan($symbol);
        
        if (!$plan) {
            wp_send_json_error(array('message' => __('No plan found.', 'freeride-automated-trading-plan')));
        }
        
        wp_send_json_success(array('plan' => $plan));
    }
    
    /**
     * Generate daily plans (cron job)
     */
    public function generate_daily_plans() {
        $symbols = explode(',', get_option('fratp_stock_symbols', 'TSLA'));
        $symbols = array_map('trim', $symbols);
        
        $plan_generator = new FRATP_Plan_Generator();
        $create_tbow = get_option('fratp_create_tbow_posts', false);
        
        foreach ($symbols as $symbol) {
            $plan = $plan_generator->generate_plan($symbol);
            
            // Create TBOW post if enabled
            if ($create_tbow && !is_wp_error($plan)) {
                $this->create_tbow_post($plan);
            }
        }
    }
    
    /**
     * Create TBOW WordPress post from trading plan
     * 
     * @param array $plan Trading plan data
     * @return int|WP_Error Post ID or error
     */
    private function create_tbow_post($plan) {
        $tbow_generator = new FRATP_TBOW_Generator();
        $html_content = $tbow_generator->generate_tbow_html($plan);
        
        $title = $plan['symbol'] . ' TBoW Tactic: Automated Strategy Plan - ' . $plan['date'];
        
        // Check if post already exists
        $existing_posts = get_posts(array(
            'title' => $title,
            'post_type' => 'post',
            'post_status' => 'any',
            'numberposts' => 1,
        ));
        
        $post_data = array(
            'post_title' => $title,
            'post_content' => $html_content,
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_date' => $plan['generated_at'],
        );
        
        // Add category
        $category = get_option('fratp_tbow_category', 'tbow-tactic');
        $category_id = get_cat_ID($category);
        if ($category_id) {
            $post_data['post_category'] = array($category_id);
        }
        
        if (!empty($existing_posts)) {
            // Update existing post
            $post_data['ID'] = $existing_posts[0]->ID;
            $post_id = wp_update_post($post_data);
        } else {
            // Create new post
            $post_id = wp_insert_post($post_data);
        }
        
        if (!is_wp_error($post_id)) {
            // Add meta data
            update_post_meta($post_id, '_fratp_plan_symbol', $plan['symbol']);
            update_post_meta($post_id, '_fratp_plan_date', $plan['date']);
            update_post_meta($post_id, '_fratp_plan_signal', $plan['signal']);
            update_post_meta($post_id, '_fratp_plan_price', $plan['current_price']);
        }
        
        return $post_id;
    }
    
    /**
     * Activation hook
     */
    public function activate() {
        // Create database tables
        FRATP_Database::create_tables();
        
        // Create user roles
        FRATP_Membership::create_user_roles();
        
        // Schedule cron job for daily plan generation
        if (!wp_next_scheduled('fratp_daily_plan_generation')) {
            $schedule_time = strtotime('today 9:30 AM');
            if ($schedule_time < time()) {
                $schedule_time = strtotime('tomorrow 9:30 AM');
            }
            wp_schedule_event($schedule_time, 'daily', 'fratp_daily_plan_generation');
        }
        
        // Set default options
        $this->set_default_options();
        
        // Create default pages if they don't exist
        $this->create_default_pages();
    }
    
    /**
     * Create default pages for sales funnel
     */
    private function create_default_pages() {
        // Premium Signup Page
        $signup_page = get_page_by_path('premium-signup');
        if (!$signup_page) {
            $page_id = wp_insert_post(array(
                'post_title' => 'Premium Signup',
                'post_name' => 'premium-signup',
                'post_content' => '[fratp_premium_signup]',
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
            if ($page_id) {
                update_option('fratp_premium_signup_page', $page_id);
            }
        }
        
        // Plans Dashboard Page
        $plans_page = get_page_by_path('trading-plans');
        if (!$plans_page) {
            wp_insert_post(array(
                'post_title' => 'Daily Trading Plans',
                'post_name' => 'trading-plans',
                'post_content' => '[fratp_plans_list]',
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
        }
    }
    
    /**
     * Deactivation hook
     */
    public function deactivate() {
        // Clear scheduled cron
        $timestamp = wp_next_scheduled('fratp_daily_plan_generation');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'fratp_daily_plan_generation');
        }
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = array(
            'fratp_ma_short_length' => 50,
            'fratp_ma_long_length' => 200,
            'fratp_rsi_length' => 14,
            'fratp_rsi_overbought' => 60,
            'fratp_rsi_oversold' => 40,
            'fratp_risk_pct_equity' => 0.5,
            'fratp_stop_pct_price' => 1.0,
            'fratp_target_pct_price' => 15.0,
            'fratp_use_trailing_stop' => true,
            'fratp_trail_offset_pct' => 0.5,
            'fratp_trail_trigger_pct' => 5.0,
            'fratp_stock_symbols' => 'TSLA',
            'fratp_initial_capital' => 1000000,
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
}

// Initialize plugin after WordPress is loaded
add_action('plugins_loaded', function() {
    FRATP_Plugin::get_instance();
}, 10);

