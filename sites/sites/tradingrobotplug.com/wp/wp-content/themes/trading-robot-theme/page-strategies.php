<?php
/*
Strategy Marketplace Page Template
Description: Interactive marketplace for trading strategies and algorithms
Author: Agent-7 (Web Development)
Version: 1.0.0
Updated: 2026-01-15
*/
get_header(); ?>

<!-- ===== STRATEGY MARKETPLACE HERO ===== -->
<section class="strategy-marketplace-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="gradient-text">AI Strategy Marketplace</h1>
            <p class="hero-subheadline">Browse, test, and deploy algorithmic trading strategies. From momentum to mean-reversion, find the perfect strategy for your trading style.</p>

            <div class="marketplace-stats">
                <div class="stat-item">
                    <span class="stat-number" id="total-strategies">24</span>
                    <span class="stat-label">Active Strategies</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="live-testing">12</span>
                    <span class="stat-label">In Live Testing</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="avg-performance">+18.7%</span>
                    <span class="stat-label">Avg. Monthly Return</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== STRATEGY FILTERS & SEARCH ===== -->
<section class="marketplace-filters">
    <div class="container">
        <div class="filters-bar">
            <div class="search-box">
                <input type="text" id="strategy-search" placeholder="Search strategies..." class="search-input">
                <button class="search-btn">🔍</button>
            </div>

            <div class="filter-controls">
                <select id="category-filter" class="filter-select">
                    <option value="">All Categories</option>
                    <option value="momentum">Momentum</option>
                    <option value="mean-reversion">Mean Reversion</option>
                    <option value="breakout">Breakout</option>
                    <option value="scalping">Scalping</option>
                    <option value="swing">Swing Trading</option>
                    <option value="arbitrage">Arbitrage</option>
                </select>

                <select id="performance-filter" class="filter-select">
                    <option value="">All Performance</option>
                    <option value="high">High Performers (+15%+)</option>
                    <option value="moderate">Moderate (5-15%)</option>
                    <option value="new">New Strategies</option>
                </select>

                <select id="risk-filter" class="filter-select">
                    <option value="">All Risk Levels</option>
                    <option value="low">Low Risk</option>
                    <option value="medium">Medium Risk</option>
                    <option value="high">High Risk</option>
                </select>

                <button id="clear-filters" class="clear-filters-btn">Clear All</button>
            </div>
        </div>
    </div>
</section>

<!-- ===== STRATEGY GRID ===== -->
<section class="strategy-marketplace">
    <div class="container">
        <div id="strategy-grid" class="strategy-grid">
            <!-- Strategies loaded dynamically -->
            <div class="loading-strategies">
                <div class="loading-spinner"></div>
                <p>Loading trading strategies...</p>
            </div>
        </div>

        <!-- Load More Button -->
        <div class="load-more-container">
            <button id="load-more-strategies" class="load-more-btn">Load More Strategies</button>
        </div>
    </div>
</section>

