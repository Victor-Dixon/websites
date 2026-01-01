<?php
/**
 * Plugin Health Check for FreeRide Investor Website
 * 
 * This script performs basic health checks on plugins without requiring WordPress
 */

echo "🔍 FreeRide Plugin Health Check\n";
echo "===============================\n\n";

// Define plugin directory
$plugin_dir = __DIR__ . '/plugins';
echo "📁 Plugin directory: $plugin_dir\n\n";

if (!is_dir($plugin_dir)) {
    echo "❌ Plugin directory not found!\n";
    exit(1);
}

// Get all plugin directories
$plugins = array_filter(glob($plugin_dir . '/*'), 'is_dir');
echo "📊 Found " . count($plugins) . " plugin directories\n\n";

$results = [];
$critical_issues = [];
$warnings = [];

foreach ($plugins as $plugin_path) {
    $plugin_name = basename($plugin_path);
    echo "🔍 Checking: $plugin_name\n";
    
    $plugin_result = [
        'name' => $plugin_name,
        'path' => $plugin_path,
        'tests' => [],
        'status' => 'unknown'
    ];
    
    // Test 1: Main plugin file exists
    $main_files = ['plugin-name.php', $plugin_name . '.php', 'index.php'];
    
    // Add common naming variations for custom plugins
    if (is_custom_plugin($plugin_name)) {
        $main_files[] = 'freeride-investor-' . $plugin_name . '.php';
        $main_files[] = 'freeride-' . $plugin_name . '.php';
        $main_files[] = 'frtc-' . $plugin_name . '.php';
        
        // Handle specific naming patterns
        if ($plugin_name === 'freeride-smart-dashboard') {
            $main_files[] = 'freeride-investor-smart-dashboard.php';
        }
        if ($plugin_name === 'freerideinvestor') {
            $main_files[] = 'freeride-investor.php';
        }
        if ($plugin_name === 'freerideinvestor-db-setup') {
            $main_files[] = 'freerideinvest-database-setup.php';
        }
        if ($plugin_name === 'freerideinvestor-test') {
            $main_files[] = 'freeride-investor.php';
        }
        if ($plugin_name === 'habit-tracker-disabled') {
            $main_files[] = 'habit-tracker.php';
            $main_files[] = 'habit-tracker-disabled.php';
        }
        if ($plugin_name === 'wpforms-lite') {
            $main_files[] = 'wpforms.php';
        }
    }
    
    $main_file_found = false;
    $main_file_path = '';
    
    // Debug: Show what files we're checking (only for troubleshooting)
    // if ($plugin_name === 'habit-tracker-disabled') {
    //     echo "  🔍 Checking files: " . implode(', ', $main_files) . "\n";
    //     echo "  📁 Plugin path: $plugin_path\n";
    // }
    
    foreach ($main_files as $file) {
        if (file_exists($plugin_path . '/' . $file)) {
            $main_file_found = true;
            $main_file_path = $file;
            break;
        }
    }
    
    if ($main_file_found) {
        $plugin_result['tests']['main_file'] = ['status' => 'pass', 'message' => "Main file: $main_file_path"];
        echo "  ✅ Main file: $main_file_path\n";
    } else {
        $plugin_result['tests']['main_file'] = ['status' => 'fail', 'message' => 'No main plugin file found'];
        echo "  ❌ Main file: NOT FOUND\n";
        $critical_issues[] = "No main plugin file in: $plugin_name";
    }
    
    // Test 2: Check if it's a custom plugin
    $is_custom = is_custom_plugin($plugin_name);
    if ($is_custom) {
        echo "  🎯 Custom plugin detected\n";
        $plugin_result['tests']['custom'] = ['status' => 'pass', 'message' => 'Custom plugin'];
    }
    
    // Test 3: Check for required directories
    $required_dirs = ['assets', 'includes', 'js', 'css'];
    $found_dirs = [];
    
    foreach ($required_dirs as $dir) {
        if (is_dir($plugin_path . '/' . $dir)) {
            $found_dirs[] = $dir;
        }
    }
    
    if (!empty($found_dirs)) {
        $plugin_result['tests']['directories'] = ['status' => 'pass', 'message' => 'Directories: ' . implode(', ', $found_dirs)];
        echo "  ✅ Directories: " . implode(', ', $found_dirs) . "\n";
    } else {
        $plugin_result['tests']['directories'] = ['status' => 'warning', 'message' => 'No standard directories found'];
        echo "  ⚠️  Directories: None found\n";
        $warnings[] = "No standard directories in: $plugin_name";
    }
    
    // Test 4: Check for PHP files
    $php_files = glob($plugin_path . '/*.php');
    if (count($php_files) > 0) {
        $plugin_result['tests']['php_files'] = ['status' => 'pass', 'message' => count($php_files) . ' PHP files found'];
        echo "  ✅ PHP files: " . count($php_files) . " found\n";
    } else {
        $plugin_result['tests']['php_files'] = ['status' => 'fail', 'message' => 'No PHP files found'];
        echo "  ❌ PHP files: None found\n";
        $critical_issues[] = "No PHP files in: $plugin_name";
    }
    
    // Test 5: Check for JavaScript files
    $js_files = glob($plugin_path . '/**/*.js', GLOB_BRACE);
    if (count($js_files) > 0) {
        $plugin_result['tests']['js_files'] = ['status' => 'pass', 'message' => count($js_files) . ' JS files found'];
        echo "  ✅ JS files: " . count($js_files) . " found\n";
    } else {
        $plugin_result['tests']['js_files'] = ['status' => 'info', 'message' => 'No JS files found'];
        echo "  ℹ️  JS files: None found\n";
    }
    
    // Test 6: Check for CSS files
    $css_files = glob($plugin_path . '/**/*.css', GLOB_BRACE);
    if (count($css_files) > 0) {
        $plugin_result['tests']['css_files'] = ['status' => 'pass', 'message' => count($css_files) . ' CSS files found'];
        echo "  ✅ CSS files: " . count($css_files) . " found\n";
    } else {
        $plugin_result['tests']['css_files'] = ['status' => 'info', 'message' => 'No CSS files found'];
        echo "  ℹ️  CSS files: None found\n";
    }
    
    // Test 7: Check for README or documentation
    $docs = ['README.md', 'readme.txt', 'README.txt', 'documentation.md'];
    $doc_found = false;
    
    foreach ($docs as $doc) {
        if (file_exists($plugin_path . '/' . $doc)) {
            $doc_found = true;
            break;
        }
    }
    
    if ($doc_found) {
        $plugin_result['tests']['documentation'] = ['status' => 'pass', 'message' => 'Documentation found'];
        echo "  ✅ Documentation: Found\n";
    } else {
        $plugin_result['tests']['documentation'] = ['status' => 'warning', 'message' => 'No documentation found'];
        echo "  ⚠️  Documentation: None found\n";
        $warnings[] = "No documentation in: $plugin_name";
    }
    
    // Determine overall status
    $plugin_result['status'] = determine_plugin_status($plugin_result['tests']);
    
    echo "  📊 Status: " . strtoupper($plugin_result['status']) . "\n\n";
    
    $results[] = $plugin_result;
}

