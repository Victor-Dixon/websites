<?php
/**
 * Register Custom Post Type: ICP Definition
 * Phase 1 Brand Core Fix - BRAND-03
 *
 * @package CrosbyUltimateEvents
 */

function crosbyultimateevents_register_icp_definition() {
    $labels = [
        'name'                  => __('ICP Definitions', 'crosbyultimateevents'),
        'singular_name'         => __('ICP Definition', 'crosbyultimateevents'),
        'menu_name'             => __('ICP Definitions', 'crosbyultimateevents'),
        'name_admin_bar'        => __('ICP Definition', 'crosbyultimateevents'),
        'add_new'               => __('Add New', 'crosbyultimateevents'),
        'add_new_item'          => __('Add New ICP Definition', 'crosbyultimateevents'),
        'new_item'              => __('New ICP Definition', 'crosbyultimateevents'),
        'edit_item'             => __('Edit ICP Definition', 'crosbyultimateevents'),
        'view_item'             => __('View ICP Definition', 'crosbyultimateevents'),
        'all_items'             => __('All ICP Definitions', 'crosbyultimateevents'),
        'search_items'          => __('Search ICP Definitions', 'crosbyultimateevents'),
        'not_found'             => __('No ICP Definitions found.', 'crosbyultimateevents'),
        'not_found_in_trash'    => __('No ICP Definitions found in Trash.', 'crosbyultimateevents'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Ideal Customer Profile definitions for revenue engine websites.', 'crosbyultimateevents'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'icp-definitions'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => ['title', 'editor', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('icp_definition', $args);
}
add_action('init', 'crosbyultimateevents_register_icp_definition');

