<?php
/**
 * Plugin Name: Project Gallery
 * Description: Advanced project gallery with image management
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 */
if (!defined('ABSPATH')) exit;
function project_gallery_init() {
    add_shortcode('project_gallery', 'project_gallery_shortcode');
}
function project_gallery_shortcode() {
    return '<div class="project-gallery"><h3>Project Gallery</h3><p>Advanced project showcase with image galleries.</p></div>';
}
add_action('plugins_loaded', 'project_gallery_init');