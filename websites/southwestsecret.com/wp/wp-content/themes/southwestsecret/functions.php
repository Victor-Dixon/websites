<?php
function southwestsecretcom_enqueue_styles() {
    wp_enqueue_style('southwestsecretcom-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'southwestsecretcom_enqueue_styles');
?>
