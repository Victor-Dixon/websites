<?php
/**
 * Dashboard Page Template
 * Trading performance dashboard with metrics, charts, and trade history
 * 
 * @package TradingRobotPlug
 * @version 2.0.0
 * @since 2025-12-25
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<main id="dashboard" class="dashboard-main">
    <div class="container">
        <header class="dashboard-header">
            <h1 class="dashboard-title">Trading Performance Dashboard</h1>
            <p class="dashboard-subtitle">Real-time performance metrics and trade analysis</p>
        </header>

        <!-- Metrics Cards Grid (12 cards) - Reordered per Agent-5 recommendations -->
        <section class="dashboard-metrics" id="dashboard-metrics">
            <div class="metrics-grid">
                <!-- Top Row (Critical): Total P&L, Daily P&L, Win Rate, ROI -->
                <div class="metric-card metric-card-profit">
                    <div class="metric-card-header">
                        <span class="metric-label">Total P&L</span>
                        <span class="metric-icon">💰</span>
                    </div>
                    <div class="metric-value" data-metric="total_pnl">$-</div>
                    <div class="metric-change" data-change="pnl_change">-</div>
                </div>

                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Daily P&L</span>
                        <span class="metric-icon">📅</span>
                    </div>
                    <div class="metric-value" data-metric="daily_pnl">$-</div>
                    <div class="metric-change" data-change="daily_pnl_change">-</div>
                </div>

                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Win Rate</span>
                        <span class="metric-icon">🎯</span>
                    </div>
                    <div class="metric-value" data-metric="win_rate">-%</div>
                    <div class="metric-change">Winning trades percentage</div>
                </div>

                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">ROI</span>
                        <span class="metric-icon">📊</span>
                    </div>
                    <div class="metric-value" data-metric="roi">-%</div>
                    <div class="metric-change">Return on investment</div>
                </div>

                <!-- Second Row (Strategy): Active Strategies, Sharpe Ratio, Profit Factor -->
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Active Strategies</span>
                        <span class="metric-icon">⚡</span>
                    </div>
                    <div class="metric-value" data-metric="active_strategies">-</div>
                    <div class="metric-change">Currently running</div>
                </div>

                <div class="metric-card" data-tooltip="Risk-adjusted return metric (>1 is good, >2 is excellent)">
                    <div class="metric-card-header">
                        <span class="metric-label">Sharpe Ratio</span>
                        <span class="metric-icon">📉</span>
                        <span class="tooltip-icon" title="Risk-adjusted return metric (>1 is good, >2 is excellent)">ℹ️</span>
                    </div>
                    <div class="metric-value" data-metric="sharpe_ratio">-</div>
                    <div class="metric-change">Risk-adjusted return</div>
                </div>

                <div class="metric-card" data-tooltip="Gross profit / Gross loss (>1.5 is sustainable)">
                    <div class="metric-card-header">
                        <span class="metric-label">Profit Factor</span>
                        <span class="metric-icon">⚖️</span>
                        <span class="tooltip-icon" title="Gross profit / Gross loss (>1.5 is sustainable)">ℹ️</span>
                    </div>
                    <div class="metric-value" data-metric="profit_factor">-</div>
                    <div class="metric-change">Gross profit / gross loss</div>
                </div>

                <!-- Third Row (Risk): Max Drawdown, Avg Return, Monthly P&L -->
                <div class="metric-card metric-card-warning" data-tooltip="Largest peak-to-trough decline">
                    <div class="metric-card-header">
                        <span class="metric-label">Max Drawdown</span>
                        <span class="metric-icon">⚠️</span>
                        <span class="tooltip-icon" title="Largest peak-to-trough decline">ℹ️</span>
                    </div>
                    <div class="metric-value" data-metric="max_drawdown">-%</div>
                    <div class="metric-change">Maximum peak-to-trough decline</div>
                </div>

                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Avg Return</span>
                        <span class="metric-icon">📊</span>
                    </div>
                    <div class="metric-value" data-metric="avg_return">-%</div>
                    <div class="metric-change">Average trade return</div>
                </div>

                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Monthly P&L</span>
                        <span class="metric-icon">📆</span>
                    </div>
                    <div class="metric-value" data-metric="monthly_pnl">$-</div>
                    <div class="metric-change" data-change="monthly_pnl_change">-</div>
                </div>

                <!-- Bottom Row (Volume): Total Trades, Total Strategies -->
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Total Trades</span>
                        <span class="metric-icon">📈</span>
                    </div>
                    <div class="metric-value" data-metric="total_trades">-</div>
                    <div class="metric-change">All-time trades executed</div>
                </div>

                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-label">Total Strategies</span>
                        <span class="metric-icon">📊</span>
                    </div>
                    <div class="metric-value" data-metric="total_strategies">-</div>
                    <div class="metric-change">Active trading strategies</div>
                </div>
            </div>
        </section>

        <!-- Charts Section (4 charts) - Reordered per Agent-5 recommendations -->
        <section class="dashboard-charts" id="dashboard-charts">
            <div class="charts-grid">
                <!-- Performance Chart - Primary position (largest) -->
                <div class="chart-card chart-card-primary">
                    <div class="chart-header">
                        <h2 class="chart-title">Performance Over Time</h2>
                        <div class="chart-controls">
                            <select class="chart-period-select" data-chart="performance">
                                <option value="7d">7 Days</option>
                                <option value="30d" selected>30 Days</option>
                                <option value="90d">90 Days</option>
                                <option value="1y">1 Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <!-- Win/Loss Ratio Chart - Secondary (pie chart) -->
                <div class="chart-card chart-card-secondary">
                    <div class="chart-header">
                        <h2 class="chart-title">Win/Loss Ratio</h2>
                        <div class="chart-controls">
                            <select class="chart-period-select" data-chart="winloss">
                                <option value="7d">7 Days</option>
                                <option value="30d" selected>30 Days</option>
                                <option value="90d">90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="winlossChart"></canvas>
                    </div>
                </div>

                <!-- Strategy Comparison Chart - Side panel -->
                <div class="chart-card chart-card-side">
                    <div class="chart-header">
                        <h2 class="chart-title">Strategy Comparison</h2>
                        <div class="chart-controls">
                            <select class="chart-period-select" data-chart="strategy">
                                <option value="7d">7 Days</option>
                                <option value="30d" selected>30 Days</option>
                                <option value="90d">90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="strategyChart"></canvas>
                    </div>
                </div>

                <!-- Trades Distribution Chart - Below main chart -->
                <div class="chart-card chart-card-bottom">
                    <div class="chart-header">
                        <h2 class="chart-title">Trades Distribution</h2>
                        <div class="chart-controls">
                            <select class="chart-period-select" data-chart="trades">
                                <option value="7d">7 Days</option>
                                <option value="30d" selected>30 Days</option>
                                <option value="90d">90 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="tradesChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trades Table -->
        <section class="dashboard-trades" id="dashboard-trades">
            <div class="trades-header">
                <h2 class="trades-title">Recent Trades</h2>
                <div class="trades-controls">
                    <input type="text" class="trades-search" placeholder="Search trades..." id="tradesSearch">
                    <select class="trades-filter" id="tradesFilter">
                        <option value="all">All Trades</option>
                        <option value="win">Winning Trades</option>
                        <option value="loss">Losing Trades</option>
                        <option value="pending">Pending Trades</option>
                    </select>
                </div>
            </div>
            <div class="trades-table-container">
                <table class="trades-table" id="tradesTable">
                    <thead>
                        <tr>
                            <th>Trade ID</th>
                            <th>Strategy</th>
                            <th>Symbol</th>
                            <th>Side</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>P&L</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tradesTableBody">
                        <tr>
                            <td colspan="9" class="trades-loading">Loading trades...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="trades-pagination" id="tradesPagination"></div>
        </section>
    </div>
</main>

<?php
get_footer();

