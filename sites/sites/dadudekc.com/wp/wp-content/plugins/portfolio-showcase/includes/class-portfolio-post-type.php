<?php
class Portfolio_Post_Type {
    public function __construct() {
        add_action('init', array($this, 'register_portfolio_post_type'));
    }
    public function register_portfolio_post_type() {
        register_post_type('portfolio_project', array(
            'labels' => array(
                'name' => 'Portfolio Projects',
                'singular_name' => 'Portfolio Project'
            ),
            'public' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon' => 'dashicons-portfolio',
            'show_in_rest' => true,
        ));
    }
}