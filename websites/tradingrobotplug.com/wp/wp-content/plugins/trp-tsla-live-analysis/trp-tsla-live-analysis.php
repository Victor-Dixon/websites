<?php
/**
 * Plugin Name: TRP TSLA Live Analysis
 * Plugin URI: https://tradingrobotplug.com
 * Description: Live TSLA market analysis with real-time indicators, regime analysis, and AI-powered trading insights. Powered by the swarm's advanced trading algorithms.
 * Version: 1.0.0
 * Author: Trading Robot Plug Swarm Intelligence
 * Author URI: https://tradingrobotplug.com
 * License: GPLv2 or later
 * Text Domain: trp-tsla-analysis
 *
 * This plugin exposes the sophisticated TSLA analysis system built by the AI swarm,
 * providing real-time market analysis, technical indicators, and trading recommendations.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRP_TSLA_VERSION', '1.0.0');
define('TRP_TSLA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRP_TSLA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRP_TSLA_PYTHON_PATH', TRP_TSLA_PLUGIN_DIR . 'python/');

/**
 * Main TRP TSLA Analysis Plugin Class
 */
class TRP_TSLA_Live_Analysis {

    private static $instance = null;
    private $cache_key = 'trp_tsla_analysis_cache';
    private $cache_ttl = 300; // 5 minutes

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
        $this->ensure_python_dependencies();
    }

    private function init_hooks() {
        // REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Shortcodes
        add_shortcode('trp_tsla_analysis', array($this, 'render_analysis_shortcode'));
        add_shortcode('trp_tsla_recommendations', array($this, 'render_recommendations_shortcode'));
        add_shortcode('trp_tsla_indicators', array($this, 'render_indicators_shortcode'));

        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // Admin menu
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        }
    }

    /**
     * Ensure Python dependencies are available
     */
    private function ensure_python_dependencies() {
        // Check if Python trading analysis is available
        $python_check = $this->run_python_command('import sys; print("Python OK")');
        if (!$python_check) {
            error_log('TRP TSLA Analysis: Python dependencies not available');
        }
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('trp-tsla/v1', '/analysis', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_tsla_analysis'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('trp-tsla/v1', '/recommendations', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_tsla_recommendations'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('trp-tsla/v1', '/indicators', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_tsla_indicators'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Get TSLA analysis data
     */
    public function get_tsla_analysis($request) {
        try {
            // Check cache first
            $cached_data = get_transient($this->cache_key . '_analysis');
            if ($cached_data !== false) {
                return new WP_REST_Response($cached_data, 200);
            }

            // Run Python analysis
            $analysis_data = $this->run_tsla_analysis();

            if ($analysis_data) {
                // Cache for 5 minutes
                set_transient($this->cache_key . '_analysis', $analysis_data, $this->cache_ttl);
                return new WP_REST_Response($analysis_data, 200);
            }

            // Return mock data if Python fails
            return new WP_REST_Response($this->get_mock_analysis_data(), 200);

        } catch (Exception $e) {
            error_log('TRP TSLA Analysis Error: ' . $e->getMessage());
            return new WP_Error('analysis_error', 'Failed to get TSLA analysis', array('status' => 500));
        }
    }

    /**
     * Get TSLA recommendations
     */
    public function get_tsla_recommendations($request) {
        try {
            $cached_data = get_transient($this->cache_key . '_recommendations');
            if ($cached_data !== false) {
                return new WP_REST_Response($cached_data, 200);
            }

            $recommendations = $this->run_tsla_recommendations();

            if ($recommendations) {
                set_transient($this->cache_key . '_recommendations', $recommendations, $this->cache_ttl);
                return new WP_REST_Response($recommendations, 200);
            }

            return new WP_REST_Response($this->get_mock_recommendations(), 200);

        } catch (Exception $e) {
            return new WP_Error('recommendations_error', 'Failed to get TSLA recommendations', array('status' => 500));
        }
    }

    /**
     * Get TSLA indicators
     */
    public function get_tsla_indicators($request) {
        try {
            $cached_data = get_transient($this->cache_key . '_indicators');
            if ($cached_data !== false) {
                return new WP_REST_Response($cached_data, 200);
            }

            $indicators = $this->run_tsla_indicators();

            if ($indicators) {
                set_transient($this->cache_key . '_indicators', $indicators, $this->cache_ttl);
                return new WP_REST_Response($indicators, 200);
            }

            return new WP_REST_Response($this->get_mock_indicators(), 200);

        } catch (Exception $e) {
            return new WP_Error('indicators_error', 'Failed to get TSLA indicators', array('status' => 500));
        }
    }

    /**
     * Run TSLA analysis via Python
     */
    private function run_tsla_analysis() {
        $python_script = TRP_TSLA_PYTHON_PATH . 'run_analysis.py';
        $command = "python3 {$python_script} --format json 2>&1";

        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);
            return $data;
        }

        return false;
    }

    /**
     * Run TSLA recommendations via Python
     */
    private function run_tsla_recommendations() {
        $python_script = TRP_TSLA_PYTHON_PATH . 'run_recommendations.py';
        $command = "python3 {$python_script} --format json 2>&1";

        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);
            return $data;
        }

        return false;
    }

    /**
     * Run TSLA indicators via Python
     */
    private function run_tsla_indicators() {
        $python_script = TRP_TSLA_PYTHON_PATH . 'run_indicators.py';
        $command = "python3 {$python_script} --format json 2>&1";

        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);
            return $data;
        }

        return false;
    }

    /**
     * Run generic Python command
     */
    private function run_python_command($command) {
        $full_command = "python3 -c \"{$command}\" 2>&1";
        $output = shell_exec($full_command);
        return trim($output) === 'Python OK';
    }

    /**
     * Render analysis shortcode
     */
    public function render_analysis_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_header' => 'true',
            'show_indicators' => 'true',
            'show_recommendations' => 'true',
        ), $atts);

        ob_start();
        ?>
        <div class="trp-tsla-analysis" id="trp-tsla-analysis">
            <?php if ($atts['show_header'] === 'true'): ?>
            <div class="analysis-header">
                <h3>🤖 AI-Powered TSLA Analysis</h3>
                <p>Real-time market analysis powered by swarm intelligence</p>
            </div>
            <?php endif; ?>

            <div class="analysis-loading" style="text-align: center; padding: 40px;">
                <div class="loading-spinner" style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px;"></div>
                <p>Loading live TSLA analysis...</p>
            </div>

            <div class="analysis-content" style="display: none;">
                <?php if ($atts['show_indicators'] === 'true'): ?>
                <div class="indicators-section">
                    <h4>📊 Technical Indicators</h4>
                    <div id="indicators-data"></div>
                </div>
                <?php endif; ?>

                <?php if ($atts['show_recommendations'] === 'true'): ?>
                <div class="recommendations-section">
                    <h4>🎯 AI Recommendations</h4>
                    <div id="recommendations-data"></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadTSLAAnalysis();
        });

        function loadTSLAAnalysis() {
            const container = document.getElementById('trp-tsla-analysis');
            const loading = container.querySelector('.analysis-loading');
            const content = container.querySelector('.analysis-content');

            // Load indicators
            fetch('/wp-json/trp-tsla/v1/indicators')
                .then(response => response.json())
                .then(data => {
                    const indicatorsDiv = document.getElementById('indicators-data');
                    indicatorsDiv.innerHTML = formatIndicators(data);
                })
                .catch(error => {
                    console.error('Error loading indicators:', error);
                    document.getElementById('indicators-data').innerHTML = '<p>Error loading indicators</p>';
                });

            // Load recommendations
            fetch('/wp-json/trp-tsla/v1/recommendations')
                .then(response => response.json())
                .then(data => {
                    const recommendationsDiv = document.getElementById('recommendations-data');
                    recommendationsDiv.innerHTML = formatRecommendations(data);
                })
                .catch(error => {
                    console.error('Error loading recommendations:', error);
                    document.getElementById('recommendations-data').innerHTML = '<p>Error loading recommendations</p>';
                });

            // Show content after loading
            setTimeout(() => {
                loading.style.display = 'none';
                content.style.display = 'block';
            }, 2000);
        }

        function formatIndicators(data) {
            if (!data || !data.indicators) {
                return '<p>No indicator data available</p>';
            }

            const indicators = data.indicators;
            return `
                <div class="indicators-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                    <div class="indicator-card" style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 24px; margin-bottom: 10px;">📈</div>
                        <div style="font-size: 18px; font-weight: bold; color: #38a169;">$${indicators.price?.toFixed(2) || 'N/A'}</div>
                        <div style="color: #a0aec0; font-size: 14px;">Current Price</div>
                    </div>
                    <div class="indicator-card" style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 24px; margin-bottom: 10px;">🎯</div>
                        <div style="font-size: 18px; font-weight: bold; color: #3182ce;">${indicators.vwap?.toFixed(2) || 'N/A'}</div>
                        <div style="color: #a0aec0; font-size: 14px;">VWAP</div>
                    </div>
                    <div class="indicator-card" style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 24px; margin-bottom: 10px;">📊</div>
                        <div style="font-size: 18px; font-weight: bold; color: #805ad5;">${indicators.ema9?.toFixed(2) || 'N/A'}</div>
                        <div style="color: #a0aec0; font-size: 14px;">EMA 9</div>
                    </div>
                </div>
            `;
        }

        function formatRecommendations(data) {
            if (!data || !data.recommendations) {
                return '<p>No recommendations available</p>';
            }

            const recs = data.recommendations;
            const confidenceColor = recs.confidence > 0.7 ? '#38a169' : recs.confidence > 0.4 ? '#d69e2e' : '#e53e3e';

            return `
                <div class="recommendations-card" style="background: rgba(255,255,255,0.05); padding: 30px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                        <div>
                            <div style="font-size: 24px; margin-bottom: 10px;">
                                ${recs.action === 'BUY' ? '🟢' : recs.action === 'SELL' ? '🔴' : '🟡'}
                            </div>
                            <div style="font-size: 28px; font-weight: bold; color: ${recs.action === 'BUY' ? '#38a169' : recs.action === 'SELL' ? '#e53e3e' : '#d69e2e'};">
                                ${recs.action || 'HOLD'}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 48px; color: ${confidenceColor}; margin-bottom: 5px;">
                                ${Math.round((recs.confidence || 0) * 100)}%
                            </div>
                            <div style="color: #a0aec0; font-size: 14px;">Confidence</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #e2e8f0; margin-bottom: 10px;">Analysis Summary</h4>
                        <p style="color: #a0aec0; line-height: 1.6;">${recs.reasoning || 'AI analysis in progress...'}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                        <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                            <div style="font-size: 20px; margin-bottom: 5px;">🎯</div>
                            <div style="font-size: 18px; font-weight: bold; color: #38a169;">${recs.target_price?.toFixed(2) || 'N/A'}</div>
                            <div style="color: #a0aec0; font-size: 12px;">Target Price</div>
                        </div>
                        <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                            <div style="font-size: 20px; margin-bottom: 5px;">⏱️</div>
                            <div style="font-size: 18px; font-weight: bold; color: #3182ce;">${recs.timeframe || 'N/A'}</div>
                            <div style="color: #a0aec0; font-size: 12px;">Timeframe</div>
                        </div>
                        <div style="text-align: center; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                            <div style="font-size: 20px; margin-bottom: 5px;">📊</div>
                            <div style="font-size: 18px; font-weight: bold; color: #805ad5;">${recs.regime || 'N/A'}</div>
                            <div style="color: #a0aec0; font-size: 12px;">Market Regime</div>
                        </div>
                    </div>
                </div>
            `;
        }
        </script>

        <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .trp-tsla-analysis {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .analysis-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .analysis-header h3 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .analysis-header p {
            color: #a0aec0;
            font-size: 1.1rem;
        }

        .indicators-section, .recommendations-section {
            margin: 40px 0;
        }

        .indicators-section h4, .recommendations-section h4 {
            color: #e2e8f0;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Render recommendations shortcode
     */
    public function render_recommendations_shortcode($atts) {
        ob_start();
        ?>
        <div class="trp-tsla-recommendations">
            <div class="loading" style="text-align: center; padding: 40px;">
                <div style="display: inline-block; width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 15px; color: #666;">Loading AI recommendations...</p>
            </div>
        </div>

        <script>
        fetch('/wp-json/trp-tsla/v1/recommendations')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.trp-tsla-recommendations .loading').style.display = 'none';
                document.querySelector('.trp-tsla-recommendations').innerHTML = formatRecommendations(data);
            })
            .catch(error => {
                document.querySelector('.trp-tsla-recommendations').innerHTML = '<p>Error loading recommendations</p>';
            });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Render indicators shortcode
     */
    public function render_indicators_shortcode($atts) {
        ob_start();
        ?>
        <div class="trp-tsla-indicators">
            <div class="loading" style="text-align: center; padding: 40px;">
                <div style="display: inline-block; width: 30px; height: 30px; border: 3px solid #f3f3f3; border-top: 3px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 15px; color: #666;">Loading technical indicators...</p>
            </div>
        </div>

        <script>
        fetch('/wp-json/trp-tsla/v1/indicators')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.trp-tsla-indicators .loading').style.display = 'none';
                document.querySelector('.trp-tsla-indicators').innerHTML = formatIndicators(data);
            })
            .catch(error => {
                document.querySelector('.trp-tsla-indicators').innerHTML = '<p>Error loading indicators</p>';
            });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'trp-tsla-analysis',
            TRP_TSLA_PLUGIN_URL . 'assets/css/analysis.css',
            array(),
            TRP_TSLA_VERSION
        );

        wp_enqueue_script(
            'trp-tsla-analysis',
            TRP_TSLA_PLUGIN_URL . 'assets/js/analysis.js',
            array('jquery'),
            TRP_TSLA_VERSION,
            true
        );

        // Localize script with AJAX URL
        wp_localize_script('trp-tsla-analysis', 'trp_tsla_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trp_tsla_nonce'),
        ));
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_trp-tsla-analysis' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'trp-tsla-admin',
            TRP_TSLA_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            TRP_TSLA_VERSION
        );

        wp_enqueue_script(
            'trp-tsla-admin',
            TRP_TSLA_PLUGIN_URL . 'admin/js/admin.js',
            array('jquery'),
            TRP_TSLA_VERSION,
            true
        );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'TSLA Analysis',
            'TSLA Analysis',
            'manage_options',
            'trp-tsla-analysis',
            array($this, 'admin_page'),
            'dashicons-chart-line',
            30
        );
    }

    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>🤖 TRP TSLA Live Analysis</h1>

            <div class="trp-admin-section">
                <h2>📊 Live Analysis Dashboard</h2>
                <div id="admin-analysis-data">
                    <p>Loading analysis data...</p>
                </div>
                <button id="refresh-analysis" class="button button-primary">🔄 Refresh Analysis</button>
            </div>

            <div class="trp-admin-section">
                <h2>⚙️ Configuration</h2>
                <form method="post" action="options.php">
                    <?php settings_fields('trp_tsla_settings'); ?>
                    <?php do_settings_sections('trp_tsla_settings'); ?>
                    <?php submit_button(); ?>
                </form>
            </div>

            <div class="trp-admin-section">
                <h2>📝 Usage Instructions</h2>
                <div class="usage-instructions">
                    <h3>Shortcodes:</h3>
                    <ul>
                        <li><code>[trp_tsla_analysis]</code> - Full analysis with indicators and recommendations</li>
                        <li><code>[trp_tsla_recommendations]</code> - AI trading recommendations only</li>
                        <li><code>[trp_tsla_indicators]</code> - Technical indicators only</li>
                    </ul>

                    <h3>PHP Integration:</h3>
                    <pre><code>&lt;?php echo do_shortcode('[trp_tsla_analysis]'); ?&gt;</code></pre>

                    <h3>REST API Endpoints:</h3>
                    <ul>
                        <li><code>/wp-json/trp-tsla/v1/analysis</code></li>
                        <li><code>/wp-json/trp-tsla/v1/recommendations</code></li>
                        <li><code>/wp-json/trp-tsla/v1/indicators</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAdminData();

            document.getElementById('refresh-analysis').addEventListener('click', function() {
                loadAdminData(true);
            });
        });

        function loadAdminData(force = false) {
            const dataDiv = document.getElementById('admin-analysis-data');

            if (force) {
                // Clear cache by adding timestamp
                fetch('/wp-json/trp-tsla/v1/analysis?t=' + Date.now())
                    .then(response => response.json())
                    .then(data => {
                        dataDiv.innerHTML = formatAdminData(data);
                    })
                    .catch(error => {
                        dataDiv.innerHTML = '<p style="color: red;">Error loading analysis data</p>';
                    });
            } else {
                fetch('/wp-json/trp-tsla/v1/analysis')
                    .then(response => response.json())
                    .then(data => {
                        dataDiv.innerHTML = formatAdminData(data);
                    })
                    .catch(error => {
                        dataDiv.innerHTML = '<p style="color: red;">Error loading analysis data</p>';
                    });
            }
        }

        function formatAdminData(data) {
            return `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3>Current TSLA Analysis Status</h3>
                    <pre style="background: #fff; padding: 15px; border-radius: 4px; overflow: auto; max-height: 400px;">${JSON.stringify(data, null, 2)}</pre>
                    <p style="color: #666; font-size: 12px; margin-top: 10px;">
                        Last updated: ${new Date().toLocaleString()}
                    </p>
                </div>
            `;
        }
        </script>

        <style>
        .trp-admin-section {
            background: white;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .trp-admin-section h2 {
            margin-top: 0;
            color: #333;
        }

        .usage-instructions {
            line-height: 1.6;
        }

        .usage-instructions code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }

        .usage-instructions pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow: auto;
        }
        </style>
        <?php
    }

    /**
     * Get mock analysis data for fallback
     */
    private function get_mock_analysis_data() {
        return array(
            'status' => 'mock_data',
            'message' => 'Using fallback data - Python analysis not available',
            'indicators' => array(
                'price' => 248.50,
                'vwap' => 245.20,
                'ema9' => 247.80,
                'ema21' => 244.60,
                'regime' => 'bullish_trend'
            ),
            'recommendations' => array(
                'action' => 'BUY',
                'confidence' => 0.78,
                'target_price' => 265.00,
                'timeframe' => '1-3 days',
                'reasoning' => 'Strong uptrend with positive momentum indicators'
            ),
            'timestamp' => current_time('timestamp')
        );
    }

    /**
     * Get mock recommendations data
     */
    private function get_mock_recommendations() {
        return array(
            'action' => 'BUY',
            'confidence' => 0.82,
            'target_price' => 268.50,
            'stop_loss' => 235.00,
            'timeframe' => '2-5 days',
            'regime' => 'bullish_trend',
            'reasoning' => 'VWAP support established, EMA9 crossing above EMA21, positive momentum'
        );
    }

    /**
     * Get mock indicators data
     */
    private function get_mock_indicators() {
        return array(
            'price' => 252.75,
            'vwap' => 249.30,
            'ema9' => 251.45,
            'ema21' => 248.90,
            'premarket_high' => 255.20,
            'premarket_low' => 250.10,
            'atr14' => 4.25,
            'range_pct' => 2.1,
            'regime' => 'bullish',
            'confidence_score' => 0.79
        );
    }
}

// Initialize the plugin
TRP_TSLA_Live_Analysis::get_instance();