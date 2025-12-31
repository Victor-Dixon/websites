<?php
/*
TradingRobotPlug Modern Homepage - Optimized Version
Description: Streamlined 4-section homepage structure (Hero, Swarm Status, Paper Trading, Final CTA)
Author: Agent-7 (Web Development)
Version: 2.1.0
Updated: 2025-12-30
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
                    
                    const html = stockData.map(stock => {
                        // Handle both database format and API format
                        const symbol = stock.symbol || stock.SYMBOL || 'N/A';
                        // Convert price to number (handle string values from database)
                        const price = parseFloat(stock.price || stock.PRICE || 0);
                        // Convert change_percent to number (handle string values from database)
                        const changePercent = parseFloat(stock.change_percent || stock.CHANGE_PERCENT || stock.changePercent || 0);
                        
                        return `
                        <div class="market-item" data-symbol="${symbol}">
                            <span class="market-symbol">${symbol}</span>
                            <span class="market-price">${formatPrice(price)}</span>
                            <span class="market-change ${getChangeClass(changePercent)}">${formatChange(changePercent)}</span>
                        </div>
                    `;
                    }).join('');
                    
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
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Stock data received:', data);
                            if (data.stock_data && data.stock_data.length > 0) {
                                renderStockItems(data.stock_data);
                                updateTimestamp(data.timestamp);
                            } else {
                                console.warn('No stock data in response:', data);
                                // Show error message
                                const container = document.getElementById('market-items-container');
                                if (container) {
                                    container.innerHTML = '<div class="market-item"><span style="color: #ff6b6b;">No data available. API may be temporarily unavailable.</span></div>';
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching stock data:', error);
                            // Show error message to user
                            const container = document.getElementById('market-items-container');
                            if (container) {
                                container.innerHTML = '<div class="market-item"><span style="color: #ff6b6b;">Error loading market data. Please refresh the page.</span></div>';
                            }
                            const timeEl = document.getElementById('market-update-time');
                            if (timeEl) {
                                timeEl.textContent = 'Error: ' + error.message;
                            }
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


<?php get_footer(); ?>
