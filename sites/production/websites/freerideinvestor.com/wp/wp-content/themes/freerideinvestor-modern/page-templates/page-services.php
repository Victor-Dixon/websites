<?php
/**
 * Template Name: Services Page
 * Description: Professional services page template with cohesive design and conversion elements.
 */

get_header(); ?>

<main class="content-area">
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">🚀 Professional Trading Services</span>
                <h1 class="hero-title">Our Services</h1>
                <p class="hero-subtitle">
                    Discover comprehensive trading tools, strategies, and resources designed to elevate your investment journey.
                    From AI-powered analysis to educational content, we provide everything you need to succeed.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value">10+</span>
                        <span class="hero-stat-label">Core Services</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">24/7</span>
                        <span class="hero-stat-label">Support</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">Proven</span>
                        <span class="hero-stat-label">Results</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="services-overview">
        <div class="container">
            <div class="overview-content" style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2>What We Offer</h2>
                <p style="font-size: 1.2rem; line-height: 1.6; margin-bottom: 3rem; color: var(--text-secondary, #666);">
                    FreeRideInvestor provides comprehensive trading solutions designed to empower your financial decisions.
                    From AI-powered strategies to educational resources, we deliver the tools and insights you need to succeed.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section class="services-section">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 3rem; color: var(--text-primary, #333);">Our Core Services</h2>

            <div class="services-grid">
                <!-- Trading Strategies -->
                <div class="service-card">
                    <div class="service-icon">📈</div>
                    <h3>AI-Powered Trading Strategies</h3>
                    <p>Leverage our sophisticated algorithms and machine learning models to optimize your trades and maximize returns.</p>
                    <a href="<?php echo esc_url(home_url('/trading-strategies/')); ?>" class="service-link">Explore Strategies →</a>
                </div>

                <!-- Real-Time Data -->
                <div class="service-card">
                    <div class="service-icon">📊</div>
                    <h3>Real-Time Market Data</h3>
                    <p>Access comprehensive market data, trends, sentiment analysis, and real-time price tracking tools.</p>
                    <a href="<?php echo esc_url(home_url('/free-investors/')); ?>" class="service-link">View Data →</a>
                </div>

                <!-- Educational Resources -->
                <div class="service-card">
                    <div class="service-icon">🎓</div>
                    <h3>Educational Resources</h3>
                    <p>Master trading fundamentals with our comprehensive tutorials, cheat sheets, and expert webinars.</p>
                    <a href="<?php echo esc_url(home_url('/cheat-sheets/')); ?>" class="service-link">Learn More →</a>
                </div>

                <!-- Premium Analysis -->
                <div class="service-card featured">
                    <div class="service-icon">⭐</div>
                    <h3>Premium Analysis</h3>
                    <p>Get exclusive access to advanced market analysis, expert recommendations, and VIP trading signals.</p>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="service-link">Get Premium →</a>
                </div>
            </div>
        </div>
    </section>

  <!-- Subscription Section -->
  <section class="subscription-section" id="subscribe" aria-labelledby="subscribe-heading">
    <h2 id="subscribe-heading" class="section-heading"><?php esc_html_e('Stay Updated', 'mergeddarkgreenblacktheme'); ?></h2>
    <p>
      <?php esc_html_e('Subscribe to our newsletter to receive the latest trading strategies, market insights, and updates.', 'mergeddarkgreenblacktheme'); ?>
    </p>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="subscription-form">
      <?php wp_nonce_field('subscription_form', 'subscription_nonce'); ?>
      <input type="hidden" name="action" value="handle_subscription">
      <label for="subscription-email" class="screen-reader-text"><?php esc_html_e('Email Address', 'mergeddarkgreenblacktheme'); ?></label>
      <input type="email" id="subscription-email" name="email" placeholder="<?php esc_attr_e('Your email address', 'mergeddarkgreenblacktheme'); ?>" required>
      <button type="submit" class="cta-button"><?php esc_html_e('Subscribe', 'mergeddarkgreenblacktheme'); ?></button>
    </form>
  </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Transform Your Trading?</h2>
                <p>Start your journey with our comprehensive trading solutions. From beginner-friendly resources to advanced AI-powered strategies, we have everything you need to succeed in the markets.</p>
                <div class="cta-buttons">
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn-primary">Get Started Today</a>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-secondary">Explore Our Platform</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.services-section {
    padding: 4rem 0;
    background: var(--surface, #f8f9fa);
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.service-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid var(--border, #e1e5e9);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.service-card.featured {
    border-color: var(--primary-color, #007cba);
    box-shadow: 0 4px 6px rgba(0, 212, 255, 0.2);
}

.service-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.service-card h3 {
    margin: 1rem 0;
    color: var(--text-primary, #333);
}

.service-card p {
    color: var(--text-secondary, #666);
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.service-link {
    color: var(--primary-color, #007cba);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.service-link:hover {
    color: var(--primary-hover, #005a87);
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
    background: white;
    color: var(--primary-color, #007cba);
    border: 2px solid white;
}

.btn-primary:hover {
    background: transparent;
    color: white;
}

.btn-secondary {
    background: rgba(255,255,255,0.15);
    color: white;
    border: 2px solid rgba(255,255,255,0.3);
}

.btn-secondary:hover {
    background: rgba(255,255,255,0.25);
    border-color: rgba(255,255,255,0.5);
}
</style>

<?php get_footer(); ?>
