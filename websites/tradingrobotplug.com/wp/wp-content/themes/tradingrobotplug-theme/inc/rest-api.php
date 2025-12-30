<?php
/**
 * REST API Module
 * Trading data endpoints for automated trading platform
 * 
 * @package TradingRobotPlug
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize REST API routes
 */
function trp_register_rest_routes()
{
    // Alpha Vantage data endpoint
    register_rest_route('tradingrobotplug/v1', '/fetchdata', array(
        'methods' => 'GET',
        'callback' => 'trp_fetch_alpha_vantage_data',
        'permission_callback' => '__return_true',
    ));
    
    // Polygon data endpoint
    register_rest_route('tradingrobotplug/v1', '/fetchpolygondata', array(
        'methods' => 'GET',
        'callback' => 'trp_fetch_polygon_data',
        'permission_callback' => '__return_true',
    ));
    
    // Real-time data endpoint
    register_rest_route('tradingrobotplug/v1', '/fetchrealtime', array(
        'methods' => 'GET',
        'callback' => 'trp_fetch_real_time_data',
        'permission_callback' => '__return_true',
    ));
    
    // Trading signals endpoint
    register_rest_route('tradingrobotplug/v1', '/fetchsignals', array(
        'methods' => 'GET',
        'callback' => 'trp_fetch_trading_signals',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ));
    
    // AI suggestions endpoint
    register_rest_route('tradingrobotplug/v1', '/fetchaisuggestions', array(
        'methods' => 'GET',
        'callback' => 'trp_fetch_ai_suggestions',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
    ));
    
    // Stock data query endpoint
    register_rest_route('tradingrobotplug/v1', '/querystockdata', array(
        'methods' => 'GET',
        'callback' => 'trp_query_stock_data',
        'permission_callback' => '__return_true',
    ));
    
    // Waitlist endpoint (for verification)
    register_rest_route('tradingrobotplug/v1', '/waitlist', array(
        'methods' => 'GET',
        'callback' => 'trp_get_waitlist_status',
        'permission_callback' => '__return_true',
    ));
    
    // Contact endpoint (for verification)
    register_rest_route('tradingrobotplug/v1', '/contact', array(
        'methods' => 'GET',
        'callback' => 'trp_get_contact_status',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'trp_register_rest_routes');

/**
 * Fetch Alpha Vantage data
 */
function trp_fetch_alpha_vantage_data()
{
    $command = escapeshellcmd('python3 ' . TRP_THEME_DIR . '/scripts/alpha_vantage_fetcher.py');
    $output = shell_exec($command);
    
    if ($output === null) {
        error_log('Alpha Vantage data fetch failed.');
        return new WP_Error('no_data', 'Failed to fetch data from Alpha Vantage', array('status' => 500));
    }
    
    return json_decode($output, true);
}

/**
 * Fetch Polygon data
 */
function trp_fetch_polygon_data()
{
    $command = escapeshellcmd('python3 ' . TRP_THEME_DIR . '/scripts/polygon_fetcher.py');
    $output = shell_exec($command);
    
    if ($output === null) {
        error_log('Polygon data fetch failed.');
        return new WP_Error('no_data', 'Failed to fetch data from Polygon', array('status' => 500));
    }
    
    return json_decode($output, true);
}

/**
 * Fetch real-time data
 */
function trp_fetch_real_time_data()
{
    $command = escapeshellcmd('python3 ' . TRP_THEME_DIR . '/scripts/real_time_fetcher.py');
    $output = shell_exec($command);
    
    if ($output === null) {
        error_log('Real-time data fetch failed.');
        return new WP_Error('no_data', 'Failed to fetch real-time data', array('status' => 500));
    }
    
    return json_decode($output, true);
}

/**
 * Fetch trading signals
 */
function trp_fetch_trading_signals()
{
    $command = escapeshellcmd('python3 ' . TRP_THEME_DIR . '/scripts/fetch_trading_signals.py');
    $output = shell_exec($command);
    
    if ($output === null) {
        error_log('Trading signals fetch failed.');
        return new WP_Error('trading_signals_error', 'Error executing trading signals script.', array('status' => 500));
    }
    
    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Invalid JSON response from trading signals script.');
        return new WP_Error('trading_signals_error', 'Invalid response from trading signals script.', array('status' => 500));
    }
    
    return $data;
}

/**
 * Fetch AI suggestions
 */
function trp_fetch_ai_suggestions()
{
    $features = 'close, volume, RSI, SMA_10';
    $command = escapeshellcmd("python3 " . TRP_THEME_DIR . "/scripts/openai_utils.py suggest_new_features \"$features\"");
    $output = shell_exec($command);
    
    if ($output === null) {
        error_log('AI suggestions fetch failed.');
        return new WP_Error('ai_suggestions_error', 'Error executing OpenAI suggestions script.', array('status' => 500));
    }
    
    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Invalid JSON response from OpenAI suggestions script.');
        return new WP_Error('ai_suggestions_error', 'Invalid response from OpenAI suggestions script.', array('status' => 500));
    }
    
    return $data;
}

/**
 * Query stock data from database
 */
function trp_query_stock_data($request)
{
    global $wpdb;
    
    $symbol = $request->get_param('symbol');
    $start_date = $request->get_param('start_date');
    $end_date = $request->get_param('end_date');
    
    if (!$symbol || !$start_date || !$end_date) {
        return new WP_Error('missing_data', 'Missing required parameters', array('status' => 400));
    }
    
    $query = $wpdb->prepare(
        "SELECT * FROM stock_data WHERE symbol = %s AND date BETWEEN %s AND %s ORDER BY date",
        $symbol,
        $start_date,
        $end_date
    );
    
    $results = $wpdb->get_results($query, ARRAY_A);
    
    if (empty($results)) {
        return new WP_Error('no_data', 'No data found for the given parameters', array('status' => 404));
    }
    
    return $results;
}

/**
 * Allow public access to trading data endpoints
 */
function trp_rest_authentication_errors($result)
{
    if (!empty($result)) {
        return $result;
    }
    
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($request_uri, '/tradingrobotplug/v1/') !== false) {
        return true;
    }
    
    return $result;
}

add_filter('rest_authentication_errors', 'trp_rest_authentication_errors');

/**
 * Get waitlist status (for verification)
 */
function trp_get_waitlist_status($request)
{
    return new WP_REST_Response(array(
        'status' => 'active',
        'endpoint' => 'waitlist',
        'description' => 'Waitlist form endpoint',
        'timestamp' => current_time('mysql'),
    ), 200);
}

/**
 * Get contact form status (for verification)
 */
function trp_get_contact_status($request)
{
    return new WP_REST_Response(array(
        'status' => 'active',
        'endpoint' => 'contact',
        'description' => 'Contact form endpoint',
        'timestamp' => current_time('mysql'),
    ), 200);
}

