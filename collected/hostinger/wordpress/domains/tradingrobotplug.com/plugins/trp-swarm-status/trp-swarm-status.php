<?php
/**
 * Plugin Name: TRP Swarm Status
 * Plugin URI: https://tradingrobotplug.com
 * Description: Displays real-time swarm intelligence status - showing what we're building in real-time
 * Version: 1.0.0
 * Author: Swarm Intelligence System
 * Author URI: https://tradingrobotplug.com
 * License: GPLv2 or later
 * Text Domain: trp-swarm-status
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('TRP_SWARM_VERSION', '1.0.0');
define('TRP_SWARM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRP_SWARM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class TRP_Swarm_Status {
    
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
        add_shortcode('trp_swarm_status', array($this, 'render_swarm_status_shortcode'));
        
        // Enqueue scripts/styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('trp/v1', '/swarm-status', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_swarm_status'),
            'permission_callback' => '__return_true', // Public endpoint
        ));
    }
    
    /**
     * Get swarm status from Python service
     */
    public function get_swarm_status($request) {
        // Path to Python service script
        // Try multiple possible paths (plugins directory first)
        $possible_paths = array(
            plugin_dir_path(__FILE__) . '../trp-tools/get_swarm_status.py',
            ABSPATH . 'wp-content/plugins/trp-tools/get_swarm_status.py',
            ABSPATH . '../Agent_Cellphone_V2_Repository/tools/get_swarm_status.py',
            dirname(dirname(dirname(__FILE__))) . '/Agent_Cellphone_V2_Repository/tools/get_swarm_status.py',
            '/var/www/html/Agent_Cellphone_V2_Repository/tools/get_swarm_status.py',
            '/home/u996867598/Agent_Cellphone_V2_Repository/tools/get_swarm_status.py',
        );
        
        $python_script = null;
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $python_script = $path;
                break;
            }
        }
        
        if (!$python_script) {
            $project_root = get_option('trp_project_root', '');
            if ($project_root) {
                $python_script = $project_root . '/tools/get_swarm_status.py';
                if (!file_exists($python_script)) {
                    $python_script = null;
                }
            }
            
            if (!$python_script) {
                return new WP_Error(
                    'script_not_found',
                    'Swarm status service not found. Please configure project root path in plugin settings.',
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
                'Failed to execute swarm status service',
                array('status' => 500)
            );
        }
        
        // Parse JSON output
        $status = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error(
                'invalid_json',
                'Invalid JSON response from status service',
                array('status' => 500, 'raw_output' => substr($output, 0, 200))
            );
        }
        
        return rest_ensure_response($status);
    }
    
    /**
     * Render swarm status shortcode
     */
    public function render_swarm_status_shortcode($atts) {
        $atts = shortcode_atts(array(
            'mode' => 'full', // full, compact, summary
            'refresh' => '30', // Auto-refresh interval in seconds
        ), $atts);
        
        ob_start();
        ?>
        <div id="trp-swarm-status" class="trp-swarm-status" data-mode="<?php echo esc_attr($atts['mode']); ?>" data-refresh="<?php echo esc_attr($atts['refresh']); ?>">
            <div class="trp-swarm-loading">
                <p>Loading swarm status...</p>
            </div>
            <div class="trp-swarm-content" style="display: none;">
                <!-- Swarm status will be loaded via JavaScript -->
            </div>
            <div class="trp-swarm-error" style="display: none;">
                <p>Unable to load swarm status. Please try again later.</p>
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
                'trp-swarm-status',
                TRP_SWARM_PLUGIN_URL . 'assets/css/swarm-status.css',
                array(),
                TRP_SWARM_VERSION
            );
            
            wp_enqueue_script(
                'trp-swarm-status',
                TRP_SWARM_PLUGIN_URL . 'assets/js/swarm-status.js',
                array('jquery'),
                TRP_SWARM_VERSION,
                true
            );
            
            // Localize script with REST API endpoint
            wp_localize_script('trp-swarm-status', 'trpSwarm', array(
                'restUrl' => rest_url('trp/v1/swarm-status'),
                'nonce' => wp_create_nonce('wp_rest'),
            ));
        }
    }
}

// Initialize plugin
function trp_swarm_status_init() {
    return TRP_Swarm_Status::get_instance();
}

// Start plugin
add_action('plugins_loaded', 'trp_swarm_status_init');

