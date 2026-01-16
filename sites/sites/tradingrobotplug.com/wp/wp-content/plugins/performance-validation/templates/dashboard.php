<!-- Performance Validation Dashboard -->
<div class="performance-dashboard">
    <div class="dashboard-header">
        <h2>Performance Validation Dashboard</h2>
        <div class="dashboard-controls">
            <select id="timeframe-select">
                <option value="1 day">Last 24 Hours</option>
                <option value="7 days" selected>Last 7 Days</option>
                <option value="30 days">Last 30 Days</option>
                <option value="90 days">Last 90 Days</option>
            </select>
            <button id="refresh-dashboard" class="btn btn-primary">Refresh</button>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="metrics-overview">
        <div class="metric-card">
            <h3>Total Users</h3>
            <div class="metric-value" id="total-users">--</div>
            <div class="metric-change" id="users-change"></div>
        </div>

        <div class="metric-card">
            <h3>Page Views</h3>
            <div class="metric-value" id="page-views">--</div>
            <div class="metric-change" id="views-change"></div>
        </div>

        <div class="metric-card">
            <h3>Strategy Deployments</h3>
            <div class="metric-value" id="strategy-deployments">--</div>
            <div class="metric-change" id="deployments-change"></div>
        </div>

        <div class="metric-card">
            <h3>Conversion Rate</h3>
            <div class="metric-value" id="conversion-rate">--%</div>
            <div class="metric-change" id="conversion-change"></div>
        </div>
    </div>

    <!-- User Engagement Chart -->
    <div class="chart-section">
        <h3>User Engagement Trends</h3>
        <canvas id="engagement-chart" width="400" height="200"></canvas>
    </div>

    <!-- Top Performing Strategies -->
    <div class="strategies-section">
        <h3>Top Performing Strategies</h3>
        <div id="strategies-list" class="strategies-list">
            <div class="loading">Loading strategy performance data...</div>
        </div>
    </div>

    <!-- A/B Testing Results -->
    <div class="ab-testing-section">
        <h3>A/B Testing Results</h3>
        <div id="ab-tests-list" class="ab-tests-list">
            <div class="loading">Loading A/B test results...</div>
        </div>
    </div>

    <!-- Raw Data Export -->
    <div class="export-section">
        <h3>Data Export</h3>
        <button id="export-csv" class="btn btn-secondary">Export to CSV</button>
        <button id="export-json" class="btn btn-secondary">Export to JSON</button>
    </div>
</div>

<style>
.performance-dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
}

.dashboard-header h2 {
    margin: 0;
    color: #333;
}

.dashboard-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

#timeframe-select {
    padding: 0.5rem 1rem;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    font-size: 0.9rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

/* Metrics Overview */
.metrics-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.metric-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    text-align: center;
    border: 1px solid #e9ecef;
}

.metric-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.metric-change {
    font-size: 0.9rem;
    font-weight: 500;
}

.metric-change.positive {
    color: #28a745;
}

.metric-change.negative {
    color: #dc3545;
}

/* Chart Section */
.chart-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
    border: 1px solid #e9ecef;
}

.chart-section h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 1rem;
}

/* Strategies and A/B Testing Sections */
.strategies-section,
.ab-testing-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
    border: 1px solid #e9ecef;
}

.strategies-section h3,
.ab-testing-section h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 1rem;
}

.strategies-list,
.ab-tests-list {
    display: grid;
    gap: 1rem;
}

.strategy-item,
.ab-test-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.strategy-item:hover,
.ab-test-item:hover {
    background: #e9ecef;
}

.strategy-name,
.test-name {
    font-weight: 600;
    color: #333;
}

.strategy-metrics,
.test-metrics {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.metric {
    text-align: center;
}

.metric-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

/* Loading States */
.loading {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
    font-style: italic;
}

/* Export Section */
.export-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    text-align: center;
}

.export-section h3 {
    margin-top: 0;
    color: #333;
    margin-bottom: 1.5rem;
}

