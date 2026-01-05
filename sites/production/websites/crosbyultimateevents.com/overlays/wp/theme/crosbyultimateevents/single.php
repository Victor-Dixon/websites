<?php

/**
 * Single Post Template
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">

        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                <header class="entry-header">
                    <h1 class="post-title"><?php the_title(); ?></h1>
                    <div class="post-meta">
                        <span class="posted-on">
                            <?php echo get_the_date(); ?>
                        </span>
                        <?php if (has_category()) : ?>
                            <span class="cat-links">
                                <?php the_category(', '); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (has_tag()) : ?>
                            <span class="tags-links">
                                <?php the_tags('Tags: ', ', '); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <footer class="entry-footer">
                    <?php
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . __('Pages:', 'crosbyultimateevents'),
                        'after' => '</div>',
                    ));
                    ?>
                </footer>
            </article>

            <?php
            // Post navigation
            the_post_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . __('Previous:', 'crosbyultimateevents') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . __('Next:', 'crosbyultimateevents') . '</span> <span class="nav-title">%title</span>',
            ));

            // Comments
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>

        <?php endwhile; ?>

    </div>
</main>

<?php get_footer(); ?>