// Generate summary
$summary = generate_health_summary($results);

echo "===============================\n";
echo "📊 HEALTH CHECK SUMMARY\n";
echo "===============================\n";
echo "Total Plugins: " . $summary['total'] . "\n";
echo "✅ Healthy: " . $summary['healthy'] . "\n";
echo "⚠️  Warnings: " . $summary['warnings'] . "\n";
echo "❌ Critical: " . $summary['critical'] . "\n\n";

if (!empty($critical_issues)) {
    echo "❌ CRITICAL ISSUES:\n";
    foreach ($critical_issues as $issue) {
        echo "  - $issue\n";
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
    echo "🎉 All plugins are structurally sound!\n";
} else {
    echo "🚨 Some plugins have critical structural issues.\n";
}

echo "\nHealth check complete!\n";

/**
 * Determine if plugin is custom
 */
function is_custom_plugin($plugin_name) {
    $custom_patterns = [
        'freeride',
        'frtc',
        'smartstock',
        'tbow',
        'habit-tracker',
        'wpforms'
    ];
    
    foreach ($custom_patterns as $pattern) {
        if (stripos($plugin_name, $pattern) !== false) {
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
    
    return 'healthy';
}

/**
 * Generate health summary
 */
function generate_health_summary($results) {
    $total_plugins = count($results);
    $healthy = 0;
    $warnings = 0;
    $critical = 0;
    
    foreach ($results as $result) {
        switch ($result['status']) {
            case 'healthy':
                $healthy++;
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
        'healthy' => $healthy,
        'warnings' => $warnings,
        'critical' => $critical
    ];
}
