<?php
function houstonsipqueencom_enqueue_styles() {
    wp_enqueue_style('houstonsipqueencom-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'houstonsipqueencom_enqueue_styles');
?>
