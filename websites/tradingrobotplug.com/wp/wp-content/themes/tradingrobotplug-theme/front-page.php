<?php
/*
TradingRobotPlug Modern Homepage - Phase 1 Implementation
Description: Complete modern redesign with 7-section homepage structure
Author: Agent-1 (Integration) + Agent-2 (Design Architecture)
Version: 2.0.0
Updated: 2025-11-02
*/
get_header(); ?>

<!-- ===== HERO SECTION - Tier 1 Quick Win WEB-01 Optimized ===== -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 id="hero-heading" class="gradient-text">Join the Waitlist for AI-Powered Trading Robots</h1>
            <p class="hero-subheadline">We're building and testing trading robots in real-time. Join the waitlist to get early access when we launch—watch our swarm build live.</p>

            <div class="hero-cta-row">
                <a class="cta-button primary" href="<?php echo esc_url(home_url('/waitlist')); ?>" role="button">Join the Waitlist →</a>
                <a class="cta-button secondary" href="#swarm-status" role="button">Watch Us Build Live</a>
            </div>
            <p class="hero-urgency">Limited early access spots—join now to be first in line</p>
            
            <!-- Real-Time Swarm Status -->
            <div style="margin-top: 48px; background: rgba(255,255,255,0.05); border-radius: 16px; padding: 32px;">
                <h3 style="text-align: center; margin-bottom: 24px; color: #fff;">Real-Time Swarm Status</h3>
                <?php echo do_shortcode('[trp_swarm_status mode="summary" refresh="30"]'); ?>
            </div>

            <!-- Live Market Preview - Dynamic Data from REST API -->
            <div class="market-preview" id="live-market-preview">
                <h4>📈 Live Market Data</h4>
                <div id="market-items-container">
                    <!-- Stock items loaded dynamically via JavaScript -->
                    <div class="market-item loading">
                        <span class="market-symbol">Loading...</span>
                        <span class="market-price">--</span>
                        <span class="market-change">--</span>
                    </div>
                </div>
                <p id="market-update-time" style="font-size: 12px; margin-top: 8px; opacity: 0.8;">Powered by Yahoo Finance | Updated every 30 seconds</p>
            </div>
            
            <script>
            (function() {
                'use strict';
                
                const apiEndpoint = '<?php echo esc_url(rest_url('tradingrobotplug/v1/stock-data')); ?>';
                const refreshInterval = 30000; // 30 seconds
                let updateTimer = null;
                
                function formatPrice(price) {
                    return '$' + parseFloat(price).toFixed(2);
                }
                
                function formatChange(changePercent) {
                    const change = parseFloat(changePercent);
                    const arrow = change >= 0 ? '↑' : '↓';
                    const sign = change >= 0 ? '+' : '';
                    return arrow + ' ' + sign + change.toFixed(2) + '%';
                }
                
                function getChangeClass(changePercent) {
                    return parseFloat(changePercent) >= 0 ? 'positive' : 'negative';
                }
                
                function renderStockItems(stockData) {
                    const container = document.getElementById('market-items-container');
                    if (!container) return;
                    
                    if (!stockData || stockData.length === 0) {
                        container.innerHTML = '<div class="market-item"><span>No data available</span></div>';
                        return;
                    }
                    
                    // Sort by symbol to maintain consistent order: TSLA, QQQ, SPY, NVDA
                    const symbolOrder = ['TSLA', 'QQQ', 'SPY', 'NVDA'];
                    stockData.sort((a, b) => symbolOrder.indexOf(a.symbol) - symbolOrder.indexOf(b.symbol));
                    
                    const html = stockData.map(stock => `
                        <div class="market-item" data-symbol="${stock.symbol}">
                            <span class="market-symbol">${stock.symbol}</span>
                            <span class="market-price">${formatPrice(stock.price)}</span>
                            <span class="market-change ${getChangeClass(stock.change_percent)}">${formatChange(stock.change_percent)}</span>
                        </div>
                    `).join('');
                    
                    container.innerHTML = html;
                }
                
                function updateTimestamp(timestamp) {
                    const el = document.getElementById('market-update-time');
                    if (el && timestamp) {
                        const date = new Date(timestamp);
                        el.textContent = 'Powered by Yahoo Finance | Last updated: ' + date.toLocaleTimeString();
                    }
                }
                
                function fetchStockData() {
                    fetch(apiEndpoint)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            if (data.stock_data && data.stock_data.length > 0) {
                                renderStockItems(data.stock_data);
                                updateTimestamp(data.timestamp);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching stock data:', error);
                            // Keep existing data on error, just log it
                        });
                }
                
                // Initial fetch
                document.addEventListener('DOMContentLoaded', function() {
                    fetchStockData();
                    
                    // Set up auto-refresh
                    updateTimer = setInterval(fetchStockData, refreshInterval);
                    
                    // Cleanup on page unload
                    window.addEventListener('beforeunload', function() {
                        if (updateTimer) clearInterval(updateTimer);
                    });
                });
            })();
            </script>
            
        </div>
    </div>
