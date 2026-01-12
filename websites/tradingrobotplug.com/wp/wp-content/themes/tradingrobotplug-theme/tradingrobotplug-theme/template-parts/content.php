<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\template-parts\content.php
Description: Template part for displaying content within The Trading Robot Plug theme, including post titles and content.
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
</article>
