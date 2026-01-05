<?php
namespace TradingRobotPlug;

class Public_Display {
    private $plugin_name;
    private $version;
    private $performance_tracker;
    private $subscription_manager;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->performance_tracker = new Performance_Tracker();
        $this->subscription_manager = new Subscription_Manager();
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, TRADINGROBOTPLUG_PLUGIN_URL . 'public/css/public.css', [], $this->version, 'all');
    }

    public function enqueue_scripts() {
        // Enqueue Chart.js library
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', [], '3.9.1', true);
        
        wp_enqueue_script($this->plugin_name, TRADINGROBOTPLUG_PLUGIN_URL . 'public/js/public.js', ['jquery', 'chart-js'], $this->version, true);
        
        // Localize script with REST API URL
        wp_localize_script($this->plugin_name, 'tradingRobotPlug', [
            'restUrl' => rest_url('tradingrobotplug/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }

    public function render_pricing_shortcode($atts) {
        $plans = $this->subscription_manager->get_plans();
        
        ob_start();
        include TRADINGROBOTPLUG_PLUGIN_DIR . 'public/templates/pricing-table.php';
        return ob_get_clean();
    }

    public function render_performance_shortcode($atts) {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return '<div class="trp-alert">Please log in to view performance metrics.</div>';
        }

        $metrics = $this->performance_tracker->get_user_performance($user_id);
        
        ob_start();
        include TRADINGROBOTPLUG_PLUGIN_DIR . 'public/templates/performance-dashboard.php';
        return ob_get_clean();
    }

    public function render_marketplace_shortcode($atts) {
        // Mock data
        $robots = [
            ['id' => 1, 'name' => 'Trend Master', 'type' => 'Trend Following', 'win_rate' => 65, 'tier' => 'free'],
            ['id' => 2, 'name' => 'Scalp King', 'type' => 'Scalping', 'win_rate' => 72, 'tier' => 'mid'],
            ['id' => 3, 'name' => 'Swing Sniper', 'type' => 'Swing', 'win_rate' => 68, 'tier' => 'low'],
        ];

        ob_start();
        include TRADINGROBOTPLUG_PLUGIN_DIR . 'public/templates/marketplace-grid.php';
        return ob_get_clean();
    }

    public function render_dashboard_shortcode($atts) {
        if (!is_user_logged_in()) {
            return '<div class="trp-alert">Please <a href="' . wp_login_url() . '">log in</a> to access your dashboard.</div>';
        }

        $user_id = get_current_user_id();
        $subscription = $this->subscription_manager->get_user_subscription($user_id);
        $metrics = $this->performance_tracker->get_user_performance($user_id);

        ob_start();
        include TRADINGROBOTPLUG_PLUGIN_DIR . 'public/templates/user-dashboard.php';
        return ob_get_clean();
    }
}
