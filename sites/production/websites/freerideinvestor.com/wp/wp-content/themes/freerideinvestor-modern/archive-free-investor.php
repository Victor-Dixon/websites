<?php
/**
 * Archive Template for Free Investors Custom Post Type
 *
 * @package FreeRideInvestor
 */

get_header();

$queried_object = get_queried_object();
$post_type = $queried_object->name ?? 'free_investor';
?>

<main class="content-area">
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">🤖 AI-Powered Investment Insights</span>
                <h1 class="hero-title">Free Investors</h1>
                <p class="hero-subtitle">
                    Discover AI-generated investment analysis and market insights.
                    Each Free Investor provides unique perspectives on market opportunities.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo wp_count_posts('free_investor')->publish; ?></span>
                        <span class="hero-stat-label">AI Investors</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">24/7</span>
                        <span class="hero-stat-label">Active Analysis</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">Real-time</span>
                        <span class="hero-stat-label">Market Data</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Archive Content -->
    <section class="archive-content">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="posts-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="post-card">
                            <div class="post-header">
                                <h2 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="post-meta">
                                    <span class="post-date"><?php echo get_the_date(); ?></span>
                                    <?php if (get_the_terms(get_the_ID(), 'stock-category')) : ?>
                                        <span class="post-categories">
                                            <?php echo get_the_term_list(get_the_ID(), 'stock-category', '', ', '); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="post-content">
                                <?php if (has_excerpt()) : ?>
                                    <p><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
                                <?php else : ?>
                                    <p><?php echo wp_trim_words(get_the_content(), 30); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="post-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more">Read Analysis →</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← Previous',
                        'next_text' => 'Next →',
                    ));
                    ?>
                </div>

            <?php else : ?>
                <div class="no-posts">
                    <div class="no-posts-icon">📊</div>
                    <h2>No Investment Analysis Available</h2>
                    <p>Our AI investors are currently analyzing market data. Check back soon for fresh insights.</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn-primary">Return Home</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Access Premium Investment Analysis?</h2>
                <p>Join our community for unlimited access to AI-powered investment insights and market analysis.</p>
                <div class="cta-buttons">
                    <a href="<?php echo home_url('/contact/'); ?>" class="btn-primary">Get Started</a>
                    <a href="<?php echo home_url('/'); ?>" class="btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.post-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid var(--border, #e1e5e9);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.post-title {
    margin: 0 0 1rem 0;
    font-size: 1.5rem;
}

.post-title a {
    color: var(--text-primary, #333);
    text-decoration: none;
    transition: color 0.3s ease;
}

.post-title a:hover {
    color: var(--primary-color, #007cba);
}

.post-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: var(--text-secondary, #666);
}

.post-content p {
    color: var(--text-primary, #333);
    line-height: 1.6;
    margin: 0;
}

.read-more {
    color: var(--primary-color, #007cba);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: var(--primary-hover, #005a87);
}

.pagination {
    text-align: center;
    margin-top: 3rem;
}

.no-posts {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--card-bg, #fff);
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.no-posts-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.btn-primary, .btn-secondary {
    display: inline-block;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    margin: 0.5rem;
}

.btn-primary {
    background: var(--primary-color, #007cba);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover, #005a87);
}

.btn-secondary {
    background: transparent;
    color: var(--primary-color, #007cba);
    border: 2px solid var(--primary-color, #007cba);
}

.btn-secondary:hover {
    background: var(--primary-color, #007cba);
    color: white;
}

.cta-section {
    background: linear-gradient(135deg, var(--primary-color, #007cba), var(--secondary-color, #005a87));
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.cta-content h2 {
    margin-bottom: 1rem;
    font-size: 2.5rem;
}

.cta-content p {
    margin-bottom: 2rem;
    font-size: 1.2rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}
</style>

<?php get_footer(); ?>