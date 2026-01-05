<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\archive.php
Description: Template for displaying archive pages within The Trading Robot Plug theme, including a loop for posts and navigation.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<?php get_header(); ?>

<div id="primary">
    <main id="main">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php the_archive_title(); ?></h1>
            </header>

            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', get_post_format()); ?>
            <?php endwhile; ?>

            <?php the_posts_navigation(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
