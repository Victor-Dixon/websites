<?php
function crosbyultimateevents_enqueue_styles() {
    wp_enqueue_style('crosbyultimateevents-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'crosbyultimateevents_enqueue_styles');
?>
