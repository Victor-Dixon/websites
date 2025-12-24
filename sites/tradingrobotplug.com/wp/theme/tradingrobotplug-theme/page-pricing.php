<?php
/**
 * Template Name: Pricing Page
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container page-header">
        <h1>Simple, Transparent Pricing</h1>
        <p>Choose the plan that fits your trading goals.</p>
    </div>

    <div class="container">
        <!-- Render Pricing Shortcode -->
        <?php echo do_shortcode('[trading_robot_pricing]'); ?>
    </div>

    <!-- FAQ Section -->
    <div class="container faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-grid">
            <div class="faq-item">
                <h4>Can I switch plans later?</h4>
                <p>Yes, you can upgrade or downgrade your plan at any time from your dashboard.</p>
            </div>
            <div class="faq-item">
                <h4>Is my money safe?</h4>
                <p>We do not hold your funds. All trading happens in your own brokerage account via secure API keys.</p>
            </div>
            <div class="faq-item">
                <h4>Do you offer a guarantee?</h4>
                <p>We offer a 14-day money-back guarantee on all paid plans.</p>
            </div>
        </div>
    </div>
</main>

<style>
.page-header {
    text-align: center;
    padding: 60px 0 40px;
}
.page-header h1 {
    font-size: 3rem;
    margin-bottom: 10px;
}
.page-header p {
    font-size: 1.2rem;
    color: #666;
}
.faq-section {
    padding: 80px 0;
    margin-top: 40px;
    border-top: 1px solid #eee;
}
.faq-section h2 {
    text-align: center;
    margin-bottom: 40px;
}
.faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}
.faq-item h4 {
    margin-bottom: 10px;
    color: #333;
}
.faq-item p {
    color: #666;
    margin: 0;
}
</style>

<?php get_footer(); ?>
