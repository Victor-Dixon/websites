<?php
/**
 * Archive Template for TBOW Tactics Custom Post Type
 *
 * @package FreeRideInvestor
 */

get_header();

$queried_object = get_queried_object();
$post_type = $queried_object->name ?? 'tbow_tactics';
?>

<main class="content-area">
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">🎯 Tactical Trading Methods</span>
                <h1 class="hero-title">TBOW Tactics</h1>
                <p class="hero-subtitle">
                    Tactical Buy On Weakness strategies and advanced trading techniques.
                    Master the art of identifying buying opportunities in downtrends.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo wp_count_posts('tbow_tactics')->publish; ?></span>
                        <span class="hero-stat-label">TBOW Tactics</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">Advanced</span>
                        <span class="hero-stat-label">Strategies</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">Proven</span>
                        <span class="hero-stat-label">Results</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Archive Content -->
    <section class="archive-content">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="tactics-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="tactic-card">
                            <div class="tactic-header">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="tactic-badge">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </div>
                                <?php else : ?>
                                    <div class="tactic-badge default">
                                        🎯
                                    </div>
                                <?php endif; ?>

                                <div class="tactic-content">
                                    <h2 class="tactic-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>

                                    <div class="tactic-meta">
                                        <span class="post-date"><?php echo get_the_date(); ?></span>
                                        <span class="reading-time"><?php echo get_field('reading_time') ?: '5 min read'; ?></span>
                                        <?php if (get_the_terms(get_the_ID(), 'stock-category')) : ?>
                                            <span class="post-categories">
                                                <?php echo get_the_term_list(get_the_ID(), 'stock-category', '', ', '); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="tactic-excerpt">
                                        <?php if (has_excerpt()) : ?>
                                            <p><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
                                        <?php else : ?>
                                            <p><?php echo wp_trim_words(get_the_content(), 30); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="tactic-key-points">
                                        <?php if (get_field('key_points')) : ?>
                                            <ul>
                                                <?php
                                                $key_points = get_field('key_points');
                                                if (is_array($key_points)) {
                                                    $key_points = array_slice($key_points, 0, 3); // Show first 3
                                                    foreach ($key_points as $point) {
                                                        echo '<li>' . esc_html($point) . '</li>';
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>

                                    <div class="tactic-footer">
                                        <a href="<?php the_permalink(); ?>" class="learn-more">Master This Tactic →</a>
                                    </div>
                                </div>
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
                    <div class="no-posts-icon">🎯</div>
                    <h2>No TBOW Tactics Available</h2>
                    <p>Advanced tactical strategies are being developed. Stay tuned for comprehensive TBOW methodologies.</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn-primary">Return Home</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Strategy Overview -->
    <section class="strategy-overview">
        <div class="container">
            <div class="overview-content">
                <h2>What is TBOW Trading?</h2>
                <div class="overview-grid">
                    <div class="overview-card">
                        <h3>🎯 Tactical Buy On Weakness</h3>
                        <p>Identify buying opportunities during market pullbacks and weakness. Master the art of counter-trend trading.</p>
                    </div>
                    <div class="overview-card">
                        <h3>📊 Risk Management</h3>
                        <p>Advanced position sizing and risk control techniques specifically designed for TBOW strategies.</p>
                    </div>
                    <div class="overview-card">
                        <h3>⚡ Execution Timing</h3>
                        <p>Precise entry and exit timing to maximize profits while minimizing risk in volatile markets.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Master TBOW Tactics?</h2>
                <p>Join our premium community for advanced tactical training, real-time strategy signals, and expert guidance.</p>
                <div class="cta-buttons">
                    <a href="<?php echo home_url('/contact/'); ?>" class="btn-primary">Start Learning</a>
                    <a href="<?php echo home_url('/free-investors/'); ?>" class="btn-secondary">Try Free Analysis</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.tactics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.tactic-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid var(--border, #e1e5e9);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.tactic-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.tactic-header {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
}

.tactic-badge {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-color, #007cba);
    color: white;
    font-size: 2rem;
}

.tactic-badge.default {
    background: linear-gradient(135deg, var(--primary-color, #007cba), var(--secondary-color, #005a87));
}

.tactic-badge img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.tactic-content {
    flex: 1;
}

.tactic-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.tactic-title a {
    color: var(--text-primary, #333);
    text-decoration: none;
    transition: color 0.3s ease;
}

.tactic-title a:hover {
    color: var(--primary-color, #007cba);
}

.tactic-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: var(--text-secondary, #666);
    flex-wrap: wrap;
}

.tactic-excerpt p {
    color: var(--text-primary, #333);
    line-height: 1.6;
    margin: 0 0 1rem 0;
}

.tactic-key-points {
    margin-bottom: 1rem;
}

.tactic-key-points ul {
    margin: 0;
    padding-left: 1.2rem;
}

.tactic-key-points li {
    color: var(--text-primary, #333);
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.learn-more {
    color: var(--primary-color, #007cba);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.learn-more:hover {
    color: var(--primary-hover, #005a87);
}

.strategy-overview {
    background: var(--surface, #f8f9fa);
    padding: 4rem 0;
}

.strategy-overview h2 {
    text-align: center;
    margin-bottom: 3rem;
    color: var(--text-primary, #333);
}

.overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.overview-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid var(--border, #e1e5e9);
}

.overview-card h3 {
    margin-bottom: 1rem;
    color: var(--text-primary, #333);
}

.overview-card p {
    color: var(--text-secondary, #666);
    line-height: 1.6;
    margin: 0;
}

/* Reuse existing styles */
.pagination, .no-posts, .btn-primary, .btn-secondary, .cta-section, .cta-content, .cta-buttons {
    /* Same styles as other archives */
}
</style>

<?php get_footer(); ?>