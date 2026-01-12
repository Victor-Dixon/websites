<?php
/**
 * Template Name: Trading Strategies
 * Template Post Type: page
 *
 * Modern trading strategies showcase page for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="trading-strategies-page">
    <!-- Hero Section -->
    <section class="strategies-hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Trading Strategies</h1>
                <p class="hero-subtitle">Proven algorithmic strategies designed for consistent returns and superior risk management</p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">15+</span>
                        <span class="stat-label">Strategies</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">73%</span>
                        <span class="stat-label">Win Rate</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">1.8:1</span>
                        <span class="stat-label">Risk-Reward</span>
                    </div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="performance-chart">
                    <div class="chart-line"></div>
                    <div class="chart-points">
                        <div class="point" style="left: 10%; top: 80%;"></div>
                        <div class="point" style="left: 25%; top: 65%;"></div>
                        <div class="point" style="left: 40%; top: 45%;"></div>
                        <div class="point" style="left: 55%; top: 35%;"></div>
                        <div class="point" style="left: 70%; top: 25%;"></div>
                        <div class="point" style="left: 85%; top: 15%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Strategy Categories -->
    <section class="strategy-categories">
        <div class="container">
            <h2>Strategy Categories</h2>
            <div class="categories-grid">
                <div class="category-card" data-category="trend-following">
                    <div class="category-header">
                        <span class="category-icon">📈</span>
                        <h3>Trend Following</h3>
                    </div>
                    <p>Ride established market trends for consistent profits with built-in trend confirmation and momentum filters.</p>
                    <div class="category-stats">
                        <span class="stat">68% Win Rate</span>
                        <span class="stat">12% Avg Return</span>
                    </div>
                    <button class="btn btn-outline" onclick="showStrategyDetails('trend-following')">Learn More</button>
                </div>

                <div class="category-card" data-category="mean-reversion">
                    <div class="category-header">
                        <span class="category-icon">🔄</span>
                        <h3>Mean Reversion</h3>
                    </div>
                    <p>Capitalize on price deviations from statistical norms with sophisticated entry and exit algorithms.</p>
                    <div class="category-stats">
                        <span class="stat">71% Win Rate</span>
                        <span class="stat">8% Avg Return</span>
                    </div>
                    <button class="btn btn-outline" onclick="showStrategyDetails('mean-reversion')">Learn More</button>
                </div>

                <div class="category-card" data-category="scalping">
                    <div class="category-header">
                        <span class="category-icon">⚡</span>
                        <h3>Scalping</h3>
                    </div>
                    <p>High-frequency trading capturing small price movements with advanced order flow analysis and micro-timing.</p>
                    <div class="category-stats">
                        <span class="stat">75% Win Rate</span>
                        <span class="stat">6% Avg Return</span>
                    </div>
                    <button class="btn btn-outline" onclick="showStrategyDetails('scalping')">Learn More</button>
                </div>

                <div class="category-card" data-category="swing-trading">
                    <div class="category-header">
                        <span class="category-icon">🎯</span>
                        <h3>Swing Trading</h3>
                    </div>
                    <p>Capture multi-day trends with comprehensive technical analysis and optimal entry/exit timing algorithms.</p>
                    <div class="category-stats">
                        <span class="stat">69% Win Rate</span>
                        <span class="stat">15% Avg Return</span>
                    </div>
                    <button class="btn btn-outline" onclick="showStrategyDetails('swing-trading')">Learn More</button>
                </div>

                <div class="category-card" data-category="arbitrage">
                    <div class="category-header">
                        <span class="category-icon">⚖️</span>
                        <h3>Statistical Arbitrage</h3>
                    </div>
                    <p>Exploit pricing inefficiencies across correlated assets using cointegration and statistical modeling.</p>
                    <div class="category-stats">
                        <span class="stat">78% Win Rate</span>
                        <span class="stat">4% Avg Return</span>
                    </div>
                    <button class="btn btn-outline" onclick="showStrategyDetails('arbitrage')">Learn More</button>
                </div>

                <div class="category-card" data-category="options">
                    <div class="category-header">
                        <span class="category-icon">📊</span>
                        <h3>Options Strategies</h3>
                    </div>
                    <p>Sophisticated options strategies combining volatility analysis with probability-based position management.</p>
                    <div class="category-stats">
                        <span class="stat">72% Win Rate</span>
                        <span class="stat">18% Avg Return</span>
                    </div>
                    <button class="btn btn-outline" onclick="showStrategyDetails('options')">Learn More</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Strategy Details Modal -->
    <div id="strategyModal" class="strategy-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"></h3>
                <button class="modal-close" onclick="closeStrategyModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Performance Showcase -->
    <section class="performance-showcase">
        <div class="container">
            <h2>Strategy Performance</h2>
            <div class="performance-grid">
                <div class="performance-card">
                    <h3>Portfolio Performance</h3>
                    <div class="performance-chart">
                        <canvas id="portfolioChart" width="400" height="200"></canvas>
                    </div>
                    <div class="performance-metrics">
                        <div class="metric">
                            <span class="metric-value">+24.7%</span>
                            <span class="metric-label">YTD Return</span>
                        </div>
                        <div class="metric">
                            <span class="metric-value">12.3%</span>
                            <span class="metric-label">Volatility</span>
                        </div>
                        <div class="metric">
                            <span class="metric-value">1.8</span>
                            <span class="metric-label">Sharpe Ratio</span>
                        </div>
                    </div>
                </div>

                <div class="performance-card">
                    <h3>Risk Management</h3>
                    <div class="risk-metrics">
                        <div class="risk-item">
                            <span class="risk-label">Max Drawdown</span>
                            <span class="risk-value">8.2%</span>
                        </div>
                        <div class="risk-item">
                            <span class="risk-label">Recovery Time</span>
                            <span class="risk-value">23 days</span>
                        </div>
                        <div class="risk-item">
                            <span class="risk-label">Win Rate</span>
                            <span class="risk-value">71.4%</span>
                        </div>
                        <div class="risk-item">
                            <span class="risk-label">Profit Factor</span>
                            <span class="risk-value">2.1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="strategies-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Trading Like a Pro?</h2>
                <p>Join thousands of traders using our institutional-grade strategies for consistent, profitable results.</p>
                <div class="cta-buttons">
                    <a href="/contact" class="btn btn-primary">Get Started</a>
                    <a href="#performance" class="btn btn-secondary">View Performance</a>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Strategy details data
const strategyDetails = {
    'trend-following': {
        title: 'Trend Following Strategies',
        content: `
            <div class="strategy-detail">
                <h4>Advanced Trend Detection</h4>
                <p>Our trend following algorithms use multiple technical indicators including moving averages, ADX, and momentum oscillators to identify and confirm market trends.</p>

                <h4>Risk Management</h4>
                <ul>
                    <li>Dynamic position sizing based on trend strength</li>
                    <li>Trailing stops to protect profits</li>
                    <li>Maximum exposure limits per asset</li>
                    <li>Correlation-based diversification</li>
                </ul>

                <h4>Performance Metrics</h4>
                <div class="metrics-grid">
                    <div class="metric">Win Rate: 68%</div>
                    <div class="metric">Avg Return: 12%</div>
                    <div class="metric">Max DD: 12%</div>
                    <div class="metric">Sharpe: 1.6</div>
                </div>
            </div>
        `
    },
    'mean-reversion': {
        title: 'Mean Reversion Strategies',
        content: `
            <div class="strategy-detail">
                <h4>Statistical Edge</h4>
                <p>Leveraging the mathematical principle that asset prices tend to return to their long-term averages, our algorithms identify overbought and oversold conditions with precision.</p>

                <h4>Entry & Exit Logic</h4>
                <ul>
                    <li>Bollinger Band analysis with dynamic standard deviations</li>
                    <li>RSI divergence confirmation</li>
                    <li>Volume profile analysis</li>
                    <li>Time-based exit rules</li>
                </ul>

                <h4>Performance Metrics</h4>
                <div class="metrics-grid">
                    <div class="metric">Win Rate: 71%</div>
                    <div class="metric">Avg Return: 8%</div>
                    <div class="metric">Max DD: 6%</div>
                    <div class="metric">Sharpe: 2.1</div>
                </div>
            </div>
        `
    },
    'scalping': {
        title: 'Scalping Strategies',
        content: `
            <div class="strategy-detail">
                <h4>Micro-Market Analysis</h4>
                <p>High-frequency algorithms that capitalize on small price movements throughout the trading day, using advanced order flow analysis and market microstructure.</p>

                <h4>Execution Excellence</h4>
                <ul>
                    <li>Sub-millisecond order execution</li>
                    <li>Smart order routing</li>
                    <li>Real-time slippage control</li>
                    <li>Automated trade management</li>
                </ul>

                <h4>Performance Metrics</h4>
                <div class="metrics-grid">
                    <div class="metric">Win Rate: 75%</div>
                    <div class="metric">Avg Return: 6%</div>
                    <div class="metric">Max DD: 4%</div>
                    <div class="metric">Sharpe: 2.8</div>
                </div>
            </div>
        `
    },
    'swing-trading': {
        title: 'Swing Trading Strategies',
        content: `
            <div class="strategy-detail">
                <h4>Multi-Timeframe Analysis</h4>
                <p>Comprehensive swing trading systems that analyze multiple timeframes to identify high-probability setups with optimal risk-reward profiles.</p>

                <h4>Technical Integration</h4>
                <ul>
                    <li>Fibonacci retracement levels</li>
                    <li>Support/resistance analysis</li>
                    <li>Volume confirmation signals</li>
                    <li>Momentum divergence detection</li>
                </ul>

                <h4>Performance Metrics</h4>
                <div class="metrics-grid">
                    <div class="metric">Win Rate: 69%</div>
                    <div class="metric">Avg Return: 15%</div>
                    <div class="metric">Max DD: 10%</div>
                    <div class="metric">Sharpe: 1.9</div>
                </div>
            </div>
        `
    },
    'arbitrage': {
        title: 'Statistical Arbitrage',
        content: `
            <div class="strategy-detail">
                <h4>Quantitative Edge</h4>
                <p>Advanced statistical models identify pricing inefficiencies across correlated assets, providing consistent low-risk returns through mathematical arbitrage.</p>

                <h4>Risk-Neutral Approach</h4>
                <ul>
                    <li>Cointegration analysis</li>
                    <li>Statistical significance testing</li>
                    <li>Dynamic hedging algorithms</li>
                    <li>Market-neutral positioning</li>
                </ul>

                <h4>Performance Metrics</h4>
                <div class="metrics-grid">
                    <div class="metric">Win Rate: 78%</div>
                    <div class="metric">Avg Return: 4%</div>
                    <div class="metric">Max DD: 3%</div>
                    <div class="metric">Sharpe: 3.2</div>
                </div>
            </div>
        `
    },
    'options': {
        title: 'Options Strategies',
        content: `
            <div class="strategy-detail">
                <h4>Volatility Mastery</h4>
                <p>Sophisticated options strategies that combine volatility analysis, probability modeling, and position management for optimal risk-adjusted returns.</p>

                <h4>Advanced Techniques</h4>
                <ul>
                    <li>Implied volatility analysis</li>
                    <li>Greeks-based position management</li>
                    <li>Probability distribution modeling</li>
                    <li>Dynamic adjustment algorithms</li>
                </ul>

                <h4>Performance Metrics</h4>
                <div class="metrics-grid">
                    <div class="metric">Win Rate: 72%</div>
                    <div class="metric">Avg Return: 18%</div>
                    <div class="metric">Max DD: 15%</div>
                    <div class="metric">Sharpe: 1.7</div>
                </div>
            </div>
        `
    }
};

function showStrategyDetails(category) {
    const modal = document.getElementById('strategyModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');

    if (strategyDetails[category]) {
        modalTitle.textContent = strategyDetails[category].title;
        modalContent.innerHTML = strategyDetails[category].content;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeStrategyModal() {
    const modal = document.getElementById('strategyModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('strategyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStrategyModal();
    }
});

// Performance chart (simple line chart simulation)
document.addEventListener('DOMContentLoaded', function() {
    // Simple performance chart visualization would go here
    // For now, we'll just show the static elements
});
</script>

<style>
/* Trading Strategies Page Styles */
.trading-strategies-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Hero Section */
.strategies-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: white;
    padding: 6rem 0;
    position: relative;
    overflow: hidden;
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.hero-content h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1.4rem;
    opacity: 0.9;
    margin-bottom: 3rem;
    line-height: 1.6;
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: #00d4ff;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.hero-visual {
    position: relative;
    height: 300px;
}

