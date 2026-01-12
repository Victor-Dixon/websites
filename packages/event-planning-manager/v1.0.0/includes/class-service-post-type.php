<?php
class Event_Service_Post_Type {
    public function __construct() {
        add_action('init', array($this, 'register_service_post_type'));
    }
    public function register_service_post_type() {
        register_post_type('event_service', array(
            'labels' => array(
                'name' => 'Event Services',
                'singular_name' => 'Event Service'
            ),
            'public' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-calendar'
        ));
    }
}