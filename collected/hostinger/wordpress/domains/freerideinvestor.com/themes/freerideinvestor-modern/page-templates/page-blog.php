<?php
/**
 * Template Name: Blog Archive - Premium
 * 
 * A beautiful, modern blog archive page for FreeRide Investor.
 * Features a hero section, filterable grid, and sidebar.
 * 
 * @package FreeRideInvestor
 */

get_header(); 
?>

<main class="blog-page">
    
    <!-- ═══════════════════════════════════════
         BLOG HERO SECTION
         ═══════════════════════════════════════ -->
    <section class="blog-hero">
        <div class="blog-container">
            <div class="blog-hero__content">
                <span class="blog-hero__badge">
                    <span class="blog-hero__badge-icon">📊</span>
                    FreeRide Insights
                </span>
                
                <h1 class="blog-hero__title">
                    Market Intelligence<br>
                    & <span>Trading Insights</span>
                </h1>
                
                <p class="blog-hero__subtitle">
                    Deep dives into market trends, trading strategies, technical analysis, 
                    and the tools that power smart investing. Learn, grow, and trade smarter.
                </p>
            </div>
        </div>
    </section>
    
    <!-- ═══════════════════════════════════════
         BLOG CONTENT AREA
         ═══════════════════════════════════════ -->
    <div class="blog-container">
        <div class="blog-layout">
            
            <!-- Main Content -->
            <div class="blog-main">
                
                <!-- Filter Bar -->
                <div class="blog-filters">
                    <div class="blog-filters__categories">
                        <button class="blog-filter-btn active" data-filter="all">All Posts</button>
                        <?php
                        $categories = get_categories(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 5,
                            'hide_empty' => true,
                        ));
                        
                        foreach ($categories as $cat) :
                        ?>
                            <button class="blog-filter-btn" data-filter="<?php echo esc_attr($cat->slug); ?>">
                                <?php echo esc_html($cat->name); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="blog-filters__search">
                        <span class="blog-filters__search-icon">🔍</span>
                        <input type="text" 
                               class="blog-filters__search-input" 
                               placeholder="Search articles..."
                               id="blog-search">
                    </div>
                </div>
                
                <!-- Posts Grid -->
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $blog_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 8,
                    'paged' => $paged,
                    'post_status' => 'publish',
                ));
                
                if ($blog_query->have_posts()) :
                ?>
                <div class="posts-grid">
                    <?php
                    $post_count = 0;
                    while ($blog_query->have_posts()) :
                        $blog_query->the_post();
                        $post_count++;
                        
                        // Get post data
                        $categories = get_the_category();
                        $category_name = !empty($categories) ? $categories[0]->name : 'Uncategorized';
                        $category_slug = !empty($categories) ? $categories[0]->slug : '';
                        
                        // Estimate read time
                        $content = get_the_content();
                        $word_count = str_word_count(strip_tags($content));
                        $read_time = ceil($word_count / 200);
                        
                        // Author info
                        $author_name = get_the_author();
                        $author_initial = strtoupper(substr($author_name, 0, 1));
                        
                        // Featured class for first post
                        $card_class = ($post_count === 1 && $paged === 1) ? 'post-card post-card--featured animate-in' : 'post-card animate-in';
                        ?>
                        
                        <article class="<?php echo esc_attr($card_class); ?>" data-category="<?php echo esc_attr($category_slug); ?>">
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
                                    <span class="post-card__date">
                                        <?php echo get_the_date('M j, Y'); ?>
                                    </span>
                                    <span class="post-card__read-time">
                                        ⏱️ <?php echo $read_time; ?> min read
                                    </span>
                                </div>
                                
                                <h2 class="post-card__title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <p class="post-card__excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
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
                        
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <nav class="blog-pagination">
                    <?php
                    echo paginate_links(array(
                        'total' => $blog_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '← Previous',
                        'next_text' => 'Next →',
                        'type' => 'plain',
                    ));
                    ?>
                </nav>
                
                <?php
                wp_reset_postdata();
                else :
                ?>
                
                <!-- No Posts Found -->
                <div class="blog-empty">
                    <div class="blog-empty__icon">📝</div>
                    <h3 class="blog-empty__title">No articles yet</h3>
                    <p class="blog-empty__text">We're working on some great content. Check back soon!</p>
                </div>
                
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="blog-sidebar">
                
                <!-- Newsletter Widget -->
                <div class="sidebar-widget newsletter-widget">
                    <h3 class="sidebar-widget__title">📬 Stay Updated</h3>
                    <p class="newsletter-widget__text">
                        Get the latest market insights and trading strategies delivered to your inbox.
                    </p>
                    <form class="newsletter-form" action="#" method="post">
                        <input type="email" name="email" placeholder="Enter your email" required>
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
                
                <!-- Popular Posts -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">🔥 Popular Posts</h3>
                    <div class="popular-posts">
                        <?php
                        $popular = new WP_Query(array(
                            'post_type' => 'post',
                            'posts_per_page' => 4,
                            'orderby' => 'comment_count',
                            'order' => 'DESC',
                        ));
                        
                        if ($popular->have_posts()) :
                            while ($popular->have_posts()) :
                                $popular->the_post();
                                ?>
                                <div class="popular-post">
                                    <div class="popular-post__image">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('thumbnail'); ?>
                                        <?php else : ?>
                                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--blog-bg-elevated);">📊</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="popular-post__content">
                                        <h4 class="popular-post__title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h4>
                                        <span class="popular-post__date"><?php echo get_the_date('M j, Y'); ?></span>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>
                
                <!-- Categories -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">📂 Categories</h3>
                    <ul class="category-list">
                        <?php
                        $all_categories = get_categories(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'hide_empty' => true,
                        ));
                        
                        foreach ($all_categories as $cat) :
                        ?>
                            <li>
                                <a href="<?php echo get_category_link($cat->term_id); ?>">
                                    <span><?php echo esc_html($cat->name); ?></span>
                                    <span class="count"><?php echo $cat->count; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- Tags Cloud -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">🏷️ Tags</h3>
                    <div class="tag-cloud">
                        <?php
                        $tags = get_tags(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 15,
                        ));
                        
                        foreach ($tags as $tag) :
                        ?>
                            <a href="<?php echo get_tag_link($tag->term_id); ?>" class="tag-cloud__tag">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
            </aside>
            
        </div>
    </div>
    
</main>

<script>
// Simple filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.blog-filter-btn');
    const posts = document.querySelectorAll('.post-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter posts
            posts.forEach(post => {
                const category = post.dataset.category;
                if (filter === 'all' || category === filter) {
                    post.style.display = '';
                    post.style.opacity = '1';
                } else {
                    post.style.opacity = '0';
                    setTimeout(() => {
                        post.style.display = 'none';
                    }, 250);
                }
            });
        });
    });
    
    // Simple search
    const searchInput = document.getElementById('blog-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            posts.forEach(post => {
                const title = post.querySelector('.post-card__title').textContent.toLowerCase();
                const excerpt = post.querySelector('.post-card__excerpt').textContent.toLowerCase();
                
                if (title.includes(query) || excerpt.includes(query)) {
                    post.style.display = '';
                } else {
                    post.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php get_footer(); ?>
