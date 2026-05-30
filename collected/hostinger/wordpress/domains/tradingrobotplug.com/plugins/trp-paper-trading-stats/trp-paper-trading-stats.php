<?php
/**
 * Plugin Name: TRP Paper Trading Stats
 * Plugin URI: https://tradingrobotplug.com
 * Description: Displays paper trading bot statistics and performance metrics on TradingRobotPlug website. Ready to switch to live trading stats.
 * Version: 1.0.0
 * Author: Swarm Intelligence System
 * Author URI: https://tradingrobotplug.com
 * License: GPLv2 or later
 * Text Domain: trp-paper-trading-stats
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('TRP_PTS_VERSION', '1.0.0');
define('TRP_PTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRP_PTS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class TRP_Paper_Trading_Stats {
    
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
        // REST API endpoint
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Shortcode
        add_shortcode('trp_trading_stats', array($this, 'render_stats_shortcode'));
        
        // Enqueue scripts/styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('trp/v1', '/paper-trading-stats', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_paper_trading_stats'),
            'permission_callback' => '__return_true', // Public endpoint
        ));
    }
    
    /**
     * Get paper trading stats from Python service
     */
    public function get_paper_trading_stats($request) {
        // Path to Python service script
        // Try multiple possible paths (plugins directory first)
        $possible_paths = array(
            plugin_dir_path(__FILE__) . '../trp-tools/get_paper_trading_stats.py',
            ABSPATH . 'wp-content/plugins/trp-tools/get_paper_trading_stats.py',
            ABSPATH . '../Agent_Cellphone_V2_Repository/tools/get_paper_trading_stats.py',
            dirname(dirname(dirname(__FILE__))) . '/Agent_Cellphone_V2_Repository/tools/get_paper_trading_stats.py',
            '/var/www/html/Agent_Cellphone_V2_Repository/tools/get_paper_trading_stats.py',
            '/home/u996867598/Agent_Cellphone_V2_Repository/tools/get_paper_trading_stats.py',
        );
        
        $python_script = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $python_script = $path;
                break;
            }
        }
        
        if (!$python_script) {
            // Fallback: Try to find it via environment variable or config
            $project_root = get_option('trp_project_root', '');
            if ($project_root) {
                $python_script = $project_root . '/tools/get_paper_trading_stats.py';
                if (!file_exists($python_script)) {
                    $python_script = null;
                }
            }
            
            if (!$python_script) {
                return new WP_Error(
                    'script_not_found',
                    'Paper trading stats service not found. Please configure project root path in plugin settings.',
                    array('status' => 404, 'checked_paths' => $possible_paths)
                );
            }
        }
        
        // Execute Python script to get stats
        $python_cmd = get_option('trp_python_command', 'python');
        $command = escapeshellcmd($python_cmd) . ' ' . escapeshellarg($python_script);
        $output = shell_exec($command . ' 2>&1');
        
        if ($output === null) {
            return new WP_Error(
                'execution_failed',
                'Failed to execute paper trading stats service',
                array('status' => 500)
            );
        }
        
        // Parse JSON output
        $stats = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error(
                'invalid_json',
                'Invalid JSON response from stats service',
                array('status' => 500, 'raw_output' => substr($output, 0, 200))
            );
        }
        
        return rest_ensure_response($stats);
    }
    
    /**
     * Render stats shortcode
     */
    public function render_stats_shortcode($atts) {
        $atts = shortcode_atts(array(
            'mode' => 'full', // full, summary, compact
            'refresh' => '60', // Auto-refresh interval in seconds
        ), $atts);
        
        ob_start();
        ?>
        <div id="trp-trading-stats" class="trp-trading-stats" data-mode="<?php echo esc_attr($atts['mode']); ?>" data-refresh="<?php echo esc_attr($atts['refresh']); ?>">
            <div class="trp-stats-loading">
                <p>Loading trading statistics...</p>
            </div>
            <div class="trp-stats-content" style="display: none;">
                <!-- Stats will be loaded via JavaScript -->
            </div>
            <div class="trp-stats-error" style="display: none;">
                <p>Unable to load trading statistics. Please try again later.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_assets() {
        if (!is_admin()) {
            wp_enqueue_style(
                'trp-paper-trading-stats',
                TRP_PTS_PLUGIN_URL . 'assets/css/stats.css',
                array(),
                TRP_PTS_VERSION
            );
            
            wp_enqueue_script(
                'trp-paper-trading-stats',
                TRP_PTS_PLUGIN_URL . 'assets/js/stats.js',
                array('jquery'),
                TRP_PTS_VERSION,
                true
            );
            
            // Localize script with REST API endpoint
            wp_localize_script('trp-paper-trading-stats', 'trpStats', array(
                'restUrl' => rest_url('trp/v1/paper-trading-stats'),
                'nonce' => wp_create_nonce('wp_rest'),
            ));
        }
    }
}

// Initialize plugin
function trp_paper_trading_stats_init() {
    return TRP_Paper_Trading_Stats::get_instance();
}

// Start plugin
add_action('plugins_loaded', 'trp_paper_trading_stats_init');

