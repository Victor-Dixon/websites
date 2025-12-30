<?php
/**
 * Register Custom Post Type: Project
 * SSOT: Project demos (what shipped + proof)
 *
 * @package DaDudeKC
 */

function dadudekc_register_project() {
    $labels = [
        'name'                  => __('Projects', 'dadudekc'),
        'singular_name'         => __('Project', 'dadudekc'),
        'menu_name'             => __('Projects', 'dadudekc'),
        'name_admin_bar'        => __('Project', 'dadudekc'),
        'add_new'               => __('Add New', 'dadudekc'),
        'add_new_item'          => __('Add New Project', 'dadudekc'),
        'new_item'              => __('New Project', 'dadudekc'),
        'edit_item'             => __('Edit Project', 'dadudekc'),
        'view_item'             => __('View Project', 'dadudekc'),
        'all_items'             => __('All Projects', 'dadudekc'),
        'search_items'          => __('Search Projects', 'dadudekc'),
        'not_found'             => __('No Projects found.', 'dadudekc'),
        'not_found_in_trash'    => __('No Projects found in Trash.', 'dadudekc'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Project demos: what shipped and proof it works.', 'dadudekc'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'projects'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => ['title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'],
        'show_in_rest'       => true,
        'rest_base'          => 'projects',
    ];

    register_post_type('project', $args);
}
add_action('init', 'dadudekc_register_project');

/**
 * Register custom meta fields for projects
 */
function dadudekc_register_project_meta() {
    register_post_meta('project', 'project_url', [
        'type' => 'string',
        'description' => 'Live URL or demo link',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('project', 'project_github', [
        'type' => 'string',
        'description' => 'GitHub repository URL',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('project', 'project_status', [
        'type' => 'string',
        'description' => 'Status: shipped, in-progress, archived',
        'single' => true,
        'show_in_rest' => true,
        'default' => 'in-progress',
    ]);

    register_post_meta('project', 'project_skills', [
        'type' => 'string',
        'description' => 'Comma-separated list of skills used',
        'single' => true,
        'show_in_rest' => true,
    ]);

    register_post_meta('project', 'project_proof', [
        'type' => 'string',
        'description' => 'Proof/evidence (screenshots, metrics, etc.)',
        'single' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'dadudekc_register_project_meta');