.performance-chart {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    position: relative;
}

.chart-line {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    height: 2px;
    background: linear-gradient(90deg, #00d4ff 0%, #0099cc 100%);
    border-radius: 1px;
}

.chart-points {
    position: absolute;
    bottom: 18px;
    left: 20px;
    right: 20px;
    height: 6px;
}

.point {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #00d4ff;
    border-radius: 50%;
    transform: translateX(-50%);
}

/* Strategy Categories */
.strategy-categories {
    padding: 6rem 0;
    background: #f8fafc;
}

.strategy-categories h2 {
    font-size: 2.5rem;
    color: #1a202c;
    text-align: center;
    margin-bottom: 4rem;
    font-weight: 600;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.category-card {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.category-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.category-icon {
    font-size: 2.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-card h3 {
    font-size: 1.5rem;
    color: #1a202c;
    margin: 0;
    font-weight: 600;
}

.category-card p {
    color: #4a5568;
    line-height: 1.7;
    margin-bottom: 1.5rem;
}

.category-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.stat {
    background: #f7fafc;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    color: #2d3748;
    font-weight: 500;
}

/* Modal Styles */
.strategy-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    padding: 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.8rem;
    color: #1a202c;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: #718096;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-body {
    padding: 2rem;
}

.strategy-detail h4 {
    color: #1a202c;
    font-size: 1.3rem;
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.strategy-detail h4:first-child {
    margin-top: 0;
}

.strategy-detail p {
    color: #4a5568;
    line-height: 1.7;
    margin-bottom: 1rem;
}

.strategy-detail ul {
    color: #4a5568;
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.strategy-detail li {
    margin-bottom: 0.5rem;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-top: 1rem;
}

.metric {
    background: #f7fafc;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
    color: #2d3748;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Performance Showcase */
.performance-showcase {
    padding: 6rem 0;
    background: white;
}

.performance-showcase h2 {
    font-size: 2.5rem;
    color: #1a202c;
    text-align: center;
    margin-bottom: 4rem;
    font-weight: 600;
}

.performance-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
}

.performance-card {
    background: #f8fafc;
    padding: 3rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.performance-card h3 {
    font-size: 1.8rem;
    color: #1a202c;
    margin-bottom: 2rem;
    text-align: center;
}

.performance-chart {
    height: 200px;
    background: white;
    border-radius: 8px;
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #718096;
    font-size: 1.1rem;
}

.performance-metrics {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    text-align: center;
}

.metric .metric-value {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    color: #00d4ff;
    margin-bottom: 0.5rem;
}

.metric .metric-label {
    font-size: 0.9rem;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.risk-metrics {
    display: grid;
    gap: 1.5rem;
}

.risk-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.risk-label {
    font-weight: 500;
    color: #2d3748;
}

.risk-value {
    font-weight: 600;
    color: #00d4ff;
}

/* CTA Section */
.strategies-cta {
    padding: 6rem 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
}

.cta-content {
    text-align: center;
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
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
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
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

.btn-outline {
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-container {
        grid-template-columns: 1fr;
        gap: 3rem;
        text-align: center;
    }

    .hero-content h1 {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
    }

    .hero-stats {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .categories-grid {
        grid-template-columns: 1fr;
    }

    .modal-content {
        width: 95%;
        margin: 5vh auto;
    }

    .performance-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .performance-metrics {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }

    .btn {
        width: 100%;
        max-width: 300px;
    }

    .container {
        padding: 0 1rem;
    }

    .strategies-hero,
    .strategy-categories,
    .performance-showcase,
    .strategies-cta {
        padding: 4rem 0;
    }
}

@media (max-width: 480px) {
    .hero-content h1 {
        font-size: 2rem;
    }

    .strategy-categories h2,
    .performance-showcase h2,
    .strategies-cta h2 {
        font-size: 2rem;
    }

    .modal-header {
        padding: 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .performance-card {
        padding: 2rem;
    }

    .categories-grid {
        gap: 1.5rem;
    }

    .category-card {
        padding: 2rem;
    }
}
</style>

<?php get_footer(); ?>
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