<?php
/**
 * Post Scheduler - Manages automated posting schedules
 */

if (!defined('ABSPATH')) {
    exit;
}

class PostScheduler {

    public function __construct() {
        add_action('tpa_schedule_posts', array($this, 'schedule_posts'));
        add_filter('cron_schedules', array($this, 'add_cron_schedules'));
    }

    /**
     * Add custom cron schedules
     */
    public function add_cron_schedules($schedules) {
        $schedules['weekly'] = array(
            'interval' => 604800, // 7 days
            'display' => __('Once Weekly')
        );

        $schedules['monthly'] = array(
            'interval' => 2635200, // 30 days (approximate)
            'display' => __('Once Monthly')
        );

        return $schedules;
    }

    /**
     * Schedule automated posts
     */
    public function schedule_posts() {
        // Check if auto-posting is enabled
        if (get_option('tpa_auto_post', '1') !== '1') {
            return;
        }

        $this->schedule_daily_plans();
        $this->schedule_weekly_reviews();
        $this->schedule_monthly_reports();
    }

    /**
     * Schedule daily trading plans
     */
    private function schedule_daily_plans() {
        $plan_generator = new PlanGenerator();

        // Generate plan for today
        $plan_data = $plan_generator->generate_daily_plan();

        if ($plan_data) {
            // Create the post
            $post_data = array(
                'post_title' => $plan_data['title'],
                'post_content' => $plan_data['content'],
                'post_status' => 'publish',
                'post_type' => 'trading_plan',
                'post_date' => current_time('mysql'),
                'post_date_gmt' => current_time('mysql', 1),
                'meta_input' => $plan_data['meta']
            );

            $post_id = wp_insert_post($post_data);

            if (!is_wp_error($post_id)) {
                // Set categories and tags
                if (isset($plan_data['categories'])) {
                    wp_set_post_categories($post_id, $plan_data['categories']);
                }

                if (isset($plan_data['tags'])) {
                    wp_set_post_tags($post_id, $plan_data['tags']);
                }

                // Log successful posting
                error_log("Daily trading plan scheduled and posted: {$plan_data['title']} (ID: {$post_id})");

                // Send notification to tradingrobotplug.com
                $this->notify_results_display($plan_data, 'daily_plan');
            }
        }
    }

    /**
     * Schedule weekly strategy reviews
     */
    private function schedule_weekly_reviews() {
        // Only run on Mondays
        if (wp_date('N') !== '1') {
            return;
        }

        $plan_generator = new PlanGenerator();
        $review_data = $plan_generator->generate_weekly_review();

        if ($review_data) {
            $post_data = array(
                'post_title' => $review_data['title'],
                'post_content' => $review_data['content'],
                'post_status' => 'publish',
                'post_type' => 'strategy_performance',
                'post_date' => current_time('mysql'),
                'post_date_gmt' => current_time('mysql', 1),
                'meta_input' => $review_data['meta']
            );

            $post_id = wp_insert_post($post_data);

            if (!is_wp_error($post_id)) {
                if (isset($review_data['categories'])) {
                    wp_set_post_categories($post_id, $review_data['categories']);
                }

                if (isset($review_data['tags'])) {
                    wp_set_post_tags($post_id, $review_data['tags']);
                }

                error_log("Weekly strategy review scheduled and posted: {$review_data['title']} (ID: {$post_id})");

                // Send notification to tradingrobotplug.com
                $this->notify_results_display($review_data, 'weekly_review');
            }
        }
    }

    /**
     * Schedule monthly performance reports
     */
    private function schedule_monthly_reports() {
        // Only run on the 1st of the month
        if (wp_date('j') !== '1') {
            return;
        }

        $plan_generator = new PlanGenerator();
        $report_data = $plan_generator->generate_monthly_report();

        if ($report_data) {
            $post_data = array(
                'post_title' => $report_data['title'],
                'post_content' => $report_data['content'],
                'post_status' => 'publish',
                'post_type' => 'strategy_performance',
                'post_date' => current_time('mysql'),
                'post_date_gmt' => current_time('mysql', 1),
                'meta_input' => $report_data['meta']
            );

            $post_id = wp_insert_post($post_data);

            if (!is_wp_error($post_id)) {
                if (isset($report_data['categories'])) {
                    wp_set_post_categories($post_id, $report_data['categories']);
                }

                if (isset($report_data['tags'])) {
                    wp_set_post_tags($post_id, $report_data['tags']);
                }

                error_log("Monthly performance report scheduled and posted: {$report_data['title']} (ID: {$post_id})");

                // Send notification to tradingrobotplug.com
                $this->notify_results_display($report_data, 'monthly_report');
            }
        }
    }

    /**
     * Notify tradingrobotplug.com of new content
     */
    private function notify_results_display($content_data, $content_type) {
        $results_api_url = 'https://tradingrobotplug.com/wp-json/trp/v1/update-results';

        $notification_data = array(
            'content_type' => $content_type,
            'title' => $content_data['title'],
            'content' => wp_strip_all_tags($content_data['content']),
            'meta' => $content_data['meta'],
            'timestamp' => current_time('mysql'),
            'source_site' => 'freerideinvestor.com'
        );

        $response = wp_remote_post($results_api_url, array(
            'body' => wp_json_encode($notification_data),
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . get_option('tpa_results_api_key', '')
            ),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            error_log("Failed to notify tradingrobotplug.com: " . $response->get_error_message());
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code !== 200) {
                error_log("Results notification failed with code {$response_code}");
            }
        }
    }

    /**
     * Get next scheduled post times
     */
    public function get_schedule_info() {
        return array(
            'daily_plan' => wp_next_scheduled('tpa_daily_trading_plan'),
            'weekly_review' => wp_next_scheduled('tpa_weekly_strategy_review'),
            'monthly_report' => wp_next_scheduled('tpa_monthly_performance_report')
        );
    }

    /**
     * Manually trigger a post generation
     */
    public function trigger_manual_post($post_type) {
        switch ($post_type) {
            case 'daily':
                $this->schedule_daily_plans();
                break;
            case 'weekly':
                $this->schedule_weekly_reviews();
                break;
            case 'monthly':
                $this->schedule_monthly_reports();
                break;
        }
    }

    /**
     * Check if posts are being generated properly
     */
    public function health_check() {
        $issues = array();

        // Check if events are scheduled
        if (!wp_next_scheduled('tpa_daily_trading_plan')) {
            $issues[] = 'Daily trading plan schedule missing';
        }

        if (!wp_next_scheduled('tpa_weekly_strategy_review')) {
            $issues[] = 'Weekly strategy review schedule missing';
        }

        if (!wp_next_scheduled('tpa_monthly_performance_report')) {
            $issues[] = 'Monthly performance report schedule missing';
        }

        // Check API connectivity
        $api_client = new TradingAPIClient();
        if (!$api_client->test_connection()) {
            $issues[] = 'Trading API connection failed';
        }

        return array(
            'healthy' => empty($issues),
            'issues' => $issues,
            'last_check' => current_time('mysql')
        );
    }
}