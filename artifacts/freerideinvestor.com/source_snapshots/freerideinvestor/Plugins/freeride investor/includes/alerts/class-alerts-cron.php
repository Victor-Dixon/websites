<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class FRI_Alerts_Cron {

    /**
     * Initialize the cron job
     */
    public static function init() {
        add_action('fri_check_alerts_event', [__CLASS__, 'check_alerts']);
        register_activation_hook(__FILE__, [__CLASS__, 'schedule_cron']);
        register_deactivation_hook(__FILE__, [__CLASS__, 'unschedule_cron']);
    }

    /**
     * Schedule the cron job
     */
    public static function schedule_cron() {
        if (!wp_next_scheduled('fri_check_alerts_event')) {
            wp_schedule_event(time(), 'hourly', 'fri_check_alerts_event');
        }
    }

    /**
     * Unschedule the cron job
     */
    public static function unschedule_cron() {
        $timestamp = wp_next_scheduled('fri_check_alerts_event');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'fri_check_alerts_event');
        }
    }

    /**
     * Check all active alerts and send notifications
     */
    public static function check_alerts() {
        $alerts = FRI_Alerts_Handler::get_active_alerts();

        foreach ($alerts as $alert) {
            $symbol = $alert['stock_symbol'];
            $type = $alert['alert_type'];
            $condition_value = $alert['condition_value'];
            $email = $alert['email'];

            // Fetch current stock data
            $stock_data = FRI_Alpha_Vantage::get_stock_quote($symbol);
            if (is_wp_error($stock_data) || empty($stock_data['05. price'])) {
                continue;
            }

            $current_price = floatval($stock_data['05. price']);

            // Check alert condition
            $condition_met = false;
            $message = '';

            if ($type === 'price_above' && $current_price > $condition_value) {
                $condition_met = true;
                $message = "The stock price of $symbol has risen above $condition_value. Current price: $current_price.";
            } elseif ($type === 'price_below' && $current_price < $condition_value) {
                $condition_met = true;
                $message = "The stock price of $symbol has fallen below $condition_value. Current price: $current_price.";
            }

            if ($condition_met) {
                // Send notification
                FRI_Alerts_Handler::send_notification($email, "Stock Alert: $symbol", $message);

                // Deactivate the alert after triggering
                FRI_Alerts_Handler::delete_alert($alert['id']);
            }
        }
    }
}

FRI_Alerts_Cron::init();
