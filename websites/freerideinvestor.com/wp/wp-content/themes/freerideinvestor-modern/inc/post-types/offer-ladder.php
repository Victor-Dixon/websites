<?php
/**
 * Register Custom Post Type: Offer Ladder
 * Phase 1 Brand Core Fix - BRAND-02
 *
 * @package SimplifiedTradingTheme
 */

function simplifiedtheme_register_offer_ladder() {
    $labels = [
        'name'                  => __('Offer Ladders', 'simplifiedtradingtheme'),
        'singular_name'         => __('Offer Ladder', 'simplifiedtradingtheme'),
        'menu_name'             => __('Offer Ladders', 'simplifiedtradingtheme'),
        'name_admin_bar'        => __('Offer Ladder', 'simplifiedtradingtheme'),
        'add_new'               => __('Add New', 'simplifiedtradingtheme'),
        'add_new_item'          => __('Add New Offer Ladder', 'simplifiedtradingtheme'),
        'new_item'              => __('New Offer Ladder', 'simplifiedtradingtheme'),
        'edit_item'             => __('Edit Offer Ladder', 'simplifiedtradingtheme'),
        'view_item'             => __('View Offer Ladder', 'simplifiedtradingtheme'),
        'all_items'             => __('All Offer Ladders', 'simplifiedtradingtheme'),
        'search_items'          => __('Search Offer Ladders', 'simplifiedtradingtheme'),
        'not_found'             => __('No Offer Ladders found.', 'simplifiedtradingtheme'),
        'not_found_in_trash'    => __('No Offer Ladders found in Trash.', 'simplifiedtradingtheme'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Offer ladder progression for revenue engine websites.', 'simplifiedtradingtheme'),
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
    ];

    register_post_type('offer_ladder', $args);
}
add_action('init', 'simplifiedtheme_register_offer_ladder');

