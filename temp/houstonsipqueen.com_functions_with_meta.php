<?php

// Houston Sip Queen Astros Brand Theme CSS - Applied 2025-12-18
require_once get_template_directory() . '/hsq_astros_theme_css.php';


/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "Houston's Sip Queen - Luxury bartending and beverage services for your special events. Professional mixologists bringing craft cocktails to your celebration.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);
