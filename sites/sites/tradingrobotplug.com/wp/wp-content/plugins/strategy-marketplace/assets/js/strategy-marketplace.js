// Strategy Marketplace JavaScript
(function($) {
    'use strict';

    let currentPage = 1;
    const strategiesPerPage = 12;

    // Initialize marketplace
    $(document).ready(function() {
        if ($('.strategy-marketplace').length) {
            loadStrategies();
            setupFilters();
            setupModal();
            setupLoadMore();
        }
    });

    function loadStrategies(page = 1, append = false) {
        const grid = $('.strategy-grid');
        const loading = grid.find('.loading-strategies');

        if (!append) {
            loading.show();
        }

        const filters = getFilterData();

        $.ajax({
            url: strategyMarketplaceAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_strategies',
                nonce: strategyMarketplaceAjax.nonce,
                page: page,
                per_page: strategiesPerPage,
                ...filters
            },
            success: function(response) {
                if (response.success) {
                    if (append) {
                        renderStrategies(response.data.strategies, true);
                    } else {
                        loading.hide();
                        renderStrategies(response.data.strategies, false);
                    }

                    updateLoadMoreButton(response.data.pages > page);
                } else {
                    showError('Error loading strategies');
                }
            },
            error: function() {
                showError('Network error loading strategies');
            }
        });
    }

    function getFilterData() {
        return {
            category: $('#category-filter').val(),
            performance: $('#performance-filter').val(),
            risk_level: $('#risk-filter').val(),
            search: $('#strategy-search').val()
        };
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
            class: `strategy-card ${strategy.category} ${strategy.risk_level}-risk ${strategy.status}`,
            'data-strategy-id': strategy.id
        });

        const performance = strategy.performance || {};

        card.html(`
            <div class="strategy-header">
                <h3 class="strategy-name">${strategy.name}</h3>
                <div class="strategy-badges">
                    <span class="badge category">${strategy.category}</span>
                    <span class="badge risk ${strategy.risk_level}">${strategy.risk_level} risk</span>
                    <span class="badge status ${strategy.status}">${strategy.status}</span>
                </div>
            </div>

            <div class="strategy-description">
                <p>${strategy.description}</p>
            </div>

            <div class="strategy-performance">
                <div class="perf-item">
                    <span class="perf-label">Return</span>
                    <span class="perf-value ${getReturnClass(performance.total_return)}">${performance.total_return || 'N/A'}</span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">Win Rate</span>
                    <span class="perf-value">${performance.win_rate || 'N/A'}</span>
                </div>
                <div class="perf-item">
                    <span class="perf-label">Max DD</span>
                    <span class="perf-value ${getDrawdownClass(performance.max_drawdown)}">${performance.max_drawdown || 'N/A'}</span>
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
                currentPage = 1;
                loadStrategies(1, false);
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

    function updateLoadMoreButton(show) {
        const btn = $('#load-more-strategies');
        if (show) {
            btn.show();
        } else {
            btn.hide();
        }
    }

    function showStrategyModal(strategyId) {
        // This would load strategy details via AJAX
        // For now, show a placeholder
        alert(`Strategy details modal for strategy ${strategyId} would open here`);
    }

    function deployStrategy(strategyId) {
        $.ajax({
            url: strategyMarketplaceAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'deploy_strategy',
                nonce: strategyMarketplaceAjax.nonce,
                strategy_id: strategyId
            },
            success: function(response) {
                if (response.success) {
                    alert('Strategy deployed successfully!');
                } else {
                    alert('Error deploying strategy: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                alert('Network error deploying strategy');
            }
        });
    }

    function showError(message) {
        const grid = $('.strategy-grid');
        grid.find('.loading-strategies').hide();
        grid.append(`<div class="error-message">${message}</div>`);
    }

})(jQuery);