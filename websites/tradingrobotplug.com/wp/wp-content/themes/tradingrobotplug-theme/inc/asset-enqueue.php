<?php
/**
 * Asset Enqueuing Module
 * Styles and scripts for professional dark theme
 * 
 * @package TradingRobotPlug
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue theme styles and scripts
 */
function trp_enqueue_assets()
{
    // Main stylesheet
    wp_enqueue_style(
        'trp-main-style',
        get_stylesheet_uri(),
        array(),
        TRP_THEME_VERSION
    );
    
    // Custom CSS with dark theme support
    wp_enqueue_style(
        'trp-custom-style',
        TRP_THEME_URI . '/assets/css/custom.css',
        array('trp-main-style'),
        TRP_THEME_VERSION
    );
    
    // Dark theme variables
    wp_enqueue_style(
        'trp-variables',
        TRP_THEME_URI . '/variables.css',
        array('trp-main-style'),
        TRP_THEME_VERSION
    );
    
    // Main JavaScript
    wp_enqueue_script(
        'trp-main-script',
        TRP_THEME_URI . '/assets/js/main.js',
        array('jquery'),
        TRP_THEME_VERSION,
        true
    );
    
    // Localize script for AJAX
    wp_localize_script('trp-main-script', 'trpData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'restUrl' => rest_url('tradingrobotplug/v1/'),
        'nonce' => wp_create_nonce('trp_nonce'),
    ));
    
    // Dashboard-specific assets (only on dashboard page)
    if (is_page('dashboard') || is_page_template('page-dashboard.php')) {
        // Chart.js library
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            array(),
            '4.4.0',
            true
        );
        
        // Dashboard JavaScript
        wp_enqueue_script(
            'trp-dashboard-script',
            TRP_THEME_URI . '/assets/js/dashboard.js',
            array('jquery', 'chart-js'),
            TRP_THEME_VERSION,
            true
        );
        
        // Localize dashboard script
        wp_localize_script('trp-dashboard-script', 'trpDashboardData', array(
            'apiBase' => rest_url('tradingrobotplug/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }
}

add_action('wp_enqueue_scripts', 'trp_enqueue_assets');

