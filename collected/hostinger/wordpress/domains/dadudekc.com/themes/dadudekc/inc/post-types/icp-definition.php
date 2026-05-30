<?php
/**
 * Register Custom Post Type: ICP Definition
 * Phase 1 Brand Core Fix - BRAND-03
 *
 * @package DaDudeKC
 */

function dadudekc_register_icp_definition() {
    $labels = [
        'name'                  => __('ICP Definitions', 'dadudekc'),
        'singular_name'         => __('ICP Definition', 'dadudekc'),
        'menu_name'             => __('ICP Definitions', 'dadudekc'),
        'name_admin_bar'        => __('ICP Definition', 'dadudekc'),
        'add_new'               => __('Add New', 'dadudekc'),
        'add_new_item'          => __('Add New ICP Definition', 'dadudekc'),
        'new_item'              => __('New ICP Definition', 'dadudekc'),
        'edit_item'             => __('Edit ICP Definition', 'dadudekc'),
        'view_item'             => __('View ICP Definition', 'dadudekc'),
        'all_items'             => __('All ICP Definitions', 'dadudekc'),
        'search_items'          => __('Search ICP Definitions', 'dadudekc'),
        'not_found'             => __('No ICP Definitions found.', 'dadudekc'),
        'not_found_in_trash'    => __('No ICP Definitions found in Trash.', 'dadudekc'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Ideal Customer Profile definitions for revenue engine websites.', 'dadudekc'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'icp-definitions'],
        'capability_type'    => 'post',
        'capabilities'       => [
            'edit_post'          => 'edit_post',
            'read_post'          => 'read_post',
            'delete_post'        => 'delete_post',
            'edit_posts'         => 'edit_posts',
            'edit_others_posts'  => 'edit_others_posts',
            'publish_posts'      => 'publish_posts',
            'read_private_posts' => 'read_private_posts',
        ],
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => ['title', 'editor', 'custom-fields'],
        'show_in_rest'       => true,
        'rest_base'          => 'icp_definition',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ];

    register_post_type('icp_definition', $args);
}
add_action('init', 'dadudekc_register_icp_definition');

