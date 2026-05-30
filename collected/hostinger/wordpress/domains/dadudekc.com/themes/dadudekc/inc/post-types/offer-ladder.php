<?php
/**
 * Register Custom Post Type: Offer Ladder
 * Phase 1 Brand Core Fix - BRAND-02
 *
 * @package DaDudeKC
 */

function dadudekc_register_offer_ladder() {
    $labels = [
        'name'                  => __('Offer Ladders', 'dadudekc'),
        'singular_name'         => __('Offer Ladder', 'dadudekc'),
        'menu_name'             => __('Offer Ladders', 'dadudekc'),
        'name_admin_bar'        => __('Offer Ladder', 'dadudekc'),
        'add_new'               => __('Add New', 'dadudekc'),
        'add_new_item'          => __('Add New Offer Ladder', 'dadudekc'),
        'new_item'              => __('New Offer Ladder', 'dadudekc'),
        'edit_item'             => __('Edit Offer Ladder', 'dadudekc'),
        'view_item'             => __('View Offer Ladder', 'dadudekc'),
        'all_items'             => __('All Offer Ladders', 'dadudekc'),
        'search_items'          => __('Search Offer Ladders', 'dadudekc'),
        'not_found'             => __('No Offer Ladders found.', 'dadudekc'),
        'not_found_in_trash'    => __('No Offer Ladders found in Trash.', 'dadudekc'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Offer ladder progression for revenue engine websites.', 'dadudekc'),
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
add_action('init', 'dadudekc_register_offer_ladder');

