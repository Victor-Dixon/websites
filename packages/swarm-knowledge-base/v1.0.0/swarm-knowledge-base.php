<?php
/**
 * Plugin Name: Swarm Knowledge Base
 * Description: Knowledge base for swarm intelligence documentation
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 */
if (!defined('ABSPATH')) exit;
function swarm_knowledge_base_init() {
    register_post_type('swarm_doc', array(
        'labels' => array('name' => 'Swarm Docs', 'singular_name' => 'Swarm Doc'),
        'public' => true,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-book'
    ));
}
add_action('init', 'swarm_knowledge_base_init');