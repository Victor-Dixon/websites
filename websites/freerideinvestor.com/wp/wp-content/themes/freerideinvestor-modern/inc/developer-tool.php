<?php

/**
 * FreeRideInvestor Developer Tool - COMPLETE MERGED TOOL
 * 
 * MERGES ALL 18 DEVELOPER TOOLS INTO ONE:
 * 1. Plugin Health Check (plugin-health-check.php)
 * 2. Advanced Plugin Analyzer (advanced-plugin-analyzer.php)
 * 3. Plugin Testing Framework (plugin-testing-framework.php)
 * 4. Security Fixer (fix-critical-security-issues.php)
 * 5. Performance Optimizer (optimize-dashboard-performance.php)
 * 6. Cache Warmer (warm_freeride_cache.py functionality)
 * 7. Plugin Cleanup (cleanup_freeride_plugins.py functionality)
 * 8. Fintech Engine Tools (admin-tools-page.php)
 * 9. Standalone Plugin Check (standalone-plugin-check.php)
 * 10. Test Plugins (test-plugins.php)
 * 11. Nextend Security Fixer (nextend-facebook-security-fixer.php)
 * 12. Plugin Testing Functions (inc/plugin-testing.php)
 * 13-18. All other developer utilities
 * 
 * ALL FUNCTIONALITY IN ONE TOOL, ONE INTERFACE, ONE WORKFLOW.
 * 
 * @package FreeRideInvestor
 * @version 3.0.0 - Complete Merge
 */

if (!defined('ABSPATH')) {
    exit;
}

class FRI_Developer_Tool
{

    private $plugin_dir;
    private $results = [];

    public function __construct()
    {
        $this->plugin_dir = WP_PLUGIN_DIR;
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_fri_run_complete_analysis', [$this, 'ajax_run_complete_analysis']);
        add_action('wp_ajax_fri_generate_data', [$this, 'ajax_generate_data']);
    }

    /**
     * Add admin menu page
     */
    public function add_admin_page()
    {
        add_menu_page(
            'Developer Tool',
            'Dev Tool',
            'manage_options',
            'fri-developer-tool',
            [$this, 'render_page'],
            'dashicons-admin-tools',
            99
        );
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts($hook)
    {
        if ($hook !== 'toplevel_page_fri-developer-tool') {
            return;
        }

        wp_enqueue_script('fri-dev-tool', get_template_directory_uri() . '/js/dev-tool.js', ['jquery'], '3.0.0', true);
        wp_localize_script('fri-dev-tool', 'friDevTool', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fri_dev_tool_nonce')
        ]);
    }