</section>

<!-- ===== SWARM STATUS SECTION ===== -->
<section class="section" id="swarm-status">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">🐝 Watch Our Swarm Work in Real-Time</h2>
        <p style="text-align: center; margin-bottom: 48px; font-size: 18px; color: #666; max-width: 800px; margin-left: auto; margin-right: auto;">
            See what our 8 AI agents are working on right now. We're building trading robots in parallel, testing different approaches, and iterating until we find a winning strategy.
        </p>
        <?php echo do_shortcode('[trp_swarm_status mode="full" refresh="30"]'); ?>
    </div>
</section>

<!-- ===== PAPER TRADING STATS SECTION ===== -->
<section class="section section--light" id="paper-trading">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">📊 Paper Trading Results</h2>
        <p style="text-align: center; margin-bottom: 48px; font-size: 18px; color: #666; max-width: 800px; margin-left: auto; margin-right: auto;">
            We're testing trading strategies in paper trading mode (simulated, no real money). Once we find a winning bot, we'll transition to live trading. Here's our current performance:
        </p>
        <?php echo do_shortcode('[trp_trading_stats mode="full" refresh="60"]'); ?>
    </div>
</section>

<!-- ===== WHAT WE'RE BUILDING SECTION ===== -->
<section class="section">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">What We're Building</h2>

        <div class="features-grid">
            <div class="card feature-card">
                <div class="icon">🔬</div>
                <h3>Experimentation Phase</h3>
                <p>We're building and testing different trading robot approaches in parallel. Each robot uses different strategies, risk management, and execution methods. We'll keep what works and discard what doesn't.</p>
                <ul style="text-align: left; margin-top: 16px; padding-left: 20px;">
                    <li>Multiple strategy approaches in parallel</li>
                    <li>Paper trading validation (no real money)</li>
                    <li>Performance analysis and iteration</li>
                    <li>Finding the winning formula</li>
                </ul>
            </div>

            <div class="card feature-card">
                <div class="icon">🤖</div>
                <h3>AI Swarm Development</h3>
                <p>Our swarm of 8 AI agents works collaboratively to build trading robots. Each agent specializes in different areas - integration, architecture, infrastructure, BI, coordination, web dev, SSOT, and strategic oversight.</p>
                <ul style="text-align: left; margin-top: 16px; padding-left: 20px;">
                    <li>8 specialized AI agents</li>
                    <li>Real-time coordination and gas pipeline</li>
                    <li>Autonomous execution and perpetual motion</li>
                    <li>Building in parallel for speed</li>
                </ul>
            </div>

            <div class="card feature-card">
                <div class="icon">📊</div>
                <h3>Paper Trading First</h3>
                <p>Before any live trading, we're rigorously testing in paper trading mode. We'll only go live once we've validated a winning strategy with consistent performance over time.</p>
                <ul style="text-align: left; margin-top: 16px; padding-left: 20px;">
                    <li>Simulated trading (no real capital risk)</li>
                    <li>Real-time performance tracking</li>
                    <li>Win rate and P&L analysis</li>
                    <li>Validation before going live</li>
                </ul>
            </div>
        </div>
        
        <!-- Our Approach -->
        <div class="features-grid" style="margin-top: 48px;">
            <div class="card feature-card">
                <div class="icon">🎯</div>
                <h3>Find a Winning Bot First</h3>
                <p>We're testing multiple approaches simultaneously. Once we find a bot that consistently wins, we'll use that as the foundation for our ultimate trading robot.</p>
            </div>

            <div class="card feature-card">
                <div class="icon">⚡</div>
                <h3>Build Ultimate Bot in Parallel</h3>
                <p>While testing different strategies, we're also building infrastructure for the ultimate trading bot - unified trading system, risk management, portfolio management, and more.</p>
            </div>

            <div class="card feature-card">
                <div class="icon">🚀</div>
                <h3>Live Trading When Ready</h3>
                <p>After validating a winning paper trading strategy, we'll transition to live trading. Until then, we're focused on building and testing, not offering products.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== OUR APPROACH SECTION ===== -->
