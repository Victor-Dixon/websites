<?php
/**
 * Main Template File
 * 
 * @package Swarm_Theme
 */

get_header(); ?>

<main class="site-main">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="container">
                    <header class="entry-header">
                        <h2 class="entry-title"><?php the_title(); ?></h2>
                    </header>
                    
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </article>
            <?php
        endwhile;
    else :
        ?>
        <p class="text-center"><?php _e('No content found.', 'swarm-theme'); ?></p>
        <?php
    endif;
    ?>
</main>

<?php get_footer(); ?>

