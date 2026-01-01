<?php
/**
 * Plugin Testing Framework for FreeRide Investor Website
 * 
 * This file provides a comprehensive testing framework to verify all plugins
 * are working correctly after security updates.
 * 
 * Usage: Add this to your theme's functions.php or run as a standalone script
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class FreeRide_Plugin_Testing_Framework {
    
    private $test_results = [];
    private $critical_errors = [];
    private $warnings = [];
    
    public function __construct() {
        \add_action('admin_menu', [$this, 'add_testing_page']);
        \add_action('wp_ajax_run_plugin_tests', [$this, 'run_tests_ajax']);
        \add_action('wp_ajax_test_specific_plugin', [$this, 'test_specific_plugin_ajax']);
    }
    
    /**
     * Add testing page to admin menu
     */
    public function add_testing_page() {
        \add_menu_page(
            'Plugin Testing',
            'Plugin Testing',
            'manage_options',
            'freeride-plugin-testing',
            [$this, 'render_testing_page'],
            'dashicons-testing',
            99
        );
    }
    
    /**
     * Render the testing page
     */
    public function render_testing_page() {
        ?>
        <div class="wrap">
            <h1>🔍 FreeRide Plugin Testing Framework</h1>
            
            <div class="notice notice-info">
                <p><strong>Purpose:</strong> This framework tests all plugins to ensure they're working correctly after security updates.</p>
            </div>
            
            <div class="card">
                <h2>Quick Actions</h2>
                <button id="run-all-tests" class="button button-primary">🚀 Run All Plugin Tests</button>
                <button id="test-custom-plugins" class="button button-secondary">🎯 Test Custom Plugins Only</button>
                <button id="test-third-party" class="button button-secondary">📦 Test Third-Party Plugins</button>
            </div>
            
            <div id="test-results" class="card" style="display: none;">
                <h2>Test Results</h2>
                <div id="results-content"></div>
            </div>
            
            <div id="plugin-status" class="card">
                <h2>Plugin Status Overview</h2>
                <div id="status-content">
                    <p>Click "Run All Plugin Tests" to get started.</p>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#run-all-tests').click(function() {
                runPluginTests('all');
            });
            
            $('#test-custom-plugins').click(function() {
                runPluginTests('custom');
            });
            
            $('#test-third-party').click(function() {
                runPluginTests('third-party');
            });
            
            function runPluginTests(type) {
                $('#test-results').show();
                $('#results-content').html('<p>Running tests...</p>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'run_plugin_tests',
                        test_type: type,
                        nonce: '<?php echo wp_create_nonce('plugin_testing'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#results-content').html(response.data.html);
                            updatePluginStatus(response.data.status);
                        } else {
                            $('#results-content').html('<p class="error">Error: ' + response.data + '</p>');
                        }
                    },
                    error: function() {
                        $('#results-content').html('<p class="error">AJAX request failed.</p>');
                    }
                });
            }
            
            function updatePluginStatus(status) {
                $('#status-content').html(status);
            }
        });
        </script>
        <?php
    }
    
    /**
     * Run all plugin tests via AJAX
     */
    public function run_tests_ajax() {
        \check_ajax_referer('plugin_testing', 'nonce');
        
        if (!\current_user_can('manage_options')) {
            \wp_send_json_error('Insufficient permissions');
        }
        
        $test_type = $_POST['test_type'] ?? 'all';
        $results = $this->run_comprehensive_tests($test_type);
        
        \wp_send_json_success([
            'html' => $this->format_test_results($results),
            'status' => $this->format_status_summary($results)
        ]);
    }
    
    /**
     * Run comprehensive plugin tests
     */
    public function run_comprehensive_tests($test_type = 'all') {
        $this->test_results = [];
        $this->critical_errors = [];
        $this->warnings = [];
        
        // Get all active plugins
        $active_plugins = \get_option('active_plugins');
        $all_plugins = \get_plugins();
        
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            $plugin_name = $plugin_data['Name'];
            $is_active = in_array($plugin_file, $active_plugins);
            $is_custom = $this->is_custom_plugin($plugin_file);
            
            // Skip based on test type
            if ($test_type === 'custom' && !$is_custom) continue;
            if ($test_type === 'third-party' && $is_custom) continue;
            
            $this->test_single_plugin($plugin_file, $plugin_data, $is_active, $is_custom);
        }
        
        return [
            'results' => $this->test_results,
            'critical_errors' => $this->critical_errors,
            'warnings' => $this->warnings,
            'summary' => $this->generate_summary()
        ];
    }
    
    /**
     * Test a single plugin
     */
    private function test_single_plugin($plugin_file, $plugin_data, $is_active, $is_custom) {
        $plugin_name = $plugin_data['Name'];
        $results = [
            'name' => $plugin_name,
            'file' => $plugin_file,
            'active' => $is_active,
            'custom' => $is_custom,
            'tests' => [],
            'status' => 'unknown'
        ];
        
        // Test 1: File existence and readability
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        if (file_exists($plugin_path) && is_readable($plugin_path)) {
            $results['tests']['file_access'] = ['status' => 'pass', 'message' => 'File accessible'];
        } else {
            $results['tests']['file_access'] = ['status' => 'fail', 'message' => 'File not accessible'];
            $this->critical_errors[] = "Plugin file not accessible: $plugin_name";
        }
        
        // Test 2: PHP syntax validation
        if ($this->test_php_syntax($plugin_path)) {
            $results['tests']['php_syntax'] = ['status' => 'pass', 'message' => 'PHP syntax valid'];
        } else {
            $results['tests']['php_syntax'] = ['status' => 'fail', 'message' => 'PHP syntax errors'];
            $this->critical_errors[] = "PHP syntax errors in: $plugin_name";
        }
        
        // Test 3: Plugin header validation
        if ($this->test_plugin_headers($plugin_data)) {
            $results['tests']['headers'] = ['status' => 'pass', 'message' => 'Plugin headers valid'];
        } else {
            $results['tests']['headers'] = ['status' => 'fail', 'message' => 'Missing required headers'];
            $this->warnings[] = "Missing plugin headers in: $plugin_name";
        }
        
        // Test 4: Security checks
        $security_results = $this->test_plugin_security($plugin_path, $plugin_name);
        $results['tests']['security'] = $security_results;
        
        // Test 5: Database table checks (for custom plugins)
        if ($is_custom) {
            $db_results = $this->test_plugin_database($plugin_name);
            $results['tests']['database'] = $db_results;
        }
        
        // Test 6: Shortcode availability (if applicable)
        if ($this->has_shortcodes($plugin_data)) {
            $shortcode_results = $this->test_shortcodes($plugin_name);
            $results['tests']['shortcodes'] = $shortcode_results;
        }
        
        // Determine overall status
        $results['status'] = $this->determine_plugin_status($results['tests']);
        
        $this->test_results[] = $results;
    }
    
    /**
     * Test PHP syntax
     */
    private function test_php_syntax($file_path) {
        $output = [];
        $return_var = 0;
        exec("php -l " . escapeshellarg($file_path) . " 2>&1", $output, $return_var);
        return $return_var === 0;
    }
    
    /**
     * Test plugin headers
     */
    private function test_plugin_headers($plugin_data) {
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
    private function test_plugin_security($file_path, $plugin_name) {
        $content = file_get_contents($file_path);
        $issues = [];
        
        // Check for direct file access prevention
        if (!strpos($content, 'ABSPATH') && !strpos($content, 'defined(\'ABSPATH\')')) {
            $issues[] = 'Missing ABSPATH check';
        }
        
        // Check for SQL injection vulnerabilities
        if (preg_match('/\$_(GET|POST|REQUEST|COOKIE|SERVER)\s*\[.*\]\s*[^=]*=.*WHERE/i', $content)) {
            $issues[] = 'Potential SQL injection vulnerability';
        }
        
        // Check for command injection
        if (preg_match('/(system|exec|shell_exec|passthru|eval)\s*\(/i', $content)) {
            $issues[] = 'Potential command injection vulnerability';
        }
        
        if (empty($issues)) {
            return ['status' => 'pass', 'message' => 'Security checks passed'];
        } else {
            $this->warnings[] = "Security issues in $plugin_name: " . implode(', ', $issues);
            return ['status' => 'warning', 'message' => 'Security issues found: ' . implode(', ', $issues)];
        }
    }
    
    /**
     * Test plugin database tables
     */
    private function test_plugin_database($plugin_name) {
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
    private function has_shortcodes($plugin_data) {
        $description = $plugin_data['Description'] ?? '';
        return strpos($description, 'shortcode') !== false || 
               strpos($description, 'Shortcode') !== false;
    }
    
    /**
     * Test shortcode availability
     */
    private function test_shortcodes($plugin_name) {
        // This is a basic check - in a real implementation, you'd test actual shortcode registration
        return ['status' => 'info', 'message' => 'Shortcode availability check not implemented'];
    }
    
    /**
     * Determine if plugin is custom
     */
    private function is_custom_plugin($plugin_file) {
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
    private function determine_plugin_status($tests) {
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
    private function generate_summary() {
        $total_plugins = count($this->test_results);
        $passing = 0;
        $warnings = 0;
        $critical = 0;
        
        foreach ($this->test_results as $result) {
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
    
    /**
     * Format test results for display
     */
    private function format_test_results($results) {
        $html = '<div class="plugin-test-results">';
        
        foreach ($results['results'] as $plugin_result) {
            $status_class = 'status-' . $plugin_result['status'];
            $status_icon = $this->get_status_icon($plugin_result['status']);
            
            $html .= '<div class="plugin-result ' . $status_class . '">';
            $html .= '<h3>' . $status_icon . ' ' . esc_html($plugin_result['name']) . '</h3>';
            $html .= '<p><strong>Status:</strong> ' . ucfirst($plugin_result['status']) . '</p>';
            $html .= '<p><strong>File:</strong> ' . esc_html($plugin_result['file']) . '</p>';
            $html .= '<p><strong>Active:</strong> ' . ($plugin_result['active'] ? 'Yes' : 'No') . '</p>';
            $html .= '<p><strong>Custom:</strong> ' . ($plugin_result['custom'] ? 'Yes' : 'No') . '</p>';
            
            $html .= '<h4>Test Results:</h4>';
            $html .= '<ul>';
            foreach ($plugin_result['tests'] as $test_name => $test_result) {
                $test_status_class = 'test-' . $test_result['status'];
                $html .= '<li class="' . $test_status_class . '">';
                $html .= '<strong>' . ucfirst($test_name) . ':</strong> ';
                $html .= esc_html($test_result['message']);
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Format status summary
     */
    private function format_status_summary($results) {
        $summary = $results['summary'];
        
        $html = '<div class="status-summary">';
        $html .= '<h3>Overall Status</h3>';
        $html .= '<p><strong>Total Plugins:</strong> ' . $summary['total'] . '</p>';
        $html .= '<p><strong>Passing:</strong> <span class="status-pass">' . $summary['passing'] . '</span></p>';
        $html .= '<p><strong>Warnings:</strong> <span class="status-warning">' . $summary['warnings'] . '</span></p>';
        $html .= '<p><strong>Critical:</strong> <span class="status-critical">' . $summary['critical'] . '</span></p>';
        
        if (!empty($results['critical_errors'])) {
            $html .= '<h4>Critical Errors:</h4>';
            $html .= '<ul class="critical-errors">';
            foreach ($results['critical_errors'] as $error) {
                $html .= '<li>' . esc_html($error) . '</li>';
            }
            $html .= '</ul>';
        }
        
        if (!empty($results['warnings'])) {
            $html .= '<h4>Warnings:</h4>';
            $html .= '<ul class="warnings">';
            foreach ($results['warnings'] as $warning) {
                $html .= '<li>' . esc_html($warning) . '</li>';
            }
            $html .= '</ul>';
        }
        
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Get status icon
     */
    private function get_status_icon($status) {
        switch ($status) {
            case 'pass':
                return '✅';
            case 'warning':
                return '⚠️';
            case 'critical':
                return '❌';
            default:
                return '❓';
        }
    }
}

// Initialize the testing framework
new FreeRide_Plugin_Testing_Framework();
