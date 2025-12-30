<?php
/**
 * Theme Setup Module
 * WordPress theme supports, menus, and core setup
 * 
 * @package TradingRobotPlug
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme setup function
 */
function trp_theme_setup()
{
    // Title tag support
    add_theme_support('title-tag');
    
    // Post thumbnails
    add_theme_support('post-thumbnails');
    
    // Custom logo
    add_theme_support('custom-logo', array(
        'height' => 60,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));
    
    // Selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');
    
    // HTML5 support
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ));
    
    // Automatic feed links
    add_theme_support('automatic-feed-links');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'tradingrobotplug'),
        'footer' => __('Footer Menu', 'tradingrobotplug'),
    ));
}

add_action('after_setup_theme', 'trp_theme_setup');

