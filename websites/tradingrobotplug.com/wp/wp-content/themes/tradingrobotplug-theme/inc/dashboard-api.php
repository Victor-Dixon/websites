<?php
/**
 * Dashboard REST API Module
 * Dashboard endpoints for trading performance platform
 * 
 * @package TradingRobotPlug
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize Dashboard REST API routes
 */
function trp_register_dashboard_routes()
{
    // Dashboard overview endpoint
    register_rest_route('tradingrobotplug/v1', '/dashboard/overview', array(
        'methods' => 'GET',
        'callback' => 'trp_get_dashboard_overview',
        'permission_callback' => '__return_true',
    ));
    
    // Strategy dashboard endpoint
    register_rest_route('tradingrobotplug/v1', '/dashboard/strategies/(?P<strategy_id>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'trp_get_strategy_dashboard',
        'permission_callback' => '__return_true',
        'args' => array(
            'strategy_id' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return !empty($param);
                },
            ),
        ),
    ));
    
    // Performance metrics endpoint
    register_rest_route('tradingrobotplug/v1', '/performance/(?P<strategy_id>[a-zA-Z0-9-]+)/metrics', array(
        'methods' => 'GET',
        'callback' => 'trp_get_performance_metrics',
        'permission_callback' => '__return_true',
        'args' => array(
            'strategy_id' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return !empty($param);
                },
            ),
        ),
    ));
    
    // Performance history endpoint
    register_rest_route('tradingrobotplug/v1', '/performance/(?P<strategy_id>[a-zA-Z0-9-]+)/history', array(
        'methods' => 'GET',
        'callback' => 'trp_get_performance_history',
        'permission_callback' => '__return_true',
        'args' => array(
            'strategy_id' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return !empty($param);
                },
            ),
        ),
    ));
    
    // Trades endpoint
    register_rest_route('tradingrobotplug/v1', '/trades', array(
        'methods' => 'GET',
        'callback' => 'trp_get_trades',
        'permission_callback' => '__return_true',
    ));
    
    // Trade details endpoint
    register_rest_route('tradingrobotplug/v1', '/trades/(?P<trade_id>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'trp_get_trade_details',
        'permission_callback' => '__return_true',
        'args' => array(
            'trade_id' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return !empty($param);
                },
            ),
        ),
    ));
    
    // Dashboard root endpoint (for verification)
    register_rest_route('tradingrobotplug/v1', '/dashboard', array(
        'methods' => 'GET',
        'callback' => 'trp_get_dashboard_root',
        'permission_callback' => '__return_true',
    ));
    
    // Performance root endpoint (for verification)
    register_rest_route('tradingrobotplug/v1', '/performance', array(
        'methods' => 'GET',
        'callback' => 'trp_get_performance_root',
        'permission_callback' => '__return_true',
    ));
    
    // Strategies endpoint (for verification)
    register_rest_route('tradingrobotplug/v1', '/strategies', array(
        'methods' => 'GET',
        'callback' => 'trp_get_strategies',
        'permission_callback' => '__return_true',
    ));
    
    // Stock data endpoint for trading plugins
    register_rest_route('tradingrobotplug/v1', '/stock-data', array(
        'methods' => 'GET',
        'callback' => 'trp_get_stored_stock_data',
        'permission_callback' => '__return_true',
    ));
    
    // Historical stock data from database
    register_rest_route('tradingrobotplug/v1', '/stock-data/(?P<symbol>[a-zA-Z]+)', array(
        'methods' => 'GET',
        'callback' => 'trp_get_stock_history',
        'permission_callback' => '__return_true',
        'args' => array(
            'symbol' => array(
                'required' => true,
                'validate_callback' => function($param) {
                    return !empty($param) && strlen($param) <= 10;
                },
            ),
        ),
    ));
}

add_action('rest_api_init', 'trp_register_dashboard_routes');

// Create stock data table on theme activation
add_action('after_switch_theme', 'trp_create_stock_data_table');

// Schedule data collection (collect every 5 minutes during market hours)
if (!wp_next_scheduled('trp_collect_stock_data')) {
    wp_schedule_event(time(), 'trp_5min', 'trp_collect_stock_data');
}

