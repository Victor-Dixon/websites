<?php
/*
Strategy Marketplace Page Template
Description: Interactive marketplace for trading strategies and algorithms
Author: Agent-7 (Web Development)
Version: 1.0.0
Updated: 2025-01-15
*/
get_header(); ?>

<!-- ===== STRATEGY MARKETPLACE HERO ===== -->
<section class="marketplace-hero">
    <div class="container">
        <div class="marketplace-hero-content">
            <h1 class="gradient-text">AI Trading Strategy Marketplace</h1>
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

                <div class="strategy-chart" id="strategy-chart">
                    <!-- Chart will be loaded here -->
                    <div class="chart-placeholder">
                        <p>Performance chart loading...</p>
                    </div>
                </div>

                <div class="strategy-parameters">
                    <h3>Strategy Parameters</h3>
                    <div class="parameters-grid" id="modal-parameters"></div>
                </div>
            </div>

            <div class="strategy-actions">
                <button class="btn btn-primary" id="deploy-strategy">Deploy Strategy</button>
                <button class="btn btn-secondary" id="backtest-strategy">Run Backtest</button>
                <button class="btn btn-outline" id="view-source">View Source Code</button>
            </div>
        </div>
    </div>
</div>

<script>
// Strategy Marketplace JavaScript
(function() {
    'use strict';

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

    let currentPage = 1;
    const strategiesPerPage = 12;

    // Initialize marketplace
    document.addEventListener('DOMContentLoaded', function() {
        loadStrategies();
        setupFilters();
        setupModal();
    });

    function loadStrategies() {
        const grid = document.getElementById('strategy-grid');
        const loading = grid.querySelector('.loading-strategies');

        // Simulate API delay
        setTimeout(() => {
            loading.style.display = 'none';
            renderStrategies(sampleStrategies);
        }, 1000);
    }

    function renderStrategies(strategies) {
        const grid = document.getElementById('strategy-grid');

        strategies.forEach(strategy => {
            const strategyCard = createStrategyCard(strategy);
            grid.appendChild(strategyCard);
        });
    }

    function createStrategyCard(strategy) {
        const card = document.createElement('div');
        card.className = `strategy-card ${strategy.category} ${strategy.risk}-risk ${strategy.status}`;
        card.dataset.strategyId = strategy.id;

        card.innerHTML = `
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
                    <span class="perf-value ${getReturnClass(strategy.performance.totalReturn)}">${strategy.performance.totalReturn}</span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">Win Rate</span>
                    <span class="perf-value">${strategy.performance.winRate}</span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">Max DD</span>
                    <span class="perf-value ${getDrawdownClass(strategy.performance.maxDrawdown)}">${strategy.performance.maxDrawdown}</span>
                </div>
            </div>

            <div class="strategy-actions">
                <button class="btn btn-primary view-details" data-strategy-id="${strategy.id}">View Details</button>
                <button class="btn btn-outline deploy-mini" data-strategy-id="${strategy.id}">Quick Deploy</button>
            </div>
        `;

        // Add click handlers
        card.querySelector('.view-details').addEventListener('click', () => showStrategyModal(strategy.id));
        card.querySelector('.deploy-mini').addEventListener('click', () => quickDeployStrategy(strategy.id));

        return card;
    }

    function getReturnClass(returnValue) {
        const value = parseFloat(returnValue);
        if (value > 30) return 'excellent';
        if (value > 15) return 'good';
        if (value > 5) return 'moderate';
        return 'poor';
    }

    function getDrawdownClass(drawdownValue) {
        const value = Math.abs(parseFloat(drawdownValue));
        if (value > 20) return 'high-risk';
        if (value > 10) return 'moderate-risk';
        return 'low-risk';
    }

    function setupFilters() {
        const searchInput = document.getElementById('strategy-search');
        const categoryFilter = document.getElementById('category-filter');
        const performanceFilter = document.getElementById('performance-filter');
        const riskFilter = document.getElementById('risk-filter');
        const clearFiltersBtn = document.getElementById('clear-filters');

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryValue = categoryFilter.value;
            const performanceValue = performanceFilter.value;
            const riskValue = riskFilter.value;

            const cards = document.querySelectorAll('.strategy-card');

            cards.forEach(card => {
                const strategyName = card.querySelector('.strategy-name').textContent.toLowerCase();
                const category = card.classList.contains(categoryValue) || !categoryValue;
                const risk = card.classList.contains(`${riskValue}-risk`) || !riskValue;
                const matchesSearch = strategyName.includes(searchTerm);

                // Performance filtering logic
                let matchesPerformance = true;
                if (performanceValue) {
                    const returnValue = parseFloat(card.querySelector('.perf-value').textContent);
                    switch (performanceValue) {
                        case 'high':
                            matchesPerformance = returnValue > 15;
                            break;
                        case 'moderate':
                            matchesPerformance = returnValue >= 5 && returnValue <= 15;
                            break;
                        case 'new':
                            matchesPerformance = card.classList.contains('new');
                            break;
                    }
                }

                if (matchesSearch && category && risk && matchesPerformance) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', applyFilters);
        categoryFilter.addEventListener('change', applyFilters);
        performanceFilter.addEventListener('change', applyFilters);
        riskFilter.addEventListener('change', applyFilters);

        clearFiltersBtn.addEventListener('click', () => {
            searchInput.value = '';
            categoryFilter.value = '';
            performanceFilter.value = '';
            riskFilter.value = '';
            applyFilters();
        });
    }

    function setupModal() {
        const modal = document.getElementById('strategy-modal');
        const closeBtn = modal.querySelector('.modal-close');

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    function showStrategyModal(strategyId) {
        const strategy = sampleStrategies.find(s => s.id === strategyId);
        if (!strategy) return;

        const modal = document.getElementById('strategy-modal');
        const title = document.getElementById('modal-strategy-title');
        const category = document.getElementById('modal-category');
        const risk = document.getElementById('modal-risk');
        const status = document.getElementById('modal-status');
        const description = document.getElementById('modal-description');

        title.textContent = strategy.name;
        category.textContent = strategy.category;
        risk.textContent = `${strategy.risk} risk`;
        status.textContent = strategy.status;
        description.textContent = strategy.description;

        // Update performance metrics
        document.getElementById('modal-total-return').textContent = strategy.performance.totalReturn;
        document.getElementById('modal-win-rate').textContent = strategy.performance.winRate;
        document.getElementById('modal-max-drawdown').textContent = strategy.performance.maxDrawdown;
        document.getElementById('modal-sharpe').textContent = strategy.performance.sharpeRatio;

        // Update parameters
        const paramsContainer = document.getElementById('modal-parameters');
        paramsContainer.innerHTML = '';

        Object.entries(strategy.parameters).forEach(([key, value]) => {
            const paramDiv = document.createElement('div');
            paramDiv.className = 'parameter-item';
            paramDiv.innerHTML = `
                <span class="param-label">${key}:</span>
                <span class="param-value">${value}</span>
            `;
            paramsContainer.appendChild(paramDiv);
        });

        modal.style.display = 'block';
    }

    function quickDeployStrategy(strategyId) {
        const strategy = sampleStrategies.find(s => s.id === strategyId);
        alert(`🚀 Deploying ${strategy.name} to your account!\n\nYou'll receive an email confirmation and can monitor performance in your dashboard.`);
    }

})();
</script>

<style>
/* Strategy Marketplace Styles */
.marketplace-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.marketplace-hero .gradient-text {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
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
    color: #ffd700;
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
    color: #212529;
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
    .marketplace-hero .gradient-text {
        font-size: 2.5rem;
    }

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

<?php get_footer(); ?>