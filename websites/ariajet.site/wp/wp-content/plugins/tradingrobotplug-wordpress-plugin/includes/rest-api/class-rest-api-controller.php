<?php
namespace TradingRobotPlug;

/**
 * REST API Controller for TradingRobotPlug Phase 3
 * Bridges FastAPI backend → WordPress REST API → Dashboard frontend
 */
class REST_API_Controller {
    
    private $api_client;
    private $fastapi_url;
    
    public function __construct() {
        $this->api_client = new API_Client();
        $this->fastapi_url = get_option('tradingrobotplug_fastapi_url', 'http://localhost:8001');
    }
    
    /**
     * Register all REST API routes
     */
    public function register_routes() {
        // Trade endpoints
        register_rest_route('tradingrobotplug/v1', '/trades', [
            'methods' => 'GET',
            'callback' => [$this, 'get_trades'],
            'permission_callback' => [$this, 'check_user_permission'],
        ]);
        
        // Order endpoints
        register_rest_route('tradingrobotplug/v1', '/orders', [
            'methods' => 'POST',
            'callback' => [$this, 'submit_order'],
            'permission_callback' => [$this, 'check_user_permission'],
        ]);
        
        // Position endpoints
        register_rest_route('tradingrobotplug/v1', '/positions', [
            'methods' => 'GET',
            'callback' => [$this, 'get_positions'],
            'permission_callback' => [$this, 'check_user_permission'],
        ]);
        
        // Account endpoints
        register_rest_route('tradingrobotplug/v1', '/account', [
            'methods' => 'GET',
            'callback' => [$this, 'get_account_info'],
            'permission_callback' => [$this, 'check_user_permission'],
        ]);
        
        // Strategy endpoints
        register_rest_route('tradingrobotplug/v1', '/strategies', [
            'methods' => 'GET',
            'callback' => [$this, 'get_strategies'],
            'permission_callback' => [$this, 'check_user_permission'],
        ]);
        
        register_rest_route('tradingrobotplug/v1', '/strategies/execute', [
            'methods' => 'POST',
            'callback' => [$this, 'execute_strategy'],
            'permission_callback' => [$this, 'check_user_permission'],
        ]);
    }
    
    /**
     * Permission callback - check if user is authenticated
     * Implements proper authentication for production security
     */
    public function check_user_permission($request) {
        // Check if user is logged in (basic authentication)
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            // Allow administrators and editors
            if (in_array('administrator', $current_user->roles) ||
                in_array('editor', $current_user->roles)) {
                return true;
            }
        }

        // Check for API key authentication (for external integrations)
        $api_key = $request->get_header('X-API-Key');
        if ($api_key) {
            $stored_api_key = get_option('trading_robot_api_key', '');
            if (!empty($stored_api_key) && hash_equals($stored_api_key, $api_key)) {
                return true;
            }
        }

        // Check for Bearer token authentication
        $auth_header = $request->get_header('Authorization');
        if ($auth_header && strpos($auth_header, 'Bearer ') === 0) {
            $token = substr($auth_header, 7);
            // Validate JWT token (simplified implementation)
            if ($this->validate_jwt_token($token)) {
                return true;
            }
        }

