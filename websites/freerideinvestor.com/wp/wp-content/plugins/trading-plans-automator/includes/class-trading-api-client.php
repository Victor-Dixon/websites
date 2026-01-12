<?php
/**
 * Trading API Client - Connects to Trading Robot System
 */

if (!defined('ABSPATH')) {
    exit;
}

class TradingAPIClient {

    private $api_endpoint;
    private $api_key;
    private $timeout = 30;

    public function __construct() {
        $this->api_endpoint = $this->get_safe_api_endpoint();
        $this->api_key = get_option('tpa_api_key', '');
    }

    private function get_safe_api_endpoint() {
        $configured_endpoint = get_option('tpa_api_endpoint');

        // If explicitly configured and not the default localhost, use it
        if (!empty($configured_endpoint) && $configured_endpoint !== 'http://localhost:8000') {
            return $configured_endpoint;
        }

        // Environment-based defaults - NO localhost in production
        $environment = $this->detect_environment();
        if ($environment === 'production') {
            return 'https://api.freerideinvestor.com'; // Production API URL
        } elseif ($environment === 'staging') {
            return 'https://staging-api.freerideinvestor.com'; // Staging API URL
        } else {
            // Development only
            return $configured_endpoint ?: 'http://localhost:8000';
        }
    }

    private function detect_environment() {
        // Simple environment detection for freerideinvestor
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false || strpos($host, '.local') !== false) {
                return 'development';
            } elseif (strpos($host, 'staging') !== false || strpos($host, 'dev.') !== false) {
                return 'staging';
            }
        }
        return 'production'; // Safe default
    }

    /**
     * Get trading data from the robot system
     */
    public function get_trading_data() {
        return $this->make_request('GET', '/api/v1/trading/status');
    }

    /**
     * Get account information
     */
    public function get_account_info() {
        return $this->make_request('GET', '/api/v1/account/info');
    }

    /**
     * Get active trading strategies
     */
    public function get_active_strategies() {
        return $this->make_request('GET', '/api/v1/strategies/active');
    }

    /**
     * Get performance metrics
     */
    public function get_performance_metrics() {
        return $this->make_request('GET', '/api/v1/performance/metrics');
    }

    /**
     * Get recent trades
     */
    public function get_recent_trades($limit = 10) {
        return $this->make_request('GET', "/api/v1/trades/recent?limit={$limit}");
    }

    /**
     * Get trading journal summary
     */
    public function get_journal_summary($year = 2025) {
        return $this->make_request('GET', "/api/v1/journal/summary?year={$year}");
    }

    /**
     * Get risk management status
     */
    public function get_risk_status() {
        return $this->make_request('GET', '/api/v1/risk/status');
    }

    /**
     * Get strategy recommendations
     */
    public function get_strategy_recommendations() {
        return $this->make_request('GET', '/api/v1/strategies/recommendations');
    }

    /**
     * Make HTTP request to trading API
     */
    private function make_request($method, $endpoint, $data = null) {
        $url = rtrim($this->api_endpoint, '/') . $endpoint;

        $args = array(
            'method' => $method,
            'timeout' => $this->timeout,
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'TradingPlansAutomator/2.0'
            ),
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking' => true,
        );

        // Add API key if configured
        if (!empty($this->api_key)) {
            $args['headers']['Authorization'] = 'Bearer ' . $this->api_key;
        }

        // Add body for POST/PUT requests
        if ($data && in_array($method, array('POST', 'PUT', 'PATCH'))) {
            $args['body'] = wp_json_encode($data);
        }

        // Add query parameters for GET requests
        if ($data && $method === 'GET') {
            $url = add_query_arg($data, $url);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            error_log("Trading API request failed: " . $response->get_error_message());
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        if ($response_code >= 200 && $response_code < 300) {
            $data = json_decode($response_body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            } else {
                error_log("Failed to decode JSON response: " . $response_body);
                return false;
            }
        } else {
            error_log("Trading API returned error {$response_code}: {$response_body}");
            return false;
        }
    }

    /**
     * Test API connectivity
     */
    public function test_connection() {
        $health_data = $this->make_request('GET', '/health');
        return $health_data !== false;
    }

    /**
     * Get system status
     */
    public function get_system_status() {
        $status_data = $this->make_request('GET', '/api/v1/system/status');
        return $status_data ?: array(
            'status' => 'unknown',
            'timestamp' => current_time('mysql'),
            'version' => 'unknown'
        );
    }
}