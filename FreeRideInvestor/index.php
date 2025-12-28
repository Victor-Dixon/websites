<?php
/**
 * The main template file for FreeRideInvestor
 *
 * Displays the latest blog posts with additional FreeRideInvestor-specific features.
 *
 * @package FreeRideInvestor
 */

get_header(); 
?>

<main id="main-content" class="site-main">
    <div class="container">

        <!-- Hero Section with Positioning Statement -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title"><?php esc_html_e('Stop Losing Money on Untested Trading Strategies', 'freerideinvestor'); ?></h1>
                <p class="hero-description"><?php esc_html_e('For traders tired of generic advice and backtest-only bots that fail in live markets. We provide actionable TBOW tactics and proven strategies—validated through rigorous paper trading—that actually work in real market conditions.', 'freerideinvestor'); ?></p>
                <div class="hero-cta-group">
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cta-button cta-primary"><?php esc_html_e('Get Started Free', 'freerideinvestor'); ?></a>
                    <a href="#tbow-tactics-section" class="cta-button cta-secondary"><?php esc_html_e('See Our Tactics', 'freerideinvestor'); ?></a>
                </div>
                <p class="hero-subtext"><?php esc_html_e('Unlike theory-heavy courses or untested signal services—we focus on practical, tested methods.', 'freerideinvestor'); ?></p>
            </div>
        </section>

        <!-- Offer Ladder Section - BRAND-02 Tier 2 -->
        <section id="offer-ladder-section" class="content-section offer-ladder">
            <h2 class="section-title"><?php esc_html_e('Your Path to Profitable Trading', 'freerideinvestor'); ?></h2>
            <p class="section-description"><?php esc_html_e('From free resources to premium strategies—choose your level.', 'freerideinvestor'); ?></p>
            
            <div class="offer-grid">
                <!-- Tier 1: Free Content -->
                <div class="offer-tier offer-free">
                    <div class="offer-badge"><?php esc_html_e('FREE', 'freerideinvestor'); ?></div>
                    <h3 class="offer-title"><?php esc_html_e('TBOW Tactics Blog', 'freerideinvestor'); ?></h3>
                    <p class="offer-description"><?php esc_html_e('Actionable trading strategies, market insights, and proven tactics—free forever.', 'freerideinvestor'); ?></p>
                    <ul class="offer-features">
                        <li><?php esc_html_e('✓ Weekly strategy posts', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Market analysis', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Trading psychology tips', 'freerideinvestor'); ?></li>
                    </ul>
                    <a href="#tbow-tactics-section" class="offer-cta"><?php esc_html_e('Read Latest Tactics →', 'freerideinvestor'); ?></a>
                </div>

                <!-- Tier 2: Lead Magnets -->
                <div class="offer-tier offer-starter">
                    <div class="offer-badge"><?php esc_html_e('FREE DOWNLOAD', 'freerideinvestor'); ?></div>
                    <h3 class="offer-title"><?php esc_html_e('Trading Resource Pack', 'freerideinvestor'); ?></h3>
                    <p class="offer-description"><?php esc_html_e('Essential PDFs to accelerate your trading journey—Trading Roadmap + Mindset Journal.', 'freerideinvestor'); ?></p>
                    <ul class="offer-features">
                        <li><?php esc_html_e('✓ Trading Roadmap PDF', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Mindset Journal Template', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Strategy Cheat Sheet', 'freerideinvestor'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/resources/')); ?>" class="offer-cta"><?php esc_html_e('Download Free Resources →', 'freerideinvestor'); ?></a>
                </div>

                <!-- Tier 3: Newsletter -->
                <div class="offer-tier offer-growth">
                    <div class="offer-badge offer-popular"><?php esc_html_e('POPULAR', 'freerideinvestor'); ?></div>
                    <h3 class="offer-title"><?php esc_html_e('Weekly Newsletter', 'freerideinvestor'); ?></h3>
                    <p class="offer-description"><?php esc_html_e('Get exclusive insights, trade setups, and market analysis delivered to your inbox.', 'freerideinvestor'); ?></p>
                    <ul class="offer-features">
                        <li><?php esc_html_e('✓ Weekly trade setups', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Exclusive market analysis', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Early access to content', 'freerideinvestor'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/newsletter/')); ?>" class="offer-cta offer-cta-primary"><?php esc_html_e('Subscribe Now →', 'freerideinvestor'); ?></a>
                </div>

                <!-- Tier 4: Premium (Coming Soon) -->
                <div class="offer-tier offer-premium">
                    <div class="offer-badge"><?php esc_html_e('COMING SOON', 'freerideinvestor'); ?></div>
                    <h3 class="offer-title"><?php esc_html_e('Premium Membership', 'freerideinvestor'); ?></h3>
                    <p class="offer-description"><?php esc_html_e('Advanced strategies, exclusive courses, and performance analytics.', 'freerideinvestor'); ?></p>
                    <ul class="offer-features">
                        <li><?php esc_html_e('✓ Advanced TBOW strategies', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Trading courses', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Performance dashboard', 'freerideinvestor'); ?></li>
                        <li><?php esc_html_e('✓ Community access', 'freerideinvestor'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="offer-cta offer-cta-secondary"><?php esc_html_e('Join Waitlist →', 'freerideinvestor'); ?></a>
                </div>
            </div>
        </section>

        <!-- Proof & Trust Section - WEB-02 Tier 2 -->
        <section id="proof-section" class="content-section proof-trust">
            <h2 class="section-title"><?php esc_html_e('Why Traders Trust FreeRideInvestor', 'freerideinvestor'); ?></h2>
            <p class="section-description"><?php esc_html_e('Transparent results. Proven methods. Real accountability.', 'freerideinvestor'); ?></p>
            
            <div class="proof-metrics">
                <div class="metric-card">
                    <span class="metric-value">12+</span>
                    <span class="metric-label"><?php esc_html_e('Strategies Tested', 'freerideinvestor'); ?></span>
                </div>
                <div class="metric-card">
                    <span class="metric-value">100%</span>
                    <span class="metric-label"><?php esc_html_e('Paper Trade Validated', 'freerideinvestor'); ?></span>
                </div>
                <div class="metric-card">
                    <span class="metric-value">24/7</span>
                    <span class="metric-label"><?php esc_html_e('Market Monitoring', 'freerideinvestor'); ?></span>
                </div>
                <div class="metric-card">
                    <span class="metric-value">0</span>
                    <span class="metric-label"><?php esc_html_e('Hidden Fees', 'freerideinvestor'); ?></span>
                </div>
            </div>
            
            <div class="trust-elements">
                <div class="trust-item">
                    <span class="trust-icon">📊</span>
                    <h4><?php esc_html_e('Transparent Performance', 'freerideinvestor'); ?></h4>
                    <p><?php esc_html_e('Every strategy is paper-traded and results shared publicly—no cherry-picking.', 'freerideinvestor'); ?></p>
                </div>
                <div class="trust-item">
                    <span class="trust-icon">🔍</span>
                    <h4><?php esc_html_e('Build in Public', 'freerideinvestor'); ?></h4>
                    <p><?php esc_html_e('Follow along as we develop, test, and refine strategies in real-time.', 'freerideinvestor'); ?></p>
                </div>
                <div class="trust-item">
                    <span class="trust-icon">🎯</span>
                    <h4><?php esc_html_e('No Guru Promises', 'freerideinvestor'); ?></h4>
                    <p><?php esc_html_e('We focus on education and tested methods—not get-rich-quick schemes.', 'freerideinvestor'); ?></p>
                </div>
            </div>
        </section>

        <!-- Dev-Log Section -->
        <section id="dev-log-section" class="content-section">
            <h2 class="section-title"><?php esc_html_e('Dev-Log', 'freerideinvestor'); ?></h2>
            <p class="section-description"><?php esc_html_e('Follow along as I build and refine FreeRideInvestor—one step at a time.', 'freerideinvestor'); ?></p>
            <div class="post-grid">
                <?php
                $dev_log_query = new WP_Query([
                    'category_name'  => 'dev-log',
                    'posts_per_page' => 3,
                ]);

                if ($dev_log_query->have_posts()) :
                    while ($dev_log_query->have_posts()) : $dev_log_query->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', ['alt' => esc_attr(get_the_title())]); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <h3 class="post-title">
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </article>
                    <?php endwhile; 
                else : ?>
                    <p><?php esc_html_e('No Dev-Log entries yet. Stay tuned!', 'freerideinvestor'); ?></p>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        </section>

        <!-- Tbow Tactics Section -->
        <section id="tbow-tactics-section" class="content-section">
            <h2 class="section-title"><?php esc_html_e('Tbow Tactics', 'freerideinvestor'); ?></h2>
            <p class="section-description"><?php esc_html_e('Actionable strategies and techniques to up your trading game.', 'freerideinvestor'); ?></p>
            <div class="post-grid">
                <?php
                $tbow_tactics_query = new WP_Query([
                    'category_name'  => 'tbow-tactics',
                    'posts_per_page' => 3,
                ]);

                if ($tbow_tactics_query->have_posts()) :
                    while ($tbow_tactics_query->have_posts()) : $tbow_tactics_query->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', ['alt' => esc_attr(get_the_title())]); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <h3 class="post-title">
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </article>
                    <?php endwhile; 
                else : ?>
                    <p><?php esc_html_e('No Tbow Tactics posts yet. Let\'s create some winners!', 'freerideinvestor'); ?></p>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        </section>

        <!-- Journal Insights Section -->
        <section id="journal-insights-section" class="content-section">
            <h2 class="section-title"><?php esc_html_e('Journal Insights', 'freerideinvestor'); ?></h2>
            <p class="section-description"><?php esc_html_e('Reflections on trades, strategies, and lessons learned.', 'freerideinvestor'); ?></p>
            <div class="journal-grid">
                <div class="journal-category">
                    <h3><?php esc_html_e('Best of the Winners', 'freerideinvestor'); ?></h3>
                    <div class="post-list">
                        <?php
                        $best_winners_query = new WP_Query([
                            'category_name'  => 'best-of-the-winners',
                            'posts_per_page' => 3,
                        ]);

                        if ($best_winners_query->have_posts()) :
                            while ($best_winners_query->have_posts()) : $best_winners_query->the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                                    <h4 class="post-title">
                                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h4>
                                    <div class="post-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </article>
                            <?php endwhile; 
                        else : ?>
                            <p><?php esc_html_e('No insights yet for the winners. Let\'s log those wins!', 'freerideinvestor'); ?></p>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                </div>

                <div class="journal-category">
                    <h3><?php esc_html_e('Best of the Worst', 'freerideinvestor'); ?></h3>
                    <div class="post-list">
                        <?php
                        $best_worst_query = new WP_Query([
                            'category_name'  => 'best-of-the-worst',
                            'posts_per_page' => 3,
                        ]);

                        if ($best_worst_query->have_posts()) :
                            while ($best_worst_query->have_posts()) : $best_worst_query->the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                                    <h4 class="post-title">
                                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h4>
                                    <div class="post-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </article>
                            <?php endwhile; 
                        else : ?>
                            <p><?php esc_html_e('No insights yet for the worst. Let\'s learn from those mistakes!', 'freerideinvestor'); ?></p>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
