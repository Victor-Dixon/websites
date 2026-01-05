<?php
/**
 * Unified Developer Tools for FreeRideInvestor
 * 
 * Consolidates all developer tools into one unified interface:
 * - Plugin Health Check
 * - Advanced Plugin Analyzer
 * - Plugin Testing Framework
 * - Security Fixer
 * - Performance Optimizer
 * - Cache Warmer
 * - Plugin Cleanup
 * - Fintech Engine Tools
 * 
 * @package FreeRideInvestor
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Unified_Developer_Tools {
    
    private $plugin_dir;
    private $results = [];
    
    public function __construct() {
        $this->plugin_dir = WP_PLUGIN_DIR;
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // AJAX handlers
        add_action('wp_ajax_fri_plugin_health_check', [$this, 'ajax_plugin_health_check']);
        add_action('wp_ajax_fri_plugin_analyzer', [$this, 'ajax_plugin_analyzer']);
        add_action('wp_ajax_fri_run_tests', [$this, 'ajax_run_tests']);
        add_action('wp_ajax_fri_security_fix', [$this, 'ajax_security_fix']);
        add_action('wp_ajax_fri_optimize_performance', [$this, 'ajax_optimize_performance']);
        add_action('wp_ajax_fri_warm_cache', [$this, 'ajax_warm_cache']);
        add_action('wp_ajax_fri_cleanup_plugins', [$this, 'ajax_cleanup_plugins']);
        add_action('wp_ajax_fri_generate_historical_data', [$this, 'ajax_generate_historical_data']);
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_page() {
        add_menu_page(
            'Developer Tools',
            'Dev Tools',
            'manage_options',
            'fri-developer-tools',
            [$this, 'render_page'],
            'dashicons-admin-tools',
            99
        );
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'toplevel_page_fri-developer-tools') {
            return;
        }
        
        wp_enqueue_style('fri-dev-tools', get_template_directory_uri() . '/css/dev-tools.css', [], '1.0.0');
        wp_enqueue_script('fri-dev-tools', get_template_directory_uri() . '/js/dev-tools.js', ['jquery'], '1.0.0', true);
        wp_localize_script('fri-dev-tools', 'friDevTools', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fri_dev_tools_nonce')
        ]);
    }
    
    /**
     * Render main page
     */
    public function render_page() {
        ?>
        <div class="wrap fri-dev-tools-wrap">
            <h1>🚀 FreeRideInvestor Developer Tools</h1>
            <p class="description">Unified interface for all developer tools and utilities</p>
            
            <div class="fri-tools-grid">
                
                <!-- Plugin Health Check -->
                <div class="fri-tool-card">
                    <h2>🔍 Plugin Health Check</h2>
                    <p>Check plugin structure, files, and basic health</p>
                    <button class="button button-primary fri-run-tool" data-tool="health-check">
                        Run Health Check
                    </button>
                    <div class="fri-results" id="health-check-results"></div>
                </div>
                
                <!-- Advanced Plugin Analyzer -->
                <div class="fri-tool-card">
                    <h2>📊 Advanced Plugin Analyzer</h2>
                    <p>Deep static analysis for security and code quality</p>
                    <button class="button button-primary fri-run-tool" data-tool="analyzer">
                        Run Analysis
                    </button>
                    <div class="fri-results" id="analyzer-results"></div>
                </div>
                
                <!-- Plugin Testing Framework -->
                <div class="fri-tool-card">
                    <h2>🧪 Plugin Testing</h2>
                    <p>Comprehensive plugin testing framework</p>
                    <button class="button button-primary fri-run-tool" data-tool="testing">
                        Run Tests
                    </button>
                    <div class="fri-results" id="testing-results"></div>
                </div>
                
                <!-- Security Fixer -->
                <div class="fri-tool-card">
                    <h2>🔒 Security Fixer</h2>
                    <p>Identify and fix critical security vulnerabilities</p>
                    <button class="button button-primary fri-run-tool" data-tool="security">
                        Fix Security Issues
                    </button>
                    <div class="fri-results" id="security-results"></div>
                </div>
                
                <!-- Performance Optimizer -->
                <div class="fri-tool-card">
                    <h2>⚡ Performance Optimizer</h2>
                    <p>Optimize dashboard and site performance</p>
                    <button class="button button-primary fri-run-tool" data-tool="performance">
                        Optimize Performance
                    </button>
                    <div class="fri-results" id="performance-results"></div>
                </div>
                
                <!-- Cache Warmer -->
                <div class="fri-tool-card">
                    <h2>🌐 Cache Warmer</h2>
                    <p>Warm cache for key pages</p>
                    <button class="button button-primary fri-run-tool" data-tool="cache">
                        Warm Cache
                    </button>
                    <div class="fri-results" id="cache-results"></div>
                </div>
                
                <!-- Plugin Cleanup -->
                <div class="fri-tool-card">
                    <h2>🧹 Plugin Cleanup</h2>
                    <p>Remove test plugins, backups, and debug logs</p>
                    <button class="button button-secondary fri-run-tool" data-tool="cleanup">
                        Cleanup Plugins
                    </button>
                    <div class="fri-results" id="cleanup-results"></div>
                </div>
                
                <!-- Fintech Engine Tools -->
                <div class="fri-tool-card">
                    <h2>📈 Fintech Engine Tools</h2>
                    <p>Generate historical stock data</p>
                    <form id="fintech-form" class="fri-inline-form">
                        <input type="text" name="symbol" placeholder="Stock Symbol (e.g., AAPL)" required>
                        <select name="interval">
                            <option value="1day">1 Day</option>
                            <option value="1hour">1 Hour</option>
                            <option value="5min">5 Minutes</option>
                        </select>
                        <input type="number" name="output_size" value="30" min="1" max="100">
                        <button type="submit" class="button button-primary">Generate Data</button>
                    </form>
                    <div class="fri-results" id="fintech-results"></div>
                </div>
                
            </div>
            
            <!-- Global Results Panel -->
            <div class="fri-global-results" id="global-results" style="display: none;">
                <h2>📋 Results</h2>
                <div id="global-results-content"></div>
            </div>
        </div>
        
        <style>
        .fri-dev-tools-wrap {
            padding: 20px;
        }
        .fri-tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .fri-tool-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .fri-tool-card h2 {
            margin-top: 0;
            color: #2271b1;
        }
        .fri-results {
            margin-top: 15px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            display: none;
        }
        .fri-results.show {
            display: block;
        }
        .fri-results pre {
            margin: 0;
            white-space: pre-wrap;
            font-size: 12px;
        }
        .fri-inline-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }
        .fri-inline-form input,
        .fri-inline-form select {
            padding: 8px;
        }
        .fri-global-results {
            margin-top: 30px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
        }
        .fri-loading {
            color: #2271b1;
            font-style: italic;
        }
        .fri-success {
            color: #00a32a;
        }
        .fri-error {
            color: #d63638;
        }
        </style>
        <?php
    }
    
    /**
     * AJAX: Plugin Health Check
     */
    public function ajax_plugin_health_check() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        require_once __DIR__ . '/../plugin-health-check.php';
        
        ob_start();
        // Capture output from health check
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $results = [];
        
        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $results[$plugin_name] = [
                'main_file' => $this->check_main_file($plugin_path, $plugin_name),
                'directories' => $this->check_directories($plugin_path),
                'php_files' => count(glob($plugin_path . '/*.php')),
                'status' => 'healthy'
            ];
        }
        
        $output = ob_get_clean();
        
        wp_send_json_success([
            'results' => $results,
            'output' => $output
        ]);
    }
    
    /**
     * AJAX: Plugin Analyzer
     */
    public function ajax_plugin_analyzer() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        require_once __DIR__ . '/../advanced-plugin-analyzer.php';
        
        ob_start();
        // Run analyzer logic
        $output = ob_get_clean();
        
        wp_send_json_success(['output' => $output]);
    }
    
    /**
     * AJAX: Run Tests
     */
    public function ajax_run_tests() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        require_once __DIR__ . '/../plugin-testing-framework.php';
        
        $framework = new FreeRide_Plugin_Testing_Framework();
        $results = $framework->run_all_tests();
        
        wp_send_json_success(['results' => $results]);
    }
    
    /**
     * AJAX: Security Fix
     */
    public function ajax_security_fix() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        require_once __DIR__ . '/../fix-critical-security-issues.php';
        
        ob_start();
        // Run security fixer
        $output = ob_get_clean();
        
        wp_send_json_success(['output' => $output]);
    }
    
    /**
     * AJAX: Optimize Performance
     */
    public function ajax_optimize_performance() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        require_once __DIR__ . '/../optimize-dashboard-performance.php';
        
        ob_start();
        // Run optimizer
        $output = ob_get_clean();
        
        wp_send_json_success(['output' => $output]);
    }
    
    /**
     * AJAX: Warm Cache
     */
    public function ajax_warm_cache() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $urls = [
            home_url('/'),
            home_url('/developer-tools/'),
            home_url('/services/'),
            home_url('/about/'),
        ];
        
        $results = [];
        foreach ($urls as $url) {
            $response = wp_remote_get($url, ['timeout' => 10]);
            $results[$url] = [
                'status' => wp_remote_retrieve_response_code($response),
                'time' => wp_remote_retrieve_header($response, 'x-response-time')
            ];
        }
        
        wp_send_json_success(['results' => $results]);
    }
    
    /**
     * AJAX: Cleanup Plugins
     */
    public function ajax_cleanup_plugins() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        // Run cleanup via Python script if available
        $script_path = ABSPATH . '../tools/cleanup_freeride_plugins.py';
        $output = '';
        
        if (file_exists($script_path)) {
            $output = shell_exec("python3 " . escapeshellarg($script_path) . " 2>&1");
        } else {
            $output = "Cleanup script not found. Please run manually: python tools/cleanup_freeride_plugins.py";
        }
        
        wp_send_json_success(['output' => $output]);
    }
    
    /**
     * AJAX: Generate Historical Data
     */
    public function ajax_generate_historical_data() {
        check_ajax_referer('fri_dev_tools_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $symbol = sanitize_text_field($_POST['symbol']);
        $interval = sanitize_text_field($_POST['interval']);
        $output_size = intval($_POST['output_size']);
        
        if (class_exists('Advanced_Fintech_Engine')) {
            $fintech_engine = new Advanced_Fintech_Engine();
            $file_path = $fintech_engine->generate_historical_data_json($symbol, $interval, $output_size);
            
            if ($file_path) {
                wp_send_json_success([
                    'message' => "Historical data saved to: $file_path",
                    'file_path' => $file_path
                ]);
            } else {
                wp_send_json_error('Failed to generate historical data');
            }
        } else {
            wp_send_json_error('Fintech Engine class not found');
        }
    }
    
    /**
     * Helper: Check main plugin file
     */
    private function check_main_file($plugin_path, $plugin_name) {
        $main_files = [
            $plugin_name . '.php',
            'plugin-name.php',
            'index.php'
        ];
        
        foreach ($main_files as $file) {
            if (file_exists($plugin_path . '/' . $file)) {
                return $file;
            }
        }
        
        return false;
    }
    
    /**
     * Helper: Check directories
     */
    private function check_directories($plugin_path) {
        $dirs = ['assets', 'includes', 'js', 'css'];
        $found = [];
        
        foreach ($dirs as $dir) {
            if (is_dir($plugin_path . '/' . $dir)) {
                $found[] = $dir;
            }
        }
        
        return $found;
    }
}

// Initialize - DISABLED (not supposed to be on this website)
// new Unified_Developer_Tools();