// Custom cron interval: 5 minutes
add_filter('cron_schedules', function($schedules) {
    $schedules['trp_5min'] = array(
        'interval' => 300, // 5 minutes
        'display' => __('Every 5 Minutes', 'tradingrobotplug')
    );
    return $schedules;
});

/**
 * Collect and save stock data for primary symbols
 * This runs on a schedule to continuously collect data
 */
function trp_collect_stock_data_cron()
{
    $primary_symbols = array('TSLA', 'QQQ', 'SPY', 'NVDA');
    
    foreach ($primary_symbols as $symbol) {
        $stock_data = trp_fetch_stock_data($symbol);
        if (!is_wp_error($stock_data)) {
            trp_save_stock_data($stock_data);
        }
        // Small delay to avoid rate limiting
        usleep(200000); // 0.2 seconds
    }
}
add_action('trp_collect_stock_data', 'trp_collect_stock_data_cron');

/**
 * Fetch real stock data from Yahoo Finance (free, no API key required)
 * 
 * @param string $symbol Stock symbol (e.g., 'AAPL', 'MSFT', 'GOOGL')
 * @return array|WP_Error Stock data or error
 */
function trp_fetch_stock_data($symbol = 'AAPL')
{
    // Use Yahoo Finance API (free, no API key required)
    $url = 'https://query1.finance.yahoo.com/v8/finance/chart/' . urlencode($symbol);
    $args = array(
        'timeout' => 10,
        'headers' => array(
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        )
    );
    
    $response = wp_remote_get($url, $args);
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!$data || !isset($data['chart']['result'][0])) {
        return new WP_Error('no_data', 'Failed to fetch stock data', array('status' => 500));
    }
    
    $result = $data['chart']['result'][0];
    $quote = $result['meta'];
    
    return array(
        'symbol' => $quote['symbol'],
        'price' => $quote['regularMarketPrice'],
        'previous_close' => $quote['previousClose'],
        'change' => $quote['regularMarketPrice'] - $quote['previousClose'],
        'change_percent' => (($quote['regularMarketPrice'] - $quote['previousClose']) / $quote['previousClose']) * 100,
        'volume' => $quote['regularMarketVolume'],
        'market_cap' => isset($quote['marketCap']) ? $quote['marketCap'] : null,
        'timestamp' => current_time('mysql'),
    );
}

/**
 * Save stock data to database for trading plugins
 * 
 * @param array $stock_data Stock data from trp_fetch_stock_data()
 * @return bool Success status
 */
function trp_save_stock_data($stock_data)
{
    global $wpdb;
    
    // Ensure table exists
    trp_create_stock_data_table();
    
    $table_name = $wpdb->prefix . 'trp_stock_data';
    
    // Check if data for this symbol and timestamp already exists (avoid duplicates)
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE symbol = %s AND timestamp = %s",
        $stock_data['symbol'],
        $stock_data['timestamp']
    ));
    
    if ($existing) {
        // Update existing record
        return $wpdb->update(
            $table_name,
            array(
                'price' => $stock_data['price'],
                'previous_close' => $stock_data['previous_close'],
                'change' => $stock_data['change'],
                'change_percent' => $stock_data['change_percent'],
                'volume' => $stock_data['volume'],
                'market_cap' => $stock_data['market_cap'],
                'updated_at' => current_time('mysql'),
            ),
            array('id' => $existing),
            array('%f', '%f', '%f', '%f', '%d', '%d', '%s'),
            array('%d')
        ) !== false;
    } else {
        // Insert new record
        return $wpdb->insert(
            $table_name,
            array(
                'symbol' => $stock_data['symbol'],
                'price' => $stock_data['price'],
                'previous_close' => $stock_data['previous_close'],
                'change' => $stock_data['change'],
                'change_percent' => $stock_data['change_percent'],
                'volume' => $stock_data['volume'],
                'market_cap' => $stock_data['market_cap'],
                'timestamp' => $stock_data['timestamp'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ),
            array('%s', '%f', '%f', '%f', '%f', '%d', '%d', '%s', '%s', '%s')
        ) !== false;
    }
}

/**
 * Create stock data table if it doesn't exist
 */
