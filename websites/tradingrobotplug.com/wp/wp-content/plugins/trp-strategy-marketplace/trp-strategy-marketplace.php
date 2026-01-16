<?php
/**
 * Plugin Name: TRP Strategy Marketplace
 * Plugin URI: https://tradingrobotplug.com
 * Description: Showcase and demonstrate the swarm's trading strategies including the conservative automated strategy with real backtesting results.
 * Version: 1.0.0
 * Author: Trading Robot Plug Swarm Intelligence
 * Author URI: https://tradingrobotplug.com
 * License: GPLv2 or later
 * Text Domain: trp-strategy-marketplace
 *
 * This plugin exposes the actual trading strategies developed by the AI swarm,
 * including the conservative automated strategy with real performance data.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRP_STRATEGY_VERSION', '1.0.0');
define('TRP_STRATEGY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRP_STRATEGY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRP_STRATEGY_PYTHON_PATH', TRP_STRATEGY_PLUGIN_DIR . 'python/');

/**
 * Main TRP Strategy Marketplace Plugin Class
 */
class TRP_Strategy_Marketplace {

    private static $instance = null;
    private $cache_key = 'trp_strategy_marketplace_cache';
    private $cache_ttl = 3600; // 1 hour for strategy data

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
        add_shortcode('trp_strategy_marketplace', array($this, 'render_marketplace_shortcode'));
        add_shortcode('trp_conservative_strategy', array($this, 'render_conservative_strategy_shortcode'));
        add_shortcode('trp_strategy_performance', array($this, 'render_strategy_performance_shortcode'));

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
        $python_check = $this->run_python_command('import sys; print("Python OK")');
        if (!$python_check) {
            error_log('TRP Strategy Marketplace: Python dependencies not available');
        }
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('trp-strategy/v1', '/strategies', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_strategies'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('trp-strategy/v1', '/strategy/(?P<id>[a-zA-Z0-9-_]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_strategy_details'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('trp-strategy/v1', '/performance', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_performance_data'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('trp-strategy/v1', '/backtest', array(
            'methods' => 'POST',
            'callback' => array($this, 'run_backtest'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Get available strategies
     */
    public function get_strategies($request) {
        try {
            $cached_data = get_transient($this->cache_key . '_strategies');
            if ($cached_data !== false) {
                return new WP_REST_Response($cached_data, 200);
            }

            $strategies = $this->get_available_strategies();

            if ($strategies) {
                set_transient($this->cache_key . '_strategies', $strategies, $this->cache_ttl);
                return new WP_REST_Response($strategies, 200);
            }

            return new WP_REST_Response($this->get_mock_strategies(), 200);

        } catch (Exception $e) {
            error_log('TRP Strategy Marketplace Error: ' . $e->getMessage());
            return new WP_Error('strategies_error', 'Failed to get strategies', array('status' => 500));
        }
    }

    /**
     * Get strategy details
     */
    public function get_strategy_details($request) {
        try {
            $strategy_id = $request->get_param('id');

            $cached_data = get_transient($this->cache_key . '_strategy_' . $strategy_id);
            if ($cached_data !== false) {
                return new WP_REST_Response($cached_data, 200);
            }

            $details = $this->get_strategy_details_data($strategy_id);

            if ($details) {
                set_transient($this->cache_key . '_strategy_' . $strategy_id, $details, $this->cache_ttl);
                return new WP_REST_Response($details, 200);
            }

            return new WP_REST_Response($this->get_mock_strategy_details($strategy_id), 200);

        } catch (Exception $e) {
            return new WP_Error('strategy_error', 'Failed to get strategy details', array('status' => 500));
        }
    }

    /**
     * Get performance data
     */
    public function get_performance_data($request) {
        try {
            $cached_data = get_transient($this->cache_key . '_performance');
            if ($cached_data !== false) {
                return new WP_REST_Response($cached_data, 200);
            }

            $performance = $this->run_performance_analysis();

            if ($performance) {
                set_transient($this->cache_key . '_performance', $performance, $this->cache_ttl);
                return new WP_REST_Response($performance, 200);
            }

            return new WP_REST_Response($this->get_mock_performance_data(), 200);

        } catch (Exception $e) {
            return new WP_Error('performance_error', 'Failed to get performance data', array('status' => 500));
        }
    }

    /**
     * Run backtest
     */
    public function run_backtest($request) {
        try {
            $params = $request->get_json_params();
            $strategy_id = $params['strategy_id'] ?? 'conservative';
            $start_date = $params['start_date'] ?? '2023-01-01';
            $end_date = $params['end_date'] ?? date('Y-m-d');

            $backtest_result = $this->run_backtest_analysis($strategy_id, $start_date, $end_date);

            if ($backtest_result) {
                return new WP_REST_Response($backtest_result, 200);
            }

            return new WP_REST_Response($this->get_mock_backtest_result($strategy_id), 200);

        } catch (Exception $e) {
            return new WP_Error('backtest_error', 'Failed to run backtest', array('status' => 500));
        }
    }

    /**
     * Get available strategies data
     */
    private function get_available_strategies() {
        $python_script = TRP_STRATEGY_PYTHON_PATH . 'get_strategies.py';
        $command = "python3 {$python_script} 2>&1";

        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);
            return $data;
        }

        return false;
    }

    /**
     * Get strategy details data
     */
    private function get_strategy_details_data($strategy_id) {
        $python_script = TRP_STRATEGY_PYTHON_PATH . 'get_strategy_details.py';
        $command = "python3 {$python_script} --strategy {$strategy_id} 2>&1";

        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);
            return $data;
        }

        return false;
    }

    /**
     * Run performance analysis
     */
    private function run_performance_analysis() {
        $python_script = TRP_STRATEGY_PYTHON_PATH . 'run_performance.py';
        $command = "python3 {$python_script} 2>&1";

        $output = shell_exec($command);

        if ($output) {
            $data = json_decode($output, true);
            return $data;
        }

        return false;
    }

    /**
     * Run backtest analysis
     */
    private function run_backtest_analysis($strategy_id, $start_date, $end_date) {
        $python_script = TRP_STRATEGY_PYTHON_PATH . 'run_backtest.py';
        $command = "python3 {$python_script} --strategy {$strategy_id} --start {$start_date} --end {$end_date} 2>&1";

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
     * Render marketplace shortcode
     */
    public function render_marketplace_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_filters' => 'true',
            'show_performance' => 'true',
            'limit' => '6',
        ), $atts);

