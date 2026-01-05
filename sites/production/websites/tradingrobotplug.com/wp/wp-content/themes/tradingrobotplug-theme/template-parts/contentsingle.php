<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\template-parts\contentsingle.php
Description: Template part for displaying single post content within The Trading Robot Plug theme, including post titles, content, and tags.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header>
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
    <footer class="entry-footer">
        <?php the_tags('<span class="tag-links">', '', '</span>'); ?>
    </footer>
</article>