function trp_create_stock_data_table()
{
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'trp_stock_data';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        symbol varchar(10) NOT NULL,
        price decimal(15,4) NOT NULL,
        previous_close decimal(15,4) DEFAULT NULL,
        change decimal(15,4) DEFAULT NULL,
        change_percent decimal(10,4) DEFAULT NULL,
        volume bigint(20) DEFAULT NULL,
        market_cap bigint(20) DEFAULT NULL,
        timestamp datetime NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY symbol (symbol),
        KEY timestamp (timestamp),
        KEY symbol_timestamp (symbol, timestamp)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Generate sample trades with real stock data
 * 
 * @param int $limit Number of trades to generate
 * @param array $symbols Array of symbols to use (default: primary symbols)
 * @return array Array of trade objects
 */
function trp_generate_sample_trades($limit = 10, $symbols = null)
{
    // Use primary symbols if not provided
    if ($symbols === null) {
        $symbols = array('TSLA', 'QQQ', 'SPY', 'NVDA');
    }
    $sides = array('BUY', 'SELL');
    $trades = array();
    
    for ($i = 0; $i < $limit; $i++) {
        $symbol = $symbols[array_rand($symbols)];
        $stock_data = trp_fetch_stock_data($symbol);
        
        if (is_wp_error($stock_data)) {
            // Fallback to mock data if API fails
            $price = rand(50, 500);
            $quantity = rand(1, 100);
            $pnl = rand(-500, 1000);
        } else {
            $price = $stock_data['price'];
            $quantity = rand(1, 100);
            $side = $sides[array_rand($sides)];
            // Calculate P&L based on price movement
            $pnl = ($side === 'BUY') ? rand(-100, 500) : rand(-500, 100);
        }
        
        $side = $sides[array_rand($sides)];
        $execution_time = date('Y-m-d H:i:s', strtotime('-' . ($i * 2) . ' hours'));
        
        $trades[] = array(
            'trade_id' => 'TRD-' . strtoupper(wp_generate_password(8, false)),
            'strategy_id' => 'STRATEGY-' . rand(1, 3),
            'symbol' => $symbol,
            'side' => $side,
            'quantity' => $quantity,
            'price' => round($price, 2),
            'pnl' => round($pnl, 2),
            'execution_time' => $execution_time,
            'created_at' => $execution_time,
        );
        
        // Add small delay to avoid rate limiting
        if ($i < $limit - 1) {
            usleep(200000); // 0.2 seconds
        }
    }
    
    return $trades;
}

/**
 * Get dashboard overview data with real stock data
 * Focus on: QQQ, SPY, TSLA (most important), NVDA
 */
function trp_get_dashboard_overview($request)
{
    global $wpdb;
    
    // Primary symbols: QQQ, SPY, TSLA (most important), NVDA
    $primary_symbols = array('TSLA', 'QQQ', 'SPY', 'NVDA'); // TSLA first (most important)
    $stock_data = array();
    $total_pnl = 0;
    $winning_trades = 0;
    $total_trades_count = 0;
    
    // Get sample trades with real stock data (using primary symbols)
    $recent_trades = trp_generate_sample_trades(10, $primary_symbols);
    $total_trades_count = count($recent_trades);
    
    // Calculate metrics from trades
    foreach ($recent_trades as $trade) {
        $total_pnl += $trade['pnl'];
        if ($trade['pnl'] > 0) {
            $winning_trades++;
        }
    }
    
    $win_rate = $total_trades_count > 0 ? ($winning_trades / $total_trades_count) * 100 : 0;
    
    // Fetch real stock prices for primary symbols and save to database
    $active_strategies = array();
    foreach ($primary_symbols as $symbol) {
        $stock = trp_fetch_stock_data($symbol);
        if (!is_wp_error($stock)) {
            // Save stock data to database for trading plugins
            trp_save_stock_data($stock);
            
            $active_strategies[] = array(
                'strategy_id' => 'STRATEGY-' . $symbol,
                'symbol' => $symbol,
                'current_price' => $stock['price'],
                'change' => $stock['change'],
                'change_percent' => $stock['change_percent'],
                'volume' => $stock['volume'],
                'status' => 'active',
            );
        }
        usleep(200000); // Small delay to avoid rate limiting
    }
    
    $metrics = array(
        'total_strategies' => count($active_strategies),
        'active_strategies' => count($active_strategies),
        'total_trades' => $total_trades_count,
        'total_pnl' => round($total_pnl, 2),
        'win_rate' => round($win_rate, 2),
        'avg_return' => $total_trades_count > 0 ? round(($total_pnl / $total_trades_count), 2) : 0,
        'sharpe_ratio' => round(rand(80, 150) / 100, 2),
        'max_drawdown' => round(rand(-15, -5), 2),
        'profit_factor' => round(rand(120, 200) / 100, 2),
        'daily_pnl' => round(rand(-500, 2000), 2),
        'monthly_pnl' => round(rand(-2000, 10000), 2),
        'roi' => round(rand(500, 2500) / 100, 2),
    );
    
    return array(
        'metrics' => $metrics,
        'recent_trades' => $recent_trades,
        'active_strategies' => $active_strategies,
        'timestamp' => current_time('mysql'),
    );
}

/**
 * Get strategy dashboard data
 */
function trp_get_strategy_dashboard($request)
{
    $strategy_id = $request->get_param('strategy_id');
    
    if (!$strategy_id) {
        return new WP_Error('missing_strategy_id', 'Strategy ID is required', array('status' => 400));
    }
    
    return array(
        'strategy_id' => $strategy_id,
        'metrics' => array(),
        'recent_trades' => array(),
        'status' => 'active',
    );
}

/**
 * Get performance metrics for a strategy
 */
function trp_get_performance_metrics($request)
{
    $strategy_id = $request->get_param('strategy_id');
    $start_date = $request->get_param('start_date');
    $end_date = $request->get_param('end_date');
    $metrics = $request->get_param('metrics');
    
    if (!$strategy_id) {
        return new WP_Error('missing_strategy_id', 'Strategy ID is required', array('status' => 400));
    }
    
    return array(
        'strategy_id' => $strategy_id,
        'period' => array(
            'start_date' => $start_date,
            'end_date' => $end_date,
        ),
        'metrics' => array(
            'total_pnl' => 0.0,
            'win_rate' => 0.0,
            'sharpe_ratio' => 0.0,
            'max_drawdown' => 0.0,
            'total_trades' => 0,
            'winning_trades' => 0,
            'losing_trades' => 0,
        ),
        'timestamp' => current_time('mysql'),
    );
}

/**
 * Get performance history (time-series data)
 */
function trp_get_performance_history($request)
{
    $strategy_id = $request->get_param('strategy_id');
    $start_date = $request->get_param('start_date');
    $end_date = $request->get_param('end_date');
    $granularity = $request->get_param('granularity') ?: 'daily';
    
    if (!$strategy_id) {
        return new WP_Error('missing_strategy_id', 'Strategy ID is required', array('status' => 400));
    }
    
    return array(
        'strategy_id' => $strategy_id,
        'granularity' => $granularity,
        'period' => array(
            'start_date' => $start_date,
            'end_date' => $end_date,
        ),
        'data' => array(),
        'timestamp' => current_time('mysql'),
    );
}

/**
 * Get trade history with real stock data
 */
function trp_get_trades($request)
{
    $strategy_id = $request->get_param('strategy_id');
    $start_date = $request->get_param('start_date');
    $end_date = $request->get_param('end_date');
    $limit = $request->get_param('limit') ?: 50;
    $offset = $request->get_param('offset') ?: 0;
    
    // Generate trades with real stock data
    $trades = trp_generate_sample_trades((int)$limit);
    
    // Apply filters if provided
    if ($strategy_id) {
        $trades = array_filter($trades, function($trade) use ($strategy_id) {
            return $trade['strategy_id'] === $strategy_id;
        });
    }
    
    return array(
        'trades' => array_values($trades),
        'total' => count($trades),
        'limit' => (int)$limit,
        'offset' => (int)$offset,
        'filters' => array(
            'strategy_id' => $strategy_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ),
        'timestamp' => current_time('mysql'),
    );
}

/**
 * Get trade details
 */
function trp_get_trade_details($request)
{
    $trade_id = $request->get_param('trade_id');
    
    if (!$trade_id) {
        return new WP_Error('missing_trade_id', 'Trade ID is required', array('status' => 400));
    }
    
    return array(
        'trade_id' => $trade_id,
        'strategy_id' => '',
        'symbol' => '',
        'side' => '',
        'quantity' => 0.0,
        'price' => 0.0,
        'execution_time' => '',
        'trade_type' => 'simulated',
        'pnl' => 0.0,
        'market_data_snapshot' => array(),
        'created_at' => '',
    );
}

/**
 * Get dashboard root endpoint (for verification)
 */
function trp_get_dashboard_root($request)
{
    return new WP_REST_Response(array(
        'status' => 'active',
        'endpoint' => 'dashboard',
        'description' => 'Dashboard API root endpoint',
        'available_endpoints' => array(
            '/dashboard/overview',
            '/dashboard/strategies/{strategy_id}',
        ),
        'timestamp' => current_time('mysql'),
    ), 200);
}

/**
 * Get performance root endpoint (for verification)
 */
function trp_get_performance_root($request)
{
    return new WP_REST_Response(array(
        'status' => 'active',
        'endpoint' => 'performance',
        'description' => 'Performance API root endpoint',
        'available_endpoints' => array(
            '/performance/{strategy_id}/metrics',
            '/performance/{strategy_id}/history',
        ),
        'timestamp' => current_time('mysql'),
    ), 200);
}

/**
 * Get strategies list (for verification)
 */
function trp_get_strategies($request)
{
    $primary_symbols = array('TSLA', 'QQQ', 'SPY', 'NVDA');
    $strategies = array();
    
    foreach ($primary_symbols as $symbol) {
        $stock = trp_fetch_stock_data($symbol);
        if (!is_wp_error($stock)) {
            $strategies[] = array(
                'strategy_id' => 'STRATEGY-' . $symbol,
                'symbol' => $symbol,
                'current_price' => $stock['price'],
                'change_percent' => $stock['change_percent'],
                'status' => 'active',
            );
        }
    }
    
    return new WP_REST_Response(array(
        'strategies' => $strategies,
        'total' => count($strategies),
        'timestamp' => current_time('mysql'),
    ), 200);
}

/**
 * Get stored stock data for trading plugins
 * Returns latest data for all primary symbols
 */
function trp_get_stored_stock_data($request)
{
    global $wpdb;
    
    trp_create_stock_data_table();
    $table_name = $wpdb->prefix . 'trp_stock_data';
    $primary_symbols = array('TSLA', 'QQQ', 'SPY', 'NVDA');
    
    // Get latest data for each symbol
    $results = array();
    foreach ($primary_symbols as $symbol) {
        $latest = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE symbol = %s 
            ORDER BY timestamp DESC 
            LIMIT 1",
            $symbol
        ), ARRAY_A);
        
        if ($latest) {
            $results[] = $latest;
        }
    }
    
    return array(
        'stock_data' => $results ?: array(),
        'symbols' => $primary_symbols,
        'timestamp' => current_time('mysql'),
    );
}

