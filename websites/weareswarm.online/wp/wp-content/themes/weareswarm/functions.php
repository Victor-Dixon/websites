<?php
function weareswarmonline_enqueue_styles() {
    wp_enqueue_style('weareswarmonline-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'weareswarmonline_enqueue_styles');
?>
