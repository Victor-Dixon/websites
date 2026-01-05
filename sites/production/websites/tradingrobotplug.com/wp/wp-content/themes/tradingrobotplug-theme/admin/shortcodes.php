<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\shortcodes.php
Description: Shortcode definitions for The Trading Robot Plug theme, allowing users to insert dynamic content using shortcodes.
Version: 1.0.0
Author: Victor Dixon
*/

// Shortcode to Display a Custom Button
function trading_robot_plug_custom_button_shortcode($atts, $content = null) {
    $atts = shortcode_atts(
        array(
            'url' => '#',
            'color' => 'primary', // Options: primary, secondary, success, etc.
        ), 
        $atts,
        'custom_button'
    );

    return '<a href="' . esc_url($atts['url']) . '" class="btn btn-' . esc_attr($atts['color']) . '">' . do_shortcode($content) . '</a>';
}
add_shortcode('custom_button', 'trading_robot_plug_custom_button_shortcode');

// Shortcode to Display Recent Posts
function trading_robot_plug_recent_posts_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'count' => 5,
        ), 
        $atts,
        'recent_posts'
    );

    $query = new WP_Query(array(
        'posts_per_page' => intval($atts['count']),
        'post_status' => 'publish',
    ));

    if ($query->have_posts()) {
        $output = '<ul class="recent-posts">';
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
        }
        wp_reset_postdata();
        $output .= '</ul>';
    } else {
        $output = '<p>No recent posts available.</p>';
    }

    return $output;
}
add_shortcode('recent_posts', 'trading_robot_plug_recent_posts_shortcode');

// Shortcode to Display a Trading Chart (Example)
function trading_robot_plug_trading_chart_shortcode($atts) {
    return '<div class="trading-chart"><canvas id="tradingChart"></canvas></div>';
}
add_shortcode('trading_chart', 'trading_robot_plug_trading_chart_shortcode');
?>
