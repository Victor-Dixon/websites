<?php
/**
 * Template Name: Trading Strategies
 * Template Post Type: page
 *
 * The template for displaying trading strategies page
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">

                <?php while (have_posts()) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('trading-strategies-page'); ?>>
                        <header class="entry-header">
                            <div class="hero-section">
                                <h1 class="entry-title"><?php the_title(); ?></h1>
                                <div class="hero-description">
                                    <?php if (get_field('hero_description')) : ?>
                                        <p><?php echo esc_html(get_field('hero_description')); ?></p>
                                    <?php else : ?>
                                        <p>Discover proven trading strategies designed for consistent returns and risk management.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </header>

                        <div class="entry-content">
                            <div class="strategies-grid">
                                <!-- Strategy Categories -->
                                <section class="strategy-categories">
                                    <h2>Strategy Categories</h2>
                                    <div class="categories-grid">
                                        <div class="category-card">
                                            <h3>Trend Following</h3>
                                            <p>Ride market trends for consistent profits</p>
                                            <a href="#trend-following" class="btn">Explore</a>
                                        </div>
                                        <div class="category-card">
                                            <h3>Mean Reversion</h3>
                                            <p>Capitalize on price deviations from fair value</p>
                                            <a href="#mean-reversion" class="btn">Explore</a>
                                        </div>
                                        <div class="category-card">
                                            <h3>Scalping</h3>
                                            <p>Quick trades for small, frequent profits</p>
                                            <a href="#scalping" class="btn">Explore</a>
                                        </div>
                                        <div class="category-card">
                                            <h3>Swing Trading</h3>
                                            <p>Capture multi-day price movements</p>
                                            <a href="#swing-trading" class="btn">Explore</a>
                                        </div>
                                    </div>
                                </section>

                                <!-- Featured Strategy -->
                                <section class="featured-strategy">
                                    <h2>Featured Strategy</h2>
                                    <?php if (get_field('featured_strategy')) : ?>
                                        <?php echo get_field('featured_strategy'); ?>
                                    <?php else : ?>
                                        <div class="strategy-highlight">
                                            <h3>FreeRide Momentum Strategy</h3>
                                            <div class="strategy-stats">
                                                <div class="stat">
                                                    <span class="stat-label">Win Rate</span>
                                                    <span class="stat-value">68%</span>
                                                </div>
                                                <div class="stat">
                                                    <span class="stat-label">Avg Return</span>
                                                    <span class="stat-value">12.5%</span>
                                                </div>
                                                <div class="stat">
                                                    <span class="stat-label">Max Drawdown</span>
                                                    <span class="stat-value">8.2%</span>
                                                </div>
                                            </div>
                                            <p>Our proprietary momentum strategy combines technical indicators with market sentiment analysis to identify high-probability trade setups.</p>
                                            <a href="#" class="btn btn-primary">Get Strategy Details</a>
                                        </div>
                                    <?php endif; ?>
                                </section>

                                <!-- Main Content -->
                                <div class="strategy-content">
                                    <?php the_content(); ?>
                                </div>

                                <!-- Risk Warning -->
                                <section class="risk-warning">
                                    <div class="warning-box">
                                        <h3>⚠️ Important Risk Warning</h3>
                                        <p>Trading financial instruments involves significant risk. Past performance does not guarantee future results. Only trade with money you can afford to lose.</p>
                                        <p>All strategies are for educational purposes only and should not be considered as financial advice.</p>
                                    </div>
                                </section>
                            </div>

                            <?php
                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . __('Pages:', 'freerideinvestor'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>
                    </article>

                <?php endwhile; ?>

            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<style>
.trading-strategies-page .hero-section {
    text-align: center;
    padding: 3rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-bottom: 3rem;
}

.trading-strategies-page .hero-description {
    font-size: 1.2rem;
    margin: 1rem 0 0 0;
    opacity: 0.9;
}

.strategies-grid {
    display: grid;
    gap: 3rem;
}

.strategy-categories h2,
.featured-strategy h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.category-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.2s ease;
}

.category-card:hover {
    transform: translateY(-2px);
}

.category-card h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.category-card p {
    color: #666;
    margin-bottom: 1.5rem;
}

.strategy-highlight {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.strategy-highlight h3 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
}

.strategy-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.stat-label {
    display: block;
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.risk-warning {
    margin-top: 3rem;
}

.warning-box {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
}

.warning-box h3 {
    color: #856404;
    margin-bottom: 1rem;
}

.warning-box p {
    color: #856404;
    margin-bottom: 0.5rem;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #667eea;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background 0.2s ease;
}

.btn:hover {
    background: #5a67d8;
}

.btn-primary {
    background: #48bb78;
}

.btn-primary:hover {
    background: #38a169;
}
</style>

<?php
get_footer();
?>