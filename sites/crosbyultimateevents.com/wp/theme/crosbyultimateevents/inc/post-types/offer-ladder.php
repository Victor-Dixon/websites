<?php
/**
 * Register Custom Post Type: Offer Ladder
 * Phase 1 Brand Core Fix - BRAND-02
 *
 * @package CrosbyUltimateEvents
 */

function crosbyultimateevents_register_offer_ladder() {
    $labels = [
        'name'                  => __('Offer Ladders', 'crosbyultimateevents'),
        'singular_name'         => __('Offer Ladder', 'crosbyultimateevents'),
        'menu_name'             => __('Offer Ladders', 'crosbyultimateevents'),
        'name_admin_bar'        => __('Offer Ladder', 'crosbyultimateevents'),
        'add_new'               => __('Add New', 'crosbyultimateevents'),
        'add_new_item'          => __('Add New Offer Ladder', 'crosbyultimateevents'),
        'new_item'              => __('New Offer Ladder', 'crosbyultimateevents'),
        'edit_item'             => __('Edit Offer Ladder', 'crosbyultimateevents'),
        'view_item'             => __('View Offer Ladder', 'crosbyultimateevents'),
        'all_items'             => __('All Offer Ladders', 'crosbyultimateevents'),
        'search_items'          => __('Search Offer Ladders', 'crosbyultimateevents'),
        'not_found'             => __('No Offer Ladders found.', 'crosbyultimateevents'),
        'not_found_in_trash'    => __('No Offer Ladders found in Trash.', 'crosbyultimateevents'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Offer ladder progression for revenue engine websites.', 'crosbyultimateevents'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'offer-ladders'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => true,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-chart-bar',
        'supports'           => ['title', 'editor', 'page-attributes', 'custom-fields'],
        'show_in_rest'       => true,
        'rest_base'          => 'offer_ladder',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ];

    register_post_type('offer_ladder', $args);
}
add_action('init', 'crosbyultimateevents_register_offer_ladder');



