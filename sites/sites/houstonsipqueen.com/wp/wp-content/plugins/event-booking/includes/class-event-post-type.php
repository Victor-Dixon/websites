<?php
/**
 * Event Post Type Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Event_Post_Type {

    public function __construct() {
        add_action('init', array($this, 'register_event_post_type'));
    }

    public function register_event_post_type() {
        $labels = array(
            'name' => __('Events', 'event-booking'),
            'singular_name' => __('Event', 'event-booking'),
            'add_new' => __('Add New Event', 'event-booking'),
            'add_new_item' => __('Add New Event', 'event-booking'),
            'edit_item' => __('Edit Event', 'event-booking'),
            'new_item' => __('New Event', 'event-booking'),
            'view_item' => __('View Event', 'event-booking'),
            'search_items' => __('Search Events', 'event-booking'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon' => 'dashicons-calendar',
            'show_in_rest' => true,
        );

        register_post_type('event', $args);
    }
}