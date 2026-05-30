<?php
/**
 * The front page template file
 */

get_header(); ?>

<main id="primary" class="site-main">

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Automated Trading Robots That <span class="highlight">Actually Work</span></h1>
                <p>Stop guessing. Start validating. Access proven trading strategies, backtest with real data, and automate your execution.</p>
                <div class="hero-buttons">
                    <a href="/pricing" class="btn btn-primary btn-large">Start Free Trial</a>
                    <a href="/marketplace" class="btn btn-outline btn-large">View Marketplace</a>
                </div>
                <div class="hero-trust">
                    <span>Trusted by 500+ Traders</span>
                    <span>•</span>
                    <span>Verified Performance</span>
                    <span>•</span>
                    <span>Bank-Grade Security</span>
                </div>
            </div>
            <div class="hero-image">
                <!-- Placeholder for dashboard screenshot -->
                <div class="placeholder-img">
                    Performance Dashboard Preview
                </div>
            </div>
        </div>
    </section>

    <!-- Value Prop Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose TradingRobotPlug?</h2>
                <p>We provide the tools you need to trade with confidence.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Performance Tracking</h3>
                    <p>Real-time P&L, win rates, and drawdown analysis for every trade.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🤖</div>
                    <h3>Multiple Strategies</h3>
                    <p>Choose from Trend Following, Mean Reversion, and Scalping bots.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Risk Management</h3>
                    <p>Built-in safeguards to protect your capital and manage drawdowns.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section -->
    <section class="proof-section">
        <div class="container">
            <div class="section-header">
                <h2>Real Results</h2>
            </div>
            <!-- Render Public Leaderboard via Plugin Shortcode -->
            <?php echo do_shortcode('[trading_robot_performance]'); ?>
            
            <div class="cta-wrapper">
                <a href="/performance" class="btn btn-outline">See Full Leaderboard</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Automate Your Trading?</h2>
            <p>Join today and get access to our top-performing robots.</p>
            <a href="/pricing" class="btn btn-white btn-large">View Pricing</a>
        </div>
    </section>

</main>

<style>
/* Homepage Specific Styles */
.hero-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}
.hero-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 50px;
}
.hero-content h1 {
    font-size: 3.5rem;
    margin-bottom: 20px;
    line-height: 1.2;
}
.highlight {
    color: #007bff;
}
.hero-content p {
    font-size: 1.25rem;
    color: #6c757d;
    margin-bottom: 30px;
}
.hero-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-bottom: 30px;
}
.btn-large {
    padding: 15px 30px;
    font-size: 1.1rem;
}
.hero-trust {
    font-size: 0.9rem;
    color: #888;
    display: flex;
    gap: 15px;
    justify-content: center;
}
.placeholder-img {
    background: white;
    height: 400px;
    border-radius: 10px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 1.5rem;
    border: 1px dashed #ddd;
}

.features-section {
    padding: 80px 0;
}
.section-header {
    text-align: center;
    margin-bottom: 50px;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}
.feature-card {
    padding: 30px;
    border-radius: 10px;
    background: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    text-align: center;
    transition: transform 0.3s;
}
.feature-card:hover {
    transform: translateY(-5px);
}
.feature-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.proof-section {
    background: #f8f9fa;
    padding: 80px 0;
}
.cta-wrapper {
    text-align: center;
    margin-top: 30px;
}

.cta-section {
    background: #007bff;
    color: white;
    text-align: center;
    padding: 80px 0;
}
.cta-section h2 {
    margin-bottom: 20px;
}
.cta-section p {
    font-size: 1.25rem;
    margin-bottom: 30px;
    opacity: 0.9;
}
.btn-white {
    background: white;
    color: #007bff;
}
.btn-white:hover {
    background: #f8f9fa;
}
</style>

<?php get_footer(); ?>
