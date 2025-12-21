<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\inc\widgets.php
Description: Widget initialization for The Trading Robot Plug theme, registering sidebars and widget areas.
Version: 1.1.0
Author: Victor Dixon
*/

/**
 * Register widget areas for the theme.
 */
function my_custom_theme_widgets_init() {
    // Register the main sidebar
    register_sidebar(array(
        'name'          => __('Sidebar', 'my-custom-theme'),
        'id'            => 'sidebar-1',
        'description'   => __('Main Sidebar', 'my-custom-theme'),
        'before_widget' => '<section class="widget">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Register the footer widget area
    register_sidebar(array(
        'name'          => __('Footer Widgets', 'my-custom-theme'),
        'id'            => 'footer-1',
        'description'   => __('Widgets in this area will be shown in the footer.', 'my-custom-theme'),
        'before_widget' => '<section class="widget-footer">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title-footer">',
        'after_title'   => '</h2>',
    ));
}

add_action('widgets_init', 'my_custom_theme_widgets_init');
?>
