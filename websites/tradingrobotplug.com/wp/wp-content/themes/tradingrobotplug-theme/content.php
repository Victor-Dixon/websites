<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\content.php
Description: Template part for displaying post excerpts within The Trading Robot Plug theme, including post title, excerpt, and meta information.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
    </header><!-- .entry-header -->

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->

    <footer class="entry-footer">
        <?php
        echo '<span class="posted-on">' . get_the_date() . '</span>';
        echo '<span class="byline"> by ' . get_the_author() . '</span>';
        ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