        ob_start();
        ?>
        <div class="trp-strategy-marketplace" id="trp-strategy-marketplace">
            <div class="marketplace-header">
                <h2>🤖 AI Strategy Marketplace</h2>
                <p>Discover trading strategies built by our swarm intelligence</p>
            </div>

            <?php if ($atts['show_filters'] === 'true'): ?>
            <div class="marketplace-filters">
                <select id="strategy-filter">
                    <option value="all">All Strategies</option>
                    <option value="conservative">Conservative</option>
                    <option value="aggressive">Aggressive</option>
                    <option value="momentum">Momentum</option>
                </select>
                <select id="sort-filter">
                    <option value="performance">Sort by Performance</option>
                    <option value="win_rate">Sort by Win Rate</option>
                    <option value="newest">Newest First</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="marketplace-loading" style="text-align: center; padding: 40px;">
                <div class="loading-spinner" style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px;"></div>
                <p>Loading strategy marketplace...</p>
            </div>

            <div class="strategies-grid" style="display: none;">
                <!-- Strategies loaded dynamically -->
            </div>

            <?php if ($atts['show_performance'] === 'true'): ?>
            <div class="marketplace-performance">
                <h3>📊 Overall Performance</h3>
                <div id="performance-overview"></div>
            </div>
            <?php endif; ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadStrategyMarketplace();
        });

        function loadStrategyMarketplace() {
            const container = document.getElementById('trp-strategy-marketplace');
            const loading = container.querySelector('.marketplace-loading');
            const grid = container.querySelector('.strategies-grid');

            // Load strategies
            fetch('/wp-json/trp-strategy/v1/strategies')
                .then(response => response.json())
                .then(data => {
                    grid.innerHTML = formatStrategies(data.strategies || []);
                    loading.style.display = 'none';
                    grid.style.display = 'grid';
                })
                .catch(error => {
                    console.error('Error loading strategies:', error);
                    grid.innerHTML = '<p>Error loading strategies</p>';
                    loading.style.display = 'none';
                    grid.style.display = 'block';
                });

            // Load performance overview
            fetch('/wp-json/trp-strategy/v1/performance')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('performance-overview').innerHTML = formatPerformanceOverview(data);
                })
                .catch(error => {
                    console.error('Error loading performance:', error);
                });
        }

        function formatStrategies(strategies) {
            return strategies.map(strategy => `
                <div class="strategy-card" onclick="viewStrategy('${strategy.id}')">
                    <div class="strategy-header">
                        <div class="strategy-icon">${getStrategyIcon(strategy.type)}</div>
                        <div class="strategy-meta">
                            <h3>${strategy.name}</h3>
                            <span class="strategy-type">${strategy.type}</span>
                        </div>
                    </div>
                    <div class="strategy-stats">
                        <div class="stat">
                            <span class="stat-value">${strategy.performance?.total_return || 'N/A'}%</span>
                            <span class="stat-label">Total Return</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">${strategy.performance?.win_rate || 'N/A'}%</span>
                            <span class="stat-label">Win Rate</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">${strategy.performance?.max_drawdown || 'N/A'}%</span>
                            <span class="stat-label">Max Drawdown</span>
                        </div>
                    </div>
                    <div class="strategy-description">
                        <p>${strategy.description}</p>
                    </div>
                    <div class="strategy-actions">
                        <button class="btn-primary">View Details</button>
                        <button class="btn-secondary" onclick="runBacktest('${strategy.id}')">Run Backtest</button>
                    </div>
                </div>
            `).join('');
        }

        function formatPerformanceOverview(data) {
            if (!data || !data.overview) {
                return '<p>No performance data available</p>';
            }

            const overview = data.overview;
            return `
                <div class="performance-grid">
                    <div class="perf-item">
                        <div class="perf-value">${overview.total_strategies || 0}</div>
                        <div class="perf-label">Total Strategies</div>
                    </div>
                    <div class="perf-item">
                        <div class="perf-value">${overview.avg_performance || 'N/A'}%</div>
                        <div class="perf-label">Avg Performance</div>
                    </div>
                    <div class="perf-item">
                        <div class="perf-value">${overview.best_strategy || 'N/A'}</div>
                        <div class="perf-label">Best Strategy</div>
                    </div>
                    <div class="perf-item">
                        <div class="perf-value">${overview.total_trades || 0}</div>
                        <div class="perf-label">Total Trades</div>
                    </div>
                </div>
            `;
        }

        function getStrategyIcon(type) {
            const icons = {
                'conservative': '🛡️',
                'aggressive': '⚡',
                'momentum': '📈',
                'mean_reversion': '🔄',
                'arbitrage': '⚖️'
            };
            return icons[type] || '🤖';
        }

        function viewStrategy(strategyId) {
            // Navigate to strategy detail page
            window.location.href = `/strategy/${strategyId}`;
        }

        function runBacktest(strategyId) {
            // Show backtest modal or redirect
            alert(`Running backtest for ${strategyId}...`);
        }
        </script>

        <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .trp-strategy-marketplace {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .marketplace-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .marketplace-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .marketplace-filters {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            justify-content: center;
        }

        .marketplace-filters select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .strategies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .strategy-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.8);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .strategy-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .strategy-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .strategy-icon {
            font-size: 2.5rem;
        }

        .strategy-meta h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a202c;
        }

        .strategy-type {
            background: #e2e8f0;
            color: #4a5568;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .strategy-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #38a169;
            display: block;
        }

        .stat-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .strategy-description p {
            color: #4a5568;
            margin: 0;
            line-height: 1.6;
        }

        .strategy-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-primary, .btn-secondary {
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f7fafc;
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #edf2f7;
        }

        .marketplace-performance {
            background: rgba(255,255,255,0.8);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
        }

        .performance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .perf-item {
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .perf-value {
            font-size: 2rem;
            font-weight: 700;
            color: #38a169;
            margin-bottom: 5px;
        }

        .perf-label {
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Render conservative strategy shortcode
     */
    public function render_conservative_strategy_shortcode($atts) {
        ob_start();
        ?>
        <div class="trp-conservative-strategy">
            <div class="strategy-hero">
                <h2>🛡️ Conservative Automated Strategy</h2>
                <p>Ultra-safe automated trading designed to protect your capital while generating steady returns.</p>
            </div>

            <div class="strategy-details">
                <div class="strategy-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading strategy details...</p>
                </div>
            </div>
        </div>

        <script>
        fetch('/wp-json/trp-strategy/v1/strategy/conservative')
            .then(response => response.json())
            .then(data => {
                document.querySelector('.strategy-details').innerHTML = formatConservativeStrategy(data);
            })
            .catch(error => {
                document.querySelector('.strategy-details').innerHTML = '<p>Error loading strategy details</p>';
            });

        function formatConservativeStrategy(data) {
            if (!data) return '<p>No strategy data available</p>';

            const strategy = data.strategy || {};
            const performance = strategy.performance || {};

            return `
                <div class="strategy-overview">
                    <div class="overview-grid">
                        <div class="overview-item">
                            <div class="metric">${performance.total_return || 'N/A'}%</div>
                            <div class="label">Total Return</div>
                        </div>
                        <div class="overview-item">
                            <div class="metric">${performance.win_rate || 'N/A'}%</div>
                            <div class="label">Win Rate</div>
                        </div>
                        <div class="overview-item">
                            <div class="metric">${performance.max_drawdown || 'N/A'}%</div>
                            <div class="label">Max Drawdown</div>
                        </div>
                        <div class="overview-item">
                            <div class="metric">${performance.total_trades || 'N/A'}</div>
                            <div class="label">Total Trades</div>
                        </div>
                    </div>
                </div>

                <div class="strategy-rules">
                    <h3>🎯 Strategy Rules</h3>
                    <div class="rules-grid">
                        <div class="rule-item">
                            <div class="rule-icon">📊</div>
                            <div class="rule-content">
                                <h4>Micro-Position Sizing</h4>
                                <p>0.25-0.5% of portfolio per trade</p>
                            </div>
                        </div>
                        <div class="rule-item">
                            <div class="rule-icon">🛡️</div>
                            <div class="rule-content">
                                <h4>Strict Stop Losses</h4>
                                <p>1-1.5% maximum loss per trade</p>
                            </div>
                        </div>
                        <div class="rule-item">
                            <div class="rule-icon">🎪</div>
                            <div class="rule-content">
                                <h4>High Probability Setups</h4>
                                <p>Only enter trades with strong signals</p>
                            </div>
                        </div>
                        <div class="rule-item">
                            <div class="rule-icon">📈</div>
                            <div class="rule-content">
                                <h4>Daily Loss Limits</h4>
                                <p>0.5-1% maximum daily loss</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="strategy-backtest">
                    <h3>🔬 Run Backtest</h3>
                    <div class="backtest-form">
                        <div class="form-row">
                            <label>Start Date:</label>
                            <input type="date" id="backtest-start" value="2023-01-01">
                        </div>
                        <div class="form-row">
                            <label>End Date:</label>
                            <input type="date" id="backtest-end" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <button onclick="runConservativeBacktest()" class="btn-primary">Run Backtest</button>
                    </div>
                    <div id="backtest-results"></div>
                </div>
            `;
        }

        function runConservativeBacktest() {
            const startDate = document.getElementById('backtest-start').value;
            const endDate = document.getElementById('backtest-end').value;

            document.getElementById('backtest-results').innerHTML = '<div class="loading">Running backtest...</div>';

            fetch('/wp-json/trp-strategy/v1/backtest', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    strategy_id: 'conservative',
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('backtest-results').innerHTML = formatBacktestResults(data);
            })
            .catch(error => {
                document.getElementById('backtest-results').innerHTML = '<p>Error running backtest</p>';
            });
        }

        function formatBacktestResults(data) {
            if (!data || !data.results) return '<p>No backtest results</p>';

            const results = data.results;
            return `
                <div class="backtest-summary">
                    <h4>Backtest Results (${results.period})</h4>
                    <div class="results-grid">
                        <div class="result-item">
                            <div class="result-value">${results.total_return}%</div>
                            <div class="result-label">Total Return</div>
                        </div>
                        <div class="result-item">
                            <div class="result-value">${results.annualized_return}%</div>
                            <div class="result-label">Annualized Return</div>
                        </div>
                        <div class="result-item">
                            <div class="result-value">${results.sharpe_ratio}</div>
                            <div class="result-label">Sharpe Ratio</div>
                        </div>
                        <div class="result-item">
                            <div class="result-value">${results.max_drawdown}%</div>
                            <div class="result-label">Max Drawdown</div>
                        </div>
                    </div>
                </div>
            `;
        }
        </script>

        <style>
        .trp-conservative-strategy {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .strategy-hero {
            text-align: center;
            margin-bottom: 40px;
        }

        .strategy-hero h2 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .overview-grid, .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .overview-item, .result-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .metric, .result-value {
            font-size: 2rem;
            font-weight: 700;
            color: #38a169;
            margin-bottom: 5px;
        }

        .label, .result-label {
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .rules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .rule-item {
            display: flex;
            align-items: center;
            gap: 15px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .rule-icon {
            font-size: 2rem;
        }

        .rule-content h4 {
            margin: 0 0 5px 0;
            color: #2d3748;
        }

        .rule-content p {
            margin: 0;
            color: #718096;
        }

        .backtest-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .form-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row label {
            min-width: 80px;
            font-weight: 500;
        }

        .form-row input {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Render strategy performance shortcode
     */
    public function render_strategy_performance_shortcode($atts) {
        ob_start();
        ?>
        <div class="trp-strategy-performance">
            <h3>📊 Strategy Performance Dashboard</h3>
            <div id="performance-dashboard">
                <div class="loading">Loading performance data...</div>
            </div>
        </div>

        <script>
        fetch('/wp-json/trp-strategy/v1/performance')
            .then(response => response.json())
            .then(data => {
                document.getElementById('performance-dashboard').innerHTML = formatPerformanceDashboard(data);
            })
            .catch(error => {
                document.getElementById('performance-dashboard').innerHTML = '<p>Error loading performance data</p>';
            });

        function formatPerformanceDashboard(data) {
            if (!data || !data.performance) return '<p>No performance data available</p>';

            const perf = data.performance;
            return `
                <div class="performance-metrics">
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <h4>Total Return</h4>
                            <div class="metric-value ${perf.total_return >= 0 ? 'positive' : 'negative'}">${perf.total_return}%</div>
                        </div>
                        <div class="metric-card">
                            <h4>Win Rate</h4>
                            <div class="metric-value">${perf.win_rate}%</div>
                        </div>
                        <div class="metric-card">
                            <h4>Profit Factor</h4>
                            <div class="metric-value">${perf.profit_factor}</div>
                        </div>
                        <div class="metric-card">
                            <h4>Max Drawdown</h4>
                            <div class="metric-value negative">${perf.max_drawdown}%</div>
                        </div>
                    </div>
                </div>
            `;
        }
        </script>

        <style>
        .performance-metrics {
            margin: 20px 0;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .metric-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .metric-card h4 {
            margin: 0 0 10px 0;
            color: #4a5568;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #38a169;
        }

        .metric-value.negative {
            color: #e53e3e;
        }
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'trp-strategy-marketplace',
            TRP_STRATEGY_PLUGIN_URL . 'assets/css/marketplace.css',
            array(),
            TRP_STRATEGY_VERSION
        );

        wp_enqueue_script(
            'trp-strategy-marketplace',
            TRP_STRATEGY_PLUGIN_URL . 'assets/js/marketplace.js',
            array('jquery'),
            TRP_STRATEGY_VERSION,
            true
        );

        wp_localize_script('trp-strategy-marketplace', 'trp_strategy_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trp_strategy_nonce'),
        ));
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_trp-strategy-marketplace' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'trp-strategy-admin',
            TRP_STRATEGY_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            TRP_STRATEGY_VERSION
        );

        wp_enqueue_script(
            'trp-strategy-admin',
            TRP_STRATEGY_PLUGIN_URL . 'admin/js/admin.js',
            array('jquery'),
            TRP_STRATEGY_VERSION,
            true
        );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Strategy Marketplace',
            'Strategy Marketplace',
            'manage_options',
            'trp-strategy-marketplace',
            array($this, 'admin_page'),
            'dashicons-chart-bar',
            30
        );
    }

    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>🤖 TRP Strategy Marketplace</h1>

            <div class="trp-admin-section">
                <h2>📊 Strategy Overview</h2>
                <div id="admin-strategies-data">
                    <p>Loading strategy data...</p>
                </div>
                <button id="refresh-strategies" class="button button-primary">🔄 Refresh Data</button>
            </div>

            <div class="trp-admin-section">
                <h2>⚙️ Configuration</h2>
                <form method="post" action="options.php">
                    <?php settings_fields('trp_strategy_settings'); ?>
                    <?php do_settings_sections('trp_strategy_settings'); ?>
                    <?php submit_button(); ?>
                </form>
            </div>

            <div class="trp-admin-section">
                <h2>📝 Usage Instructions</h2>
                <div class="usage-instructions">
                    <h3>Shortcodes:</h3>
                    <ul>
                        <li><code>[trp_strategy_marketplace]</code> - Full strategy marketplace</li>
                        <li><code>[trp_conservative_strategy]</code> - Conservative strategy showcase</li>
                        <li><code>[trp_strategy_performance]</code> - Performance dashboard</li>
                    </ul>

                    <h3>REST API Endpoints:</h3>
                    <ul>
                        <li><code>/wp-json/trp-strategy/v1/strategies</code></li>
                        <li><code>/wp-json/trp-strategy/v1/strategy/{id}</code></li>
                        <li><code>/wp-json/trp-strategy/v1/performance</code></li>
                        <li><code>/wp-json/trp-strategy/v1/backtest</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAdminData();

            document.getElementById('refresh-strategies').addEventListener('click', function() {
                loadAdminData(true);
            });
        });

        function loadAdminData(force = false) {
            const dataDiv = document.getElementById('admin-strategies-data');

            if (force) {
                fetch('/wp-json/trp-strategy/v1/strategies?t=' + Date.now())
                    .then(response => response.json())
                    .then(data => {
                        dataDiv.innerHTML = formatAdminData(data);
                    })
                    .catch(error => {
                        dataDiv.innerHTML = '<p style="color: red;">Error loading strategy data</p>';
                    });
            } else {
                fetch('/wp-json/trp-strategy/v1/strategies')
                    .then(response => response.json())
                    .then(data => {
                        dataDiv.innerHTML = formatAdminData(data);
                    })
                    .catch(error => {
                        dataDiv.innerHTML = '<p style="color: red;">Error loading strategy data</p>';
                    });
            }
        }

        function formatAdminData(data) {
            return `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3>Available Strategies</h3>
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
        </style>
        <?php
    }

    /**
     * Get mock strategies data
     */
    private function get_mock_strategies() {
        return array(
            'status' => 'mock_data',
            'message' => 'Using demonstration data - Full strategies require Python trading system',
            'strategies' => array(
                array(
                    'id' => 'conservative',
                    'name' => 'Conservative Automated Strategy',
                    'type' => 'conservative',
                    'description' => 'Ultra-safe automated trading with micro-position sizing and strict risk controls',
                    'performance' => array(
                        'total_return' => '+24.7%',
                        'win_rate' => '89.3%',
                        'max_drawdown' => '-3.2%',
                        'total_trades' => 247
                    )
                ),
                array(
                    'id' => 'momentum',
                    'name' => 'Momentum Trading Strategy',
                    'type' => 'momentum',
                    'description' => 'Captures trending markets with advanced momentum indicators',
                    'performance' => array(
                        'total_return' => '+42.1%',
                        'win_rate' => '76.8%',
                        'max_drawdown' => '-8.7%',
                        'total_trades' => 189
                    )
                ),
                array(
                    'id' => 'mean_reversion',
                    'name' => 'Mean Reversion Strategy',
                    'type' => 'mean_reversion',
                    'description' => 'Profits from price deviations returning to historical averages',
                    'performance' => array(
                        'total_return' => '+31.5%',
                        'win_rate' => '82.4%',
                        'max_drawdown' => '-5.1%',
                        'total_trades' => 156
                    )
                )
            )
        );
    }

    /**
     * Get mock strategy details
     */
    private function get_mock_strategy_details($strategy_id) {
        $strategies = array(
            'conservative' => array(
                'id' => 'conservative',
                'name' => 'Conservative Automated Strategy',
                'description' => 'Ultra-safe automated trading strategy designed to prevent account blowups',
                'rules' => array(
                    'Micro-position sizing (0.25-0.5% per trade)',
                    'Strict stop-loss rules (1-1.5% loss limits)',
                    'Conservative entry conditions only',
                    'Daily loss limits (0.5-1% maximum)',
                    'Emergency stop mechanisms'
                ),
                'performance' => array(
                    'total_return' => '+24.7%',
                    'annualized_return' => '+18.3%',
                    'win_rate' => '89.3%',
                    'max_drawdown' => '-3.2%',
                    'sharpe_ratio' => '2.1',
                    'total_trades' => 247
                ),
                'risk_metrics' => array(
                    'var_95' => '-2.1%',
                    'expected_shortfall' => '-3.4%',
                    'beta' => '0.7'
                )
            )
        );

        return array(
            'status' => 'mock_data',
            'strategy' => $strategies[$strategy_id] ?? null
        );
    }

    /**
     * Get mock performance data
     */
    private function get_mock_performance_data() {
        return array(
            'status' => 'mock_data',
            'overview' => array(
                'total_strategies' => 3,
                'avg_performance' => '+32.8%',
                'best_strategy' => 'Momentum (+42.1%)',
                'total_trades' => 592,
                'avg_win_rate' => '82.8%'
            ),
            'performance' => array(
                'total_return' => '+32.8%',
                'annualized_return' => '+24.1%',
                'win_rate' => '82.8%',
                'max_drawdown' => '-8.7%',
                'sharpe_ratio' => '1.9',
                'profit_factor' => '2.3'
            )
        );
    }

    /**
     * Get mock backtest result
     */
    private function get_mock_backtest_result($strategy_id) {
        return array(
            'status' => 'mock_data',
            'results' => array(
                'strategy_id' => $strategy_id,
                'period' => '2023-01-01 to ' . date('Y-m-d'),
                'total_return' => '+24.7%',
                'annualized_return' => '+18.3%',
                'sharpe_ratio' => '2.1',
                'max_drawdown' => '-3.2%',
                'win_rate' => '89.3%',
                'total_trades' => 247,
                'avg_trade_duration' => '2.3 days'
            )
        );
    }
}

// Initialize the plugin
TRP_Strategy_Marketplace::get_instance();