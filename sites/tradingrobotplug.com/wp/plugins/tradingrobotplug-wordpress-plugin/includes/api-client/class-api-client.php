<?php
namespace TradingRobotPlug;

class API_Client {
    private $api_url;
    private $api_key;

    public function __construct() {
        $this->api_url = TRADINGROBOTPLUG_API_URL;
        $this->api_key = get_option('tradingrobotplug_api_key');
    }

    public function get($endpoint, $args = []) {
        $url = $this->api_url . $endpoint;
        if (!empty($args)) {
            $url = add_query_arg($args, $url);
        }
        
        $response = wp_remote_get($url, $this->get_headers());
        return $this->handle_response($response);
    }

    public function post($endpoint, $body = []) {
        $response = wp_remote_post($this->api_url . $endpoint, array_merge($this->get_headers(), [
            'body' => json_encode($body)
        ]));
        return $this->handle_response($response);
    }

    private function get_headers() {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 15
        ];
    }

    private function handle_response($response) {
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($code >= 200 && $code < 300) {
            return ['success' => true, 'data' => $body];
        }
        
        return ['success' => false, 'message' => $body['message'] ?? 'Unknown error occurred'];
    }
}
