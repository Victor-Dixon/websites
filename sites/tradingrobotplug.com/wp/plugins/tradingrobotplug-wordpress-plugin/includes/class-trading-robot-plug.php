<?php
namespace TradingRobotPlug;

/**
 * The core plugin class.
 */
class Trading_Robot_Plug {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'tradingrobotplug';
        $this->version = TRADINGROBOTPLUG_VERSION;

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        // Load API Client
        require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'includes/api-client/class-api-client.php';
        
        // Load Managers
        require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'includes/user-manager/class-user-manager.php';
        require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'includes/performance-tracker/class-performance-tracker.php';
        require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'includes/subscription-manager/class-subscription-manager.php';
        
        // Load Admin
        if (is_admin()) {
            require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'admin/class-admin.php';
        }

        // Load Public (Shortcodes)
        require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'public/class-public.php';
    }

    private function define_admin_hooks() {
        if (is_admin()) {
            $plugin_admin = new Admin($this->plugin_name, $this->version);
            add_action('admin_menu', [$plugin_admin, 'add_plugin_admin_menu']);
            add_action('admin_enqueue_scripts', [$plugin_admin, 'enqueue_styles']);
            add_action('admin_enqueue_scripts', [$plugin_admin, 'enqueue_scripts']);
        }
    }

    private function define_public_hooks() {
        $plugin_public = new Public_Display($this->plugin_name, $this->version);

        add_action('wp_enqueue_scripts', [$plugin_public, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$plugin_public, 'enqueue_scripts']);
        
        // Register Shortcodes
        add_shortcode('trading_robot_pricing', [$plugin_public, 'render_pricing_shortcode']);
        add_shortcode('trading_robot_performance', [$plugin_public, 'render_performance_shortcode']);
        add_shortcode('trading_robot_marketplace', [$plugin_public, 'render_marketplace_shortcode']);
        add_shortcode('trading_robot_dashboard', [$plugin_public, 'render_dashboard_shortcode']);
        
        // Register REST API endpoints
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }
    
    public function register_rest_routes() {
        register_rest_route('tradingrobotplug/v1', '/chart-data', [
            'methods' => 'GET',
            'callback' => [$this, 'get_chart_data'],
            'permission_callback' => '__return_true', // Public endpoint for now
        ]);
    }
    
    public function get_chart_data($request) {
        try {
            $user_id = get_current_user_id();
            $period = $request->get_param('period') ?: 'all_time';
            
            // Get performance data (works for both logged-in and non-logged-in users)
            $performance_tracker = new Performance_Tracker();
            $metrics = $performance_tracker->get_user_performance($user_id, $period);
            
            // Generate mock chart data (30 days of performance)
            $chart_data = [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Cumulative P&L',
                        'data' => [],
                        'borderColor' => '#007bff',
                        'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
                        'fill' => true,
                        'tension' => 0.4
                    ]
                ]
            ];
            
            // Generate 30 days of mock data
            $base_pnl = isset($metrics['total_pnl']) && $metrics['total_pnl'] > 0 ? $metrics['total_pnl'] / 30 : 100;
            $cumulative = 0;
            for ($i = 29; $i >= 0; $i--) {
                $date = date('M j', strtotime("-$i days"));
                $daily_pnl = $base_pnl + (rand(-50, 50));
                $cumulative += $daily_pnl;
                
                $chart_data['labels'][] = $date;
                $chart_data['datasets'][0]['data'][] = round($cumulative, 2);
            }
            
            return rest_ensure_response($chart_data);
        } catch (\Exception $e) {
            return new \WP_Error('chart_data_error', 'Failed to generate chart data: ' . $e->getMessage(), ['status' => 500]);
        }
    }

    public function run() {
        // Hook execution is handled by WordPress actions/filters defined in sub-classes
    }
}
