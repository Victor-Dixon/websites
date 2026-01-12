<?php
/**
 * Beverage Post Type Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Beverage_Post_Type {

    public function __construct() {
        add_action('init', array($this, 'register_beverage_post_type'));
        add_action('init', array($this, 'register_beverage_taxonomies'));
    }

    public function register_beverage_post_type() {
        $labels = array(
            'name' => __('Beverages', 'beverage-menu'),
            'singular_name' => __('Beverage', 'beverage-menu'),
            'menu_name' => __('Beverage Menu', 'beverage-menu'),
            'add_new' => __('Add New Beverage', 'beverage-menu'),
            'add_new_item' => __('Add New Beverage', 'beverage-menu'),
            'edit_item' => __('Edit Beverage', 'beverage-menu'),
            'new_item' => __('New Beverage', 'beverage-menu'),
            'view_item' => __('View Beverage', 'beverage-menu'),
            'search_items' => __('Search Beverages', 'beverage-menu'),
            'not_found' => __('No beverages found', 'beverage-menu'),
            'not_found_in_trash' => __('No beverages found in trash', 'beverage-menu'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'menu_icon' => 'dashicons-carrot',
            'show_in_rest' => true,
        );

        register_post_type('beverage', $args);
    }

    public function register_beverage_taxonomies() {
        // Beverage Categories
        register_taxonomy('beverage_category', 'beverage', array(
            'labels' => array(
                'name' => __('Beverage Categories', 'beverage-menu'),
                'singular_name' => __('Beverage Category', 'beverage-menu'),
            ),
            'hierarchical' => true,
            'show_in_rest' => true,
        ));

        // Beverage Tags
        register_taxonomy('beverage_tag', 'beverage', array(
            'labels' => array(
                'name' => __('Beverage Tags', 'beverage-menu'),
                'singular_name' => __('Beverage Tag', 'beverage-menu'),
            ),
            'hierarchical' => false,
            'show_in_rest' => true,
        ));
    }
}