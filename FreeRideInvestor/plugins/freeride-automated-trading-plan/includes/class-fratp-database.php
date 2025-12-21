<?php
/**
 * Database Class
 * Handles database operations for trading plans
 */

if (!defined('ABSPATH')) {
    exit;
}

class FRATP_Database {
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fratp_trading_plans';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            symbol varchar(10) NOT NULL,
            date date NOT NULL,
            plan_data longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY symbol_date (symbol, date),
            KEY symbol (symbol),
            KEY date (date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Save trading plan
     * 
     * @param array $plan Plan data
     * @return int|false Plan ID or false on error
     */
    public function save_plan($plan) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fratp_trading_plans';
        
        $data = array(
            'symbol' => sanitize_text_field($plan['symbol']),
            'date' => sanitize_text_field($plan['date']),
            'plan_data' => wp_json_encode($plan),
        );
        
        $format = array('%s', '%s', '%s');
        
        // Check if plan exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE symbol = %s AND date = %s",
            $plan['symbol'],
            $plan['date']
        ));
        
        if ($existing) {
            // Update existing
            $result = $wpdb->update(
                $table_name,
                $data,
                array('id' => $existing),
                $format,
                array('%d')
            );
            return $existing;
        } else {
            // Insert new
            $result = $wpdb->insert($table_name, $data, $format);
            return $result ? $wpdb->insert_id : false;
        }
    }
    
    /**
     * Get plan for symbol and date
     * 
     * @param string $symbol Stock symbol
     * @param string $date Date (Y-m-d format)
     * @return array|false Plan data or false if not found
     */
    public function get_plan($symbol, $date) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fratp_trading_plans';
        
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT plan_data FROM $table_name WHERE symbol = %s AND date = %s",
            $symbol,
            $date
        ), ARRAY_A);
        
        if ($result) {
            return json_decode($result['plan_data'], true);
        }
        
        return false;
    }
    
    /**
     * Get latest plan for symbol
     * 
     * @param string $symbol Stock symbol
     * @return array|false Latest plan or false if not found
     */
    public function get_latest_plan($symbol) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fratp_trading_plans';
        
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT plan_data FROM $table_name WHERE symbol = %s ORDER BY date DESC LIMIT 1",
            $symbol
        ), ARRAY_A);
        
        if ($result) {
            return json_decode($result['plan_data'], true);
        }
        
        return false;
    }
    
    /**
     * Get plans for date range
     * 
     * @param string $symbol Stock symbol
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array Plans
     */
    public function get_plans_range($symbol, $start_date, $end_date) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fratp_trading_plans';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT plan_data FROM $table_name WHERE symbol = %s AND date BETWEEN %s AND %s ORDER BY date DESC",
            $symbol,
            $start_date,
            $end_date
        ), ARRAY_A);
        
        $plans = array();
        foreach ($results as $result) {
            $plans[] = json_decode($result['plan_data'], true);
        }
        
        return $plans;
    }
    
    /**
     * Delete old plans (cleanup)
     * 
     * @param int $days_old Delete plans older than this many days
     * @return int Number of plans deleted
     */
    public function delete_old_plans($days_old = 90) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fratp_trading_plans';
        $cutoff_date = date('Y-m-d', strtotime("-{$days_old} days"));
        
        return $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE date < %s",
            $cutoff_date
        ));
    }
}



