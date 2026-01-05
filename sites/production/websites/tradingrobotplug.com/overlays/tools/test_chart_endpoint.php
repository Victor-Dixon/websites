<?php
/**
 * Test Chart Data REST API Endpoint
 * 
 * This script tests if the chart data endpoint is accessible.
 * Place this file in the WordPress root directory and access it via browser.
 * 
 * Usage: https://tradingrobotplug.com/test_chart_endpoint.php
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

header('Content-Type: application/json');

// Test the endpoint
$rest_url = rest_url('tradingrobotplug/v1/chart-data');
$response = wp_remote_get($rest_url);

if (is_wp_error($response)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch endpoint',
        'error' => $response->get_error_message(),
        'url' => $rest_url
    ], JSON_PRETTY_PRINT);
} else {
    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    echo json_encode([
        'status' => $status_code === 200 ? 'success' : 'error',
        'status_code' => $status_code,
        'url' => $rest_url,
        'response' => json_decode($body, true)
    ], JSON_PRETTY_PRINT);
}

