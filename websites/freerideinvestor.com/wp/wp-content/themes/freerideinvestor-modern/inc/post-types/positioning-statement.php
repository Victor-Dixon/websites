<?php
/**
 * Register Custom Post Type: Positioning Statement
 * Phase 1 Brand Core Fix - BRAND-01
 *
 * @package SimplifiedTradingTheme
 */

function simplifiedtheme_register_positioning_statement() {
    $labels = [
        'name'                  => __('Positioning Statements', 'simplifiedtradingtheme'),
        'singular_name'         => __('Positioning Statement', 'simplifiedtradingtheme'),
        'menu_name'             => __('Positioning Statements', 'simplifiedtradingtheme'),
        'name_admin_bar'        => __('Positioning Statement', 'simplifiedtradingtheme'),
        'add_new'               => __('Add New', 'simplifiedtradingtheme'),
        'add_new_item'          => __('Add New Positioning Statement', 'simplifiedtradingtheme'),
        'new_item'              => __('New Positioning Statement', 'simplifiedtradingtheme'),
        'edit_item'             => __('Edit Positioning Statement', 'simplifiedtradingtheme'),
        'view_item'             => __('View Positioning Statement', 'simplifiedtradingtheme'),
        'all_items'             => __('All Positioning Statements', 'simplifiedtradingtheme'),
        'search_items'          => __('Search Positioning Statements', 'simplifiedtradingtheme'),
        'not_found'             => __('No Positioning Statements found.', 'simplifiedtradingtheme'),
        'not_found_in_trash'    => __('No Positioning Statements found in Trash.', 'simplifiedtradingtheme'),
    ];

    $args = [
        'labels'             => $labels,
        'description'        => __('Brand positioning statements for revenue engine websites.', 'simplifiedtradingtheme'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'positioning-statements'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-format-quote',
        'supports'           => ['title', 'editor', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('positioning_statement', $args);
}
add_action('init', 'simplifiedtheme_register_positioning_statement');

