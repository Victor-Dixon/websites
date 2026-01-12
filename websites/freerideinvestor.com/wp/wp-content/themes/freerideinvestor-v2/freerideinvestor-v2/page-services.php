<?php
/**
 * Template Name: Services
 * Template Post Type: page
 *
 * Services page template for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="services-page">
    <!-- Hero Section -->
    <section class="services-hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Our Services</h1>
                <p class="hero-subtitle">Comprehensive trading solutions designed to help you achieve consistent, profitable results</p>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="services-overview">
        <div class="container">
            <div class="overview-intro">
                <h2>Complete Trading Ecosystem</h2>
                <p>We provide everything you need to succeed in the markets, from educational resources to advanced trading tools and personalized guidance.</p>
            </div>

            <div class="services-grid">
                <!-- Core Services -->
                <div class="service-category">
                    <h3>🚀 Trading Strategies</h3>
                    <div class="category-services">
                        <div class="service-item">
                            <h4>Algorithmic Strategies</h4>
                            <p>Pre-built, battle-tested trading algorithms across multiple asset classes with customizable parameters.</p>
                            <ul>
                                <li>Trend following systems</li>
                                <li>Mean reversion strategies</li>
                                <li>Statistical arbitrage</li>
                                <li>Options strategies</li>
                            </ul>
                        </div>

                        <div class="service-item">
                            <h4>Custom Strategy Development</h4>
                            <p>Work with our quantitative analysts to develop strategies tailored to your specific goals and risk tolerance.</p>
                            <ul>
                                <li>Personalized strategy design</li>
                                <li>Backtesting and optimization</li>
                                <li>Risk management integration</li>
                                <li>Performance monitoring</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="service-category">
                    <h3>📚 Education & Training</h3>
                    <div class="category-services">
                        <div class="service-item">
                            <h4>Trading Education</h4>
                            <p>Comprehensive educational resources covering all aspects of trading and investing.</p>
                            <ul>
                                <li>Market fundamentals</li>
                                <li>Technical analysis</li>
                                <li>Risk management</li>
                                <li>Psychology of trading</li>
                            </ul>
                        </div>

                        <div class="service-item">
                            <h4>Mentorship Program</h4>
                            <p>One-on-one guidance from experienced traders and portfolio managers.</p>
                            <ul>
                                <li>Personal trading plan development</li>
                                <li>Weekly strategy reviews</li>
                                <li>Performance analysis</li>
                                <li>Ongoing support</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="service-category">
                    <h3>🛠️ Tools & Technology</h3>
                    <div class="category-services">
                        <div class="service-item">
                            <h4>Trading Platform</h4>
                            <p>Advanced trading platform with real-time data, automated execution, and comprehensive analytics.</p>
                            <ul>
                                <li>Real-time market data</li>
                                <li>Automated trade execution</li>
                                <li>Portfolio tracking</li>
                                <li>Performance analytics</li>
                            </ul>
                        </div>

                        <div class="service-item">
                            <h4>API Integration</h4>
                            <p>Seamlessly integrate our strategies and tools with your existing trading infrastructure.</p>
                            <ul>
                                <li>REST API access</li>
                                <li>WebSocket real-time data</li>
                                <li>Custom integrations</li>
                                <li>White-label solutions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Tiers -->
    <section class="service-tiers">
        <div class="container">
            <h2>Membership Tiers</h2>
            <div class="tiers-comparison">
                <div class="tier-card free">
                    <div class="tier-header">
                        <h3>Free</h3>
                        <div class="tier-price">$0<span>/month</span></div>
                    </div>
                    <div class="tier-features">
                        <ul>
                            <li>✓ Basic trading education</li>
                            <li>✓ Community access</li>
                            <li>✓ Market analysis</li>
                            <li>✓ Strategy templates</li>
                            <li>✗ Advanced strategies</li>
                            <li>✗ Personal mentorship</li>
                            <li>✗ API access</li>
                        </ul>
                    </div>
                    <a href="/register" class="tier-button">Get Started</a>
                </div>

                <div class="tier-card basic">
                    <div class="tier-header">
                        <h3>Basic</h3>
                        <div class="tier-price">$29<span>/month</span></div>
                        <div class="tier-badge">Most Popular</div>
                    </div>
                    <div class="tier-features">
                        <ul>
                            <li>✓ Everything in Free</li>
                            <li>✓ Advanced trading strategies</li>
                            <li>✓ Real-time alerts</li>
                            <li>✓ Performance reports</li>
                            <li>✓ Email support</li>
                            <li>✗ Personal mentorship</li>
                            <li>✗ API access</li>
                        </ul>
                    </div>
                    <a href="/pricing" class="tier-button">Upgrade Now</a>
                </div>

                <div class="tier-card premium">
                    <div class="tier-header">
                        <h3>Premium</h3>
                        <div class="tier-price">$79<span>/month</span></div>
                    </div>
                    <div class="tier-features">
                        <ul>
                            <li>✓ Everything in Basic</li>
                            <li>✓ Institutional strategies</li>
                            <li>✓ Portfolio management</li>
                            <li>✓ Priority support</li>
                            <li>✓ API access</li>
                            <li>✗ Personal mentorship</li>
                            <li>✗ Custom development</li>
                        </ul>
                    </div>
                    <a href="/pricing" class="tier-button">Go Premium</a>
                </div>

                <div class="tier-card pro">
                    <div class="tier-header">
                        <h3>Pro</h3>
                        <div class="tier-price">$199<span>/month</span></div>
                    </div>
                    <div class="tier-features">
                        <ul>
                            <li>✓ Everything in Premium</li>
                            <li>✓ Personal account manager</li>
                            <li>✓ Custom strategy development</li>
                            <li>✓ White-label solutions</li>
                            <li>✓ Direct trading desk access</li>
                            <li>✓ Unlimited API calls</li>
                            <li>✓ Custom integrations</li>
                        </ul>
                    </div>
                    <a href="/pricing" class="tier-button">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="services-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Transform Your Trading?</h2>
                <p>Join thousands of traders who have elevated their performance with our comprehensive suite of tools and strategies.</p>
                <div class="cta-buttons">
                    <a href="/pricing" class="btn btn-primary">View Pricing</a>
                    <a href="/contact" class="btn btn-secondary">Schedule Demo</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Services Page Styles */