<!-- ===== STRATEGY DETAIL MODAL ===== -->
<div id="strategy-modal" class="strategy-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-strategy-title"></h2>
            <button class="modal-close">&times;</button>
        </div>

        <div class="modal-body">
            <div class="strategy-overview">
                <div class="strategy-meta">
                    <span class="strategy-category" id="modal-category"></span>
                    <span class="strategy-risk" id="modal-risk"></span>
                    <span class="strategy-status" id="modal-status"></span>
                </div>

                <div class="strategy-description" id="modal-description"></div>

                <div class="strategy-performance">
                    <h3>Performance Metrics</h3>
                    <div class="performance-grid">
                        <div class="metric">
                            <span class="metric-label">Total Return</span>
                            <span class="metric-value" id="modal-total-return">+24.7%</span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Win Rate</span>
                            <span class="metric-value" id="modal-win-rate">68.4%</span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Max Drawdown</span>
                            <span class="metric-value" id="modal-max-drawdown">-12.3%</span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Sharpe Ratio</span>
                            <span class="metric-value" id="modal-sharpe">1.8</span>
                        </div>
                    </div>
                </div>

                <!-- Backtesting Interface -->
                <div class="backtest-section">
                    <h3>Test This Strategy</h3>
                    <div class="backtest-controls">
                        <div class="date-range">
                            <label>Start Date:</label>
                            <input type="date" id="backtest-start" value="2023-01-01">
                            <label>End Date:</label>
                            <input type="date" id="backtest-end" value="2024-01-01">
                        </div>
                        <button id="run-backtest" class="btn btn-primary">Run Backtest</button>
                    </div>

                    <div id="backtest-results" class="backtest-results" style="display: none;">
                        <h4>Backtest Results</h4>
                        <div class="results-grid">
                            <div class="result-item">
                                <span class="result-label">Strategy Return:</span>
                                <span class="result-value" id="backtest-return">--</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Benchmark (SPY):</span>
                                <span class="result-value" id="benchmark-return">--</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Alpha:</span>
                                <span class="result-value" id="strategy-alpha">--</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Max Drawdown:</span>
                                <span class="result-value" id="backtest-drawdown">--</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="strategy-parameters">
                    <h3>Strategy Parameters</h3>
                    <div class="parameters-grid" id="modal-parameters"></div>
                </div>
            </div>

            <div class="strategy-actions">
                <button class="btn btn-primary" id="deploy-strategy">Deploy Strategy</button>
                <button class="btn btn-secondary" id="view-source">View Source Code</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Strategy Marketplace Styles */
.strategy-marketplace-hero {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #020617 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.strategy-marketplace-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
    animation: pulse-glow 8s ease-in-out infinite;
}

.marketplace-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 3rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: #60a5fa;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Filters */
.marketplace-filters {
    background: #f8f9fa;
    padding: 2rem 0;
    border-bottom: 1px solid #e9ecef;
}

.filters-bar {
    display: flex;
    gap: 2rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    display: flex;
    gap: 0.5rem;
    flex: 1;
    min-width: 300px;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 1rem;
}

.search-btn {
    padding: 0.75rem 1.5rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.filter-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 0.9rem;
    min-width: 150px;
}

