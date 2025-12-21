<?php
/**
 * Standalone Plugin Testing Script for FreeRide Investor Website
 * 
 * Run this script from the command line to test all plugins:
 * php test-plugins.php
 */

// Prevent direct web access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line.');
}

echo "🔍 FreeRide Plugin Testing Framework\n";
echo "=====================================\n\n";

// Check if we're in a WordPress environment
if (!file_exists('wp-config.php') && !file_exists('../wp-config.php')) {
    echo "❌ Error: WordPress not found. Please run this script from your WordPress root directory.\n";
    exit(1);
}

// Load WordPress if not already loaded
if (!defined('ABSPATH')) {
    if (file_exists('wp-config.php')) {
        require_once 'wp-config.php';
    } else {
        require_once '../wp-config.php';
    }
    
    // Load WordPress
    require_once ABSPATH . 'wp-load.php';
}

echo "✅ WordPress loaded successfully\n";
echo "📁 Plugin directory: " . WP_PLUGIN_DIR . "\n\n";

// Test results storage
$test_results = [];
$critical_errors = [];
$warnings = [];

/**
 * Test a single plugin
 */
function test_single_plugin($plugin_file, $plugin_data) {
    global $wpdb;
    
    $plugin_name = $plugin_data['Name'];
    $results = [
        'name' => $plugin_name,
        'file' => $plugin_file,
        'tests' => [],
        'status' => 'unknown'
    ];
    
    echo "Testing: $plugin_name\n";
    echo "  File: $plugin_file\n";
    
    // Test 1: File existence and readability
    $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
    if (file_exists($plugin_path) && is_readable($plugin_path)) {
        $results['tests']['file_access'] = ['status' => 'pass', 'message' => 'File accessible'];
        echo "  ✅ File access: OK\n";
    } else {
        $results['tests']['file_access'] = ['status' => 'fail', 'message' => 'File not accessible'];
        echo "  ❌ File access: FAILED\n";
    }
    
    // Test 2: Plugin header validation
    if (test_plugin_headers($plugin_data)) {
        $results['tests']['headers'] = ['status' => 'pass', 'message' => 'Plugin headers valid'];
        echo "  ✅ Headers: OK\n";
    } else {
        $results['tests']['headers'] = ['status' => 'fail', 'message' => 'Missing required headers'];
        echo "  ❌ Headers: FAILED\n";
    }
    
    // Test 3: Security checks
    $security_results = test_plugin_security($plugin_path, $plugin_name);
    $results['tests']['security'] = $security_results;
    if ($security_results['status'] === 'pass') {
        echo "  ✅ Security: OK\n";
    } elseif ($security_results['status'] === 'warning') {
        echo "  ⚠️  Security: WARNING\n";
    } else {
        echo "  ❌ Security: FAILED\n";
    }
    
    // Test 4: Check if it's a custom plugin
    $is_custom = is_custom_plugin($plugin_file);
    if ($is_custom) {
        echo "  🎯 Custom plugin detected\n";
        
        // Test database tables for custom plugins
        $db_results = test_plugin_database($plugin_name);
        $results['tests']['database'] = $db_results;
        if ($db_results['status'] === 'pass') {
            echo "  ✅ Database: OK\n";
        } else {
            echo "  ℹ️  Database: " . $db_results['message'] . "\n";
        }
    }
    
    // Test 5: Check for shortcodes
    if (plugin_has_shortcodes($plugin_data)) {
        echo "  📝 Shortcodes mentioned in description\n";
    }
    
    // Determine overall status
    $results['status'] = determine_plugin_status($results['tests']);
    
    echo "  📊 Overall Status: " . strtoupper($results['status']) . "\n";
    echo "\n";
    
    return $results;
}

/**
 * Test plugin headers
 */
function test_plugin_headers($plugin_data) {
    $required_headers = ['Plugin Name', 'Version'];
    foreach ($required_headers as $header) {
        if (empty($plugin_data[$header])) {
            return false;
        }
    }
    return true;
}

/**
 * Test plugin security
 */
function test_plugin_security($file_path, $plugin_name) {
    $content = file_get_contents($file_path);
    $issues = [];
    
    // Check for direct file access prevention
    if (!strpos($content, 'ABSPATH') && !strpos($content, 'defined(\'ABSPATH\')')) {
        $issues[] = 'Missing ABSPATH check';
    }
    
    // Check for potential SQL injection vulnerabilities
    if (preg_match('/\$_(GET|POST|REQUEST|COOKIE|SERVER)\s*\[.*\]\s*[^=]*=.*WHERE/i', $content)) {
        $issues[] = 'Potential SQL injection vulnerability';
    }
    
    // Check for potential command injection
    if (preg_match('/(system|exec|shell_exec|passthru|eval)\s*\(/i', $content)) {
        $issues[] = 'Potential command injection vulnerability';
    }
    
    if (empty($issues)) {
        return ['status' => 'pass', 'message' => 'Security checks passed'];
    } else {
        return ['status' => 'warning', 'message' => 'Security issues found: ' . implode(', ', $issues)];
    }
}