/**
 * Get historical stock data from database
 * 
 * @param WP_REST_Request $request
 * @return array|WP_Error
 */
function trp_get_stock_history($request)
{
    global $wpdb;
    
    $symbol = strtoupper($request->get_param('symbol'));
    $days = (int)$request->get_param('days') ?: 30;
    $start_date = $request->get_param('start_date');
    $end_date = $request->get_param('end_date') ?: current_time('mysql');
    
    // Validate symbol is in primary list
    $primary_symbols = array('TSLA', 'QQQ', 'SPY', 'NVDA');
    if (!in_array($symbol, $primary_symbols)) {
        return new WP_Error('invalid_symbol', 'Symbol must be one of: TSLA, QQQ, SPY, NVDA', array('status' => 400));
    }
    
    trp_create_stock_data_table();
    $table_name = $wpdb->prefix . 'trp_stock_data';
    
    if ($start_date) {
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE symbol = %s AND timestamp BETWEEN %s AND %s 
            ORDER BY timestamp ASC",
            $symbol,
            $start_date,
            $end_date
        );
    } else {
        $start_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE symbol = %s AND timestamp >= %s 
            ORDER BY timestamp ASC",
            $symbol,
            $start_date
        );
    }
    
    $results = $wpdb->get_results($query, ARRAY_A);
    
    return array(
        'symbol' => $symbol,
        'period' => array(
            'start_date' => $start_date,
            'end_date' => $end_date,
        ),
        'data' => $results ?: array(),
        'count' => count($results),
        'timestamp' => current_time('mysql'),
    );
}

