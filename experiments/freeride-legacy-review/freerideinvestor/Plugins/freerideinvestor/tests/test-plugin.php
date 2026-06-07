<?php
require_once dirname(__FILE__) . '/freeride-investor.php';

echo '<h1>Testing Freeride Investor Plugin</h1>';

try {
    // Test 1: Log a message
    error_log('Testing Freeride Investor Plugin Debugging', 3, FRI_LOG_FILE);

    // Test 2: Check database table
    global $wpdb;
    $table_name = $wpdb->prefix . 'fri_cache';
    echo '<h3>Table Exists: ' . ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name ? 'Yes' : 'No') . '</h3>';

    // Test 3: Output table schema
    $schema = $wpdb->get_results("SHOW COLUMNS FROM $table_name", ARRAY_A);
    echo '<pre>' . print_r($schema, true) . '</pre>';

    // Test 4: Check log file
    echo '<h3>Log File Writable: ' . (is_writable(FRI_LOG_FILE) ? 'Yes' : 'No') . '</h3>';
} catch (Exception $e) {
    echo '<h3>Error: ' . $e->getMessage() . '</h3>';
    error_log('Error during test: ' . $e->getMessage(), 3, FRI_LOG_FILE);
}