.services-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Hero Section */
.services-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: white;
    padding: 6rem 0;
    text-align: center;
}

.hero-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
}

.services-hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1.4rem;
    opacity: 0.9;
    line-height: 1.6;
}

/* Services Overview */
.services-overview {
    padding: 6rem 0;
    background: #f8fafc;
}

.services-overview .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.overview-intro {
    text-align: center;
    margin-bottom: 4rem;
}

.overview-intro h2 {
    font-size: 2.5rem;
    color: #1a202c;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.overview-intro p {
    font-size: 1.2rem;
    color: #4a5568;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.services-grid {
    display: grid;
    gap: 4rem;
}

.service-category h3 {
    font-size: 2rem;
    color: #1a202c;
    margin-bottom: 2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.category-services {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.service-item {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
}

.service-item h4 {
    font-size: 1.4rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
}

.service-item p {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.service-item ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.service-item li {
    padding: 0.5rem 0;
    color: #4a5568;
    border-bottom: 1px solid #f7fafc;
}

.service-item li:last-child {
    border-bottom: none;
}

/* Service Tiers */
.service-tiers {
    padding: 6rem 0;
    background: white;
}

.service-tiers h2 {
    font-size: 2.5rem;
    color: #1a202c;
    text-align: center;
    margin-bottom: 4rem;
    font-weight: 600;
}

.tiers-comparison {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.tier-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.tier-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.tier-card.basic {
    border-color: #4299e1;
    box-shadow: 0 4px 20px rgba(66, 153, 225, 0.2);
}

.tier-card.premium {
    border-color: #48bb78;
    box-shadow: 0 4px 20px rgba(72, 187, 120, 0.2);
}

.tier-card.pro {
    border-color: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
}

.tier-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: #4299e1;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.tier-header {
    padding: 2rem 2rem 1.5rem;
    text-align: center;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-bottom: 1px solid #e2e8f0;
}

.tier-header h3 {
    font-size: 1.8rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
}

.tier-price {
    font-size: 3rem;
    font-weight: 700;
    color: #48bb78;
    margin-bottom: 0.5rem;
}

.tier-price span {
    font-size: 1rem;
    color: #718096;
    font-weight: 400;
}

.tier-features {
    padding: 2rem;
}

.tier-features ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tier-features li {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f7fafc;
    color: #4a5568;
    font-size: 0.95rem;
}

.tier-features li:last-child {
    border-bottom: none;
}

.tier-button {
    display: block;
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
    color: white;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    border-radius: 0 0 8px 8px;
    transition: all 0.3s ease;
}

.tier-button:hover {
    transform: translateY(2px);
    box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
}

/* CTA Section */
.services-cta {
    padding: 6rem 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
}

.cta-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.cta-content p {
    font-size: 1.3rem;
    margin-bottom: 3rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .services-hero h1 {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
    }

    .category-services {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .tiers-comparison {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .container {
        padding: 0 1rem;
    }

    .services-hero,
    .services-overview,
    .service-tiers,
    .services-cta {
        padding: 4rem 0;
    }
}

@media (max-width: 480px) {
    .services-hero h1 {
        font-size: 2rem;
    }

    .service-category h3,
    .overview-intro h2,
    .service-tiers h2,
    .services-cta h2 {
        font-size: 2rem;
    }

    .tier-price {
        font-size: 2.5rem;
    }

    .service-item {
        padding: 2rem;
    }
}
</style>

<?php get_footer(); ?>