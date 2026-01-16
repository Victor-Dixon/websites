/**
 * TRP Strategy Marketplace - Frontend JavaScript
 * ==============================================
 */

(function($) {
    'use strict';

    class TRP_Strategy_Marketplace {
        constructor() {
            this.strategies = [];
            this.filteredStrategies = [];
            this.currentFilters = {
                type: 'all',
                sort: 'performance'
            };
            this.cache = {};
            this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
            this.init();
        }

        init() {
            this.bindEvents();
            this.loadStrategies();
        }

        bindEvents() {
            // Filter changes
            $(document).on('change', '#strategy-filter', (e) => {
                this.currentFilters.type = $(e.target).val();
                this.applyFilters();
            });

            $(document).on('change', '#sort-filter', (e) => {
                this.currentFilters.sort = $(e.target).val();
                this.applyFilters();
            });

            // Strategy card clicks
            $(document).on('click', '.strategy-card', (e) => {
                // Don't trigger if clicking on buttons
                if ($(e.target).hasClass('btn-primary') || $(e.target).hasClass('btn-secondary')) {
                    return;
                }
                const strategyId = $(e.target).closest('.strategy-card').data('strategy-id');
                if (strategyId) {
                    this.viewStrategyDetails(strategyId);
                }
            });

            // Run backtest buttons
            $(document).on('click', '.btn-secondary', (e) => {
                e.stopPropagation();
                const strategyId = $(e.target).closest('.strategy-card').data('strategy-id');
                if (strategyId) {
                    this.showBacktestModal(strategyId);
                }
            });
        }

        loadStrategies() {
            const $grid = $('.strategies-grid');
            const $loading = $('.marketplace-loading');

            if (!$grid.length) return;

            $loading.show();
            $grid.hide();

            fetch('/wp-json/trp-strategy/v1/strategies')
                .then(response => response.json())
                .then(data => {
                    this.strategies = data.strategies || [];
                    this.filteredStrategies = [...this.strategies];
                    this.applyFilters();
                    $loading.hide();
                    $grid.show();
                })
                .catch(error => {
                    console.error('Error loading strategies:', error);
                    $grid.html('<div class="error-message">Error loading strategies. Please try again later.</div>');
                    $loading.hide();
                });
        }

        applyFilters() {
            let filtered = [...this.strategies];

            // Apply type filter
            if (this.currentFilters.type !== 'all') {
                filtered = filtered.filter(strategy => strategy.type === this.currentFilters.type);
            }

            // Apply sorting
            filtered.sort((a, b) => {
                switch (this.currentFilters.sort) {
                    case 'performance':
                        const aReturn = parseFloat(a.performance?.total_return?.replace('%', '') || '0');
                        const bReturn = parseFloat(b.performance?.total_return?.replace('%', '') || '0');
                        return bReturn - aReturn;

                    case 'win_rate':
                        const aWinRate = parseFloat(a.performance?.win_rate?.replace('%', '') || '0');
                        const bWinRate = parseFloat(b.performance?.win_rate?.replace('%', '') || '0');
                        return bWinRate - aWinRate;

                    case 'newest':
                        // For demo, just randomize
                        return Math.random() - 0.5;

                    default:
                        return 0;
                }
            });

            this.filteredStrategies = filtered;
            this.renderStrategies();
        }

        renderStrategies() {
            const $grid = $('.strategies-grid');

            if (!this.filteredStrategies.length) {
                $grid.html('<div class="no-results">No strategies match your filters.</div>');
                return;
            }

            const html = this.filteredStrategies.map(strategy => `
                <div class="strategy-card" data-strategy-id="${strategy.id}">
                    <div class="strategy-header">
                        <div class="strategy-icon">${this.getStrategyIcon(strategy.type)}</div>
                        <div class="strategy-meta">
                            <h3>${strategy.name}</h3>
                            <span class="strategy-type">${strategy.type}</span>
                        </div>
                    </div>
                    <div class="strategy-stats">
                        <div class="stat">
                            <span class="stat-value">${strategy.performance?.total_return || 'N/A'}</span>
                            <span class="stat-label">Total Return</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">${strategy.performance?.win_rate || 'N/A'}</span>
                            <span class="stat-label">Win Rate</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value ${parseFloat(strategy.performance?.max_drawdown?.replace('%', '') || '0') < 0 ? 'negative' : ''}">${strategy.performance?.max_drawdown || 'N/A'}</span>
                            <span class="stat-label">Max Drawdown</span>
                        </div>
                    </div>
                    <div class="strategy-description">
                        <p>${strategy.description}</p>
                    </div>
                    <div class="strategy-actions">
                        <button class="btn-primary">View Details</button>
                        <button class="btn-secondary">Run Backtest</button>
                    </div>
                </div>
            `).join('');

            $grid.html(html);
        }

        getStrategyIcon(type) {
            const icons = {
                'conservative': '🛡️',
                'aggressive': '⚡',
                'momentum': '📈',
                'mean_reversion': '🔄',
                'arbitrage': '⚖️'
            };
            return icons[type] || '🤖';
        }

        viewStrategyDetails(strategyId) {
            // For demo, show an alert. In production, this would navigate to a detail page
            const strategy = this.strategies.find(s => s.id === strategyId);
            if (strategy) {
                alert(`Viewing details for: ${strategy.name}\n\nDescription: ${strategy.description}\n\nPerformance: ${strategy.performance?.total_return || 'N/A'} total return`);
            }
        }

        showBacktestModal(strategyId) {
            const strategy = this.strategies.find(s => s.id === strategyId);
            if (!strategy) return;

            const modalHtml = `
                <div class="backtest-modal-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; display: flex; align-items: center; justify-content: center;">
                    <div class="backtest-modal" style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
                        <h3 style="margin-top: 0; color: #333;">Run Backtest: ${strategy.name}</h3>

                        <form id="backtest-form">
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Start Date:</label>
                                <input type="date" id="backtest-start" value="2023-01-01" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">End Date:</label>
                                <input type="date" id="backtest-end" value="${new Date().toISOString().split('T')[0]}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" style="flex: 1; padding: 10px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer;">Run Backtest</button>
                                <button type="button" onclick="closeBacktestModal()" style="padding: 10px; background: #f8f9fa; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Cancel</button>
                            </div>
                        </form>

                        <div id="backtest-results" style="margin-top: 20px; display: none;">
                            <h4>Backtest Results</h4>
                            <div id="backtest-loading" style="text-align: center; padding: 20px;">
                                <div style="display: inline-block; width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <p style="margin-top: 10px;">Running backtest...</p>
                            </div>
                            <div id="backtest-content"></div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHtml);

            // Handle form submission
            $('#backtest-form').on('submit', (e) => {
                e.preventDefault();
                this.runBacktest(strategyId);
            });
        }

        runBacktest(strategyId) {
            const startDate = $('#backtest-start').val();
            const endDate = $('#backtest-end').val();

            $('#backtest-loading').show();
            $('#backtest-content').hide();

            fetch('/wp-json/trp-strategy/v1/backtest', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    strategy_id: strategyId,
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                $('#backtest-loading').hide();
                $('#backtest-results').show();

                if (data.results) {
                    const results = data.results;
                    $('#backtest-content').html(`
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-bottom: 15px;">
                                <div><strong>Total Return:</strong><br>${results.total_return}</div>
                                <div><strong>Win Rate:</strong><br>${results.win_rate}</div>
                                <div><strong>Max Drawdown:</strong><br>${results.max_drawdown}</div>
                                <div><strong>Sharpe Ratio:</strong><br>${results.sharpe_ratio}</div>
                            </div>
                            <p style="margin: 0; font-size: 12px; color: #666;">Backtest period: ${results.period}</p>
                        </div>
                    `);
                } else {
                    $('#backtest-content').html('<p style="color: red;">Error running backtest</p>');
                }
            })
            .catch(error => {
                console.error('Error running backtest:', error);
                $('#backtest-loading').hide();
                $('#backtest-content').html('<p style="color: red;">Error running backtest</p>');
            });
        }
    }

    // Global function to close modal
    window.closeBacktestModal = function() {
        $('.backtest-modal-overlay').remove();
    };

    // Initialize on document ready
    $(document).ready(function() {
        if ($('.trp-strategy-marketplace').length) {
            window.trpStrategyMarketplace = new TRP_Strategy_Marketplace();
        }
    });

})(jQuery);