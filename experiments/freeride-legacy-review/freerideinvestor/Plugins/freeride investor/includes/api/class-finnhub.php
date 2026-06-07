<?php

if (!defined('ABSPATH')) exit;

class FRI_Finnhub {

    private static $base_url = 'https://finnhub.io/api/v1';
    private static $credential_env = 'FINNHUB_API_KEY';

    /**
     * Fetch company news
     *
     * @param string $symbol The stock symbol.
     * @param int $days Number of days to fetch news for.
     * @return array|WP_Error The news data or WP_Error on failure.
     */
    public static function get_company_news($symbol, $days = 7) {
        $from = date('Y-m-d', strtotime("-{$days} days"));
        $to = date('Y-m-d');

        $url = add_query_arg([
            'symbol' => $symbol,
            'token'  => getenv(self::$credential_env) ?: '',
            'from'   => $from,
            'to'     => $to,
        ], self::$base_url . '/company-news');

        return FRI_API_Requests::make_request($url);
    }
}