.export-section .btn {
    margin: 0 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .performance-dashboard {
        padding: 1rem;
    }

    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .metrics-overview {
        grid-template-columns: 1fr;
    }

    .metric-card {
        padding: 1rem;
    }

    .metric-value {
        font-size: 2rem;
    }

    .strategy-metrics,
    .test-metrics {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function($) {
    'use strict';

    let engagementChart = null;

    $(document).ready(function() {
        loadDashboardData();
        setupEventHandlers();
    });

    function setupEventHandlers() {
        $('#timeframe-select').on('change', function() {
            loadDashboardData();
        });

        $('#refresh-dashboard').on('click', function() {
            loadDashboardData();
        });

        $('#export-csv, #export-json').on('click', function() {
            const format = $(this).attr('id').split('-')[1];
            exportData(format);
        });
    }

    function loadDashboardData() {
        const timeframe = $('#timeframe-select').val();

        $.ajax({
            url: performanceValidationAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_performance_metrics',
                nonce: performanceValidationAjax.nonce,
                timeframe: timeframe
            },
            success: function(response) {
                if (response.success) {
                    updateDashboard(response.data);
                } else {
                    console.error('Failed to load dashboard data');
                }
            },
            error: function() {
                console.error('Network error loading dashboard data');
            }
        });
    }

    function updateDashboard(data) {
        // Update key metrics
        updateKeyMetrics(data.metrics);

        // Update charts
        updateEngagementChart(data);

        // Update strategies list
        updateStrategiesList(data);

        // Update A/B tests
        updateABTests(data);
    }

    function updateKeyMetrics(metrics) {
        // Calculate key metrics from the data
        const totalUsers = metrics.filter(m => m.metric_type === 'user_action').length;
        const pageViews = metrics.filter(m => m.metric_key === 'page_view').reduce((sum, m) => sum + parseInt(m.total_value), 0);
        const deployments = metrics.filter(m => m.metric_key === 'strategy_deployment').reduce((sum, m) => sum + parseInt(m.total_value), 0);
        const conversions = metrics.filter(m => m.metric_type === 'conversion').length;
        const conversionRate = totalUsers > 0 ? ((conversions / totalUsers) * 100).toFixed(1) : 0;

        $('#total-users').text(totalUsers.toLocaleString());
        $('#page-views').text(pageViews.toLocaleString());
        $('#strategy-deployments').text(deployments.toLocaleString());
        $('#conversion-rate').text(conversionRate + '%');

        // Add change indicators (simplified - in real implementation, compare with previous period)
        $('#users-change').html('<span class="positive">↗ +12%</span>');
        $('#views-change').html('<span class="positive">↗ +8%</span>');
        $('#deployments-change').html('<span class="positive">↗ +25%</span>');
        $('#conversion-change').html('<span class="positive">↗ +5%</span>');
    }

    function updateEngagementChart(data) {
        const ctx = document.getElementById('engagement-chart').getContext('2d');

        if (engagementChart) {
            engagementChart.destroy();
        }

        // Sample data - in real implementation, this would be processed from metrics
        const chartData = {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Page Views',
                data: [120, 150, 180, 200, 250, 220, 190],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4
            }, {
                label: 'Strategy Deployments',
                data: [5, 8, 12, 15, 20, 18, 16],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.4
            }]
        };

        engagementChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'User Engagement Over Time'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function updateStrategiesList(data) {
        const container = $('#strategies-list');
        const strategies = data.metrics.filter(m => m.metric_type === 'strategy_performance');

        if (strategies.length === 0) {
            container.html('<div class="strategy-item">No strategy performance data available</div>');
            return;
        }

        let html = '';
        strategies.slice(0, 5).forEach(strategy => {
            html += `
                <div class="strategy-item">
                    <div class="strategy-name">${strategy.metric_key}</div>
                    <div class="strategy-metrics">
                        <div class="metric">
                            <div class="metric-label">Deployments</div>
                            <div class="metric-value">${strategy.total_value}</div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">Avg Return</div>
                            <div class="metric-value">${strategy.avg_value}%</div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
    }

    function updateABTests(data) {
        const container = $('#ab-tests-list');

        // Mock A/B test data - in real implementation, this would come from the database
        const abTests = [
            {
                name: 'Hero Section Design',
                control_conversion: 2.1,
                variant_conversion: 2.8,
                improvement: '+33%'
            },
            {
                name: 'Strategy Marketplace Layout',
                control_conversion: 1.8,
                variant_conversion: 2.3,
                improvement: '+28%'
            }
        ];

        let html = '';
        abTests.forEach(test => {
            html += `
                <div class="ab-test-item">
                    <div class="test-name">${test.name}</div>
                    <div class="test-metrics">
                        <div class="metric">
                            <div class="metric-label">Control</div>
                            <div class="metric-value">${test.control_conversion}%</div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">Variant</div>
                            <div class="metric-value">${test.variant_conversion}%</div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">Improvement</div>
                            <div class="metric-value positive">${test.improvement}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
    }

    function exportData(format) {
        const timeframe = $('#timeframe-select').val();
        const filename = `performance_data_${timeframe.replace(' ', '_')}.${format}`;

        // In a real implementation, this would generate and download the actual data
        alert(`Exporting ${format.toUpperCase()} file: ${filename}\n\nThis feature would download all performance metrics for the selected timeframe.`);
    }

})(jQuery);
</script>