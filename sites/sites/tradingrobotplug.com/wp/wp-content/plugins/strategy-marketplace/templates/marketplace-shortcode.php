<!-- Strategy Marketplace Shortcode Template -->
<div class="strategy-marketplace-shortcode">
    <!-- Mini Filters -->
    <div class="marketplace-mini-filters">
        <select id="mini-category-filter" class="mini-filter">
            <option value="">All Categories</option>
            <option value="momentum">Momentum</option>
            <option value="mean-reversion">Mean Reversion</option>
            <option value="breakout">Breakout</option>
            <option value="scalping">Scalping</option>
        </select>

        <select id="mini-risk-filter" class="mini-filter">
            <option value="">All Risk Levels</option>
            <option value="low">Low Risk</option>
            <option value="medium">Medium Risk</option>
            <option value="high">High Risk</option>
        </select>
    </div>

    <!-- Featured Strategies -->
    <div id="featured-strategies" class="featured-strategies">
        <div class="loading-spinner"></div>
        <p>Loading featured strategies...</p>
    </div>

    <!-- View All Link -->
    <div class="view-all-container">
        <a href="<?php echo esc_url(home_url('/strategy-marketplace')); ?>" class="view-all-btn">
            View All Strategies →
        </a>
    </div>
</div>

<style>
.strategy-marketplace-shortcode {
    margin: 2rem 0;
}

.marketplace-mini-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    justify-content: center;
}

.mini-filter {
    padding: 0.5rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    font-size: 0.9rem;
    min-width: 150px;
}

.featured-strategies {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.featured-strategy-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.featured-strategy-card:hover {
    transform: translateY(-2px);
}

.strategy-mini-header {
    padding: 1rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.strategy-mini-name {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.strategy-mini-badges {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.mini-badge {
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 500;
    text-transform: uppercase;
}

.strategy-mini-performance {
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.mini-perf-item {
    text-align: center;
    flex: 1;
}

.mini-perf-label {
    display: block;
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.mini-perf-value {
    display: block;
    font-size: 1rem;
    font-weight: 600;
    color: #28a745;
}

.strategy-mini-actions {
    padding: 1rem;
    background: #f8f9fa;
    text-align: center;
}

.view-all-container {
    text-align: center;
    margin-top: 2rem;
}

.view-all-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: background 0.3s ease;
}

.view-all-btn:hover {
    background: #0056b3;
    color: white;
}
</style>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        loadFeaturedStrategies();

        // Filter change handlers
        $('#mini-category-filter, #mini-risk-filter').on('change', function() {
            loadFeaturedStrategies();
        });
    });

    function loadFeaturedStrategies() {
        const category = $('#mini-category-filter').val();
        const risk = $('#mini-risk-filter').val();

        $.ajax({
            url: strategyMarketplaceAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_strategies',
                nonce: strategyMarketplaceAjax.nonce,
                category: category,
                risk_level: risk,
                per_page: 3,
                featured: true
            },
            success: function(response) {
                if (response.success) {
                    renderFeaturedStrategies(response.data.strategies);
                } else {
                    $('#featured-strategies').html('<p>Error loading strategies</p>');
                }
            },
            error: function() {
                $('#featured-strategies').html('<p>Error loading strategies</p>');
            }
        });
    }

    function renderFeaturedStrategies(strategies) {
        if (!strategies || strategies.length === 0) {
            $('#featured-strategies').html('<p>No strategies found</p>');
            return;
        }

        let html = '';
        strategies.forEach(function(strategy) {
            html += `
                <div class="featured-strategy-card">
                    <div class="strategy-mini-header">
                        <h4 class="strategy-mini-name">${strategy.name}</h4>
                        <div class="strategy-mini-badges">
                            <span class="mini-badge">${strategy.category}</span>
                            <span class="mini-badge">${strategy.risk_level}</span>
                        </div>
                    </div>

                    <div class="strategy-mini-performance">
                        <div class="mini-perf-item">
                            <span class="mini-perf-label">Return</span>
                            <span class="mini-perf-value">${strategy.performance.total_return || 'N/A'}</span>
                        </div>
                        <div class="mini-perf-item">
                            <span class="mini-perf-label">Win Rate</span>
                            <span class="mini-perf-value">${strategy.performance.win_rate || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="strategy-mini-actions">
                        <button class="deploy-mini-btn" data-strategy-id="${strategy.id}">
                            Deploy Strategy
                        </button>
                    </div>
                </div>
            `;
        });

        $('#featured-strategies').html(html);

        // Attach deploy handlers
        $('.deploy-mini-btn').on('click', function() {
            const strategyId = $(this).data('strategy-id');
            deployStrategy(strategyId);
        });
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
                    alert('Error deploying strategy: ' + response.data);
                }
            },
            error: function() {
                alert('Error deploying strategy');
            }
        });
    }
})(jQuery);
</script>