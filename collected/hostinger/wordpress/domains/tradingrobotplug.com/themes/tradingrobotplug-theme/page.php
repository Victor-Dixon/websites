<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\page.php
Description: The page template for The Trading Robot Plug theme, displaying single page content and handling pagination if supported.
Version: 1.1.0
Author: Victor Dixon
*/
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', 'page');
            endwhile;
        else :
            get_template_part('template-parts/content', 'none');
        endif;
        ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
