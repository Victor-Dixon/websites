<?php
/**
 * Template Name: Marketplace
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container page-header">
        <h1>Robot Marketplace</h1>
        <p>Discover automated strategies tailored to your trading style.</p>
    </div>

    <div class="container">
        <!-- Render Marketplace Shortcode -->
        <?php echo do_shortcode('[trading_robot_marketplace]'); ?>
    </div>
</main>

<style>
.page-header {
    text-align: center;
    padding: 60px 0 40px;
}
</style>

<?php get_footer(); ?>
