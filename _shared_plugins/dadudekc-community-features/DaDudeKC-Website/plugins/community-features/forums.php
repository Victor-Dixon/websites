<?php
/**
 * Plugin Name: DaDudeKC Forum Features
 * Description: Adds forum functionality to the WordPress site, allowing user-created topics and discussions.
 * Version: 1.0
 * Author: DaDudeKC
 * Text Domain: dadudekc-forum
 */

/**
 * Registers a custom post type (CPT) for forum topics.
 */
function dadudekc_register_forum_post_type() {
    $labels = array(
        'name'                  => __('Forums', 'dadudekc-forum'),
        'singular_name'         => __('Forum', 'dadudekc-forum'),
        // Additional labels omitted for brevity
    );

    $args = array(
        'label'                 => __('Forum', 'dadudekc-forum'),
        'description'           => __('Forum for discussing various topics.', 'dadudekc-forum'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'comments', 'author'),
        'public'                => true,
        'show_in_rest'          => true,  // Enable Gutenberg support
        'capability_type'       => 'post',
        'map_meta_cap'          => true,  // Properly map capabilities
        // Additional arguments omitted for brevity
    );

    register_post_type('forum', $args);
}
add_action('init', 'dadudekc_register_forum_post_type');

/**
 * Checks permissions to limit access to the forum posting capabilities.
 */
function dadudekc_forums_permission_check() {
    // Limit forum posting to users who can edit posts
    if (!current_user_can('edit_posts')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'dadudekc-forum'));
    }
}
add_action('admin_init', 'dadudekc_forums_permission_check');

/**
 * Adds capabilities to specified roles for managing the forum.
 */
function dadudekc_add_forum_capabilities() {
    $roles = ['administrator', 'editor', 'subscriber'];  // Target roles
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        $role->add_cap('read_private_forums');
        $role->add_cap('edit_others_forums');
        $role->add_cap('edit_private_forums');
    }
}
add_action('admin_init', 'dadudekc_add_forum_capabilities');