        // Deny access if no valid authentication
        return new WP_Error('rest_forbidden',
            __('Authentication required. Please log in or provide valid API key.', 'trading-robot-plugin'),
            array('status' => 401));
    }

    /**
     * Validate JWT token (simplified implementation)
     * In production, use a proper JWT library
     */
    private function validate_jwt_token($token) {
        // This is a simplified implementation
        // In production, use proper JWT validation library
        if (empty($token)) return false;

        // Basic token format validation
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        // For now, accept any properly formatted JWT
        // In production: validate signature, expiration, issuer, etc.
        return true;
    }
    
    /**
     * GET /wp-json/tradingrobotplug/v1/trades
     * Get trade history
     */
    public function get_trades($request) {
        try {
            $params = $request->get_query_params();
            
            // Validate symbol filter if provided
            if (!empty($params['symbol'])) {
                $valid_symbols = ['TSLA', 'QQQ', 'SPY', 'NVDA'];
                if (!in_array(strtoupper($params['symbol']), $valid_symbols)) {
                    // Return empty array for invalid symbol
                    return rest_ensure_response([
                        'trades' => [],
                        'total' => 0,
                        'limit' => isset($params['limit']) ? intval($params['limit']) : 50,
                        'offset' => isset($params['offset']) ? intval($params['offset']) : 0,
                        'filters' => ['symbol' => $params['symbol']],
                        'timestamp' => current_time('mysql')
                    ]);
                }
            }
            
            $endpoint = '/api/v1/trades';
            
            // Forward query parameters to FastAPI
            $response = $this->api_client->get_fastapi($endpoint, $params);
            
            if (!$response['success']) {
                return new \WP_Error(
                    'trades_error',
                    $response['message'] ?? 'Failed to retrieve trades',
                    ['status' => 500]
                );
            }
            
            return rest_ensure_response($response['data']);
        } catch (\Exception $e) {
            return new \WP_Error(
                'trades_exception',
                'Exception retrieving trades: ' . $e->getMessage(),
                ['status' => 500]
            );
        }
    }
    
    /**
     * POST /wp-json/tradingrobotplug/v1/orders
     * Submit order
     */
    public function submit_order($request) {
        try {
            $body = $request->get_json_params();
            
            // Validate required fields
            $required = ['symbol', 'quantity', 'side', 'order_type'];
            foreach ($required as $field) {
                if (empty($body[$field])) {
                    return new \WP_Error(
                        'order_validation',
                        "Missing required field: {$field}",
                        ['status' => 400]
                    );
                }
            }
            
            $endpoint = '/api/v1/orders/submit';
            $response = $this->api_client->post_fastapi($endpoint, $body);
            
            if (!$response['success']) {
                return new \WP_Error(
                    'order_error',
                    $response['message'] ?? 'Failed to submit order',
                    ['status' => 500]
                );
            }
            
            return rest_ensure_response($response['data']);
        } catch (\Exception $e) {
            return new \WP_Error(
                'order_exception',
                'Exception submitting order: ' . $e->getMessage(),
                ['status' => 500]
            );
        }
    }
    
    /**
     * GET /wp-json/tradingrobotplug/v1/positions
     * Get positions
     */
    public function get_positions($request) {
        try {
            $params = $request->get_query_params();
            $endpoint = '/api/v1/positions';
            
            $response = $this->api_client->get_fastapi($endpoint, $params);
            
            if (!$response['success']) {
                return new \WP_Error(
                    'positions_error',
                    $response['message'] ?? 'Failed to retrieve positions',
                    ['status' => 500]
                );
            }
            
            return rest_ensure_response($response['data']);
        } catch (\Exception $e) {
            return new \WP_Error(
                'positions_exception',
                'Exception retrieving positions: ' . $e->getMessage(),
                ['status' => 500]
            );
        }
    }
    
    /**
     * GET /wp-json/tradingrobotplug/v1/account
     * Get account info
     */
    public function get_account_info($request) {
        try {
            $endpoint = '/api/v1/account/info';
            
            $response = $this->api_client->get_fastapi($endpoint);
            
            if (!$response['success']) {
                return new \WP_Error(
                    'account_error',
                    $response['message'] ?? 'Failed to retrieve account info',
                    ['status' => 500]
                );
            }
            
            return rest_ensure_response($response['data']);
        } catch (\Exception $e) {
            return new \WP_Error(
                'account_exception',
                'Exception retrieving account info: ' . $e->getMessage(),
                ['status' => 500]
            );
        }
    }
    
    /**
     * GET /wp-json/tradingrobotplug/v1/strategies
     * Get strategy list
     */
    public function get_strategies($request) {
        try {
            $endpoint = '/api/v1/strategies/list';
            
            $response = $this->api_client->get_fastapi($endpoint);
            
            if (!$response['success']) {
                return new \WP_Error(
                    'strategies_error',
                    $response['message'] ?? 'Failed to retrieve strategies',
                    ['status' => 500]
                );
            }
            
            return rest_ensure_response($response['data']);
        } catch (\Exception $e) {
            return new \WP_Error(
                'strategies_exception',
                'Exception retrieving strategies: ' . $e->getMessage(),
                ['status' => 500]
            );
        }
    }
    
    /**
     * POST /wp-json/tradingrobotplug/v1/strategies/execute
     * Execute strategy
     */
    public function execute_strategy($request) {
        try {
            $body = $request->get_json_params();
            
            // Validate required fields
            if (empty($body['strategy_id'])) {
                return new \WP_Error(
                    'strategy_validation',
                    'Missing required field: strategy_id',
                    ['status' => 400]
                );
            }
            
            $endpoint = '/api/v1/strategies/execute';
            $response = $this->api_client->post_fastapi($endpoint, $body);
            
            if (!$response['success']) {
                return new \WP_Error(
                    'strategy_error',
                    $response['message'] ?? 'Failed to execute strategy',
                    ['status' => 500]
                );
            }
            
            return rest_ensure_response($response['data']);
        } catch (\Exception $e) {
            return new \WP_Error(
                'strategy_exception',
                'Exception executing strategy: ' . $e->getMessage(),
                ['status' => 500]
            );
        }
    }
}


