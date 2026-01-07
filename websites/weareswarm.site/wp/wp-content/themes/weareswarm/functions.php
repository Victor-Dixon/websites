<?php
function weareswarmsite_enqueue_styles() {
    wp_enqueue_style('weareswarmsite-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'weareswarmsite_enqueue_styles');
?>
