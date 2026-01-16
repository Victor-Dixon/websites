<?php
/**
 * Plugin Name: Strategy Marketplace
 * Plugin URI: https://tradingrobotplug.com
 * Description: Interactive marketplace for trading strategies and algorithms
 * Version: 1.0.0
 * Author: Agent-7
 * License: GPL v2 or later
 * Text Domain: strategy-marketplace
 */

if (!defined('ABSPATH')) {
    exit;
}

class StrategyMarketplace {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_shortcode('strategy_marketplace', array($this, 'render_marketplace'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_get_strategies', array($this, 'ajax_get_strategies'));
        add_action('wp_ajax_deploy_strategy', array($this, 'ajax_deploy_strategy'));
    }

    public function init() {
        // Register custom post type for strategies
        $this->register_strategy_post_type();
    }

    private function register_strategy_post_type() {
        register_post_type('trading_strategy', array(
            'labels' => array(
                'name' => 'Trading Strategies',
                'singular_name' => 'Trading Strategy',
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'show_in_rest' => true,
        ));

        // Register categories for strategies
        register_taxonomy('strategy_category', 'trading_strategy', array(
            'labels' => array(
                'name' => 'Strategy Categories',
                'singular_name' => 'Strategy Category',
            ),
            'hierarchical' => true,
            'show_in_rest' => true,
        ));
    }

    public function enqueue_scripts() {
        if (is_page_template('page-strategy-marketplace.php') || has_shortcode(get_post()->post_content, 'strategy_marketplace')) {
            wp_enqueue_script('strategy-marketplace-js', plugin_dir_url(__FILE__) . 'assets/js/strategy-marketplace.js', array('jquery'), '1.0.0', true);
            wp_enqueue_style('strategy-marketplace-css', plugin_dir_url(__FILE__) . 'assets/css/strategy-marketplace.css', array(), '1.0.0');

            wp_localize_script('strategy-marketplace-js', 'strategyMarketplaceAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('strategy_marketplace_nonce'),
            ));
        }
    }

    public function render_marketplace($atts) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/marketplace-shortcode.php';
        return ob_get_clean();
    }

    public function ajax_get_strategies() {
        check_ajax_referer('strategy_marketplace_nonce', 'nonce');

        $args = array(
            'post_type' => 'trading_strategy',
            'posts_per_page' => isset($_POST['per_page']) ? intval($_POST['per_page']) : 12,
            'paged' => isset($_POST['page']) ? intval($_POST['page']) : 1,
            'meta_query' => array(),
            'tax_query' => array(),
        );

        // Apply filters
        if (!empty($_POST['category'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'strategy_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($_POST['category']),
            );
        }

        if (!empty($_POST['risk_level'])) {
            $args['meta_query'][] = array(
                'key' => 'risk_level',
                'value' => sanitize_text_field($_POST['risk_level']),
                'compare' => '=',
            );
        }

        if (!empty($_POST['min_performance'])) {
            $args['meta_query'][] = array(
                'key' => 'total_return',
                'value' => floatval($_POST['min_performance']),
                'compare' => '>=',
                'type' => 'DECIMAL',
            );
        }

        $query = new WP_Query($args);
        $strategies = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $strategies[] = $this->format_strategy_data(get_the_ID());
            }
        }

        wp_reset_postdata();

        wp_send_json_success(array(
            'strategies' => $strategies,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ));
    }

    private function format_strategy_data($post_id) {
        return array(
            'id' => $post_id,
            'name' => get_the_title($post_id),
            'description' => get_the_excerpt($post_id),
            'category' => $this->get_strategy_category($post_id),
            'performance' => array(
                'total_return' => get_post_meta($post_id, 'total_return', true),
                'win_rate' => get_post_meta($post_id, 'win_rate', true),
                'max_drawdown' => get_post_meta($post_id, 'max_drawdown', true),
                'sharpe_ratio' => get_post_meta($post_id, 'sharpe_ratio', true),
            ),
            'risk_level' => get_post_meta($post_id, 'risk_level', true),
            'status' => get_post_meta($post_id, 'status', true),
            'parameters' => get_post_meta($post_id, 'strategy_parameters', true),
        );
    }

    private function get_strategy_category($post_id) {
        $terms = get_the_terms($post_id, 'strategy_category');
        return $terms ? $terms[0]->name : 'Uncategorized';
    }

    public function ajax_deploy_strategy() {
        check_ajax_referer('strategy_marketplace_nonce', 'nonce');

        $strategy_id = intval($_POST['strategy_id']);
        $user_id = get_current_user_id();

        if (!$user_id) {
            wp_send_json_error('User not logged in');
            return;
        }

        // Record deployment
        $deployment = array(
            'user_id' => $user_id,
            'strategy_id' => $strategy_id,
            'deployed_at' => current_time('mysql'),
            'status' => 'active',
        );

        // Store in user meta or custom table
        $deployments = get_user_meta($user_id, 'deployed_strategies', true);
        if (!is_array($deployments)) {
            $deployments = array();
        }
        $deployments[] = $deployment;
        update_user_meta($user_id, 'deployed_strategies', $deployments);

        wp_send_json_success(array(
            'message' => 'Strategy deployed successfully',
            'deployment_id' => count($deployments) - 1,
        ));
    }
}

// Initialize the plugin
new StrategyMarketplace();

// Activation hook
register_activation_hook(__FILE__, 'strategy_marketplace_activate');
function strategy_marketplace_activate() {
    // Create database tables if needed
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'strategy_marketplace_deactivate');
function strategy_marketplace_deactivate() {
    // Cleanup if needed
    flush_rewrite_rules();
}