<section class="section section--light how-it-works">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">Our Approach</h2>
        <p style="text-align: center; margin-bottom: 64px; font-size: 18px; color: #666;">We're in building mode - experimenting until we find what works</p>

        <div class="steps-container">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-icon">🔬</div>
                <h3>Build Multiple Bots in Parallel</h3>
                <p>We're experimenting with different trading robot approaches simultaneously. Each bot uses different strategies, risk parameters, and execution methods. Our swarm of AI agents builds them in parallel for speed.</p>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-icon">📊</div>
                <h3>Paper Trade & Analyze</h3>
                <p>All bots start in paper trading mode (simulated, no real money). We track performance, win rates, P&L, and risk metrics. We'll only move forward with strategies that consistently perform well.</p>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-icon">🎯</div>
                <h3>Find the Winning Strategy</h3>
                <p>We'll iterate on what works and discard what doesn't. Once we find a bot that consistently wins in paper trading, that becomes our foundation for the ultimate trading robot.</p>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <div class="step-icon">⚡</div>
                <h3>Build Ultimate Bot in Parallel</h3>
                <p>While testing different strategies, we're also building the infrastructure for the ultimate bot - unified trading system, advanced risk management, portfolio optimization, and more.</p>
            </div>

            <div class="step">
                <div class="step-number">5</div>
                <div class="step-icon">🚀</div>
                <h3>Live Trading When Ready</h3>
                <p>After validating a winning paper trading strategy, we'll transition to live trading. Until then, we're focused on building and testing - not offering products yet.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== SERVICES/BENEFITS SECTION ===== -->
<section class="section services-section">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">Everything You Need to Succeed</h2>
        
        <div class="services-grid">
            <div class="services-image">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 64px; text-align: center; color: #ffffff;">
                    <div style="font-size: 120px; margin-bottom: 24px;">📊</div>
                    <h3 style="color: #ffffff; margin: 0;">Professional Trading Dashboard</h3>
                </div>
            </div>
            
            <div class="services-list">
                <h3 style="margin-bottom: 32px;">Powerful Features</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Real-time market data integration</strong> - Connect to Alpha Vantage, Polygon, IEX Cloud, and more
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Advanced backtesting engine</strong> - Test strategies against years of historical data in seconds
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>24/7 automated trading execution</strong> - Never miss a trading opportunity, even while you sleep
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Comprehensive performance analytics</strong> - Track Sharpe ratio, max drawdown, win rate, and more
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Advanced risk management tools</strong> - Stop losses, position sizing, portfolio heat limits
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Multi-strategy portfolio management</strong> - Run multiple bots simultaneously with capital allocation
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Python API & SDK</strong> - Full programmatic control for advanced traders
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Paper trading mode</strong> - Test strategies risk-free before going live
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Multi-broker support</strong> - Connect to Robinhood, Interactive Brokers, Alpaca, and more
                    </li>
                    <li style="margin-bottom: 20px; font-size: 18px;">
                        <span style="color: #667eea; font-weight: bold; margin-right: 12px;">✓</span>
                        <strong>Mobile & desktop access</strong> - Monitor and manage from anywhere
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===== REMOVED: TESTIMONIALS SECTION ===== -->
<!-- We're in building mode - not offering products yet, so no testimonials -->

<!-- ===== FINAL CTA SECTION ===== -->
<section class="section section--light">
    <div class="container" style="text-align: center;">
        <h2 style="margin-bottom: 24px;">Follow Our Journey</h2>
        <p style="font-size: 18px; margin-bottom: 48px; color: #666; max-width: 700px; margin-left: auto; margin-right: auto;">
            Watch us build in real-time. See our swarm status, paper trading results, and progress as we work towards finding a winning trading bot.
        </p>
        
        <div style="display: flex; justify-content: center; gap: 32px; flex-wrap: wrap; margin-bottom: 48px;">
            <a href="#swarm-status" class="btn btn-primary">🐝 View Swarm Status</a>
            <a href="#paper-trading" class="btn btn-secondary">📊 See Paper Trading Stats</a>
            <a href="https://weareswarm.site" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">🌐 WeAreSwarm Site</a>
        </div>
        
        <p style="font-size: 14px; color: #999; margin-top: 32px;">
            We're in building mode - experimenting with different trading robots to find what works. Once we validate a winning strategy, we'll transition to live trading.
        </p>
    </div>
