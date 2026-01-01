<?php
/**
 * Test script for stock data API
 * Run via: wp eval-file test_stock_api.php
 */

// Load WordPress
require_once(__DIR__ . '/../../tradingrobotplug.com/wp/wp-load.php');

// Include the theme functions
require_once(get_template_directory() . '/inc/dashboard-api.php');

echo "Testing stock data fetch...\n\n";

$symbols = ['TSLA', 'QQQ', 'SPY', 'NVDA'];

foreach ($symbols as $symbol) {
    echo "Fetching $symbol...\n";
    $result = trp_fetch_stock_data($symbol);
    
    if (is_wp_error($result)) {
        echo "  ERROR: " . $result->get_error_message() . "\n";
        echo "  Error Code: " . $result->get_error_code() . "\n";
        if ($result->get_error_data()) {
            echo "  Error Data: " . print_r($result->get_error_data(), true) . "\n";
        }
    } else {
        echo "  SUCCESS:\n";
        echo "    Symbol: " . $result['symbol'] . "\n";
        echo "    Price: $" . number_format($result['price'], 2) . "\n";
        echo "    Change: " . number_format($result['change'], 2) . "\n";
        echo "    Change %: " . number_format($result['change_percent'], 2) . "%\n";
    }
    echo "\n";
    usleep(500000); // 0.5 second delay
}

echo "Testing API endpoint...\n";
$request = new WP_REST_Request('GET', '/tradingrobotplug/v1/stock-data');
$response = trp_get_stored_stock_data($request);
echo "Response: " . print_r($response, true) . "\n";


