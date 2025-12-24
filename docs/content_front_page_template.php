<?php
/**
 * Template part for displaying front page content
 *
 * @package freerideinvestor-modern
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        the_content();
        ?>
    </div>
</article>
