<?php
/**
 * Template Name: Blog
 * @package Crosbyultimateevents
 */
get_header();
?>
<main class="site-main">
    <div class="content-area">
        <section class="blog-section">
            <div class="container">
                <header class="entry-header">
                    <h1 class="page-title">Blog</h1>
                    <p class="page-subtitle">Latest news, tips, and insights from Crosby Ultimate Events</p>
                </header>

                <div class="blog-content">
                    <?php
                    // Query blog posts
                    $blog_query = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 10,
                        'post_status' => 'publish',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));

                    if ($blog_query->have_posts()) :
                    ?>
                        <div class="blog-posts-grid">
                            <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post-card'); ?>>
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium_large'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="post-content">
                                        <h2 class="post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <div class="post-meta">
                                            <span class="post-date"><?php echo get_the_date(); ?></span>
                                            <?php if (get_the_category()) : ?>
                                                <span class="post-category"><?php the_category(', '); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="post-excerpt">
                                            <?php the_excerpt(); ?>
                                        </div>
                                        
                                        <a href="<?php the_permalink(); ?>" class="read-more">Read More →</a>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                        
                        <?php
                        // Pagination
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => __('← Previous', 'crosbyultimateevents'),
                            'next_text' => __('Next →', 'crosbyultimateevents'),
                        ));
                        ?>
                        
                        <?php wp_reset_postdata(); ?>
                    <?php else : ?>
                        <div class="no-posts">
                            <p>No blog posts found. Check back soon for updates!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>
<?php get_footer(); ?>