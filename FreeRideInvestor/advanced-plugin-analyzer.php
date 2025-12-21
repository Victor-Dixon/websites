<?php
/**
 * Advanced Plugin Analyzer for FreeRide Investor
 * 
 * Performs deep static analysis to catch potential issues
 * without requiring WordPress to be running
 */

echo "🔍 Advanced Plugin Analysis for FreeRide Investor\n";
echo "================================================\n\n";

$plugin_dir = __DIR__ . '/plugins';
$issues_found = [];
$warnings_found = [];
$recommendations = [];

// Function to analyze a PHP file for potential issues
function analyze_php_file($file_path) {
    $issues = [];
    $warnings = [];
    $content = file_get_contents($file_path);
    
    // Check for common WordPress issues
    $patterns = [
        // Security issues
        '/\$_(GET|POST|REQUEST)\[/' => 'Direct superglobal access (security risk)',
        '/mysql_query\(/' => 'Deprecated mysql_query function',
        '/eval\s*\(/' => 'eval() function detected (security risk)',
        '/exec\s*\(/' => 'exec() function detected (security risk)',
        '/system\s*\(/' => 'system() function detected (security risk)',
        
        // WordPress best practices
        '/wp_enqueue_script\([^)]*\'jquery\'/' => 'jQuery dependency should be explicit',
        '/wp_enqueue_style\([^)]*\'dashicons\'/' => 'Dashicons dependency should be explicit',
        '/add_action\([^)]*\'wp_head\'/' => 'wp_head action should be used carefully',
        
        // Common errors
        '/\$wpdb->query\([^)]*\$/' => 'Direct variable in SQL query (SQL injection risk)',
        '/wp_remote_get\([^)]*\$/' => 'Variable in wp_remote_get (validate URL)',
        '/wp_remote_post\([^)]*\$/' => 'Variable in wp_remote_post (validate URL)',
        
        // Performance issues
        '/get_posts\([^)]*\'posts_per_page\' => -1/' => 'Getting all posts (performance risk)',
        '/WP_Query\([^)]*\'posts_per_page\' => -1/' => 'Getting all posts (performance risk)',
    ];
    
    foreach ($patterns as $pattern => $message) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[0] as $match) {
                $line_num = substr_count(substr($content, 0, strpos($content, $match)), "\n") + 1;
                
                if (strpos($message, 'security') !== false || strpos($message, 'injection') !== false) {
                    $issues[] = "Line $line_num: $message - '$match'";
                } else {
                    $warnings[] = "Line $line_num: $message - '$match'";
                }
            }
        }
    }
    
    return ['issues' => $issues, 'warnings' => $warnings];
}

// Function to check plugin structure
function check_plugin_structure($plugin_path) {
    $issues = [];
    $warnings = [];
    
    // Check for required files
    $required_files = ['*.php']; // Main plugin file
    $recommended_files = ['readme.txt', 'README.md', 'uninstall.php'];
    
    $php_files = glob($plugin_path . '/*.php');
    if (empty($php_files)) {
        $issues[] = "No main PHP file found";
    }
    
    // Check for plugin header
    if (!empty($php_files)) {
        $main_file = $php_files[0];
        $content = file_get_contents($main_file);
        if (!preg_match('/Plugin Name:/', $content)) {
            $issues[] = "Missing 'Plugin Name:' header";
        }
        if (!preg_match('/Version:/', $content)) {
            $warnings[] = "Missing 'Version:' header";
        }
    }
    
    // Check for recommended files
    foreach ($recommended_files as $file) {
        if (!glob($plugin_path . '/' . $file)) {
            $warnings[] = "Missing recommended file: $file";
        }
    }
    
    // Check for proper directory structure
    $recommended_dirs = ['includes', 'assets', 'css', 'js', 'templates', 'admin'];
    $found_dirs = 0;
    foreach ($recommended_dirs as $dir) {
        if (is_dir($plugin_path . '/' . $dir)) {
            $found_dirs++;
        }
    }
    
    if ($found_dirs === 0) {
        $warnings[] = "No standard directories found (includes, assets, etc.)";
    }
    
    return ['issues' => $issues, 'warnings' => $warnings];
}

// Function to check for WordPress function usage
function check_wordpress_functions($file_path) {
    $content = file_get_contents($file_path);
    $wp_functions = [
        'wp_enqueue_script', 'wp_enqueue_style', 'add_action', 'add_filter',
        'register_activation_hook', 'register_deactivation_hook',
        'wp_remote_get', 'wp_remote_post', 'wp_insert_post', 'wp_update_post',
        'get_option', 'update_option', 'add_option', 'delete_option',
        'wp_create_user', 'wp_update_user', 'get_user_meta', 'update_user_meta',
        'wp_nonce_field', 'wp_verify_nonce', 'sanitize_text_field',
        'esc_html', 'esc_attr', 'esc_url', 'wp_kses'
    ];
    
    $found_functions = [];
    foreach ($wp_functions as $function) {
        if (preg_match_all("/\\b$function\\s*\\(/", $content, $matches)) {
            $found_functions[$function] = count($matches[0]);
        }
    }
    
    return $found_functions;
}

