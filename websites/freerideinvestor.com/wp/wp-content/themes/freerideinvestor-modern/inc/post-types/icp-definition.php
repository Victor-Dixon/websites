<?php
/**
 * Register Custom Post Type: ICP Definition
 * Phase 1 Brand Core Fix - BRAND-03
 *
 * @package SimplifiedTradingTheme
 */

function simplifiedtheme_register_icp_definition() {
    $labels = [
        'name'                  => __('ICP Definitions', 'simplifiedtradingtheme'),
        'singular_name'         => __('ICP Definition', 'simplifiedtradingtheme'),
        'menu_name'             => __('ICP Definitions', 'simplifiedtradingtheme'),
        'name_admin_bar'        => __('ICP Definition', 'simplifiedtradingtheme'),
        'add_new'               => __('Add New', 'simplifiedtradingtheme'),
        'add_new_item'          => __('Add New ICP Definition', 'simplifiedtradingtheme'),
        'new_item'              => __('New ICP Definition', 'simplifiedtradingtheme'),
        'edit_item'             => __('Edit ICP Definition', 'simplifiedtradingtheme'),
        'view_item'             => __('View ICP Definition', 'simplifiedtradingtheme'),
        'all_items'             => __('All ICP Definitions', 'simplifiedtradingtheme'),
        'search_items'          => __('Search ICP Definitions', 'simplifiedtradingtheme'),
        'not_found'             => __('No ICP Definitions found.', 'simplifiedtradingtheme'),
        'not_found_in_trash'    => __('No ICP Definitions found in Trash.', 'simplifiedtradingtheme'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Ideal Customer Profile definitions for revenue engine websites.', 'simplifiedtradingtheme'),
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
add_action('init', 'simplifiedtheme_register_icp_definition');

