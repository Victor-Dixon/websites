<?php

/**
 * Single Post Template
 * 
 * @package FreeRideInvestor_Modern
 */

get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="entry-meta">
                            <span><?php echo get_the_date(); ?></span>
                            <span><?php echo get_the_author(); ?></span>
                            <?php if (has_category()) : ?>
                                <span><?php the_category(', '); ?></span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <footer class="entry-footer">
                        <?php if (has_tag()) : ?>
                            <div class="post-tags">
                                <?php the_tags('<span class="tags-label">Tags: </span>', ', ', ''); ?>
                            </div>
                        <?php endif; ?>
                    </footer>
                </article>

                <?php
                // Post navigation
                the_post_navigation(array(
                    'prev_text' => '<span class="nav-subtitle">' . __('Previous:', 'freerideinvestor-modern') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . __('Next:', 'freerideinvestor-modern') . '</span> <span class="nav-title">%title</span>',
                ));

                // Comments
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>
            <?php endwhile; ?>
        </div>

        <?php if (is_active_sidebar('sidebar-1')) : ?>
            <aside class="sidebar">
                <?php dynamic_sidebar('sidebar-1'); ?>
            </aside>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
