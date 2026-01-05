<?php
/**
 * Terms of Service Page Template
 * P0 Compliance - Required legal page for services
 * 
 * @package TradingRobotPlug
 * @version 1.0.0
 * @since 2025-12-28
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<section class="legal-page">
    <div class="container">
        <h1>Terms of Service</h1>
        <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
        
        <div class="legal-content">
            <section>
                <h2>1. Agreement to Terms</h2>
                <p>By accessing or using TradingRobotPlug ("the Service"), you agree to be bound by these Terms of Service. If you disagree with any part of the terms, you may not access the Service.</p>
            </section>
            
            <section>
                <h2>2. Description of Service</h2>
                <p>TradingRobotPlug provides:</p>
                <ul>
                    <li><strong>Custom Trading Robot Development Services:</strong> We build custom trading robots tailored to your specifications and requirements.</li>
                    <li><strong>Trading Robot Showcase:</strong> We display and may offer access to trading robots we have developed, along with their performance data.</li>
                    <li><strong>Educational Content:</strong> Information about algorithmic trading, strategy development, and trading automation.</li>
                </ul>
                <p>We are currently in <strong>building and testing mode</strong>. All trading strategies are being validated through paper trading before any live trading implementation.</p>
            </section>
            
            <section>
                <h2>3. Service Terms (Custom Development)</h2>
                <h3>3.1 Custom Development Services</h3>
                <p>When you engage us for custom trading robot development:</p>
                <ul>
                    <li>We will provide a detailed scope of work and timeline</li>
                    <li>Development fees are agreed upon before work begins</li>
                    <li>You will receive the completed trading robot as specified</li>
                    <li>Support and maintenance terms will be outlined in your service agreement</li>
                </ul>
                
                <h3>3.2 Intellectual Property</h3>
                <p>For custom development projects:</p>
                <ul>
                    <li>You retain ownership of the custom trading robot developed for you</li>
                    <li>We retain the right to use general methodologies and non-proprietary techniques</li>
                    <li>Specific ownership terms will be detailed in your service agreement</li>
                </ul>
            </section>
            
            <section>
                <h2>4. User Responsibilities</h2>
                <p>You are responsible for:</p>
                <ul>
                    <li>Providing accurate information when using our services</li>
                    <li>Maintaining the confidentiality of your account credentials</li>
                    <li>Understanding the risks associated with algorithmic trading</li>
                    <li>Complying with all applicable laws and regulations in your jurisdiction</li>
                    <li>Making your own investment decisions</li>
                </ul>
            </section>
            
            <section>
                <h2>5. Disclaimers</h2>
                <div class="disclaimer-box">
                    <h3>Important Trading Disclaimer</h3>
                    <p><strong>Trading in financial markets involves substantial risk of loss.</strong> Past performance is not indicative of future results. Any trading robot or strategy, including those developed or showcased by TradingRobotPlug, may result in losses.</p>
                    <p>The information and services provided by TradingRobotPlug are for informational and development purposes only and should not be considered investment advice. You should consult with a qualified financial advisor before making any investment decisions.</p>
                </div>
                
                <h3>5.1 No Guarantee of Performance</h3>
                <p>We make no representations or warranties regarding:</p>
                <ul>
                    <li>Future performance of any trading robot or strategy</li>
                    <li>Profitability of any trading approach</li>
                    <li>Suitability of our services for your specific situation</li>
                </ul>
                
                <h3>5.2 Paper Trading vs. Live Trading</h3>
                <p>Paper trading (simulated trading) results may differ significantly from live trading results due to factors including market conditions, execution timing, slippage, and other variables.</p>
            </section>
            
            <section>
                <h2>6. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, TradingRobotPlug shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to:</p>
                <ul>
                    <li>Loss of profits, revenue, or data</li>
                    <li>Trading losses resulting from use of our services</li>
                    <li>Business interruption</li>
                    <li>Any other intangible losses</li>
                </ul>
            </section>
            
            <section>
                <h2>7. Modifications to Terms</h2>
                <p>We reserve the right to modify these Terms at any time. We will provide notice of significant changes by updating the "Last Updated" date and, where appropriate, notifying you via email or through our website.</p>
            </section>
            
            <section>
                <h2>8. Governing Law</h2>
                <p>These Terms shall be governed by and construed in accordance with the laws of the United States, without regard to its conflict of law provisions.</p>
            </section>
            
            <section>
                <h2>9. Contact Information</h2>
                <p>For questions about these Terms of Service, please contact us through our <a href="<?php echo esc_url(home_url('/contact')); ?>">Contact Page</a>.</p>
            </section>
        </div>
        
        <div class="legal-nav">
            <a href="<?php echo esc_url(home_url('/privacy')); ?>" class="legal-link">Privacy Policy</a>
            <a href="<?php echo esc_url(home_url('/product-terms')); ?>" class="legal-link">Product Terms & Risk Disclosure</a>
        </div>
    </div>
</section>

<style>
.legal-page {
    padding: 80px 0;
    background: #f9f9f9;
    min-height: 100vh;
}

.legal-page h1 {
    color: #333;
    margin-bottom: 10px;
}

.legal-page .last-updated {
    color: #666;
    font-size: 14px;
    margin-bottom: 40px;
}

.legal-content {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.legal-content section {
    margin-bottom: 32px;
}

.legal-content h2 {
    color: #667eea;
    font-size: 1.5rem;
    margin-bottom: 16px;
    border-bottom: 2px solid #eee;
    padding-bottom: 8px;
}

.legal-content h3 {
    color: #333;
    font-size: 1.1rem;
    margin: 16px 0 8px;
}

.legal-content p {
    color: #555;
    line-height: 1.8;
    margin-bottom: 12px;
}

.legal-content ul {
    margin: 12px 0 12px 24px;
    color: #555;
}

.legal-content li {
    margin-bottom: 8px;
    line-height: 1.6;
}

.disclaimer-box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-left: 4px solid #ff6b6b;
    padding: 20px;
    border-radius: 8px;
    margin: 16px 0;
}

.disclaimer-box h3 {
    color: #856404;
    margin-top: 0;
}

.disclaimer-box p {
    color: #856404;
    margin-bottom: 8px;
}

.legal-nav {
    margin-top: 40px;
    text-align: center;
}

.legal-link {
    display: inline-block;
    margin: 0 16px;
    color: #667eea;
    text-decoration: none;
}

.legal-link:hover {
    text-decoration: underline;
}
</style>

<?php get_footer(); ?>