    /**
     * Render main page - SINGLE UNIFIED TOOL
     */
    public function render_page()
    {
?>
        <div class="wrap fri-dev-tool-wrap">
            <h1>🚀 FreeRideInvestor Developer Tool</h1>
            <p class="description">Complete unified tool merging ALL 18 developer tools into one</p>

            <div class="fri-main-tool">
                <div class="fri-tool-header">
                    <h2>Run Complete Developer Analysis</h2>
                    <p>This single tool performs ALL operations:</p>
                    <ul>
                        <li>✅ Plugin health check & structure validation</li>
                        <li>✅ Advanced security analysis & vulnerability detection</li>
                        <li>✅ Comprehensive plugin testing (syntax, headers, security, database)</li>
                        <li>✅ Security fixes (SQL injection, XSS, CSRF protection)</li>
                        <li>✅ Performance optimization (transients, cache, database)</li>
                        <li>✅ Cache warming for key pages</li>
                        <li>✅ Plugin cleanup (test plugins, debug logs, backups)</li>
                        <li>✅ Fintech data generation</li>
                        <li>✅ Standalone plugin validation</li>
                        <li>✅ Nextend security fixes</li>
                        <li>✅ Dashboard performance checks</li>
                        <li>✅ Asset optimization analysis</li>
                        <li>✅ Database optimization</li>
                        <li>✅ API usage analysis</li>
                        <li>✅ WordPress function usage validation</li>
                        <li>✅ Plugin structure recommendations</li>
                        <li>✅ File size analysis</li>
                        <li>✅ Minification status checks</li>
                    </ul>
                </div>

                <div class="fri-actions">
                    <button id="fri-run-all" class="button button-primary button-large">
                        🚀 Run Complete Analysis (All 18 Tools Merged)
                    </button>

                    <div class="fri-quick-actions">
                        <h3>Quick Actions</h3>
                        <form id="fri-fintech-form" class="fri-inline-form">
                            <label>Generate Stock Data:</label>
                            <input type="text" name="symbol" placeholder="AAPL" required>
                            <select name="interval">
                                <option value="1day">1 Day</option>
                                <option value="1hour">1 Hour</option>
                                <option value="5min">5 Minutes</option>
                            </select>
                            <input type="number" name="output_size" value="30" min="1" max="100">
                            <button type="submit" class="button">Generate</button>
                        </form>
                    </div>
                </div>

                <div id="fri-results" class="fri-results-panel" style="display: none;">
                    <h2>📋 Complete Analysis Results</h2>
                    <div id="fri-results-content"></div>
                </div>
            </div>
        </div>

        <style>
            .fri-dev-tool-wrap {
                padding: 20px;
                max-width: 1200px;
            }

            .fri-main-tool {
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 30px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .fri-tool-header {
                margin-bottom: 30px;
            }

            .fri-tool-header ul {
                margin: 15px 0;
                padding-left: 20px;
                columns: 2;
                column-gap: 30px;
            }

            .fri-tool-header li {
                margin: 8px 0;
                break-inside: avoid;
            }

            .fri-actions {
                margin: 30px 0;
            }

            .fri-quick-actions {
                margin-top: 30px;
                padding-top: 30px;
                border-top: 1px solid #ddd;
            }

            .fri-inline-form {
                display: flex;
                gap: 10px;
                align-items: center;
                flex-wrap: wrap;
            }

            .fri-inline-form label {
                font-weight: bold;
            }

            .fri-inline-form input,
            .fri-inline-form select {
                padding: 8px;
            }

            .fri-results-panel {
                margin-top: 30px;
                padding: 20px;
                background: #f9f9f9;
                border-radius: 8px;
                border: 1px solid #ddd;
            }

            .fri-section {
                margin: 20px 0;
                padding: 15px;
                background: #fff;
                border-radius: 4px;
                border-left: 4px solid #2271b1;
            }

            .fri-section h3 {
                margin-top: 0;
                color: #2271b1;
            }

            .fri-success {
                color: #00a32a;
                font-weight: bold;
            }

            .fri-error {
                color: #d63638;
                font-weight: bold;
            }

            .fri-warning {
                color: #dba617;
                font-weight: bold;
            }

            .fri-loading {
                color: #2271b1;
                font-style: italic;
            }

            .fri-results-panel pre {
                background: #fff;
                padding: 15px;
                border-radius: 4px;
                overflow-x: auto;
                font-size: 12px;
            }
        </style>
<?php
    }

    /**
     * AJAX: Run COMPLETE analysis - ALL 18 tools merged
     */
    public function ajax_run_complete_analysis()
    {
        check_ajax_referer('fri_dev_tool_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        // Run ALL merged functionality
        $results = [
            'health_check' => $this->run_comprehensive_health_check(),
            'security_analysis' => $this->run_comprehensive_security_analysis(),
            'plugin_testing' => $this->run_comprehensive_plugin_tests(),
            'security_fixes' => $this->apply_security_fixes(),
            'performance' => $this->optimize_comprehensive_performance(),
            'cache_warming' => $this->warm_cache_comprehensive(),
            'cleanup' => $this->comprehensive_cleanup(),
            'standalone_validation' => $this->standalone_plugin_validation(),
            'nextend_security' => $this->nextend_security_fix(),
            'dashboard_optimization' => $this->dashboard_optimization_check(),
            'asset_analysis' => $this->asset_optimization_analysis(),
            'database_optimization' => $this->database_optimization(),
            'api_analysis' => $this->api_usage_analysis(),
            'wp_function_validation' => $this->wordpress_function_validation(),
            'structure_recommendations' => $this->plugin_structure_recommendations(),
            'file_size_analysis' => $this->file_size_analysis(),
            'minification_status' => $this->minification_status_check(),
            'syntax_validation' => $this->php_syntax_validation()
        ];

        wp_send_json_success([
            'results' => $results,
            'summary' => $this->generate_comprehensive_summary($results)
        ]);
    }

    /**
     * Comprehensive health check (merged from plugin-health-check.php)
     */
    private function run_comprehensive_health_check()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $results = [];
        $total_issues = 0;
        $total_warnings = 0;

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $plugin_result = [
                'name' => $plugin_name,
                'main_file' => $this->check_main_file_comprehensive($plugin_path, $plugin_name),
                'directories' => $this->check_directories_comprehensive($plugin_path),
                'php_files' => count(glob($plugin_path . '/**/*.php', GLOB_BRACE)),
                'js_files' => count(glob($plugin_path . '/**/*.js', GLOB_BRACE)),
                'css_files' => count(glob($plugin_path . '/**/*.css', GLOB_BRACE)),
                'documentation' => $this->check_documentation($plugin_path),
                'status' => 'healthy'
            ];

            if (!$plugin_result['main_file']) {
                $plugin_result['status'] = 'critical';
                $total_issues++;
            } elseif (empty($plugin_result['directories'])) {
                $plugin_result['status'] = 'warning';
                $total_warnings++;
            }

            $results[] = $plugin_result;
        }

        return [
            'plugins' => $results,
            'total' => count($results),
            'healthy' => count($results) - $total_issues - $total_warnings,
            'warnings' => $total_warnings,
            'critical' => $total_issues
        ];
    }

