<?php
/**
 * Main Index Template
 *
 * @package Swarm
 * @since 1.0.0
 */

get_header(); ?>

<div class="content-page">
    <div class="container">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                the_content();
            endwhile;
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>


