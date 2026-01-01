<?php

/**
 * Main Template File
 * 
 * @package FreeRideInvestor_Modern
 */

get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <?php if (have_posts()) : ?>
                <div class="posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large', array('class' => 'post-card-image')); ?>
                                </a>
                            <?php endif; ?>

                            <h2 class="post-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="post-card-excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <div class="post-card-meta">
                                <span><?php echo get_the_date(); ?></span>
                                <span><?php echo get_the_author(); ?></span>
                                <?php if (has_category()) : ?>
                                    <span><?php the_category(', '); ?></span>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <div class="pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('← Previous', 'freerideinvestor-modern'),
                        'next_text' => __('Next →', 'freerideinvestor-modern'),
                    ));
                    ?>
                </div>
            <?php else : ?>
                <div class="no-posts">
                    <h2><?php _e('Nothing Found', 'freerideinvestor-modern'); ?></h2>
                    <p><?php _e('It looks like nothing was found at this location. Maybe try a search?', 'freerideinvestor-modern'); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
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
