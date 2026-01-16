/**
 * TRP TSLA Live Analysis - Frontend JavaScript
 * ============================================
 */

(function($) {
    'use strict';

    class TRP_TSLA_Analysis {
        constructor() {
            this.cache = {};
            this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
            this.init();
        }

        init() {
            this.bindEvents();
            this.loadInitialData();
            this.setupAutoRefresh();
        }

        bindEvents() {
            // Refresh buttons
            $(document).on('click', '.trp-tsla-refresh', (e) => {
                e.preventDefault();
                this.refreshData();
            });

            // Analysis tabs
            $(document).on('click', '.analysis-tab', (e) => {
                e.preventDefault();
                this.switchTab($(e.target));
            });
        }

        loadInitialData() {
            // Load analysis data for all visible components
            $('.trp-tsla-analysis').each((index, element) => {
                this.loadAnalysisData($(element));
            });
        }

        loadAnalysisData($container) {
            const containerId = $container.attr('id') || 'analysis-' + Date.now();

            // Show loading state
            this.showLoading($container);

            // Load indicators if requested
            if ($container.find('#indicators-data').length) {
                this.loadIndicators($container);
            }

            // Load recommendations if requested
            if ($container.find('#recommendations-data').length) {
                this.loadRecommendations($container);
            }
        }

        loadIndicators($container) {
            const $indicatorsContainer = $container.find('#indicators-data');

            fetch('/wp-json/trp-tsla/v1/indicators')
                .then(response => response.json())
                .then(data => {
                    $indicatorsContainer.html(this.formatIndicators(data));
                    this.hideLoading($container);
                })
                .catch(error => {
                    console.error('Error loading indicators:', error);
                    $indicatorsContainer.html('<div class="error-message">Error loading technical indicators</div>');
                    this.hideLoading($container);
                });
        }

        loadRecommendations($container) {
            const $recommendationsContainer = $container.find('#recommendations-data');

            fetch('/wp-json/trp-tsla/v1/recommendations')
                .then(response => response.json())
                .then(data => {
                    $recommendationsContainer.html(this.formatRecommendations(data));
                    this.hideLoading($container);
                })
                .catch(error => {
                    console.error('Error loading recommendations:', error);
                    $recommendationsContainer.html('<div class="error-message">Error loading AI recommendations</div>');
                    this.hideLoading($container);
                });
        }

        formatIndicators(data) {
            if (!data || !data.indicators) {
                return '<p>No indicator data available</p>';
            }

            const indicators = data.indicators;
            const isMock = data.status === 'mock_data';

            return `
                <div class="indicators-grid">
                    <div class="indicator-card">
                        <div class="price">$${indicators.price?.toFixed(2) || 'N/A'}</div>
                        <div class="label">Current Price</div>
                    </div>
                    <div class="indicator-card">
                        <div class="price">$${indicators.vwap?.toFixed(2) || 'N/A'}</div>
                        <div class="label">VWAP</div>
                    </div>
                    <div class="indicator-card">
                        <div class="price">$${indicators.ema9?.toFixed(2) || 'N/A'}</div>
                        <div class="label">EMA 9</div>
                    </div>
                    <div class="indicator-card">
                        <div class="price">$${indicators.ema21?.toFixed(2) || 'N/A'}</div>
                        <div class="label">EMA 21</div>
                    </div>
                </div>
                ${isMock ? '<div class="data-notice">📊 Showing demo data - Configure Alpha Vantage API for live data</div>' : ''}
            `;
        }

        formatRecommendations(data) {
            if (!data || !data.recommendations) {
                return '<p>No recommendations available</p>';
            }

            const recs = data.recommendations;
            const isMock = data.status === 'mock_data';

            const actionColor = recs.action === 'BUY' ? '#38a169' :
                               recs.action === 'SELL' ? '#e53e3e' : '#d69e2e';

            const confidencePercent = Math.round((recs.confidence || 0) * 100);

            return `
                <div class="recommendations-card">
                    <div class="action-header">
                        <div>
                            <div class="action-icon">${recs.action === 'BUY' ? '🟢' : recs.action === 'SELL' ? '🔴' : '🟡'}</div>
                            <div class="action-text" style="color: ${actionColor}">${recs.action || 'HOLD'}</div>
                        </div>
                        <div class="confidence-score">
                            <div class="confidence-number" style="color: ${actionColor}">${confidencePercent}%</div>
                            <div class="confidence-label">Confidence</div>
                        </div>
                    </div>

                    <div class="analysis-summary">
                        <h4>Analysis Summary</h4>
                        <p>${recs.reasoning || 'AI analysis in progress...'}</p>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-icon">🎯</div>
                            <div class="stat-value">$${recs.target_price?.toFixed(2) || 'N/A'}</div>
                            <div class="stat-label">Target Price</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">⏱️</div>
                            <div class="stat-value">${recs.timeframe || 'N/A'}</div>
                            <div class="stat-label">Timeframe</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">📊</div>
                            <div class="stat-value">${recs.regime || 'N/A'}</div>
                            <div class="stat-label">Market Regime</div>
                        </div>
                    </div>

                    ${isMock ? '<div class="data-notice">🤖 Showing AI-generated demo recommendations - Configure for live analysis</div>' : ''}
                </div>
            `;
        }

        showLoading($container) {
            if (!$container.find('.analysis-loading').length) {
                $container.append(`
                    <div class="analysis-loading">
                        <div class="loading-spinner"></div>
                        <p>Loading live TSLA analysis...</p>
                    </div>
                `);
            }
        }

        hideLoading($container) {
            $container.find('.analysis-loading').remove();
            $container.find('.analysis-content').show();
        }

        refreshData() {
            // Clear cache and reload all data
            this.cache = {};
            $('.trp-tsla-analysis').each((index, element) => {
                this.loadAnalysisData($(element));
            });
        }

        switchTab($tab) {
            const target = $tab.data('target');
            const $container = $tab.closest('.trp-tsla-analysis');

            // Update tab states
            $container.find('.analysis-tab').removeClass('active');
            $tab.addClass('active');

            // Show target content
            $container.find('.analysis-content').hide();
            $container.find(target).show();
        }

        setupAutoRefresh() {
            // Auto-refresh every 5 minutes
            setInterval(() => {
                this.refreshData();
            }, this.cacheTimeout);
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        window.trpTSLAAnalysis = new TRP_TSLA_Analysis();
    });

})(jQuery);