// Function to check for API usage
function check_api_usage($file_path) {
    $content = file_get_contents($file_path);
    $apis = [];
    
    // Check for API endpoints
    $api_patterns = [
        '/alphavantage\.co/' => 'Alpha Vantage API',
        '/api\.openai\.com/' => 'OpenAI API',
        '/finnhub\.io/' => 'Finnhub API',
        '/api\.twitter\.com/' => 'Twitter API',
        '/api\.reddit\.com/' => 'Reddit API',
    ];
    
    foreach ($api_patterns as $pattern => $api_name) {
        if (preg_match($pattern, $content)) {
            $apis[] = $api_name;
        }
    }
    
    return $apis;
}

echo "🔍 Analyzing all plugins...\n\n";

$plugin_folders = array_filter(glob($plugin_dir . '/*'), 'is_dir');
$total_issues = 0;
$total_warnings = 0;

foreach ($plugin_folders as $plugin_folder) {
    $plugin_name = basename($plugin_folder);
    echo "📦 Analyzing: $plugin_name\n";
    
    // Check plugin structure
    $structure_check = check_plugin_structure($plugin_folder);
    $issues_found[$plugin_name] = $structure_check['issues'];
    $warnings_found[$plugin_name] = $structure_check['warnings'];
    
    // Analyze PHP files
    $php_files = glob($plugin_folder . '/*.php');
    foreach ($php_files as $php_file) {
        $analysis = analyze_php_file($php_file);
        $issues_found[$plugin_name] = array_merge($issues_found[$plugin_name], $analysis['issues']);
        $warnings_found[$plugin_name] = array_merge($warnings_found[$plugin_name], $analysis['warnings']);
        
        // Check WordPress function usage
        $wp_functions = check_wordpress_functions($php_file);
        if (!empty($wp_functions)) {
            $recommendations[$plugin_name] = $wp_functions;
        }
        
        // Check API usage
        $apis = check_api_usage($php_file);
        if (!empty($apis)) {
            if (!isset($recommendations[$plugin_name])) {
                $recommendations[$plugin_name] = [];
            }
            $recommendations[$plugin_name]['apis'] = $apis;
        }
    }
    
    $plugin_issues = count($issues_found[$plugin_name]);
    $plugin_warnings = count($warnings_found[$plugin_name]);
    $total_issues += $plugin_issues;
    $total_warnings += $plugin_warnings;
    
    if ($plugin_issues > 0) {
        echo "  ❌ $plugin_issues critical issues\n";
    }
    if ($plugin_warnings > 0) {
        echo "  ⚠️  $plugin_warnings warnings\n";
    }
    if ($plugin_issues === 0 && $plugin_warnings === 0) {
        echo "  ✅ No issues found\n";
    }
    echo "\n";
}

// Summary
echo "📊 ANALYSIS SUMMARY\n";
echo "===================\n";
echo "Total Plugins: " . count($plugin_folders) . "\n";
echo "Critical Issues: $total_issues\n";
echo "Warnings: $total_warnings\n\n";

// Show critical issues
if ($total_issues > 0) {
    echo "🚨 CRITICAL ISSUES FOUND:\n";
    echo "========================\n";
    foreach ($issues_found as $plugin => $issues) {
        if (!empty($issues)) {
            echo "\n📦 $plugin:\n";
            foreach ($issues as $issue) {
                echo "  ❌ $issue\n";
            }
        }
    }
    echo "\n";
}

// Show warnings
if ($total_warnings > 0) {
    echo "⚠️  WARNINGS FOUND:\n";
    echo "==================\n";
    foreach ($warnings_found as $plugin => $warnings) {
        if (!empty($warnings)) {
            echo "\n📦 $plugin:\n";
            foreach ($warnings as $warning) {
                echo "  ⚠️  $warning\n";
            }
        }
    }
    echo "\n";
}

// Show recommendations
if (!empty($recommendations)) {
    echo "💡 RECOMMENDATIONS:\n";
    echo "===================\n";
    foreach ($recommendations as $plugin => $recs) {
        echo "\n📦 $plugin:\n";
        if (isset($recs['apis'])) {
            echo "  🔗 Uses APIs: " . implode(', ', $recs['apis']) . "\n";
            echo "     → Ensure API keys are configured\n";
            echo "     → Test API connectivity\n";
        }
        if (isset($recs['wp_enqueue_script'])) {
            echo "  📜 Enqueues scripts: " . $recs['wp_enqueue_script'] . " times\n";
            echo "     → Verify script dependencies\n";
        }
        if (isset($recs['add_action'])) {
            echo "  ⚡ Uses actions: " . $recs['add_action'] . " times\n";
            echo "     → Test action hooks work properly\n";
        }
    }
    echo "\n";
}

// Final assessment
echo "🎯 FINAL ASSESSMENT:\n";
echo "===================\n";

if ($total_issues === 0) {
    echo "✅ No critical issues found!\n";
    echo "✅ Plugins should work in WordPress\n";
} else {
    echo "❌ Critical issues found that need fixing\n";
    echo "❌ Plugins may not work properly in WordPress\n";
}

if ($total_warnings > 0) {
    echo "⚠️  $total_warnings warnings should be addressed for better quality\n";
}

echo "\n📋 NEXT STEPS:\n";
echo "1. Fix any critical issues\n";
echo "2. Address warnings for better code quality\n";
echo "3. Test in actual WordPress environment\n";
echo "4. Configure API keys for full functionality\n";
echo "5. Test user interactions and data flow\n\n";

echo "✅ Advanced analysis complete!\n";
?>
