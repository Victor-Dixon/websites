<?php
/**
 * Debug script to capture HTTP 500 error details
 * Access via: https://freerideinvestor.com/wp-content/themes/freerideinvestor-modern/debug-500.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>Debug 500 Error</h1>";
echo "<h2>PHP Configuration</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Error Reporting: " . error_reporting() . "\n";
echo "Display Errors: " . ini_get('display_errors') . "\n";
echo "Log Errors: " . ini_get('log_errors') . "\n";
echo "Error Log: " . ini_get('error_log') . "\n";
echo "</pre>";

echo "<h2>Testing WordPress Load</h2>";
$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
echo "<p>WP Load Path: $wp_load_path</p>";
echo "<p>WP Load Exists: " . (file_exists($wp_load_path) ? 'YES' : 'NO') . "</p>";

if (file_exists($wp_load_path)) {
    echo "<p>Attempting to load WordPress...</p>";
    try {
        require_once $wp_load_path;
        echo "<p style='color: green;'>✓ WordPress loaded successfully!</p>";
        echo "<p>WordPress Version: " . get_bloginfo('version') . "</p>";
        echo "<p>Active Theme: " . wp_get_theme()->get('Name') . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error loading WordPress: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color: red;'>✗ Fatal Error loading WordPress: " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . "</p>";
        echo "<p>Line: " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
}

echo "<h2>Testing Theme Functions</h2>";
$functions_path = __DIR__ . '/functions.php';
echo "<p>Functions Path: $functions_path</p>";
echo "<p>Functions Exists: " . (file_exists($functions_path) ? 'YES' : 'NO') . "</p>";

if (file_exists($functions_path)) {
    echo "<p>Checking functions.php syntax...</p>";
    $output = [];
    $return_var = 0;
    exec("php -l \"$functions_path\" 2>&1", $output, $return_var);
    if ($return_var === 0) {
        echo "<p style='color: green;'>✓ Syntax check passed</p>";
    } else {
        echo "<p style='color: red;'>✗ Syntax errors found:</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
}

echo "<h2>Server Information</h2>";
echo "<pre>";
echo "SERVER_SOFTWARE: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "</pre>";