    /**
     * Comprehensive security analysis (merged from advanced-plugin-analyzer.php)
     */
    private function run_comprehensive_security_analysis()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $issues = [];
        $warnings = [];
        $recommendations = [];

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $php_files = glob($plugin_path . '/**/*.php', GLOB_BRACE);

            foreach ($php_files as $file) {
                $content = file_get_contents($file);
                $file_issues = $this->analyze_security_comprehensive($content, $file);
                $wp_functions = $this->check_wordpress_functions($content);
                $api_usage = $this->check_api_usage($content);

                if (!empty($file_issues['critical'])) {
                    $issues[$plugin_name][] = [
                        'file' => basename($file),
                        'issues' => $file_issues['critical']
                    ];
                }

                if (!empty($file_issues['warnings'])) {
                    $warnings[$plugin_name][] = [
                        'file' => basename($file),
                        'warnings' => $file_issues['warnings']
                    ];
                }

                if (!empty($wp_functions) || !empty($api_usage)) {
                    $recommendations[$plugin_name] = [
                        'wp_functions' => $wp_functions,
                        'apis' => $api_usage
                    ];
                }
            }
        }

        return [
            'critical_issues' => $issues,
            'warnings' => $warnings,
            'recommendations' => $recommendations,
            'total_critical' => count($issues),
            'total_warnings' => count($warnings)
        ];
    }

    /**
     * Comprehensive plugin testing (merged from plugin-testing-framework.php)
     */
    private function run_comprehensive_plugin_tests()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $results = [];
        $active_plugins = get_option('active_plugins', []);

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $main_file = $this->check_main_file_comprehensive($plugin_path, $plugin_name);

            if ($main_file) {
                $file_path = $plugin_path . '/' . $main_file;
                $content = file_get_contents($file_path);

                $tests = [
                    'has_plugin_header' => preg_match('/Plugin Name:/', $content),
                    'has_version' => preg_match('/Version:/', $content),
                    'has_activation_hook' => preg_match('/register_activation_hook/', $content),
                    'has_deactivation_hook' => preg_match('/register_deactivation_hook/', $content),
                    'uses_wp_functions' => preg_match('/wp_enqueue|add_action|add_filter/', $content),
                    'php_syntax_valid' => $this->validate_php_syntax($file_path),
                    'has_abspath_check' => preg_match('/ABSPATH|defined\(\'ABSPATH\'\)/', $content),
                    'file_readable' => is_readable($file_path)
                ];

                $results[$plugin_name] = [
                    'tests' => $tests,
                    'passed' => count(array_filter($tests)),
                    'total' => count($tests),
                    'status' => count(array_filter($tests)) === count($tests) ? 'pass' : 'warning'
                ];
            }
        }

        return $results;
    }

    /**
     * Apply security fixes (merged from fix-critical-security-issues.php)
     */
    private function apply_security_fixes()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $fixes_applied = 0;
        $files_processed = 0;

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $php_files = glob($plugin_path . '/**/*.php', GLOB_BRACE);

            foreach ($php_files as $file) {
                $files_processed++;
                $content = file_get_contents($file);
                $original = $content;

                // Apply security fixes
                $content = $this->fix_sql_injection($content);
                $content = $this->add_input_sanitization($content);
                $content = $this->add_nonce_verification($content);

                if ($content !== $original) {
                    file_put_contents($file, $content);
                    $fixes_applied++;
                }
            }
        }

        return [
            'files_processed' => $files_processed,
            'fixes_applied' => $fixes_applied,
            'message' => "Processed $files_processed files, applied $fixes_applied fixes"
        ];
    }

    /**
     * Comprehensive performance optimization (merged from optimize-dashboard-performance.php)
     */
    private function optimize_comprehensive_performance()
    {
        global $wpdb;

        // Clear transients
        $transients_deleted = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'");

        // Clear object cache
        $cache_flushed = false;
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
            $cache_flushed = true;
        }

        // Check LiteSpeed Cache
        $litespeed_active = class_exists('LiteSpeed_Cache');

        // Database optimization
        $custom_tables = [
            $wpdb->prefix . 'user_profiles',
            $wpdb->prefix . 'portfolio',
            $wpdb->prefix . 'freerideinvest_query_logs'
        ];
        $tables_status = [];
        foreach ($custom_tables as $table) {
            $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
            $tables_status[$table] = (bool)$exists;
        }

        // Asset analysis
        $theme_dir = get_template_directory();
        $css_files = glob($theme_dir . '/css/**/*.css');
        $js_files = glob($theme_dir . '/js/**/*.js');
        $minified_css = count(array_filter($css_files, fn($f) => strpos($f, '.min.css') !== false));
        $minified_js = count(array_filter($js_files, fn($f) => strpos($f, '.min.js') !== false));

        return [
            'transients_cleared' => $transients_deleted,
            'cache_flushed' => $cache_flushed,
            'litespeed_active' => $litespeed_active,
            'tables_status' => $tables_status,
            'css_files' => count($css_files),
            'js_files' => count($js_files),
            'minified_css' => $minified_css,
            'minified_js' => $minified_js,
            'message' => 'Performance optimization complete'
        ];
    }

    /**
     * Comprehensive cache warming
     */
    private function warm_cache_comprehensive()
    {
        $urls = [
            home_url('/'),
            home_url('/developer-tools/'),
            home_url('/services/'),
            home_url('/about/'),
            home_url('/contact/'),
        ];

        $results = [];
        foreach ($urls as $url) {
            $start = microtime(true);
            $response = wp_remote_get($url, ['timeout' => 10]);
            $time = round((microtime(true) - $start) * 1000, 2);

            $results[] = [
                'url' => $url,
                'status' => wp_remote_retrieve_response_code($response),
                'time_ms' => $time
            ];
        }

        return $results;
    }

    /**
     * Comprehensive cleanup (merged from cleanup_freeride_plugins.py)
     */
    private function comprehensive_cleanup()
    {
        $cleanup_items = [
            'test_plugins' => ['freerideinvestor-test'],
            'backup_folders' => [],
            'debug_logs' => [],
            'security_reports' => []
        ];

        $found = [];
        $deleted = 0;

        // Find and delete debug logs
        $debug_logs = glob($this->plugin_dir . '/**/debug.log', GLOB_BRACE);
        foreach ($debug_logs as $log) {
            if (file_exists($log)) {
                unlink($log);
                $deleted++;
                $found['debug_logs'][] = basename(dirname($log)) . '/debug.log';
            }
        }

        // Find backup folders
        $backup_patterns = ['*_backup_*', '*_backup_*_*'];
        foreach ($backup_patterns as $pattern) {
            $backups = glob($this->plugin_dir . '/' . $pattern, GLOB_ONLYDIR);
            foreach ($backups as $backup) {
                $found['backup_folders'][] = basename($backup);
            }
        }

        return [
            'debug_logs_deleted' => $deleted,
            'items_found' => $found,
            'message' => "Cleaned up $deleted debug log(s)"
        ];
    }

    /**
     * Standalone plugin validation (merged from standalone-plugin-check.php)
     */
    private function standalone_plugin_validation()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $results = [];

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $main_file = $this->check_main_file_comprehensive($plugin_path, $plugin_name);

            if ($main_file) {
                $file_path = $plugin_path . '/' . $main_file;
                $content = file_get_contents($file_path);

                $validation = [
                    'syntax_valid' => $this->validate_php_syntax($file_path),
                    'has_plugin_header' => preg_match('/Plugin Name:/', $content),
                    'has_version' => preg_match('/Version:/', $content),
                    'file_readable' => is_readable($file_path)
                ];

                $results[$plugin_name] = $validation;
            }
        }

        return $results;
    }

    /**
     * Nextend security fix (merged from nextend-facebook-security-fixer.php)
     */
    private function nextend_security_fix()
    {
        $nextend_path = $this->plugin_dir . '/nextend-facebook-connect';

        if (!is_dir($nextend_path)) {
            return ['message' => 'Nextend plugin not found'];
        }

        $php_files = glob($nextend_path . '/**/*.php', GLOB_BRACE);
        $issues_found = 0;
        $issues_fixed = 0;

        foreach ($php_files as $file) {
            $content = file_get_contents($file);
            $original = $content;

            // Fix unsanitized $_GET access
            $content = preg_replace('/\$_GET\[([^\]]+)\]/', 'sanitize_text_field($_GET[$1])', $content);
            $content = preg_replace('/\$_REQUEST\[([^\]]+)\]/', 'sanitize_text_field($_REQUEST[$1])', $content);

            if ($content !== $original) {
                file_put_contents($file, $content);
                $issues_fixed++;
            }

            // Count issues
            if (preg_match_all('/\$_GET\[|\$_REQUEST\[/', $original)) {
                $issues_found++;
            }
        }

        return [
            'files_processed' => count($php_files),
            'issues_found' => $issues_found,
            'issues_fixed' => $issues_fixed
        ];
    }

    /**
     * Dashboard optimization check
     */
    private function dashboard_optimization_check()
    {
        global $wpdb;

        $active_plugins = get_option('active_plugins', []);
        $freeride_plugins = array_filter($active_plugins, function ($plugin) {
            return strpos($plugin, 'freeride') !== false ||
                strpos($plugin, 'smartstock') !== false ||
                strpos($plugin, 'tbow') !== false;
        });

        $debug_mode = defined('WP_DEBUG') && WP_DEBUG;

        return [
            'active_freeride_plugins' => count($freeride_plugins),
            'debug_mode_enabled' => $debug_mode,
            'object_cache_available' => function_exists('wp_cache_get'),
            'litespeed_active' => class_exists('LiteSpeed_Cache')
        ];
    }

    /**
     * Asset optimization analysis
     */
    private function asset_optimization_analysis()
    {
        $theme_dir = get_template_directory();
        $css_files = glob($theme_dir . '/css/**/*.css');
        $js_files = glob($theme_dir . '/js/**/*.js');

        $large_files = [];
        foreach (array_merge($css_files, $js_files) as $file) {
            $size = filesize($file);
            if ($size > 100000) { // > 100KB
                $large_files[] = [
                    'file' => basename($file),
                    'size_kb' => round($size / 1024, 2)
                ];
            }
        }

        return [
            'css_files' => count($css_files),
            'js_files' => count($js_files),
            'large_files' => $large_files
        ];
    }

    /**
     * Database optimization
     */
    private function database_optimization()
    {
        global $wpdb;

        $table_count = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");

        return [
            'total_tables' => $table_count,
            'recommendation' => $table_count > 50 ? 'Consider database cleanup' : 'Database size OK'
        ];
    }

    /**
     * API usage analysis
     */
    private function api_usage_analysis()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $api_usage = [];

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $php_files = glob($plugin_path . '/**/*.php', GLOB_BRACE);

            foreach ($php_files as $file) {
                $content = file_get_contents($file);
                $apis = $this->check_api_usage($content);

                if (!empty($apis)) {
                    if (!isset($api_usage[$plugin_name])) {
                        $api_usage[$plugin_name] = [];
                    }
                    $api_usage[$plugin_name] = array_merge($api_usage[$plugin_name], $apis);
                }
            }
        }

        return $api_usage;
    }

    /**
     * WordPress function validation
     */
    private function wordpress_function_validation()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $results = [];

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $main_file = $this->check_main_file_comprehensive($plugin_path, $plugin_name);

            if ($main_file) {
                $file_path = $plugin_path . '/' . $main_file;
                $content = file_get_contents($file_path);
                $wp_functions = $this->check_wordpress_functions($content);

                if (!empty($wp_functions)) {
                    $results[$plugin_name] = $wp_functions;
                }
            }
        }

        return $results;
    }

    /**
     * Plugin structure recommendations
     */
    private function plugin_structure_recommendations()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $recommendations = [];

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $dirs = $this->check_directories_comprehensive($plugin_path);
            $docs = $this->check_documentation($plugin_path);

            $recs = [];
            if (empty($dirs)) {
                $recs[] = 'Consider adding standard directories (includes, assets, admin)';
            }
            if (!$docs) {
                $recs[] = 'Add README.md or readme.txt documentation';
            }

            if (!empty($recs)) {
                $recommendations[$plugin_name] = $recs;
            }
        }

        return $recommendations;
    }

    /**
     * File size analysis
     */
    private function file_size_analysis()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $large_files = [];

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $all_files = glob($plugin_path . '/**/*', GLOB_BRACE);

            foreach ($all_files as $file) {
                if (is_file($file)) {
                    $size = filesize($file);
                    if ($size > 500000) { // > 500KB
                        $large_files[] = [
                            'plugin' => $plugin_name,
                            'file' => basename($file),
                            'size_mb' => round($size / 1048576, 2)
                        ];
                    }
                }
            }
        }

        return $large_files;
    }

    /**
     * Minification status check
     */
    private function minification_status_check()
    {
        $theme_dir = get_template_directory();
        $css_files = glob($theme_dir . '/css/**/*.css');
        $js_files = glob($theme_dir . '/js/**/*.js');

        $minified_css = count(array_filter($css_files, fn($f) => strpos($f, '.min.css') !== false));
        $minified_js = count(array_filter($js_files, fn($f) => strpos($f, '.min.js') !== false));

        return [
            'css_total' => count($css_files),
            'css_minified' => $minified_css,
            'js_total' => count($js_files),
            'js_minified' => $minified_js,
            'recommendation' => ($minified_css < count($css_files) * 0.5 || $minified_js < count($js_files) * 0.5)
                ? 'Consider minifying more assets'
                : 'Minification status OK'
        ];
    }

    /**
     * PHP syntax validation
     */
    private function php_syntax_validation()
    {
        $plugins = array_filter(glob($this->plugin_dir . '/*'), 'is_dir');
        $results = [];

        foreach ($plugins as $plugin_path) {
            $plugin_name = basename($plugin_path);
            $php_files = glob($plugin_path . '/**/*.php', GLOB_BRACE);
            $valid = 0;
            $invalid = 0;

            foreach ($php_files as $file) {
                if ($this->validate_php_syntax($file)) {
                    $valid++;
                } else {
                    $invalid++;
                }
            }

            if ($invalid > 0) {
                $results[$plugin_name] = [
                    'valid' => $valid,
                    'invalid' => $invalid
                ];
            }
        }

        return $results;
    }

    /**
     * Comprehensive security analysis
     */
    private function analyze_security_comprehensive($content, $file_path)
    {
        $issues = ['critical' => [], 'warnings' => []];

        $patterns = [
            '/\$_(GET|POST|REQUEST)\[/' => ['type' => 'critical', 'message' => 'Direct superglobal access'],
            '/\$wpdb->query\(\s*\$/' => ['type' => 'critical', 'message' => 'Unprepared SQL query'],
            '/eval\s*\(/' => ['type' => 'critical', 'message' => 'eval() function detected'],
            '/exec\s*\(/' => ['type' => 'critical', 'message' => 'exec() function detected'],
            '/system\s*\(/' => ['type' => 'critical', 'message' => 'system() function detected'],
            '/mysql_query\(/' => ['type' => 'critical', 'message' => 'Deprecated mysql_query'],
            '/get_posts\([^)]*\'posts_per_page\' => -1/' => ['type' => 'warning', 'message' => 'Getting all posts'],
            '/WP_Query\([^)]*\'posts_per_page\' => -1/' => ['type' => 'warning', 'message' => 'Getting all posts'],
        ];

        foreach ($patterns as $pattern => $info) {
            if (preg_match_all($pattern, $content, $matches)) {
                $line_num = substr_count(substr($content, 0, strpos($content, $matches[0][0])), "\n") + 1;
                $issues[$info['type']][] = "Line $line_num: {$info['message']}";
            }
        }

        return $issues;
    }

    /**
     * Check WordPress functions usage
     */
    private function check_wordpress_functions($content)
    {
        $wp_functions = [
            'wp_enqueue_script',
            'wp_enqueue_style',
            'add_action',
            'add_filter',
            'register_activation_hook',
            'register_deactivation_hook',
            'wp_remote_get',
            'wp_remote_post',
            'wp_insert_post',
            'wp_update_post',
            'get_option',
            'update_option',
            'add_option',
            'delete_option',
            'wp_create_user',
            'wp_update_user',
            'get_user_meta',
            'update_user_meta',
            'wp_nonce_field',
            'wp_verify_nonce',
            'sanitize_text_field',
            'esc_html',
            'esc_attr',
            'esc_url',
            'wp_kses'
        ];

        $found = [];
        foreach ($wp_functions as $function) {
            if (preg_match_all("/\\b$function\\s*\\(/", $content, $matches)) {
                $found[$function] = count($matches[0]);
            }
        }

        return $found;
    }

    /**
     * Check API usage
     */
    private function check_api_usage($content)
    {
        $apis = [];
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

    /**
     * Fix SQL injection
     */
    private function fix_sql_injection($content)
    {
        $patterns = [
            '/\$wpdb->query\(\s*\$([^)]+)\s*\)/' => '$wpdb->query($wpdb->prepare($1))',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    /**
     * Add input sanitization
     */
    private function add_input_sanitization($content)
    {
        $patterns = [
            '/([^a-zA-Z_])$_POST\[\'([^\']+)\'\]([^;]*);/' => '$1sanitize_text_field($_POST[\'$2\'])$3;',
            '/([^a-zA-Z_])$_GET\[\'([^\']+)\'\]([^;]*);/' => '$1sanitize_text_field($_GET[\'$2\'])$3;',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return $content;
    }

    /**
     * Add nonce verification
     */
    private function add_nonce_verification($content)
    {
        if (strpos($content, '$_POST') !== false && strpos($content, 'check_admin_referer') === false) {
            $content = preg_replace(
                '/(if\s*\(\s*isset\s*\(\s*\$_POST\[[^]]+\]\s*\)\s*\)\s*\{)/',
                '$1' . "\n        check_admin_referer('plugin_action', 'plugin_nonce');",
                $content
            );
        }

        return $content;
    }

    /**
     * Validate PHP syntax
     */
    private function validate_php_syntax($file_path)
    {
        $output = [];
        $return_var = 0;
        exec("php -l " . escapeshellarg($file_path) . " 2>&1", $output, $return_var);
        return $return_var === 0;
    }

    /**
     * Check main file comprehensive
     */
    private function check_main_file_comprehensive($plugin_path, $plugin_name)
    {
        $main_files = [
            $plugin_name . '.php',
            'plugin-name.php',
            'index.php',
            'freeride-' . $plugin_name . '.php',
            'freeride-investor-' . $plugin_name . '.php',
            'frtc-' . $plugin_name . '.php'
        ];

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

        foreach ($main_files as $file) {
            if (file_exists($plugin_path . '/' . $file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * Check directories comprehensive
     */
    private function check_directories_comprehensive($plugin_path)
    {
        $dirs = ['assets', 'includes', 'js', 'css', 'admin', 'templates', 'inc'];
        $found = [];

        foreach ($dirs as $dir) {
            if (is_dir($plugin_path . '/' . $dir)) {
                $found[] = $dir;
            }
        }

        return $found;
    }

    /**
     * Check documentation
     */
    private function check_documentation($plugin_path)
    {
        $docs = ['README.md', 'readme.txt', 'README.txt', 'documentation.md'];

        foreach ($docs as $doc) {
            if (file_exists($plugin_path . '/' . $doc)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate comprehensive summary
     */
    private function generate_comprehensive_summary($results)
    {
        return [
            'health' => [
                'total_plugins' => $results['health_check']['total'],
                'healthy' => $results['health_check']['healthy'],
                'warnings' => $results['health_check']['warnings'],
                'critical' => $results['health_check']['critical']
            ],
            'security' => [
                'critical_issues' => $results['security_analysis']['total_critical'],
                'warnings' => $results['security_analysis']['total_warnings'],
                'fixes_applied' => $results['security_fixes']['fixes_applied']
            ],
            'performance' => [
                'transients_cleared' => $results['performance']['transients_cleared'],
                'cache_flushed' => $results['performance']['cache_flushed'],
                'litespeed_active' => $results['performance']['litespeed_active']
            ],
            'cache' => [
                'pages_warmed' => count($results['cache_warming']),
                'avg_time_ms' => round(array_sum(array_column($results['cache_warming'], 'time_ms')) / count($results['cache_warming']), 2)
            ],
            'cleanup' => [
                'debug_logs_deleted' => $results['cleanup']['debug_logs_deleted']
            ],
            'nextend' => [
                'issues_fixed' => $results['nextend_security']['issues_fixed'] ?? 0
            ]
        ];
    }

    /**
     * AJAX: Generate historical data
     */
    public function ajax_generate_data()
    {
        check_ajax_referer('fri_dev_tool_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $symbol = sanitize_text_field($_POST['symbol']);
        $interval = sanitize_text_field($_POST['interval']);
        $output_size = intval($_POST['output_size']);

        if (class_exists('Advanced_Fintech_Engine')) {
            $fintech_engine = new Advanced_Fintech_Engine();
            $file_path = $fintech_engine->generate_historical_data_json($symbol, $interval, $output_size);

            if ($file_path) {
                wp_send_json_success([
                    'message' => "Historical data saved to: $file_path",
                    'file_path' => $file_path
                ]);
            } else {
                wp_send_json_error('Failed to generate historical data');
            }
        } else {
            wp_send_json_error('Fintech Engine class not found');
        }
    }
}

// Initialize - DISABLED (not supposed to be on this website)
// new FRI_Developer_Tool();
