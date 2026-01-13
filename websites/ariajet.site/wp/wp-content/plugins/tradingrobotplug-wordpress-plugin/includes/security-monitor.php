<?php
/**
 * Security Monitoring and Alerting System
 * Monitors security events and provides real-time alerting
 */

class SecurityMonitor {
    private $log_file;
    private $alert_email;
    private $alert_levels;

    public function __construct() {
        $this->log_file = WP_CONTENT_DIR . '/security-monitor.log';
        $this->alert_email = get_option('tradingrobotplug_security_alert_email', '');
        $this->alert_levels = [
            'CRITICAL' => true,  // Always alert
            'HIGH' => true,      // Always alert
            'MEDIUM' => false,   // Log only by default
            'LOW' => false       // Log only by default
        ];
    }

    /**
     * Log a security event
     */
    public function log_security_event($level, $event_type, $message, $context = []) {
        $timestamp = current_time('Y-m-d H:i:s');
        $user_id = get_current_user_id();
        $ip = $this->get_client_ip();

        $log_entry = [
            'timestamp' => $timestamp,
            'level' => $level,
            'event_type' => $event_type,
            'message' => $message,
            'user_id' => $user_id,
            'ip_address' => $ip,
            'context' => $context,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        // Write to log file
        $this->write_to_log($log_entry);

        // Send alerts for high-priority events
        if ($this->should_alert($level)) {
            $this->send_alert($log_entry);
        }

        // Store in database for dashboard
        $this->store_in_database($log_entry);
    }

    /**
     * Log authentication events
     */
    public function log_auth_event($success, $method, $details = []) {
        $level = $success ? 'LOW' : 'HIGH';
        $event_type = $success ? 'auth_success' : 'auth_failure';
        $message = $success ?
            "Successful authentication via $method" :
            "Failed authentication attempt via $method";

        $this->log_security_event($level, $event_type, $message, $details);
    }

    /**
     * Log API access events
     */
    public function log_api_access($endpoint, $method, $status_code, $response_time = null) {
        $level = $this->determine_api_access_level($status_code);
        $event_type = 'api_access';
        $message = "API access: $method $endpoint (Status: $status_code)";

        if ($response_time) {
            $message .= " - Response time: {$response_time}ms";
        }

        $context = [
            'endpoint' => $endpoint,
            'method' => $method,
            'status_code' => $status_code,
            'response_time' => $response_time
        ];

        $this->log_security_event($level, $event_type, $message, $context);
    }

    /**
     * Log suspicious activity
     */
    public function log_suspicious_activity($activity_type, $description, $severity = 'MEDIUM') {
        $event_type = 'suspicious_activity';
        $message = "Suspicious activity detected: $description";

        $this->log_security_event($severity, $event_type, $message, [
            'activity_type' => $activity_type
        ]);
    }

    /**
     * Check for brute force attacks
     */
    public function check_brute_force($identifier, $max_attempts = 5, $time_window = 300) {
        global $wpdb;

        $ip = $this->get_client_ip();
        $time_threshold = current_time('timestamp') - $time_window;

        $attempts = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}security_events
             WHERE ip_address = %s
             AND event_type = 'auth_failure'
             AND timestamp > %s",
            $ip, date('Y-m-d H:i:s', $time_threshold)
        ));

        if ($attempts >= $max_attempts) {
            $this->log_security_event('CRITICAL', 'brute_force_detected',
                "Brute force attack detected from IP: $ip", [
                    'attempts' => $attempts,
                    'time_window' => $time_window
                ]);

            // Could implement IP blocking here
            return true;
        }

        return false;
    }

    /**
     * Get security dashboard data
     */
    public function get_dashboard_data($days = 7) {
        global $wpdb;

        $time_threshold = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return [
            'total_events' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}security_events WHERE timestamp > %s",
                $time_threshold
            )),
            'critical_events' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}security_events
                 WHERE level = 'CRITICAL' AND timestamp > %s",
                $time_threshold
            )),
            'auth_failures' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}security_events
                 WHERE event_type = 'auth_failure' AND timestamp > %s",
                $time_threshold
            )),
            'recent_events' => $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}security_events
                 WHERE timestamp > %s
                 ORDER BY timestamp DESC LIMIT 10",
                $time_threshold
            ), ARRAY_A)
        ];
    }

    private function write_to_log($log_entry) {
        $log_line = json_encode($log_entry) . "\n";

        $result = file_put_contents($this->log_file, $log_line, FILE_APPEND | LOCK_EX);

        if ($result === false) {
            error_log("Failed to write to security log: " . $this->log_file);
        }
    }

    private function should_alert($level) {
        return isset($this->alert_levels[$level]) && $this->alert_levels[$level];
    }

    private function send_alert($log_entry) {
        if (empty($this->alert_email)) {
            return; // No alert email configured
        }

        $subject = "SECURITY ALERT: {$log_entry['level']} - {$log_entry['event_type']}";
        $message = $this->format_alert_message($log_entry);

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: Security Monitor <security@ariajet.site>'
        ];

        wp_mail($this->alert_email, $subject, $message, $headers);
    }

    private function format_alert_message($log_entry) {
        return "
        <h2>Security Alert - {$log_entry['level']}</h2>
        <p><strong>Event:</strong> {$log_entry['event_type']}</p>
        <p><strong>Message:</strong> {$log_entry['message']}</p>
        <p><strong>Time:</strong> {$log_entry['timestamp']}</p>
        <p><strong>IP Address:</strong> {$log_entry['ip_address']}</p>
        <p><strong>User ID:</strong> {$log_entry['user_id']}</p>
        <p><strong>Request URI:</strong> {$log_entry['request_uri']}</p>
        <hr>
        <pre>" . json_encode($log_entry['context'], JSON_PRETTY_PRINT) . "</pre>
        ";
    }

    private function store_in_database($log_entry) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'security_events';

        // Create table if it doesn't exist
        $this->ensure_table_exists();

        $wpdb->insert(
            $table_name,
            [
                'timestamp' => $log_entry['timestamp'],
                'level' => $log_entry['level'],
                'event_type' => $log_entry['event_type'],
                'message' => $log_entry['message'],
                'user_id' => $log_entry['user_id'],
                'ip_address' => $log_entry['ip_address'],
                'context' => json_encode($log_entry['context']),
                'request_uri' => $log_entry['request_uri'],
                'user_agent' => $log_entry['user_agent']
            ],
            [
                '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s'
            ]
        );
    }

    private function ensure_table_exists() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'security_events';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            level varchar(20) NOT NULL,
            event_type varchar(50) NOT NULL,
            message text NOT NULL,
            user_id bigint(20) UNSIGNED DEFAULT 0,
            ip_address varchar(45) DEFAULT '',
            context longtext DEFAULT '',
            request_uri text DEFAULT '',
            user_agent text DEFAULT '',
            PRIMARY KEY (id),
            INDEX idx_timestamp (timestamp),
            INDEX idx_level (level),
            INDEX idx_event_type (event_type),
            INDEX idx_ip (ip_address)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function get_client_ip() {
        $ip_headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ip_headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];

                // Handle comma-separated IPs (X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }

                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    private function determine_api_access_level($status_code) {
        if ($status_code >= 500) return 'HIGH';      // Server errors
        if ($status_code >= 400) return 'MEDIUM';    // Client errors
        if ($status_code >= 300) return 'LOW';       // Redirects
        return 'LOW';                                // Success
    }
}

// Global security monitor instance
global $security_monitor;
$security_monitor = new SecurityMonitor();

// Helper functions for easy access
function log_security_event($level, $event_type, $message, $context = []) {
    global $security_monitor;
    $security_monitor->log_security_event($level, $event_type, $message, $context);
}

function log_auth_event($success, $method, $details = []) {
    global $security_monitor;
    $security_monitor->log_auth_event($success, $method, $details);
}

function log_api_access($endpoint, $method, $status_code, $response_time = null) {
    global $security_monitor;
    $security_monitor->log_api_access($endpoint, $method, $status_code, $response_time);
}

function check_brute_force($identifier, $max_attempts = 5, $time_window = 300) {
    global $security_monitor;
    return $security_monitor->check_brute_force($identifier, $max_attempts, $time_window);
}