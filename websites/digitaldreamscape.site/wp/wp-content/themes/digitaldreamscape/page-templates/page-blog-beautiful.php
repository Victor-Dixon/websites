<?php
/**
 * Template Name: Beautiful Blog
 * 
 * Beautiful Blog Template
 * Modern, elegant blog listing with card-based design
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

get_header(); ?>

<main class="site-main beautiful-blog-main">
    <div class="beautiful-blog-container">
        <!-- Hero Header -->
        <header class="beautiful-blog-header">
            <div class="beautiful-blog-header-content">
                <div class="beautiful-blog-badge">[EPISODE ARCHIVE]</div>
                <h1 class="beautiful-blog-title">Narrative Episodes</h1>
                <p class="beautiful-blog-description">
                    <strong>Digital Dreamscape</strong> is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. Each episode below is part of the persistent simulation of self + system.
                </p>
            </div>
        </header>

        <!-- Blog Posts Grid -->
        <div class="beautiful-blog-grid">
            <?php
            // Query posts
            $blog_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 12,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1
            ));
            
            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('beautiful-blog-card'); ?>>
                        <!-- Card Image -->
                        <div class="beautiful-blog-card-image">
                            <a href="<?php the_permalink(); ?>" class="beautiful-blog-card-image-link">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'beautiful-blog-card-img')); ?>
                                <?php else : ?>
                                    <div class="beautiful-blog-card-gradient">
                                        <div class="beautiful-blog-card-pattern"></div>
                                    </div>
                                <?php endif; ?>
                                <div class="beautiful-blog-card-overlay">
                                    <span class="beautiful-blog-card-read">Read Episode →</span>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Card Content -->
                        <div class="beautiful-blog-card-content">
                            <!-- Meta -->
                            <div class="beautiful-blog-card-meta">
                                <time datetime="<?php echo get_the_date('c'); ?>" class="beautiful-blog-card-date">
                                    <?php echo get_the_date('M j, Y'); ?>
                                </time>
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    $category = $categories[0];
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="beautiful-blog-card-category">' . esc_html($category->name) . '</a>';
                                }
                                ?>
                            </div>
                            
                            <!-- Title -->
                            <h2 class="beautiful-blog-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <!-- Excerpt -->
                            <div class="beautiful-blog-card-excerpt">
                                <?php 
                                $excerpt = get_the_excerpt();
                                if (empty($excerpt)) {
                                    $excerpt = wp_trim_words(get_the_content(), 25);
                                }
                                echo '<p>' . esc_html($excerpt) . '</p>';
                                ?>
                            </div>
                            
                            <!-- Author & Badges -->
                            <div class="beautiful-blog-card-footer">
                                <div class="beautiful-blog-card-author">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 32); ?>
                                    <span class="beautiful-blog-card-author-name"><?php the_author(); ?></span>
                                </div>
                                <div class="beautiful-blog-card-badges">
                                    <span class="beautiful-blog-card-badge">[EPISODE]</span>
                                    <span class="beautiful-blog-card-badge">[CANON]</span>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
            else :
                ?>
                <div class="beautiful-blog-empty">
                    <p>No episodes found. Check back soon for new content.</p>
                </div>
                <?php
            endif;
            wp_reset_postdata();
            ?>
        </div>

        <!-- Pagination -->
        <?php if ($blog_query->max_num_pages > 1) : ?>
            <nav class="beautiful-blog-pagination">
                <?php
                echo paginate_links(array(
                    'total' => $blog_query->max_num_pages,
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                    'type' => 'list'
                ));
                ?>
            </nav>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