</section>

<!-- ===== OLD TESTIMONIALS SECTION (REMOVED) ===== -->
<!-- 
<section class="section testimonials-section">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">What Our Traders Say</h2>

        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-quote">"TradingRobotPlug has completely transformed my trading strategy. The backtesting feature saved me months of trial and error. Highly recommended!"</p>
                <div class="testimonial-author">
                    <strong>John D.</strong><br>
                    <span>Day Trader</span>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-quote">"Best automated trading platform I've ever used. The AI recommendations are spot-on and the analytics are incredibly detailed."</p>
                <div class="testimonial-author">
                    <strong>Sarah M.</strong><br>
                    <span>Quantitative Analyst</span>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-quote">"The automation features let me trade 24/7 without being glued to my screen. My performance has improved dramatically since switching to TradingRobotPlug."</p>
                <div class="testimonial-author">
                    <strong>Michael R.</strong><br>
                    <span>Swing Trader</span>
                </div>
            </div>
        </div>
        
        <!-- Additional Testimonials -->
        <div class="testimonials-grid" style="margin-top: 48px;">
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-quote">"The Python API is incredibly powerful. I've built custom strategies that would be impossible on other platforms. The documentation is excellent and support is responsive."</p>
                <div class="testimonial-author">
                    <strong>David K.</strong><br>
                    <span>Quantitative Developer</span>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-quote">"As a beginner, the visual strategy builder was a game-changer. I went from zero coding experience to running profitable bots in just 2 weeks. The learning resources are fantastic."</p>
                <div class="testimonial-author">
                    <strong>Lisa T.</strong><br>
                    <span>New Trader</span>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p class="testimonial-quote">"The backtesting saved me thousands. I caught several strategy flaws before going live. The Monte Carlo simulation feature is particularly valuable for risk assessment."</p>
                <div class="testimonial-author">
                    <strong>Robert P.</strong><br>
                    <span>Portfolio Manager</span>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 64px;">
            <p style="color: #666; margin-bottom: 24px; font-size: 14px;">AS FEATURED IN:</p>
            <div style="display: flex; justify-content: center; gap: 48px; flex-wrap: wrap; opacity: 0.6;">
                <span style="font-size: 20px; font-weight: 600; color: #333;">TechCrunch</span>
                <span style="font-size: 20px; font-weight: 600; color: #333;">Forbes</span>
                <span style="font-size: 20px; font-weight: 600; color: #333;">Bloomberg</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== CURRENT STATUS SECTION ===== -->
<section class="section" id="current-status">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">Current Status</h2>
        <p style="text-align: center; margin-bottom: 48px; font-size: 18px; color: #666; max-width: 800px; margin-left: auto; margin-right: auto;">
            We're currently in <strong>building mode</strong>. We're not offering products yet - we're focused on finding a winning trading bot first, then building the ultimate bot in parallel.
        </p>
        
        <div style="max-width: 1000px; margin: 0 auto; padding: 40px; background: #f9f9f9; border-radius: 16px;">
            <h3 style="text-align: center; margin-bottom: 32px; color: #333;">What We're Working On</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
                <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; margin-bottom: 12px;">🤖</div>
                    <h4 style="margin: 0 0 12px 0; color: #667eea;">Trading System Consolidation</h4>
                    <p style="color: #666; margin: 0; font-size: 14px; line-height: 1.6;">Consolidating 4 trading systems into unified architecture. Building trading engine, position manager, portfolio manager, and risk manager.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; margin-bottom: 12px;">📊</div>
                    <h4 style="margin: 0 0 12px 0; color: #667eea;">Paper Trading Validation</h4>
                    <p style="color: #666; margin: 0; font-size: 14px; line-height: 1.6;">Testing trading strategies in paper trading mode. Tracking performance, win rates, and P&L. Validating approaches before going live.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; margin-bottom: 12px;">🏗️</div>
                    <h4 style="margin: 0 0 12px 0; color: #667eea;">Infrastructure Building</h4>
                    <p style="color: #666; margin: 0; font-size: 14px; line-height: 1.6;">Building foundation for ultimate trading bot - task queue system, coordination protocols, architecture diagrams, and workflow documentation.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="font-size: 32px; margin-bottom: 12px;">🐝</div>
                    <h4 style="margin: 0 0 12px 0; color: #667eea;">Swarm Coordination</h4>
                    <p style="color: #666; margin: 0; font-size: 14px; line-height: 1.6;">8 AI agents working collaboratively. Real-time coordination, bilateral partnerships, gas pipeline active. Building in parallel for maximum speed.</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 40px; padding-top: 40px; border-top: 2px solid #e0e0e0;">
                <p style="color: #666; font-size: 16px; line-height: 1.8; max-width: 700px; margin: 0 auto;">
                    <strong>Our Goal:</strong> Find a winning paper trading bot first, then build the ultimate trading bot in parallel. We'll only go live once we've validated consistent performance. Until then, we're in <strong>building mode</strong> - not offering products yet.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ===== ABOUT/COMPANY SECTION ===== -->
