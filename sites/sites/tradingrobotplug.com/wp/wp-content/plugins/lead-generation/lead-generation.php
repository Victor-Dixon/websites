<?php
/**
 * Lead Generation Plugin
 * Captures and manages beta user leads with automated follow-up
 * Version: 1.0.0
 * Author: Agent-7
 */

if (!defined('ABSPATH')) {
    exit;
}

class LeadGeneration {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_submit_lead', array($this, 'ajax_submit_lead'));
        add_action('wp_ajax_get_lead_stats', array($this, 'ajax_get_lead_stats'));
        add_shortcode('lead_capture_form', array($this, 'render_lead_form'));
        add_shortcode('lead_management', array($this, 'render_lead_management'));
        add_action('lead_followup_schedule', array($this, 'process_followup_emails'));

        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function init() {
        $this->create_tables();
        $this->schedule_followups();
    }

    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Leads table
        $leads_table = $wpdb->prefix . 'leads';
        $leads_sql = "CREATE TABLE $leads_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            first_name varchar(100),
            last_name varchar(100),
            phone varchar(20),
            company varchar(255),
            experience_level enum('beginner','intermediate','advanced','expert') DEFAULT 'beginner',
            interests text,
            source varchar(100) DEFAULT 'website',
            ip_address varchar(45),
            user_agent text,
            status enum('new','contacted','qualified','converted','unqualified') DEFAULT 'new',
            tags text,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_contact datetime,
            follow_up_count int DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY status (status),
            KEY created_at (created_at),
            KEY source (source)
        ) $charset_collate;";

        // Lead activities table
        $activities_table = $wpdb->prefix . 'lead_activities';
        $activities_sql = "CREATE TABLE $activities_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            lead_id mediumint(9) NOT NULL,
            activity_type varchar(50) NOT NULL,
            activity_data text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lead_id (lead_id),
            KEY activity_type (activity_type)
        ) $charset_collate;";

        // Email campaigns table
        $campaigns_table = $wpdb->prefix . 'lead_campaigns';
        $campaigns_sql = "CREATE TABLE $campaigns_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            subject varchar(255) NOT NULL,
            content longtext NOT NULL,
            trigger_event varchar(100),
            delay_hours int DEFAULT 0,
            status enum('draft','active','paused','completed') DEFAULT 'draft',
            sent_count int DEFAULT 0,
            open_count int DEFAULT 0,
            click_count int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY status (status),
            KEY trigger_event (trigger_event)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($leads_sql);
        dbDelta($activities_sql);
        dbDelta($campaigns_sql);
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'lead-generation-js',
            plugin_dir_url(__FILE__) . 'assets/js/lead-generation.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('lead-generation-js', 'leadGenerationAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lead_generation_nonce'),
        ));
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'lead-generation') !== false) {
            wp_enqueue_script(
                'lead-generation-admin-js',
                plugin_dir_url(__FILE__) . 'assets/js/admin.js',
                array('jquery'),
                '1.0.0',
                true
            );

            wp_enqueue_style(
                'lead-generation-admin-css',
                plugin_dir_url(__FILE__) . 'assets/css/admin.css',
                array(),
                '1.0.0'
            );
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            'Lead Generation',
            'Lead Generation',
            'manage_options',
            'lead-generation',
            array($this, 'admin_page'),
            'dashicons-groups',
            30
        );

        add_submenu_page(
            'lead-generation',
            'All Leads',
            'All Leads',
            'manage_options',
            'lead-generation',
            array($this, 'admin_page')
        );

        add_submenu_page(
            'lead-generation',
            'Email Campaigns',
            'Email Campaigns',
            'manage_options',
            'lead-generation-campaigns',
            array($this, 'campaigns_page')
        );

        add_submenu_page(
            'lead-generation',
            'Settings',
            'Settings',
            'manage_options',
            'lead-generation-settings',
            array($this, 'settings_page')
        );
    }

    public function admin_page() {
        include plugin_dir_path(__FILE__) . 'templates/admin/leads.php';
    }

    public function campaigns_page() {
        include plugin_dir_path(__FILE__) . 'templates/admin/campaigns.php';
    }

    public function settings_page() {
        include plugin_dir_path(__FILE__) . 'templates/admin/settings.php';
    }

    public function ajax_submit_lead() {
        check_ajax_referer('lead_generation_nonce', 'nonce');

        $email = sanitize_email($_POST['email']);
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name = sanitize_text_field($_POST['last_name'] ?? '');
        $experience_level = sanitize_text_field($_POST['experience_level'] ?? 'beginner');
        $interests = sanitize_textarea_field($_POST['interests'] ?? '');
        $source = sanitize_text_field($_POST['source'] ?? 'website');

        if (!is_email($email)) {
            wp_send_json_error('Invalid email address');
            return;
        }

        global $wpdb;
        $leads_table = $wpdb->prefix . 'leads';

        $result = $wpdb->insert(
            $leads_table,
            array(
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'experience_level' => $experience_level,
                'interests' => $interests,
                'source' => $source,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT']
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result) {
            // Log activity
            $this->log_activity($wpdb->insert_id, 'lead_created', array(
                'source' => $source,
                'experience_level' => $experience_level
            ));

            // Trigger welcome email
            $this->schedule_welcome_email($email, $first_name);

            wp_send_json_success(array(
                'message' => 'Thank you for joining our beta! Check your email for next steps.',
                'lead_id' => $wpdb->insert_id
            ));
        } else {
            if ($wpdb->last_error) {
                // Check for duplicate email
                if (strpos($wpdb->last_error, 'Duplicate entry') !== false) {
                    wp_send_json_error('This email is already registered for our beta.');
                    return;
                }
            }
            wp_send_json_error('Failed to register. Please try again.');
        }
    }

    public function ajax_get_lead_stats() {
        check_ajax_referer('lead_generation_nonce', 'nonce');

        global $wpdb;
        $leads_table = $wpdb->prefix . 'leads';

        // Get stats
        $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table");
        $new_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE status = 'new'");
        $qualified_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE status = 'qualified'");
        $converted_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE status = 'converted'");

        // Get leads by source
        $source_stats = $wpdb->get_results("
            SELECT source, COUNT(*) as count
            FROM $leads_table
            GROUP BY source
            ORDER BY count DESC
            LIMIT 10
        ", ARRAY_A);

        // Get recent leads
        $recent_leads = $wpdb->get_results("
            SELECT id, email, first_name, last_name, status, created_at
            FROM $leads_table
            ORDER BY created_at DESC
            LIMIT 10
        ", ARRAY_A);

        wp_send_json_success(array(
            'total_leads' => intval($total_leads),
            'new_leads' => intval($new_leads),
            'qualified_leads' => intval($qualified_leads),
            'converted_leads' => intval($converted_leads),
            'conversion_rate' => $total_leads > 0 ? round(($converted_leads / $total_leads) * 100, 1) : 0,
            'source_stats' => $source_stats,
            'recent_leads' => $recent_leads
        ));
    }

    public function render_lead_form($atts) {
        $atts = shortcode_atts(array(
            'source' => 'website',
            'style' => 'default'
        ), $atts);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/lead-form.php';
        return ob_get_clean();
    }

    public function render_lead_management($atts) {
        if (!current_user_can('manage_options')) {
            return '<p>You do not have permission to manage leads.</p>';
        }

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/lead-management.php';
        return ob_get_clean();
    }

    private function schedule_welcome_email($email, $first_name) {
        // Schedule welcome email (would integrate with email service)
        wp_schedule_single_event(
            time() + 300, // 5 minutes from now
            'send_welcome_email',
            array($email, $first_name)
        );
    }

    public function process_followup_emails() {
        // Process automated follow-up emails
        // This would integrate with an email service like Mailchimp, SendGrid, etc.
        global $wpdb;
        $leads_table = $wpdb->prefix . 'leads';

        // Find leads that need follow-up
        $leads = $wpdb->get_results("
            SELECT * FROM $leads_table
            WHERE status = 'new'
            AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
            AND (last_contact IS NULL OR last_contact < DATE_SUB(NOW(), INTERVAL 7 DAY))
        ");

        foreach ($leads as $lead) {
            // Send follow-up email
            $this->send_followup_email($lead);

            // Update last contact
            $wpdb->update(
                $leads_table,
                array(
                    'last_contact' => current_time('mysql'),
                    'follow_up_count' => $lead->follow_up_count + 1
                ),
                array('id' => $lead->id)
            );
        }
    }

    private function send_followup_email($lead) {
        // Placeholder for email sending functionality
        // In production, this would integrate with an email service
        $subject = "Follow-up: Your Beta Access to AI Trading Robots";
        $message = "Hi " . ($lead->first_name ?: 'there') . ",\n\nWe're excited to have you on our beta list! Here's what's happening with our AI trading robots...\n\nBest,\nThe TradingRobotPlug Team";

        // Log the email attempt
        $this->log_activity($lead->id, 'followup_email_sent', array(
            'subject' => $subject,
            'follow_up_number' => $lead->follow_up_count + 1
        ));

        // In production: wp_mail($lead->email, $subject, $message);
        error_log("Would send email to {$lead->email}: {$subject}");
    }

    private function log_activity($lead_id, $activity_type, $activity_data) {
        global $wpdb;
        $activities_table = $wpdb->prefix . 'lead_activities';

        $wpdb->insert(
            $activities_table,
            array(
                'lead_id' => $lead_id,
                'activity_type' => $activity_type,
                'activity_data' => wp_json_encode($activity_data)
            ),
            array('%d', '%s', '%s')
        );
    }

    private function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    private function schedule_followups() {
        if (!wp_next_scheduled('lead_followup_schedule')) {
            wp_schedule_event(time(), 'daily', 'lead_followup_schedule');
        }

        if (!wp_next_scheduled('send_welcome_email')) {
            // This will be scheduled on-demand when leads are submitted
        }
    }
}

// Initialize the plugin
new LeadGeneration();

// Activation hook
register_activation_hook(__FILE__, 'lead_generation_activate');
function lead_generation_activate() {
    // Schedule follow-ups
    if (!wp_next_scheduled('lead_followup_schedule')) {
        wp_schedule_event(time(), 'daily', 'lead_followup_schedule');
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'lead_generation_deactivate');
function lead_generation_deactivate() {
    wp_clear_scheduled_hook('lead_followup_schedule');
}

// Action hook for welcome emails
add_action('send_welcome_email', 'send_welcome_email_callback', 10, 2);
function send_welcome_email_callback($email, $first_name) {
    $subject = "Welcome to TradingRobotPlug Beta!";
    $message = "Hi " . ($first_name ?: 'there') . ",\n\nThank you for joining our beta program! We're building AI-powered trading robots and you're among the first to get access.\n\nStay tuned for updates!\n\nBest,\nThe TradingRobotPlug Team";

    // In production: wp_mail($email, $subject, $message);
    error_log("Welcome email sent to: $email");
}