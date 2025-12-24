<?php
/**
 * Template Name: User Dashboard
 */

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 40px;">
        <!-- Render Dashboard Shortcode -->
        <?php echo do_shortcode('[trading_robot_dashboard]'); ?>
    </div>
</main>

<?php get_footer(); ?>
