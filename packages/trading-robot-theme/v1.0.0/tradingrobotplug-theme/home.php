<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\home.php
Description: Template for displaying the home page in The Trading Robot Plug theme, including the post loop and navigation.
Version: 1.1.0
Author: Victor Dixon
*/
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php if (have_posts()) : ?>
            <?php
            // Start the Loop.
            while (have_posts()) :
                the_post();

                /*
                 * Include the Post-Format-specific template for the content.
                 * If you want to override this in a child theme, then include a file
                 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                 */
                get_template_part('template-parts/content', get_post_format());

            endwhile;

            // Previous/next page navigation.
            the_posts_navigation();

        else :

            // If no content, include the "No posts found" template.
            get_template_part('template-parts/content', 'none');

        endif;
        ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
