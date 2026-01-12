<?php
class Review_Post_Type {
    public function __construct() {
        add_action('init', array($this, 'register_review_post_type'));
    }
    public function register_review_post_type() {
        register_post_type('review', array(
            'labels' => array('name' => 'Reviews', 'singular_name' => 'Review'),
            'public' => true,
            'supports' => array('title', 'editor', 'author'),
            'menu_icon' => 'dashicons-star-filled'
        ));
    }
}