/**
 * Test plugin database tables
 */
function test_plugin_database($plugin_name) {
    global $wpdb;
    
    // Check for common table patterns
    $tables_to_check = [
        $wpdb->prefix . 'frtc_',
        $wpdb->prefix . 'freeride_',
        $wpdb->prefix . 'stock_',
        $wpdb->prefix . 'trading_'
    ];
    
    $existing_tables = [];
    foreach ($tables_to_check as $table_pattern) {
        $tables = $wpdb->get_results("SHOW TABLES LIKE '{$table_pattern}%'");
        if (!empty($tables)) {
            $existing_tables[] = $table_pattern;
        }
    }
    
    if (!empty($existing_tables)) {
        return ['status' => 'pass', 'message' => 'Database tables found: ' . implode(', ', $existing_tables)];
    } else {
        return ['status' => 'info', 'message' => 'No custom database tables found'];
    }
}

/**
 * Check if plugin has shortcodes
 */
function plugin_has_shortcodes($plugin_data) {
    $description = $plugin_data['Description'] ?? '';
    return strpos($description, 'shortcode') !== false || 
           strpos($description, 'Shortcode') !== false;
}

/**
 * Determine if plugin is custom
 */
function is_custom_plugin($plugin_file) {
    $custom_patterns = [
        'freeride',
        'frtc',
        'smartstock',
        'tbow'
    ];
    
    foreach ($custom_patterns as $pattern) {
        if (strpos($plugin_file, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Determine overall plugin status
 */
function determine_plugin_status($tests) {
    foreach ($tests as $test) {
        if ($test['status'] === 'fail') {
            return 'critical';
        }
    }
    
    foreach ($tests as $test) {
        if ($test['status'] === 'warning') {
            return 'warning';
        }
    }
    
    return 'pass';
}

/**
 * Generate test summary
 */
function generate_test_summary($test_results) {
    $total_plugins = count($test_results);
    $passing = 0;
    $warnings = 0;
    $critical = 0;
    
    foreach ($test_results as $result) {
        switch ($result['status']) {
            case 'pass':
                $passing++;
                break;
            case 'warning':
                $warnings++;
                break;
            case 'critical':
                $critical++;
                break;
        }
    }
    
    return [
        'total' => $total_plugins,
        'passing' => $passing,
        'warnings' => $warnings,
        'critical' => $critical
    ];
}

// Get all plugins
$all_plugins = get_plugins();
$active_plugins = get_option('active_plugins');

echo "📊 Found " . count($all_plugins) . " total plugins\n";
echo "🟢 Active plugins: " . count($active_plugins) . "\n\n";

// Test each plugin
foreach ($all_plugins as $plugin_file => $plugin_data) {
    $is_active = in_array($plugin_file, $active_plugins);
    $is_custom = is_custom_plugin($plugin_file);
    
    if ($is_active) {
        echo "🟢 ACTIVE";
    } else {
        echo "⚪ INACTIVE";
    }
    
    if ($is_custom) {
        echo " 🎯 CUSTOM";
    }
    
    echo "\n";
    
    $results = test_single_plugin($plugin_file, $plugin_data);
    $test_results[] = $results;
    
    // Collect errors and warnings
    if ($results['status'] === 'critical') {
        $critical_errors[] = "Critical issues in: " . $results['name'];
    }
    if ($results['status'] === 'warning') {
        $warnings[] = "Warnings in: " . $results['name'];
    }
}

// Generate summary
$summary = generate_test_summary($test_results);

echo "=====================================\n";
echo "📊 TEST SUMMARY\n";
echo "=====================================\n";
echo "Total Plugins: " . $summary['total'] . "\n";
echo "✅ Passing: " . $summary['passing'] . "\n";
echo "⚠️  Warnings: " . $summary['warnings'] . "\n";
echo "❌ Critical: " . $summary['critical'] . "\n\n";

if (!empty($critical_errors)) {
    echo "❌ CRITICAL ERRORS:\n";
    foreach ($critical_errors as $error) {
        echo "  - $error\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "  - $warning\n";
    }
    echo "\n";
}

if ($summary['critical'] === 0) {
    echo "🎉 All plugins passed critical tests!\n";
} else {
    echo "🚨 Some plugins have critical issues that need attention.\n";
}

echo "\nTesting complete!\n";