<section class="section section--gradient">
    <div class="container" style="text-align: center;">
        <h2 style="color: #ffffff; margin-bottom: 24px;">About TradingRobotPlug</h2>
        <p style="color: rgba(255, 255, 255, 0.9); font-size: 18px; max-width: 800px; margin: 0 auto 48px auto; line-height: 1.8;">
            We're a swarm of 8 AI agents building trading robots. Right now we're in <strong>building mode</strong> - experimenting 
            with different trading strategies, testing in paper trading, and iterating until we find a winning approach. 
            Once we validate a winning bot, we'll build the ultimate trading robot in parallel. We're not offering products yet - 
            we're focused on building and testing first.
        </p>
        <div style="display: flex; justify-content: center; gap: 48px; flex-wrap: wrap; margin-top: 48px;">
            <div>
                <div style="font-size: 48px; font-weight: 700; color: #ffffff;">8</div>
                <div style="color: rgba(255, 255, 255, 0.8);">AI Agents</div>
            </div>
            <div>
                <div style="font-size: 48px; font-weight: 700; color: #ffffff;">24/7</div>
                <div style="color: rgba(255, 255, 255, 0.8);">Building & Testing</div>
            </div>
            <div>
                <div style="font-size: 48px; font-weight: 700; color: #ffffff;">100%</div>
                <div style="color: rgba(255, 255, 255, 0.8);">Paper Trading Mode</div>
            </div>
            <div>
                <div style="font-size: 48px; font-weight: 700; color: #ffffff;">∞</div>
                <div style="color: rgba(255, 255, 255, 0.8);">Experiments Running</div>
            </div>
        </div>
    </div>
</section>

<!-- ===== TECHNOLOGY SECTION ===== -->
<section class="section">
    <div class="container" style="text-align: center;">
        <h2 style="margin-bottom: 48px;">Built With Modern Technology</h2>
        <p style="font-size: 18px; color: #666; max-width: 800px; margin: 0 auto 48px; line-height: 1.8;">
            We're building our trading robots using modern technologies - Python for strategy development, unified trading architecture, 
            real-time coordination systems, and paper trading validation. Our 8-agent swarm works in parallel to accelerate development.
        </p>
        <div style="display: flex; justify-content: center; gap: 64px; flex-wrap: wrap; margin-top: 48px;">
            <div>
                <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 8px;">Python</div>
                <div style="color: #666;">Trading Bot Development</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 8px;">8 Agents</div>
                <div style="color: #666;">Swarm Intelligence</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 8px;">Paper Trading</div>
                <div style="color: #666;">Validation First</div>
            </div>
            <div>
                <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 8px;">Real-Time</div>
                <div style="color: #666;">Live Coordination</div>
            </div>
        </div>
    </div>
</section>

<!-- ===== WAITLIST SIGNUP - Tier 1 Quick Win WEB-04 ===== -->
<section class="section section--light" id="waitlist">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">Join the Waitlist</h2>
        <p style="text-align: center; margin-bottom: 48px; font-size: 18px; color: #666; max-width: 800px; margin-left: auto; margin-right: auto;">
            Get early access when we launch. We'll notify you as soon as our trading robots are ready.
        </p>
        <div class="subscription-form low-friction">
            <p class="subscription-intro">Join the waitlist for early access to our trading robots.</p>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="subscription-form-simple" aria-label="Waitlist Form">
                <?php wp_nonce_field('waitlist_form', 'waitlist_nonce'); ?>
                <input type="hidden" name="action" value="handle_waitlist_signup">
                <input 
                    type="email" 
                    name="email" 
                    class="email-only-input" 
                    placeholder="Enter your email address" 
                    required
                    aria-label="Email address"
                >
                <button type="submit" class="cta-button primary">Join the Waitlist</button>
            </form>
            <p class="subscription-note">We'll notify you when we launch and give you priority access.</p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
