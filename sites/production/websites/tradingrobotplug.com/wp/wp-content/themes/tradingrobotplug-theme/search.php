<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\search.php
Description: Template for displaying search results within The Trading Robot Plug theme, including search results loop and navigation.
Version: 1.1.0
Author: Victor Dixon
*/
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php printf(__('Search Results for: %s', 'my-custom-theme'), get_search_query()); ?></h1>
            </header>

            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', 'search'); ?>
            <?php endwhile; ?>

            <?php the_posts_navigation(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </main><!-- .site-main -->
</div><!-- .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
