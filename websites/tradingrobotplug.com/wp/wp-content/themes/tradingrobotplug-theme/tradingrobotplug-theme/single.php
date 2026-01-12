<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\single.php
Description: Template for displaying single posts within The Trading Robot Plug theme, including post content, navigation, and comments.
Version: 1.1.0
Author: Victor Dixon
*/
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php
        while (have_posts()) : the_post();
            get_template_part('template-parts/content', get_post_format());
            the_post_navigation();
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
        endwhile;
        ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
