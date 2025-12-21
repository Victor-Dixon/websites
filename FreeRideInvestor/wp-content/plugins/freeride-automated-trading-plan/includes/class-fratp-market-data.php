<?php
/**
 * Market Data Class
 * Fetches market data from various APIs
 */

if (!defined('ABSPATH')) {
    exit;
}

class FRATP_Market_Data {
    
    /**
     * Get current quote for a symbol
     * 
     * @param string $symbol Stock symbol
     * @return array|WP_Error Quote data or error
     */
    public function get_quote($symbol) {
        $symbol = strtoupper(trim($symbol));
        
        // Try Alpha Vantage first
        $quote = $this->get_alpha_vantage_quote($symbol);
        if (!is_wp_error($quote)) {
            return $quote;
        }
        
        // Try Finnhub as fallback
        $quote = $this->get_finnhub_quote($symbol);
        if (!is_wp_error($quote)) {
            return $quote;
        }
        
        // Try using SmartStock Pro if available
        if (class_exists('SSP_Alpha_Vantage')) {
            $quote = SSP_Alpha_Vantage::get_stock_quote($symbol);
            if (!is_wp_error($quote)) {
                return $this->format_quote($quote, $symbol);
            }
        }
        
        return new WP_Error('no_data', __('Unable to fetch market data.', 'freeride-automated-trading-plan'));
    }
    
    /**
     * Get historical data for a symbol
     * 
     * @param string $symbol Stock symbol
     * @param int $days Number of days of history needed
     * @return array|WP_Error Historical data or error
     */
    public function get_historical_data($symbol, $days = 200) {
        $symbol = strtoupper(trim($symbol));
        $cache_key = 'fratp_historical_' . $symbol . '_' . $days;
        
        // Check cache (cache for 1 hour)
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }
        
        // Try Alpha Vantage
        $data = $this->get_alpha_vantage_historical($symbol, $days);
        if (!is_wp_error($data) && !empty($data)) {
            set_transient($cache_key, $data, HOUR_IN_SECONDS);
            return $data;
        }
        
        // Try Finnhub
        $data = $this->get_finnhub_historical($symbol, $days);
        if (!is_wp_error($data) && !empty($data)) {
            set_transient($cache_key, $data, HOUR_IN_SECONDS);
            return $data;
        }
        
