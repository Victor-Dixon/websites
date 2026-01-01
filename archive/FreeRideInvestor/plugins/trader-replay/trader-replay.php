<?php
/**
 * Plugin Name: Trader Replay
 * Plugin URI: https://freerideinvestor.com
 * Description: Interactive stock chart replay trainer with journaling. Allows users to replay historical trading sessions, place paper trades, and journal their decisions.
 * Version: 1.0.0
 * Author: FreeRideInvestor
 * Author URI: https://freerideinvestor.com
 * Text Domain: trader-replay
 * License: GPL v2 or later
 * 
 * Note: This plugin requires the FastAPI backend to be running separately.
 * See README.md for setup instructions.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('TRADER_REPLAY_VERSION', '1.0.0');
define('TRADER_REPLAY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRADER_REPLAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRADER_REPLAY_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class Trader_Replay_Plugin {
    
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
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_shortcode('trader_replay', array($this, 'render_shortcode'));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Only enqueue on pages with the shortcode
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'trader_replay')) {
            // Enqueue React frontend if built
            $frontend_build = TRADER_REPLAY_PLUGIN_DIR . 'frontend/build';
            if (file_exists($frontend_build)) {
                wp_enqueue_script(
                    'trader-replay-app',
                    TRADER_REPLAY_PLUGIN_URL . 'frontend/build/static/js/main.js',
                    array(),
                    TRADER_REPLAY_VERSION,
                    true
                );
                wp_enqueue_style(
                    'trader-replay-app',
                    TRADER_REPLAY_PLUGIN_URL . 'frontend/build/static/css/main.css',
                    array(),
                    TRADER_REPLAY_VERSION
                );
            }
            
            // Add configuration
            wp_localize_script('trader-replay-app', 'traderReplayConfig', array(
                'apiUrl' => get_option('trader_replay_api_url', 'http://localhost:8000'),
                'nonce' => wp_create_nonce('trader_replay_nonce'),
            ));
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Trader Replay Settings',
            'Trader Replay',
            'manage_options',
            'trader-replay-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (isset($_POST['trader_replay_settings_submit'])) {
            check_admin_referer('trader_replay_settings');
            update_option('trader_replay_api_url', sanitize_text_field($_POST['trader_replay_api_url']));
            echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
        }
        
        $api_url = get_option('trader_replay_api_url', 'http://localhost:8000');
        ?>
        <div class="wrap">
            <h1>Trader Replay Settings</h1>
            <form method="post" action="">
                <?php wp_nonce_field('trader_replay_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="trader_replay_api_url">FastAPI Backend URL</label>
                        </th>
                        <td>
                            <input 
                                type="url" 
                                id="trader_replay_api_url" 
                                name="trader_replay_api_url" 
                                value="<?php echo esc_attr($api_url); ?>" 
                                class="regular-text"
                            />
                            <p class="description">
                                URL where the FastAPI backend is running (default: http://localhost:8000)
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Settings', 'primary', 'trader_replay_settings_submit'); ?>
            </form>
            <div class="card">
                <h2>Setup Instructions</h2>
                <p>This plugin requires the FastAPI backend to be running separately.</p>
                <ol>
                    <li>Navigate to the <code>backend</code> directory</li>
                    <li>Install dependencies: <code>pip install -r requirements.txt</code></li>
                    <li>Run the server: <code>python main.py</code></li>
                    <li>Configure the API URL above to match your backend location</li>
                </ol>
                <p>For frontend development, see the <code>frontend</code> directory and README.md</p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'height' => '600px',
        ), $atts);
        
        ob_start();
        ?>
        <div id="trader-replay-container" style="height: <?php echo esc_attr($atts['height']); ?>; width: 100%;">
            <div style="padding: 20px; text-align: center;">
                <h2>Trading Replay Journal System</h2>
                <p>Loading trader replay interface...</p>
                <p><small>Make sure the FastAPI backend is running and configured in settings.</small></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize plugin
Trader_Replay_Plugin::get_instance();

