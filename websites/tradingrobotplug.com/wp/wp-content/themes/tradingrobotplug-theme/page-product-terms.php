<?php
/**
 * Product Terms & Risk Disclosure Page Template
 * P0 Compliance - CRITICAL for financial product regulations
 * 
 * @package TradingRobotPlug
 * @version 1.0.0
 * @since 2025-12-28
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<section class="legal-page risk-disclosure">
    <div class="container">
        <h1>Product Terms & Risk Disclosure</h1>
        <p class="last-updated">Last Updated: <?php echo date('F j, Y'); ?></p>
        
        <div class="critical-warning">
            <h2>⚠️ Important Risk Warning</h2>
            <p><strong>Trading financial instruments involves significant risk of loss.</strong> You should carefully consider whether trading is suitable for you in light of your financial situation. Only trade with money you can afford to lose.</p>
        </div>
        
        <div class="legal-content">
            <section>
                <h2>1. Trading Robot Products</h2>
                <p>TradingRobotPlug develops and may offer access to automated trading systems ("Trading Robots"). These products are sophisticated software tools designed to execute trades based on predetermined algorithms and strategies.</p>
                
                <h3>1.1 Current Development Status</h3>
                <p>We are currently in <strong>building and testing mode</strong>. Our trading robots are being validated through extensive paper trading (simulated trading) before any live deployment. We do not currently offer live trading products.</p>
                
                <h3>1.2 Product Types</h3>
                <ul>
                    <li><strong>Paper Trading Robots:</strong> Simulated trading systems for strategy validation</li>
                    <li><strong>Custom Development:</strong> Trading robots built to your specifications</li>
                    <li><strong>Educational Tools:</strong> Backtesting and analysis tools for learning</li>
                </ul>
            </section>
            
            <section class="risk-section">
                <h2>2. Risk Disclosure Statement</h2>
                
                <div class="risk-box">
                    <h3>2.1 General Trading Risks</h3>
                    <p>Trading in financial markets carries a high level of risk and may not be suitable for all investors. Before deciding to trade, you should carefully consider your investment objectives, level of experience, and risk appetite.</p>
                    <ul>
                        <li><strong>Loss of Capital:</strong> You could lose some or all of your invested capital</li>
                        <li><strong>Market Volatility:</strong> Markets can move rapidly and unpredictably</li>
                        <li><strong>Leverage Risk:</strong> Using leverage amplifies both gains and losses</li>
                        <li><strong>Liquidity Risk:</strong> Markets may become illiquid, affecting your ability to exit positions</li>
                    </ul>
                </div>
                
                <div class="risk-box">
                    <h3>2.2 Algorithmic Trading Specific Risks</h3>
                    <p>Automated trading systems present unique risks beyond traditional trading:</p>
                    <ul>
                        <li><strong>Technology Failures:</strong> Software bugs, connectivity issues, or hardware failures may prevent proper execution</li>
                        <li><strong>Strategy Risk:</strong> Strategies that performed well historically may not perform well in the future</li>
                        <li><strong>Over-Optimization:</strong> Strategies may be overfitted to historical data and fail in live conditions</li>
                        <li><strong>Market Changes:</strong> Market conditions change, and strategies may become ineffective</li>
                        <li><strong>Execution Risk:</strong> Actual execution may differ from expected due to slippage, latency, or market impact</li>
                        <li><strong>Flash Crashes:</strong> Algorithms may behave unexpectedly during extreme market events</li>
                    </ul>
                </div>
                
                <div class="risk-box">
                    <h3>2.3 Paper Trading Limitations</h3>
                    <p><strong>Paper trading results are hypothetical and do not reflect actual trading.</strong></p>
                    <ul>
                        <li>No actual orders are placed in paper trading mode</li>
                        <li>Simulated results may not account for real-world execution factors</li>
                        <li>Paper trading does not involve the emotional stress of real trading</li>
                        <li>Past paper trading performance does not guarantee future live trading results</li>
                    </ul>
                </div>
            </section>
            
            <section>
                <h2>3. Performance Disclaimers</h2>
                
                <div class="disclaimer-highlight">
                    <h3>HYPOTHETICAL PERFORMANCE DISCLAIMER</h3>
                    <p>HYPOTHETICAL PERFORMANCE RESULTS HAVE MANY INHERENT LIMITATIONS, SOME OF WHICH ARE DESCRIBED BELOW. NO REPRESENTATION IS BEING MADE THAT ANY ACCOUNT WILL OR IS LIKELY TO ACHIEVE PROFITS OR LOSSES SIMILAR TO THOSE SHOWN.</p>
                    <p>IN FACT, THERE ARE FREQUENTLY SHARP DIFFERENCES BETWEEN HYPOTHETICAL PERFORMANCE RESULTS AND THE ACTUAL RESULTS SUBSEQUENTLY ACHIEVED BY ANY PARTICULAR TRADING PROGRAM.</p>
                    <p>ONE OF THE LIMITATIONS OF HYPOTHETICAL PERFORMANCE RESULTS IS THAT THEY ARE GENERALLY PREPARED WITH THE BENEFIT OF HINDSIGHT. IN ADDITION, HYPOTHETICAL TRADING DOES NOT INVOLVE FINANCIAL RISK, AND NO HYPOTHETICAL TRADING RECORD CAN COMPLETELY ACCOUNT FOR THE IMPACT OF FINANCIAL RISK IN ACTUAL TRADING.</p>
                </div>
                
                <h3>3.1 Past Performance</h3>
                <p><strong>Past performance is not indicative of future results.</strong> Historical returns, expected returns, and probability projections may not reflect actual future performance.</p>
                
                <h3>3.2 No Guarantees</h3>
                <p>We make no guarantees regarding:</p>
                <ul>
                    <li>Future profitability of any trading robot or strategy</li>
                    <li>Consistency of returns</li>
                    <li>Maximum drawdown levels</li>
                    <li>Win rates or other performance metrics</li>
                </ul>
            </section>
            
            <section>
                <h2>4. Not Investment Advice</h2>
                <p><strong>TradingRobotPlug does not provide investment advice.</strong> Our products and services are tools and educational resources, not recommendations to buy or sell any financial instrument.</p>
                <ul>
                    <li>You should consult with a qualified financial advisor before making investment decisions</li>
                    <li>We do not consider your personal financial situation, goals, or risk tolerance</li>
                    <li>Any trading decisions you make are your sole responsibility</li>
                    <li>We are not registered as investment advisors or broker-dealers</li>
                </ul>
            </section>
            
            <section>
                <h2>5. User Acknowledgments</h2>
                <p>By using our products or services, you acknowledge that:</p>
                <ul>
                    <li>You have read and understood this Risk Disclosure</li>
                    <li>You understand that trading involves significant risk of loss</li>
                    <li>You are trading with funds you can afford to lose</li>
                    <li>You are solely responsible for your trading decisions</li>
                    <li>You will not hold TradingRobotPlug liable for trading losses</li>
                    <li>You meet the legal age requirements for trading in your jurisdiction</li>
                </ul>
            </section>
            
            <section>
                <h2>6. Regulatory Compliance</h2>
                <p>You are responsible for ensuring compliance with all applicable laws and regulations in your jurisdiction, including:</p>
                <ul>
                    <li>Securities regulations</li>
                    <li>Tax obligations on trading profits</li>
                    <li>Reporting requirements</li>
                    <li>Any restrictions on algorithmic trading</li>
                </ul>
            </section>
            
            <section>
                <h2>7. Contact Information</h2>
                <p>If you have questions about these Product Terms or the risks involved, please contact us through our <a href="<?php echo esc_url(home_url('/contact')); ?>">Contact Page</a> before using our products or services.</p>
            </section>
        </div>
        
        <div class="legal-nav">
            <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>" class="legal-link">Terms of Service</a>
            <a href="<?php echo esc_url(home_url('/privacy')); ?>" class="legal-link">Privacy Policy</a>
        </div>
    </div>
</section>

<style>
.legal-page {
    padding: 80px 0;
    background: #f9f9f9;
    min-height: 100vh;
}

.legal-page.risk-disclosure {
    background: #fafafa;
}

.legal-page h1 {
    color: #333;
    margin-bottom: 10px;
}

.legal-page .last-updated {
    color: #666;
    font-size: 14px;
    margin-bottom: 24px;
}

.critical-warning {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
    color: #fff;
    padding: 24px;
    border-radius: 12px;
    margin-bottom: 32px;
    text-align: center;
}

.critical-warning h2 {
    color: #fff;
    margin: 0 0 12px 0;
    font-size: 1.5rem;
}

.critical-warning p {
    color: #fff;
    margin: 0;
    font-size: 1.1rem;
    line-height: 1.6;
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

.risk-section {
    background: #fef9f9;
    margin: -20px;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 32px;
}

.risk-box {
    background: #fff;
    border: 1px solid #ffcccc;
    border-left: 4px solid #ff6b6b;
    padding: 20px;
    border-radius: 8px;
    margin: 16px 0;
}

.risk-box h3 {
    color: #c0392b;
    margin-top: 0;
}

.disclaimer-highlight {
    background: #333;
    color: #fff;
    padding: 24px;
    border-radius: 8px;
    margin: 24px 0;
}

.disclaimer-highlight h3 {
    color: #ffd700;
    margin-top: 0;
    text-transform: uppercase;
}

.disclaimer-highlight p {
    color: #fff;
    font-size: 0.95rem;
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


