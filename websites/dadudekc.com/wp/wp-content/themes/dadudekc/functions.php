<?php
function dadudekc_enqueue_styles() {
    wp_enqueue_style('dadudekc-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'dadudekc_enqueue_styles');
?>
