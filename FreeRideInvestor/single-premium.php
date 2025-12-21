<?php
/**
 * Template Name: Single Post - Premium
 * Template Post Type: post
 * 
 * A beautiful, premium single post template for FreeRide Investor.
 * 
 * @package FreeRideInvestor
 */

get_header(); 
?>

<main class="blog-page">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('template-parts/content-single', 'premium');
    endwhile;
    ?>
    
    <!-- Related Posts Section -->
    <section class="related-posts">
        <div class="blog-container">
            <h2 class="related-posts__title">📚 Related Articles</h2>
            
            <div class="posts-grid">
                <?php
                $categories = get_the_category();
                $category_ids = array();
                foreach ($categories as $cat) {
                    $category_ids[] = $cat->term_id;
                }
                
                $related = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post__not_in' => array(get_the_ID()),
                    'category__in' => $category_ids,
                    'orderby' => 'rand',
                ));
                
                if ($related->have_posts()) :
                    while ($related->have_posts()) :
                        $related->the_post();
                        
                        $cat = get_the_category();
                        $category_name = !empty($cat) ? $cat[0]->name : 'Article';
                        
                        $content = get_the_content();
                        $word_count = str_word_count(strip_tags($content));
                        $read_time = ceil($word_count / 200);
                        
                        $author_name = get_the_author();
                        $author_initial = strtoupper(substr($author_name, 0, 1));
                        ?>
                        
                        <article class="post-card animate-in">
                            <div class="post-card__image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('large'); ?>
                                    </a>
                                <?php else : ?>
                                    <div class="post-card__image-placeholder">📈</div>
                                <?php endif; ?>
                                
                                <span class="post-card__category"><?php echo esc_html($category_name); ?></span>
                            </div>
                            
                            <div class="post-card__body">
                                <div class="post-card__meta">
                                    <span class="post-card__date"><?php echo get_the_date('M j, Y'); ?></span>
                                    <span class="post-card__read-time">⏱️ <?php echo $read_time; ?> min</span>
                                </div>
                                
                                <h3 class="post-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <p class="post-card__excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 18); ?>
                                </p>
                                
                                <div class="post-card__footer">
                                    <div class="post-card__author">
                                        <span class="post-card__author-avatar"><?php echo esc_html($author_initial); ?></span>
                                        <span class="post-card__author-name"><?php echo esc_html($author_name); ?></span>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="post-card__read-more">
                                        Read more →
                                    </a>
                                </div>
                            </div>
                        </article>
                        
                    <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </section>
</main>

<style>
.related-posts {
    padding: var(--blog-space-4xl, 6rem) 0;
    background: var(--blog-bg-secondary, #141414);
    border-top: 1px solid var(--blog-border, rgba(255,255,255,0.06));
}

.related-posts__title {
    font-family: var(--blog-font-display, 'Inter', sans-serif);
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--blog-text-primary, #F5F5F5);
    margin: 0 0 var(--blog-space-2xl, 3rem);
    text-align: center;
}

.related-posts .posts-grid {
    grid-template-columns: repeat(3, 1fr);
}

@media (max-width: 1024px) {
    .related-posts .posts-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .related-posts .posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>
