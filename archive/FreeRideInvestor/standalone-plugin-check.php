<?php
/**
 * Standalone Plugin Health Check for FreeRide Investor
 * 
 * This script checks all plugins without requiring WordPress to be running.
 * It validates file structure, syntax, and basic functionality.
 */

echo "🔍 FreeRide Investor Plugin Health Check\n";
echo "========================================\n\n";

$plugin_dir = __DIR__ . '/plugins';
$results = [];
$total_plugins = 0;
$healthy_plugins = 0;
$warning_plugins = 0;
$error_plugins = 0;

// Function to check if a file exists and is readable
function check_file_readable($file_path) {
    return file_exists($file_path) && is_readable($file_path);
}

// Function to validate PHP syntax
function validate_php_syntax($file_path) {
    $output = [];
    $return_var = 0;
    exec("php -l \"$file_path\" 2>&1", $output, $return_var);
    return $return_var === 0;
}

// Function to get plugin info from main file
function get_plugin_info($plugin_file) {
    if (!check_file_readable($plugin_file)) {
        return false;
    }
    
    $content = file_get_contents($plugin_file);
    $info = [];
    
    // Extract plugin header info
    if (preg_match('/Plugin Name:\s*(.+)/i', $content, $matches)) {
        $info['name'] = trim($matches[1]);
    }
    if (preg_match('/Version:\s*(.+)/i', $content, $matches)) {
        $info['version'] = trim($matches[1]);
    }
    if (preg_match('/Description:\s*(.+)/i', $content, $matches)) {
        $info['description'] = trim($matches[1]);
    }
    if (preg_match('/Author:\s*(.+)/i', $content, $matches)) {
        $info['author'] = trim($matches[1]);
    }
    
    return $info;
}

// Function to check plugin structure
function check_plugin_structure($plugin_path) {
    $issues = [];
    
    // Check for main plugin file
    $main_files = ['*.php', 'index.php'];
    $found_main = false;
    
    foreach (glob($plugin_path . '/*.php') as $file) {
        if (basename($file) !== 'index.php') {
            $found_main = true;
            break;
        }
    }
    
    if (!$found_main) {
        $issues[] = "No main plugin file found";
    }
    
    // Check for common directories
    $common_dirs = ['includes', 'assets', 'css', 'js', 'templates', 'admin'];
    $found_dirs = 0;
    
    foreach ($common_dirs as $dir) {
        if (is_dir($plugin_path . '/' . $dir)) {
            $found_dirs++;
        }
    }
    
    if ($found_dirs === 0) {
        $issues[] = "No standard directories found (includes, assets, etc.)";
    }
    
    // Check for README or documentation
    $doc_files = ['README.md', 'readme.txt', 'README.txt', 'CHANGELOG.md'];
    $found_doc = false;
    
    foreach ($doc_files as $doc) {
        if (check_file_readable($plugin_path . '/' . $doc)) {
            $found_doc = true;
            break;
        }
    }
    
    if (!$found_doc) {
        $issues[] = "No documentation file found";
    }
    
    return $issues;
}

// Scan plugins directory
if (!is_dir($plugin_dir)) {
    echo "❌ Error: Plugins directory not found at $plugin_dir\n";
    exit(1);
}

echo "Scanning plugins directory: $plugin_dir\n\n";

$plugin_folders = array_filter(glob($plugin_dir . '/*'), 'is_dir');

foreach ($plugin_folders as $plugin_folder) {
    $plugin_name = basename($plugin_folder);
    $total_plugins++;
    
    echo "Checking: $plugin_name\n";
    
    $plugin_result = [
        'name' => $plugin_name,
        'status' => 'healthy',
        'issues' => [],
        'info' => null
    ];
    
    // Find main plugin file
    $main_file = null;
    $php_files = glob($plugin_folder . '/*.php');
    
    foreach ($php_files as $php_file) {
        if (basename($php_file) !== 'index.php') {
            $main_file = $php_file;
            break;
        }
    }
    
    if (!$main_file) {
        $plugin_result['status'] = 'error';
        $plugin_result['issues'][] = "No main plugin file found";
        $error_plugins++;
        echo "  ❌ No main plugin file found\n";
        continue;
    }
    
    // Check file readability
    if (!check_file_readable($main_file)) {
        $plugin_result['status'] = 'error';
        $plugin_result['issues'][] = "Main plugin file not readable";
        $error_plugins++;
        echo "  ❌ Main plugin file not readable\n";
        continue;
    }
    
    // Validate PHP syntax
    if (!validate_php_syntax($main_file)) {
        $plugin_result['status'] = 'error';
        $plugin_result['issues'][] = "PHP syntax errors in main file";
        $error_plugins++;
        echo "  ❌ PHP syntax errors in main file\n";
        continue;
    }
    
    // Get plugin info
    $plugin_info = get_plugin_info($main_file);
    if ($plugin_info) {
        $plugin_result['info'] = $plugin_info;
        echo "  📋 Name: " . ($plugin_info['name'] ?? 'Unknown') . "\n";
        echo "  📋 Version: " . ($plugin_info['version'] ?? 'Unknown') . "\n";
        echo "  📋 Author: " . ($plugin_info['author'] ?? 'Unknown') . "\n";
    }
    
    // Check plugin structure
    $structure_issues = check_plugin_structure($plugin_folder);
    if (!empty($structure_issues)) {
        $plugin_result['status'] = 'warning';
        $plugin_result['issues'] = array_merge($plugin_result['issues'], $structure_issues);
        $warning_plugins++;
        
        foreach ($structure_issues as $issue) {
            echo "  ⚠️  $issue\n";
        }
    } else {
        $healthy_plugins++;
        echo "  ✅ All checks passed\n";
    }
    
    $results[] = $plugin_result;
    echo "\n";
}

// Summary
echo "📊 PLUGIN HEALTH CHECK SUMMARY\n";
echo "==============================\n";
echo "Total Plugins: $total_plugins\n";
echo "✅ Healthy: $healthy_plugins\n";
echo "⚠️  Warnings: $warning_plugins\n";
echo "❌ Errors: $error_plugins\n\n";

if ($error_plugins > 0) {
    echo "🚨 CRITICAL ISSUES FOUND:\n";
    foreach ($results as $result) {
        if ($result['status'] === 'error') {
            echo "- {$result['name']}: " . implode(', ', $result['issues']) . "\n";
        }
    }
    echo "\n";
}

if ($warning_plugins > 0) {
    echo "⚠️  WARNINGS FOUND:\n";
    foreach ($results as $result) {
        if ($result['status'] === 'warning') {
            echo "- {$result['name']}: " . implode(', ', $result['issues']) . "\n";
        }
    }
    echo "\n";
}

echo "🎯 RECOMMENDATIONS:\n";
echo "- Fix any critical errors before deployment\n";
echo "- Address warnings for better plugin organization\n";
echo "- Add documentation files for custom plugins\n";
echo "- Test plugins in WordPress environment for full functionality\n\n";

echo "✅ Plugin health check complete!\n";
