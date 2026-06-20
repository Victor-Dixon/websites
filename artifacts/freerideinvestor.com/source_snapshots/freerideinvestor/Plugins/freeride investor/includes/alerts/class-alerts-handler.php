<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FRI_Alerts_Handler {

    /**
     * Initialize the alerts handler
     */
    public static function init() {
        register_activation_hook(__FILE__, [__CLASS__, 'create_alerts_table']);
        register_deactivation_hook(__FILE__, [__CLASS__, 'delete_alerts_table']);
    }

    /**
     * Create alerts table on plugin activation
     */
    public static function create_alerts_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fri_alerts';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT NOT NULL AUTO_INCREMENT,
            email VARCHAR(255) NOT NULL,
            stock_symbol VARCHAR(20) NOT NULL,
            alert_type VARCHAR(50) NOT NULL,
            condition_value FLOAT NOT NULL,
            active TINYINT DEFAULT 1 NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Delete alerts table on plugin deactivation
     */
    public static function delete_alerts_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fri_alerts';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }

    /**
     * Add a new alert
     *
     * @param string $email User's email.
     * @param string $symbol Stock symbol.
     * @param string $type Alert type (e.g., 'price_above').
     * @param float $value Condition value.
     * @return bool|int The inserted alert ID or false on failure.
     */
    public static function add_alert($email, $symbol, $type, $value) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fri_alerts';

        return $wpdb->insert(
            $table_name,
            [
                'email'           => $email,
                'stock_symbol'    => strtoupper($symbol),
                'alert_type'      => $type,
                'condition_value' => $value,
            ],
            ['%s', '%s', '%s', '%f']
        );
    }

    /**
     * Delete an alert
     *
     * @param int $alert_id The alert ID.
     * @return bool|int Rows affected or false on failure.
     */
    public static function delete_alert($alert_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fri_alerts';

        return $wpdb->delete($table_name, ['id' => $alert_id], ['%d']);
    }

    /**
     * Get all active alerts
     *
     * @return array List of active alerts.
     */
    public static function get_active_alerts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fri_alerts';

        return $wpdb->get_results("SELECT * FROM $table_name WHERE active = 1", ARRAY_A);
    }

    /**
     * Send notification email
     *
     * @param string $email Recipient email.
     * @param string $subject Email subject.
     * @param string $message Email body.
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public static function send_notification($email, $subject, $message) {
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        return wp_mail($email, $subject, $message, $headers);
    }
}

FRI_Alerts_Handler::init();
