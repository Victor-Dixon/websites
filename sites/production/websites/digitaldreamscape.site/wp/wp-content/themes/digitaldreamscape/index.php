<?php
/**
 * Main Template File
 * 
 * @package DigitalDreamscape
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="container">
        <div class="content-area">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </article>
                    <?php
                endwhile;
            else :
                ?>
                <div class="no-content">
                    <h1>Welcome to Digital Dreamscape</h1>
                    <p>This is a build-in-public & streaming hub. Content coming soon!</p>
                </div>
                <?php
            endif;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>

