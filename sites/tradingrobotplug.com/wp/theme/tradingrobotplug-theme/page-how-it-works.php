<?php
/**
 * Template Name: How It Works
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container page-header">
        <h1>How It Works</h1>
        <p>Your journey to automated trading in 4 simple steps.</p>
    </div>

    <div class="container steps-container">
        <div class="step-card">
            <div class="step-number">1</div>
            <div class="step-content">
                <h3>Select a Robot</h3>
                <p>Browse our marketplace and choose a strategy that matches your risk tolerance (e.g., Trend Following, Scalping).</p>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-number">2</div>
            <div class="step-content">
                <h3>Connect Your Broker</h3>
                <p>Securely link your brokerage account (e.g., Alpaca) using API keys. We never have direct access to withdraw funds.</p>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-number">3</div>
            <div class="step-content">
                <h3>Validate (Paper Trading)</h3>
                <p>Run the robot in "Paper Mode" first. Watch it execute trades in real-time without risking real money until you are confident.</p>
            </div>
        </div>
        
        <div class="step-card">
            <div class="step-number">4</div>
            <div class="step-content">
                <h3>Go Live</h3>
                <p>Switch to "Live Mode" and let the robot manage your trades automatically according to your risk settings.</p>
            </div>
        </div>
    </div>
    
    <div class="cta-section">
        <div class="container">
            <h2>Ready to start?</h2>
            <a href="/pricing" class="btn btn-primary btn-large">Get Started Free</a>
        </div>
    </div>
</main>

<style>
.page-header {
    text-align: center;
    padding: 60px 0 40px;
}
.steps-container {
    max-width: 800px;
    margin: 0 auto 60px;
}
.step-card {
    display: flex;
    gap: 30px;
    margin-bottom: 40px;
    align-items: flex-start;
}
.step-number {
    background: #007bff;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    flex-shrink: 0;
}
.step-content h3 {
    margin-top: 0;
    margin-bottom: 10px;
}
.step-content p {
    color: #666;
    margin: 0;
}
.cta-section {
    background: #f8f9fa;
    text-align: center;
    padding: 60px 0;
}
</style>

<?php get_footer(); ?>
