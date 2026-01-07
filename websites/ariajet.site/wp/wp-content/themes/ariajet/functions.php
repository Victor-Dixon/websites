<?php
function ariajetsite_enqueue_styles() {
    wp_enqueue_style('ariajetsite-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'ariajetsite_enqueue_styles');
?>
