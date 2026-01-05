<?php
/**
 * Register Custom Post Type: Experiment
 * SSOT: Builder logs (experiments → learnings → next build)
 *
 * @package DaDudeKC
 */

function dadudekc_register_experiment() {
    $labels = [
        'name'                  => __('Experiments', 'dadudekc'),
        'singular_name'         => __('Experiment', 'dadudekc'),
        'menu_name'             => __('Experiments', 'dadudekc'),
        'name_admin_bar'        => __('Experiment', 'dadudekc'),
        'add_new'               => __('Add New', 'dadudekc'),
        'add_new_item'          => __('Add New Experiment', 'dadudekc'),
        'new_item'              => __('New Experiment', 'dadudekc'),
        'edit_item'             => __('Edit Experiment', 'dadudekc'),
        'view_item'             => __('View Experiment', 'dadudekc'),
        'all_items'             => __('All Experiments', 'dadudekc'),
        'search_items'          => __('Search Experiments', 'dadudekc'),
        'not_found'             => __('No Experiments found.', 'dadudekc'),
        'not_found_in_trash'    => __('No Experiments found in Trash.', 'dadudekc'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Builder logs: experiments, learnings, and next builds.', 'dadudekc'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'experiments'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-testimonial',
        'supports'           => ['title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'],
        'show_in_rest'       => true,
        'rest_base'          => 'experiments',
    ];

    register_post_type('experiment', $args);
}
add_action('init', 'dadudekc_register_experiment');

/**
 * Register custom meta fields for experiments
 */
function dadudekc_register_experiment_meta() {
    register_post_meta('experiment', 'experiment_status', [
        'type' => 'string',
        'description' => 'Status: live, in-progress, shipped, archived',
        'single' => true,
        'show_in_rest' => true,
        'default' => 'in-progress',
    ]);

    register_post_meta('experiment', 'experiment_url', [
        'type' => 'string',
        'description' => 'URL to experiment (GitHub, demo, etc.)',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('experiment', 'experiment_stats', [
        'type' => 'string',
        'description' => 'JSON string of stats/metrics',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('experiment', 'experiment_learnings', [
        'type' => 'string',
        'description' => 'Key learnings from this experiment',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('experiment', 'next_build', [
        'type' => 'string',
        'description' => 'What to build next based on this experiment',
        'single' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'dadudekc_register_experiment_meta');

