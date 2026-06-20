<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FRI_API_Requests {

    /**
     * Make a generic API request
     *
     * @param string $url The API endpoint URL.
     * @param string $method The HTTP method: 'GET', 'POST', etc.
     * @param array|null $body The request body for POST requests.
     * @param array $headers Additional request headers.
     * @param int $timeout Timeout for the request in seconds.
     * @param int $retries Number of retry attempts for transient failures.
     * @return array|WP_Error The API response or WP_Error on failure.
     */
    public static function make_request($url, $method = 'GET', $body = null, $headers = [], $timeout = 30, $retries = 3) {
        $args = [
            'method'  => $method,
            'headers' => $headers,
            'timeout' => $timeout,
        ];

        if ($body) {
            $args['body'] = $body;
        }

        $attempts = 0;
        $response = null;

        while ($attempts < $retries) {
            $response = wp_remote_request($url, $args);

            if (!is_wp_error($response)) {
                break; // Request succeeded
            }

            $attempts++;
            fri_log('WARNING', "API request failed on attempt $attempts: " . $response->get_error_message());

            if ($attempts < $retries) {
                sleep(1); // Delay between retries
            }
        }

        if (is_wp_error($response)) {
            fri_log('ERROR', "API request failed after $retries retries: " . $response->get_error_message());
            return $response;
        }

        $body = wp_remote_retrieve_body($response);

        fri_log('INFO', "API Response (truncated): " . substr($body, 0, 500));

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            fri_log('ERROR', "JSON decoding failed: " . json_last_error_msg());
            return new WP_Error('json_error', __('Error decoding API response.', 'freeride-investor'));
        }

        return $data;
    }

    /**
     * Helper function to add common headers
     *
     * @param string $api_key The API key for the request.
     * @return array Default headers with Authorization.
     */
    public static function get_default_headers($api_key) {
        return [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ];
    }
}