        return new WP_Error('no_data', __('Unable to fetch historical data.', 'freeride-automated-trading-plan'));
    }
    
    /**
     * Get quote from Alpha Vantage
     * 
     * @param string $symbol Stock symbol
     * @return array|WP_Error Quote data or error
     */
    private function get_alpha_vantage_quote($symbol) {
        $api_key = get_option('fratp_alpha_vantage_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Alpha Vantage API key not configured.', 'freeride-automated-trading-plan'));
        }
        
        $url = add_query_arg(array(
            'function' => 'GLOBAL_QUOTE',
            'symbol' => $symbol,
            'apikey' => $api_key,
        ), 'https://www.alphavantage.co/query');
        
        $response = wp_remote_get($url, array('timeout' => 15));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['Error Message']) || isset($data['Note'])) {
            return new WP_Error('api_error', __('Alpha Vantage API error.', 'freeride-automated-trading-plan'));
        }
        
        if (isset($data['Global Quote']) && !empty($data['Global Quote'])) {
            return $this->format_quote($data['Global Quote'], $symbol);
        }
        
        return new WP_Error('no_data', __('No quote data from Alpha Vantage.', 'freeride-automated-trading-plan'));
    }
    
    /**
     * Get historical data from Alpha Vantage
     * 
     * @param string $symbol Stock symbol
     * @param int $days Number of days
     * @return array|WP_Error Historical data or error
     */
    private function get_alpha_vantage_historical($symbol, $days) {
        $api_key = get_option('fratp_alpha_vantage_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Alpha Vantage API key not configured.', 'freeride-automated-trading-plan'));
        }
        
        $url = add_query_arg(array(
            'function' => 'TIME_SERIES_DAILY',
            'symbol' => $symbol,
            'apikey' => $api_key,
            'outputsize' => $days > 100 ? 'full' : 'compact',
        ), 'https://www.alphavantage.co/query');
        
        $response = wp_remote_get($url, array('timeout' => 15));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['Error Message']) || isset($data['Note'])) {
            return new WP_Error('api_error', __('Alpha Vantage API error.', 'freeride-automated-trading-plan'));
        }
        
        if (isset($data['Time Series (Daily)'])) {
            return $this->format_historical_data($data['Time Series (Daily)'], $days);
        }
        
        return new WP_Error('no_data', __('No historical data from Alpha Vantage.', 'freeride-automated-trading-plan'));
    }
    
    /**
     * Get quote from Finnhub
     * 
     * @param string $symbol Stock symbol
     * @return array|WP_Error Quote data or error
     */
    private function get_finnhub_quote($symbol) {
        $api_key = get_option('fratp_finnhub_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Finnhub API key not configured.', 'freeride-automated-trading-plan'));
        }
        
        $url = add_query_arg(array(
            'symbol' => $symbol,
            'token' => $api_key,
        ), 'https://finnhub.io/api/v1/quote');
        
        $response = wp_remote_get($url, array('timeout' => 15));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['c']) && $data['c'] > 0) {
            return array(
                'symbol' => $symbol,
                'price' => $data['c'],
                'open' => $data['o'] ?? $data['c'],
                'high' => $data['h'] ?? $data['c'],
                'low' => $data['l'] ?? $data['c'],
                'previous_close' => $data['pc'] ?? $data['c'],
                'change' => ($data['c'] - ($data['pc'] ?? $data['c'])),
                'change_percent' => (($data['c'] - ($data['pc'] ?? $data['c'])) / ($data['pc'] ?? $data['c'])) * 100,
            );
        }
        
        return new WP_Error('no_data', __('No quote data from Finnhub.', 'freeride-automated-trading-plan'));
    }
    
    /**
     * Get historical data from Finnhub
     * 
     * @param string $symbol Stock symbol
     * @param int $days Number of days
     * @return array|WP_Error Historical data or error
     */
    private function get_finnhub_historical($symbol, $days) {
        $api_key = get_option('fratp_finnhub_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Finnhub API key not configured.', 'freeride-automated-trading-plan'));
        }
        
        $end = time();
        $start = $end - ($days * 24 * 60 * 60);
        
        $url = add_query_arg(array(
            'symbol' => $symbol,
            'resolution' => 'D',
            'from' => $start,
            'to' => $end,
            'token' => $api_key,
        ), 'https://finnhub.io/api/v1/stock/candle');
        
        $response = wp_remote_get($url, array('timeout' => 15));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['s']) && $data['s'] === 'ok' && isset($data['c'])) {
            return $this->format_finnhub_historical($data, $days);
        }
        
        return new WP_Error('no_data', __('No historical data from Finnhub.', 'freeride-automated-trading-plan'));
    }
    
    /**
     * Format quote data
     * 
     * @param array $data Raw quote data
     * @param string $symbol Stock symbol
     * @return array Formatted quote
     */
    private function format_quote($data, $symbol) {
        // Handle Alpha Vantage format
        if (isset($data['05. price'])) {
            return array(
                'symbol' => $symbol,
                'price' => floatval($data['05. price']),
                'open' => floatval($data['02. open'] ?? $data['05. price']),
                'high' => floatval($data['03. high'] ?? $data['05. price']),
                'low' => floatval($data['04. low'] ?? $data['05. price']),
                'previous_close' => floatval($data['08. previous close'] ?? $data['05. price']),
                'change' => floatval($data['09. change'] ?? 0),
                'change_percent' => floatval($data['10. change percent'] ?? 0),
            );
        }
        
        // Already formatted
        if (isset($data['price'])) {
            return $data;
        }
        
        return new WP_Error('invalid_format', __('Invalid quote format.', 'freeride-automated-trading-plan'));
    }
    
    /**
     * Format historical data from Alpha Vantage
     * 
     * @param array $data Raw historical data
     * @param int $days Number of days needed
     * @return array Formatted historical data
     */
    private function format_historical_data($data, $days) {
        $formatted = array();
        $count = 0;
        
        // Sort by date (newest first)
        krsort($data);
        
        foreach ($data as $date => $values) {
            if ($count >= $days) {
                break;
            }
            
            $formatted[] = array(
                'date' => $date,
                'open' => floatval($values['1. open']),
                'high' => floatval($values['2. high']),
                'low' => floatval($values['3. low']),
                'close' => floatval($values['4. close']),
                'volume' => intval($values['5. volume']),
            );
            
            $count++;
        }
        
        // Sort by date (oldest first) for calculations
        usort($formatted, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });
        
        return $formatted;
    }
    
    /**
     * Format historical data from Finnhub
     * 
     * @param array $data Raw historical data
     * @param int $days Number of days needed
     * @return array Formatted historical data
     */
    private function format_finnhub_historical($data, $days) {
        $formatted = array();
        $timestamps = $data['t'] ?? array();
        $opens = $data['o'] ?? array();
        $highs = $data['h'] ?? array();
        $lows = $data['l'] ?? array();
        $closes = $data['c'] ?? array();
        $volumes = $data['v'] ?? array();
        
        $count = min(count($timestamps), $days);
        
        for ($i = 0; $i < $count; $i++) {
            $formatted[] = array(
                'date' => date('Y-m-d', $timestamps[$i]),
                'open' => floatval($opens[$i]),
                'high' => floatval($highs[$i]),
                'low' => floatval($lows[$i]),
                'close' => floatval($closes[$i]),
                'volume' => intval($volumes[$i] ?? 0),
            );
        }
        
        return $formatted;
    }
}

