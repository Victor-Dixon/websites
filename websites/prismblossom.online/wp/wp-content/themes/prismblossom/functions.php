<?php
function prismblossomonline_enqueue_styles() {
    wp_enqueue_style('prismblossomonline-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'prismblossomonline_enqueue_styles');
?>
