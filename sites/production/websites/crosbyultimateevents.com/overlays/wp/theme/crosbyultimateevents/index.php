<?php

/**
 * Main Template File
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">

        <?php if (have_posts()) : ?>

            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                    <header class="entry-header">
                        <h2 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="post-meta">
                            <span class="posted-on">
                                <?php echo get_the_date(); ?>
                            </span>
                            <?php if (has_category()) : ?>
                                <span class="cat-links">
                                    <?php the_category(', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <div class="post-content">
                        <?php
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('large', array('class' => 'post-thumbnail'));
                        }
                        the_excerpt();
                        ?>
                        <a href="<?php the_permalink(); ?>" class="btn-primary">Read More</a>
                    </div>
                </article>
            <?php endwhile; ?>

            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('&laquo; Previous', 'crosbyultimateevents'),
                    'next_text' => __('Next &raquo;', 'crosbyultimateevents'),
                ));
                ?>
            </div>

        <?php else : ?>
            <div class="no-posts">
                <h2><?php _e('Nothing Found', 'crosbyultimateevents'); ?></h2>
                <p><?php _e('It seems we can\'t find what you\'re looking for.', 'crosbyultimateevents'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>