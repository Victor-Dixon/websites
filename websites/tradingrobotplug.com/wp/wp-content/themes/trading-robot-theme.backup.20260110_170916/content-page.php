<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\content-page.php
Description: Template part for displaying single page content within The Trading Robot Plug theme, including support for page pagination and edit links.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php
        the_content();

        // Optional: If your theme supports page pagination
        wp_link_pages(array(
            'before' => '<div class="page-links">' . __('Pages:', 'your-text-domain'),
            'after'  => '</div>',
        ));
        ?>
    </div><!-- .entry-content -->

    <?php if (get_edit_post_link()) : ?>
        <footer class="entry-footer">
            <?php
            edit_post_link(
                sprintf(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Edit<span class="screen-reader-text"> "%s"</span>', 'your-text-domain'),
                    get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
            );
            ?>
        </footer><!-- .entry-footer -->
    <?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
