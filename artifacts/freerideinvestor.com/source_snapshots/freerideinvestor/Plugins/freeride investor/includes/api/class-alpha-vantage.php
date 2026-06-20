<?php

if (!defined('ABSPATH')) exit;

class FRI_Alpha_Vantage {

    private static $base_url = 'https://www.alphavantage.co/query';
    private static $api_key = ALPHA_VANTAGE_API_KEY;

    /**
     * Fetch stock quote
     *
     * @param string $symbol The stock symbol.
     * @return array|WP_Error The stock data or WP_Error on failure.
     */
    public static function get_stock_quote($symbol) {
        $url = add_query_arg([
            'function' => 'GLOBAL_QUOTE',
            'symbol'   => $symbol,
            'apikey'   => self::$api_key,
        ], self::$base_url);

        return FRI_API_Requests::make_request($url);
    }

    /**
     * Fetch historical data
     *
     * @param string $symbol The stock symbol.
     * @return array|WP_Error The historical data or WP_Error on failure.
     */
    public static function get_historical_data($symbol) {
        $url = add_query_arg([
            'function'   => 'TIME_SERIES_DAILY',
            'symbol'     => $symbol,
            'apikey'     => self::$api_key,
            'outputsize' => 'compact', // Fetch last 100 data points
        ], self::$base_url);

        return FRI_API_Requests::make_request($url);
    }
}
