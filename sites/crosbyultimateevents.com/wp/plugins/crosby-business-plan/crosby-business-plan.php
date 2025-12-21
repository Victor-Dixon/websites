<?php

/**
 * Plugin Name: Crosby Ultimate Events - Business Plan
 * Plugin URI: https://crosbyultimateevents.com
 * Description: Displays the business plan for Crosby Ultimate Events. Includes shortcode and page template support.
 * Version: 1.0.0
 * Author: DaDudeKC
 * Author URI: https://dadudekc.com
 * License: GPL v2 or later
 * Text Domain: crosby-business-plan
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CROSBY_BP_VERSION', '1.0.0');
define('CROSBY_BP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CROSBY_BP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Plugin Class
 */
class Crosby_Business_Plan
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_shortcode('crosby_business_plan', array($this, 'business_plan_shortcode'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Load text domain for translations
        load_plugin_textdomain('crosby-business-plan', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Enqueue plugin styles
     */
    public function enqueue_styles()
    {
        // Always load styles on pages, or check if shortcode is in content
        global $post;
        $has_shortcode = false;

        if ($post && isset($post->post_content)) {
            $has_shortcode = has_shortcode($post->post_content, 'crosby_business_plan');
        }

        if (is_page() || $has_shortcode) {
            wp_enqueue_style(
                'crosby-business-plan-style',
                CROSBY_BP_PLUGIN_URL . 'assets/style.css',
                array(),
                CROSBY_BP_VERSION
            );
        }
    }

    /**
     * Business Plan Shortcode
     * Usage: [crosby_business_plan]
     */
    public function business_plan_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'section' => 'all', // Display specific section or 'all'
            'download' => 'true', // Show download link
        ), $atts);

        // Ensure template file exists
        $template_file = CROSBY_BP_PLUGIN_DIR . 'templates/business-plan-display.php';

        // Debug mode - show path if WP_DEBUG is enabled
        $debug_info = '';
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $debug_info = '<p style="font-size:12px; color:#999;">Looking for template at: ' . esc_html($template_file) . '<br>';
            $debug_info .= 'Plugin DIR constant: ' . esc_html(CROSBY_BP_PLUGIN_DIR) . '<br>';
            $debug_info .= 'Directory exists: ' . (is_dir(CROSBY_BP_PLUGIN_DIR) ? 'Yes' : 'No') . '<br>';
            $debug_info .= 'Templates dir exists: ' . (is_dir(CROSBY_BP_PLUGIN_DIR . 'templates/') ? 'Yes' : 'No') . '</p>';
        }

        if (!file_exists($template_file)) {
            $error_msg = '<div style="color:red; padding: 20px; border: 2px solid red; background: #ffe6e6; margin: 20px 0;">';
            $error_msg .= '<strong>Error: Business plan template not found.</strong><br>';
            $error_msg .= 'Expected location: <code>' . esc_html($template_file) . '</code><br><br>';
            $error_msg .= '<strong>Please check:</strong><ul>';
            $error_msg .= '<li>Plugin files were uploaded correctly</li>';
            $error_msg .= '<li>Template file exists at: <code>templates/business-plan-display.php</code></li>';
            $error_msg .= '<li>File permissions are correct (644 for files, 755 for directories)</li>';
            $error_msg .= '</ul>';
            $error_msg .= $debug_info;
            $error_msg .= '</div>';
            return $error_msg;
        }

        ob_start();
        // Extract $atts variables before including template
        extract($atts, EXTR_SKIP);
        // Include template with variables in scope
        include $template_file;
        $output = ob_get_clean();

        // Return output or error message
        if (empty($output) || trim($output) === '') {
            return '<p style="color:red; padding: 20px; border: 1px solid red;">Error: Business plan template returned empty content. Please check plugin files and ensure template has content.</p>';
        }

        return $output;
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu()
    {
        add_options_page(
            __('Business Plan Settings', 'crosby-business-plan'),
            __('Business Plan', 'crosby-business-plan'),
            'manage_options',
            'crosby-business-plan',
            array($this, 'admin_page')
        );
    }

    /**
     * Admin settings page
     */
    public function admin_page()
    {
?>
        <div class="wrap">
            <h1><?php _e('Business Plan Settings', 'crosby-business-plan'); ?></h1>
            <div class="card">
                <h2><?php _e('Usage Instructions', 'crosby-business-plan'); ?></h2>
                <p><?php _e('Display the business plan on any page or post using the shortcode:', 'crosby-business-plan'); ?></p>
                <code>[crosby_business_plan]</code>

                <h3><?php _e('Shortcode Options', 'crosby-business-plan'); ?></h3>
                <ul>
                    <li><code>[crosby_business_plan]</code> - <?php _e('Display full business plan', 'crosby-business-plan'); ?></li>
                    <li><code>[crosby_business_plan section="executive"]</code> - <?php _e('Display specific section', 'crosby-business-plan'); ?></li>
                    <li><code>[crosby_business_plan download="false"]</code> - <?php _e('Hide download link', 'crosby-business-plan'); ?></li>
                </ul>

                <h3><?php _e('Available Sections', 'crosby-business-plan'); ?></h3>
                <ul>
                    <li>executive</li>
                    <li>company</li>
                    <li>products</li>
                    <li>market</li>
                    <li>marketing</li>
                    <li>operations</li>
                    <li>financial</li>
                    <li>management</li>
                    <li>risks</li>
                    <li>growth</li>
                    <li>timeline</li>
                    <li>metrics</li>
                </ul>
            </div>
        </div>
<?php
    }
}

// Initialize plugin
new Crosby_Business_Plan();
