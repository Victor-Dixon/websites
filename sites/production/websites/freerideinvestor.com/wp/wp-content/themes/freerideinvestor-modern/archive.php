<?php
/**
 * Archive Template - Stunning Blog Archive
 * 
 * Displays blog posts with stunning design matching the front page.
 * This template handles pagination correctly for archive URLs.
 * 
 * @package FreeRideInvestor
 */

get_header();
?>

<style>
/* Stunning Blog Page Styles - Matches Front Page */
.stunning-blog-page {
    background: linear-gradient(135deg, var(--fri-bg-darker) 0%, var(--fri-bg-dark) 100%);
    color: var(--fri-text-light);
    min-height: 100vh;
}

:root {
    --fri-primary: #0066ff;
    --fri-primary-dark: #0052cc;
    --fri-accent-green: #00c853;
    --fri-accent-gold: #ffb300;
    --fri-bg-dark: #0d1117;
    --fri-bg-darker: #010409;
    --fri-text-light: #f0f6fc;
    --fri-text-muted: #8b949e;
    --fri-border: rgba(240, 246, 252, 0.1);
}

/* Hero Section */
.stunning-blog-hero {
    position: relative;
    padding: 120px 20px 80px;
    text-align: center;
    overflow: hidden;
}

.stunning-blog-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(0, 102, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 50%, rgba(0, 200, 83, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.stunning-blog-hero-content {
    position: relative;
    z-index: 2;
    max-width: 900px;
    margin: 0 auto;
}

.stunning-blog-hero h1 {
    font-size: clamp(2.5rem, 5vw, 4.5rem);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 24px;
    background: linear-gradient(135deg, var(--fri-text-light) 0%, var(--fri-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
}

.stunning-blog-hero .tagline {
    font-size: clamp(1.1rem, 2vw, 1.5rem);
    color: var(--fri-text-muted);
    margin: 0 0 40px;
    line-height: 1.6;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

/* Posts Grid */
.stunning-blog-content {
    padding: 60px 20px 100px;
    max-width: 1200px;
    margin: 0 auto;
}

.stunning-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.stunning-post-card {
    background: rgba(240, 246, 252, 0.03);
    border: 1px solid var(--fri-border);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.stunning-post-card:hover {
    transform: translateY(-8px);
    background: rgba(240, 246, 252, 0.06);
    border-color: var(--fri-primary);
    box-shadow: 0 10px 40px rgba(0, 102, 255, 0.2);
}

.stunning-post-thumbnail {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: linear-gradient(135deg, var(--fri-primary) 0%, var(--fri-primary-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
}

.stunning-post-card-content {
    padding: 30px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.stunning-post-meta {
    font-size: 0.85rem;
    color: var(--fri-text-muted);
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stunning-post-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 16px;
    line-height: 1.4;
}

.stunning-post-title a {
    color: var(--fri-text-light);
    text-decoration: none;
    transition: color 0.3s ease;
}

.stunning-post-title a:hover {
    color: var(--fri-primary);
}

.stunning-post-excerpt {
    color: var(--fri-text-muted);
    line-height: 1.7;
    margin: 0 0 20px;
    flex: 1;
}

.stunning-post-link {
    color: var(--fri-primary);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: gap 0.3s ease;
}

.stunning-post-link:hover {
    gap: 12px;
}

/* Empty State */
.stunning-blog-empty {
    text-align: center;
    padding: 80px 20px;
}

.stunning-blog-empty h2 {
    font-size: 2rem;
    margin: 0 0 20px;
    color: var(--fri-text-light);
}

.stunning-blog-empty p {
    color: var(--fri-text-muted);
    font-size: 1.1rem;
}

/* Pagination */
.stunning-pagination {
    margin-top: 60px;
    text-align: center;
}

.stunning-pagination ul {
    display: inline-flex;
    list-style: none;
    padding: 0;
    gap: 10px;
}

.stunning-pagination li {
    display: inline-block;
}

.stunning-pagination a,
.stunning-pagination span {
    display: inline-block;
    padding: 10px 16px;
    background: rgba(240, 246, 252, 0.03);
    border: 1px solid var(--fri-border);
    color: var(--fri-text-light);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.stunning-pagination a:hover {
    background: rgba(240, 246, 252, 0.06);
    border-color: var(--fri-primary);
}

.stunning-pagination .current {
    background: var(--fri-primary);
    border-color: var(--fri-primary);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .stunning-blog-hero {
        padding: 80px 20px 60px;
    }
    
    .stunning-posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="stunning-blog-page">
    <!-- Hero Section -->
    <section class="stunning-blog-hero">
        <div class="stunning-blog-hero-content">
            <h1>Trading Insights & Strategy Analysis</h1>
            <p class="tagline">
                Deep dives into market trends, trading strategies, technical analysis, 
                and the tools that power smart investing.
            </p>
        </div>
    </section>
    
    <!-- Blog Content -->
    <div class="stunning-blog-content">
        <?php if (have_posts()) : ?>
            <div class="stunning-posts-grid">
                <?php while (have_posts()) : the_post(); 
                    $categories = get_the_category();
                    $category_name = !empty($categories) ? $categories[0]->name : '';
                ?>
                    <article class="stunning-post-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', [
                                    'class' => 'stunning-post-thumbnail',
                                    'alt' => esc_attr(get_the_title())
                                ]); ?>
                            </a>
                        <?php else : ?>
                            <div class="stunning-post-thumbnail">📊</div>
                        <?php endif; ?>
                        
                        <div class="stunning-post-card-content">
                            <?php if ($category_name) : ?>
                                <div class="stunning-post-meta"><?php echo esc_html($category_name); ?></div>
                            <?php endif; ?>
                            
                            <h2 class="stunning-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="stunning-post-excerpt">
                                <?php 
                                if (has_excerpt()) {
                                    the_excerpt();
                                } else {
                                    $content = get_the_content();
                                    $content = wp_strip_all_tags($content);
                                    echo wp_trim_words($content, 25);
                                }
                                ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="stunning-post-link">
                                Read More →
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <nav class="stunning-pagination">
                <?php
                the_posts_pagination([
                    'mid_size' => 2,
                    'prev_text' => '« Previous',
                    'next_text' => 'Next »',
                ]);
                ?>
            </nav>
        <?php else : ?>
            <div class="stunning-blog-empty">
                <h2>Welcome to the FreeRide Investor Blog</h2>
                <p>Check back soon for trading insights and strategy analysis.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>

