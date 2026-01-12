<?php
/**
 * Register Custom Post Type: Resume Item
 * SSOT: Skills learned = added to resume, compile total resume & portfolio
 *
 * @package DaDudeKC
 */

function dadudekc_register_resume_item() {
    $labels = [
        'name'                  => __('Resume Items', 'dadudekc'),
        'singular_name'         => __('Resume Item', 'dadudekc'),
        'menu_name'             => __('Resume', 'dadudekc'),
        'name_admin_bar'        => __('Resume Item', 'dadudekc'),
        'add_new'               => __('Add New', 'dadudekc'),
        'add_new_item'          => __('Add New Resume Item', 'dadudekc'),
        'new_item'              => __('New Resume Item', 'dadudekc'),
        'edit_item'             => __('Edit Resume Item', 'dadudekc'),
        'view_item'             => __('View Resume Item', 'dadudekc'),
        'all_items'             => __('All Resume Items', 'dadudekc'),
        'search_items'          => __('Search Resume Items', 'dadudekc'),
        'not_found'             => __('No Resume Items found.', 'dadudekc'),
        'not_found_in_trash'    => __('No Resume Items found in Trash.', 'dadudekc'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Resume and portfolio items: compiled skills, projects, and proof of execution.', 'dadudekc'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-id',
        'supports'           => ['title', 'editor', 'custom-fields'],
        'show_in_rest'       => true,
        'rest_base'          => 'resume_items',
    ];

    register_post_type('resume_item', $args);
}
add_action('init', 'dadudekc_register_resume_item');

/**
 * Register custom meta fields for resume items
 */
function dadudekc_register_resume_item_meta() {
    register_post_meta('resume_item', 'resume_category', [
        'type' => 'string',
        'description' => 'Category: skill, project, achievement, education',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('resume_item', 'resume_date', [
        'type' => 'string',
        'description' => 'Date or date range',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('resume_item', 'resume_proof_url', [
        'type' => 'string',
        'description' => 'URL to proof/demo',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('resume_item', 'resume_priority', [
        'type' => 'integer',
        'description' => 'Display priority (higher = more prominent)',
        'single' => true,
        'show_in_rest' => true,
        'default' => 0,
    ]);
}
add_action('init', 'dadudekc_register_resume_item_meta');