.clear-filters-btn {
    padding: 0.75rem 1.5rem;
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

/* Strategy Grid */
.strategy-marketplace {
    padding: 4rem 0;
}

.strategy-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.strategy-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.strategy-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.strategy-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.strategy-name {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.strategy-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.badge.category {
    background: rgba(255,255,255,0.2);
}

.badge.risk {
    background: rgba(255,255,255,0.2);
}

.badge.status.live {
    background: #28a745;
}

.badge.status.testing {
    background: #ffc107;
    color: #212529;
}

.badge.status.new {
    background: #17a2b8;
}

.strategy-description {
    padding: 1.5rem;
}

.strategy-description p {
    margin: 0;
    color: #6c757d;
    line-height: 1.6;
}

.strategy-performance {
    padding: 0 1.5rem 1.5rem;
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.perf-item {
    text-align: center;
    flex: 1;
}

.perf-label {
    display: block;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.perf-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 600;
    color: #28a745;
}

.perf-value.excellent {
    color: #28a745;
}

.perf-value.good {
    color: #20c997;
}

.perf-value.moderate {
    color: #ffc107;
}

.perf-value.poor {
    color: #dc3545;
}

.perf-value.high-risk {
    color: #dc3545;
}

.perf-value.moderate-risk {
    color: #fd7e14;
}

.perf-value.low-risk {
    color: #28a745;
}

.strategy-actions {
    padding: 1.5rem;
    background: #f8f9fa;
    display: flex;
    gap: 0.75rem;
}

.strategy-actions .btn {
    flex: 1;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-outline {
    background: transparent;
    color: #007bff;
    border: 2px solid #007bff;
}

.btn-outline:hover {
    background: #007bff;
    color: white;
}

/* Modal */
.strategy-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
}

.modal-content {
    background: white;
    margin: 5% auto;
    width: 90%;
    max-width: 800px;
    border-radius: 12px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 2rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.75rem;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    color: #6c757d;
}

.modal-body {
    padding: 2rem;
}

.strategy-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.strategy-category,
.strategy-risk,
.strategy-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.strategy-category {
    background: #e9ecef;
}

.strategy-risk {
    background: #fff3cd;
}

.strategy-status {
    background: #d1ecf1;
}

.strategy-description {
    margin-bottom: 2rem;
    line-height: 1.6;
    color: #495057;
}

.performance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.metric {
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.metric-label {
    display: block;
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.metric-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 600;
    color: #28a745;
}

.backtest-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.backtest-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.date-range {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.backtest-results {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.result-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.result-label {
    font-weight: 600;
    color: #495057;
}

.result-value {
    font-weight: 700;
    color: #28a745;
}

.parameters-grid {
    display: grid;
    gap: 0.75rem;
}

.parameter-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.param-label {
    font-weight: 600;
    color: #495057;
}

.param-value {
    color: #6c757d;
}

.strategy-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
}

/* Loading states */
.loading-strategies {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem;
}

.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Load more */
.load-more-container {
    text-align: center;
    margin-top: 3rem;
}

.load-more-btn {
    padding: 1rem 2rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
}

.load-more-btn:hover {
    background: #0056b3;
}

/* Responsive */
@media (max-width: 768px) {
    .marketplace-stats {
        flex-direction: column;
        gap: 1.5rem;
    }

    .filters-bar {
        flex-direction: column;
        gap: 1rem;
    }

    .filter-controls {
        flex-direction: column;
        width: 100%;
    }

    .filter-select {
        width: 100%;
    }

    .strategy-grid {
        grid-template-columns: 1fr;
    }

    .strategy-performance {
        flex-direction: column;
        gap: 1rem;
    }

    .performance-grid {
        grid-template-columns: 1fr;
    }

    .strategy-actions {
        flex-direction: column;
    }

    .modal-content {
        margin: 2% auto;
        width: 95%;
    }
}
</style>

<script>
// Strategy Marketplace JavaScript
(function($) {
    'use strict';

    let currentPage = 1;
    const strategiesPerPage = 12;

    // Sample strategy data (would come from API in production)
    const sampleStrategies = [
        {
            id: 1,
            name: "TSLA Momentum Hunter",
            category: "momentum",
            risk: "high",
            status: "live",
            description: "Captures Tesla's explosive momentum moves using advanced pattern recognition and volume analysis.",
            performance: {
                totalReturn: "+42.3%",
                winRate: "71.2%",
                maxDrawdown: "-18.7%",
                sharpeRatio: "2.1"
            },
            parameters: {
                "Entry Threshold": "2.5σ above mean",
                "Exit Target": "15% profit or -8% stop",
                "Holding Period": "1-5 days",
                "Volume Filter": "150% above average"
            }
        },
        {
            id: 2,
            name: "QQQ Mean Reversion",
            category: "mean-reversion",
            risk: "medium",
            status: "testing",
            description: "Identifies overbought/oversold conditions in QQQ and trades against extreme moves.",
            performance: {
                totalReturn: "+18.9%",
                winRate: "65.8%",
                maxDrawdown: "-9.4%",
                sharpeRatio: "1.7"
            },
            parameters: {
                "RSI Threshold": "30/70 levels",
                "Lookback Period": "20 days",
                "Position Size": "2% per trade",
                "Exit Signal": "RSI crosses 50"
            }
        },
        {
            id: 3,
            name: "SPY Breakout Trader",
            category: "breakout",
            risk: "medium",
            status: "live",
            description: "Trades S&P 500 breakouts above resistance levels with trend confirmation.",
            performance: {
                totalReturn: "+31.6%",
                winRate: "63.9%",
                maxDrawdown: "-14.2%",
                sharpeRatio: "1.9"
            },
            parameters: {
                "Breakout Level": "52-week high",
                "Volume Confirmation": "200% above average",
                "Stop Loss": "5% below entry",
                "Trend Filter": "200-day MA"
            }
        },
        {
            id: 4,
            name: "NVDA Scalping Algorithm",
            category: "scalping",
            risk: "high",
            status: "new",
            description: "Ultra-short-term scalping strategy for NVDA volatility using tick-level data.",
            performance: {
                totalReturn: "+156.8%",
                winRate: "78.4%",
                maxDrawdown: "-22.1%",
                sharpeRatio: "2.8"
            },
            parameters: {
                "Tick Size": "0.01",
                "Target Profit": "$0.50 per share",
                "Max Hold Time": "30 seconds",
                "Volume Threshold": "10,000 shares"
            }
        }
    ];

    // Initialize marketplace
    $(document).ready(function() {
        loadStrategies();
        setupFilters();
        setupModal();
        setupLoadMore();
        setupBacktesting();
    });

    function loadStrategies(page = 1, append = false) {
        const grid = $('.strategy-grid');
        const loading = grid.find('.loading-strategies');

        if (!append) {
            loading.show();
        }

        // Simulate API delay
        setTimeout(() => {
            if (!append) {
                loading.hide();
            }
            renderStrategies(sampleStrategies, append);
        }, append ? 500 : 1000);
    }

    function renderStrategies(strategies, append = false) {
        const grid = $('.strategy-grid');

        if (!append) {
            grid.find('.strategy-card').remove();
        }

        strategies.forEach(strategy => {
            const strategyCard = createStrategyCard(strategy);
            grid.append(strategyCard);
        });
    }

    function createStrategyCard(strategy) {
        const card = $('<div>', {
            class: `strategy-card ${strategy.category} ${strategy.risk}-risk ${strategy.status}`,
            'data-strategy-id': strategy.id
        });

        const performance = strategy.performance || {};

        card.html(`
            <div class="strategy-header">
                <h3 class="strategy-name">${strategy.name}</h3>
                <div class="strategy-badges">
                    <span class="badge category">${strategy.category}</span>
                    <span class="badge risk ${strategy.risk}">${strategy.risk} risk</span>
                    <span class="badge status ${strategy.status}">${strategy.status}</span>
                </div>
            </div>

            <div class="strategy-description">
                <p>${strategy.description}</p>
            </div>

            <div class="strategy-performance">
                <div class="perf-item">
                    <span class="perf-label">Return</span>
                    <span class="perf-value ${getReturnClass(performance.totalReturn)}">${performance.totalReturn || 'N/A'}</span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">Win Rate</span>
                    <span class="perf-value">${performance.winRate || 'N/A'}</span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">Max DD</span>
                    <span class="perf-value ${getDrawdownClass(performance.maxDrawdown)}">${performance.maxDrawdown || 'N/A'}</span>
                </div>
            </div>

            <div class="strategy-actions">
                <button class="btn btn-primary view-details" data-strategy-id="${strategy.id}">View Details</button>
                <button class="btn btn-outline deploy-strategy" data-strategy-id="${strategy.id}">Deploy Strategy</button>
            </div>
        `);

        // Attach event handlers
        card.find('.view-details').on('click', function() {
            showStrategyModal($(this).data('strategy-id'));
        });

        card.find('.deploy-strategy').on('click', function() {
            deployStrategy($(this).data('strategy-id'));
        });

        return card;
    }

    function getReturnClass(returnValue) {
        if (!returnValue) return '';
        const value = parseFloat(returnValue);
        if (value > 30) return 'excellent';
        if (value > 15) return 'good';
        if (value > 5) return 'moderate';
        return 'poor';
    }

    function getDrawdownClass(drawdownValue) {
        if (!drawdownValue) return '';
        const value = Math.abs(parseFloat(drawdownValue));
        if (value > 20) return 'high-risk';
        if (value > 10) return 'moderate-risk';
        return 'low-risk';
    }

    function setupFilters() {
        let filterTimeout;

        function applyFilters() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                const searchTerm = $('#strategy-search').val().toLowerCase();
                const categoryValue = $('#category-filter').val();
                const performanceValue = $('#performance-filter').val();
                const riskValue = $('#risk-filter').val();

                const cards = $('.strategy-card');

                cards.each(function() {
                    const card = $(this);
                    const strategyName = card.find('.strategy-name').text().toLowerCase();
                    const category = card.hasClass(categoryValue) || !categoryValue;
                    const risk = card.hasClass(`${riskValue}-risk`) || !riskValue;
                    const matchesSearch = strategyName.includes(searchTerm);

                    // Performance filtering logic
                    let matchesPerformance = true;
                    if (performanceValue) {
                        const returnValue = parseFloat(card.find('.perf-value').first().text());
                        switch (performanceValue) {
                            case 'high':
                                matchesPerformance = returnValue > 15;
                                break;
                            case 'moderate':
                                matchesPerformance = returnValue >= 5 && returnValue <= 15;
                                break;
                            case 'new':
                                matchesPerformance = card.hasClass('new');
                                break;
                        }
                    }

                    if (matchesSearch && category && risk && matchesPerformance) {
                        card.show();
                    } else {
                        card.hide();
                    }
                });
            }, 300);
        }

        $('#strategy-search').on('input', applyFilters);
        $('#category-filter, #performance-filter, #risk-filter').on('change', applyFilters);

        $('#clear-filters').on('click', function() {
            $('#strategy-search').val('');
            $('#category-filter, #performance-filter, #risk-filter').val('');
            applyFilters();
        });
    }

    function setupModal() {
        $('.strategy-modal .modal-close').on('click', function() {
            $('.strategy-modal').hide();
        });

        $(window).on('click', function(e) {
            if ($(e.target).hasClass('strategy-modal')) {
                $('.strategy-modal').hide();
            }
        });
    }

    function setupLoadMore() {
        $('#load-more-strategies').on('click', function() {
            currentPage++;
            loadStrategies(currentPage, true);
        });
    }

    function showStrategyModal(strategyId) {
        const strategy = sampleStrategies.find(s => s.id === strategyId);
        if (!strategy) return;

        const modal = $('.strategy-modal');
        const title = $('#modal-strategy-title');
        const category = $('#modal-category');
        const risk = $('#modal-risk');
        const status = $('#modal-status');
        const description = $('#modal-description');

        title.text(strategy.name);
        category.text(strategy.category);
        risk.text(`${strategy.risk} risk`);
        status.text(strategy.status);
        description.text(strategy.description);

        // Update performance metrics
        $('#modal-total-return').text(strategy.performance.totalReturn);
        $('#modal-win-rate').text(strategy.performance.winRate);
        $('#modal-max-drawdown').text(strategy.performance.maxDrawdown);
        $('#modal-sharpe').text(strategy.performance.sharpeRatio);

        // Update parameters
        const paramsContainer = $('#modal-parameters');
        paramsContainer.empty();

        Object.entries(strategy.parameters).forEach(([key, value]) => {
            const paramDiv = $('<div>', { class: 'parameter-item' });
            paramDiv.html(`
                <span class="param-label">${key}:</span>
                <span class="param-value">${value}</span>
            `);
            paramsContainer.append(paramDiv);
        });

        modal.show();
    }

    function deployStrategy(strategyId) {
        const strategy = sampleStrategies.find(s => s.id === strategyId);
        alert(`🚀 Deploying ${strategy.name} to your account!\n\nYou'll receive an email confirmation and can monitor performance in your dashboard.`);
    }

    function setupBacktesting() {
        $('#run-backtest').on('click', function() {
            const startDate = $('#backtest-start').val();
            const endDate = $('#backtest-end').val();

            if (!startDate || !endDate) {
                alert('Please select both start and end dates.');
                return;
            }

            // Simulate backtest results
            $('#backtest-results').show();
            $('#backtest-return').text('+24.7%');
            $('#benchmark-return').text('+12.3%');
            $('#strategy-alpha').text('+12.4%');
            $('#backtest-drawdown').text('-8.2%');

            // In a real implementation, this would call your trading engine API
            console.log('Running backtest from', startDate, 'to', endDate);
        });
    }

})(jQuery);
</script>

<?php get_footer(); ?>