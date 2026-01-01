<?php
/**
 * Note Custom Post Type.
 * SSOT: Idea Lab notes (short-form insights).
 *
 * @package DaDudeKC
 */

if (!defined('ABSPATH')) {
    exit;
}

function dadudekc_register_note() {
    register_post_type('note', [
        'labels' => [
            'name' => __('Notes', 'dadudekc'),
            'singular_name' => __('Note', 'dadudekc'),
            'menu_name' => __('Notes', 'dadudekc'),
            'name_admin_bar' => __('Note', 'dadudekc'),
            'add_new' => __('Add New', 'dadudekc'),
            'add_new_item' => __('Add New Note', 'dadudekc'),
            'new_item' => __('New Note', 'dadudekc'),
            'edit_item' => __('Edit Note', 'dadudekc'),
            'view_item' => __('View Note', 'dadudekc'),
            'all_items' => __('All Notes', 'dadudekc'),
            'search_items' => __('Search Notes', 'dadudekc'),
            'not_found' => __('No Notes found.', 'dadudekc'),
            'not_found_in_trash' => __('No Notes found in Trash.', 'dadudekc'),
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'idea-lab/notes'],
        'menu_position' => 21,
        'menu_icon' => 'dashicons-lightbulb',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'],
        'show_in_rest' => true,
        'taxonomies' => ['post_tag', 'category'],
        'description' => __('Idea Lab notes: short-form insights and brainstorms.', 'dadudekc'),
    ]);
}
add_action('init', 'dadudekc_register_note');
