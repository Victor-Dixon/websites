/**
 * Trading Performance Dashboard JavaScript
 * Handles metrics, charts, and trades table updates
 * 
 * @package TradingRobotPlug
 * @version 2.0.0
 * @since 2025-12-25
 */

(function($) {
    'use strict';

    const Dashboard = {
        apiBase: '/wp-json/tradingrobotplug/v1',
        charts: {},
        updateInterval: 5000, // 5 seconds for real-time updates
        metricsUpdateInterval: 3000, // 3 seconds for metrics
        chartsUpdateInterval: 10000, // 10 seconds for charts (less frequent)
        tradesUpdateInterval: 8000, // 8 seconds for trades
        currentStrategy: null,
        updateTimers: {},
        isConnected: false,
        retryCount: 0,
        maxRetries: 5,
        retryDelay: 1000,

        init: function() {
            if (!$('.dashboard-main').length) return;

            this.initCharts();
            this.initRealTimeUpdates();
            this.loadDashboardData();
            this.setupEventListeners();
            this.startRealTimeUpdates();
            this.showConnectionStatus();
            
            // Track dashboard view
            this.trackGA4Event('dashboard_view', {
                page_title: 'Trading Performance Dashboard',
                page_location: window.location.href
            });
        },

        initRealTimeUpdates: function() {
            // Initialize WebSocket if available, fallback to polling
            if (this.supportsWebSocket() && this.shouldUseWebSocket()) {
                this.initWebSocket();
            } else {
                this.initPolling();
            }
        },

        supportsWebSocket: function() {
            return typeof WebSocket !== 'undefined';
        },

        shouldUseWebSocket: function() {
            // Check if WebSocket endpoint is available
            // FastAPI WebSocket endpoint: ws://localhost:8001/ws/events (dev) or wss://api.tradingrobotplug.com/ws/events (prod)
            const wsUrl = this.getWebSocketUrl();
            return wsUrl !== null;
        },
        
        getWebSocketUrl: function() {
            // Get WebSocket URL from WordPress settings or use default
            // FastAPI WebSocket endpoint: /ws/events
            // Development: ws://localhost:8001/ws/events
            // Production: wss://api.tradingrobotplug.com/ws/events
            if (typeof tradingRobotPlugConfig !== 'undefined' && tradingRobotPlugConfig.websocketUrl) {
                return tradingRobotPlugConfig.websocketUrl;
            }
            // Default to FastAPI WebSocket endpoint
            const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            const host = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
                ? 'localhost:8001' 
                : 'api.tradingrobotplug.com';
            return `${protocol}//${host}/ws/events`;
        },

        initWebSocket: function() {
            // WebSocket implementation for Phase 3
            const wsUrl = this.getWebSocketUrl();
            if (!wsUrl) {
                console.warn('WebSocket URL not configured, falling back to polling');
                this.fallbackToPolling();
                return;
            }
            
            try {
                this.ws = new WebSocket(wsUrl);
                this.clientId = null;
                this.heartbeatInterval = 30000; // 30 seconds
                this.heartbeatTimer = null;
                
                this.ws.onopen = () => {
                    console.log('WebSocket connected to:', wsUrl);
                    this.isConnected = true;
                    this.retryCount = 0;
                    this.updateConnectionStatus('connected');
                    this.startHeartbeat();
                };
                
                this.ws.onmessage = (event) => {
                    try {
                        const message = JSON.parse(event.data);
                        this.handleWebSocketMessage(message);
                    } catch (error) {
                        console.error('Failed to parse WebSocket message:', error);
                    }
                };
                
                this.ws.onerror = (error) => {
                    console.error('WebSocket error:', error);
                    this.updateConnectionStatus('error');
                    // Don't immediately fallback - let onclose handle reconnection
                };
                
                this.ws.onclose = (event) => {
                    console.log('WebSocket closed:', event.code, event.reason);
                    this.isConnected = false;
                    this.stopHeartbeat();
                    this.updateConnectionStatus('disconnected');
                    
                    // Only fallback to polling if max retries exceeded
                    if (this.retryCount < this.maxRetries) {
                        this.retryConnection();
                    } else {
                        this.fallbackToPolling();
                    }
                };
            } catch (error) {
                console.error('WebSocket initialization failed:', error);
                this.fallbackToPolling();
            }
        },
        
        handleWebSocketMessage: function(message) {
            // Handle connection.established
            if (message.type === 'connection.established') {
                this.clientId = message.data.client_id;
                console.log('WebSocket client ID:', this.clientId);
                // Subscribe to all events
                this.subscribeToEvents(['*']);
            }
            // Handle pong (heartbeat response)
            else if (message.type === 'pong') {
                // Heartbeat response received
            }
            // Handle error events
            else if (message.type === 'error.occurred') {
                console.error('WebSocket error event:', message.data);
                this.handleErrorEvent(message.data);
            }
            // Handle all other events
            else {
                this.handleRealTimeUpdate(message);
            }
        },
        
        subscribeToEvents: function(eventTypes) {
            if (!this.ws || this.ws.readyState !== WebSocket.OPEN) {
                console.warn('WebSocket not open, cannot subscribe');
                return;
            }
            
            const subscribeMessage = {
                type: 'subscribe',
                event_types: eventTypes
            };
            
            this.ws.send(JSON.stringify(subscribeMessage));
            console.log('Subscribed to events:', eventTypes);
        },
        
        startHeartbeat: function() {
            if (this.heartbeatTimer) {
                clearInterval(this.heartbeatTimer);
            }
            
            this.heartbeatTimer = setInterval(() => {
                if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                    this.ws.send(JSON.stringify({ type: 'ping' }));
                }
            }, this.heartbeatInterval);
        },
        
        stopHeartbeat: function() {
            if (this.heartbeatTimer) {
                clearInterval(this.heartbeatTimer);
                this.heartbeatTimer = null;
            }
        },
        
        handleErrorEvent: function(errorData) {
            // Display error notification to user
            console.error('Trading error:', errorData);
            // Could add UI notification here
        },

        initPolling: function() {
            // Use polling as primary method (WordPress-compatible)
            this.isConnected = true;
            this.updateConnectionStatus('polling');
        },

        fallbackToPolling: function() {
            console.log('Falling back to polling');
            this.initPolling();
            this.startRealTimeUpdates();
        },

        retryConnection: function() {
            if (this.retryCount < this.maxRetries) {
                this.retryCount++;
                const delay = this.retryDelay * this.retryCount;
                console.log(`Retrying WebSocket connection (attempt ${this.retryCount}/${this.maxRetries}) in ${delay}ms...`);
                setTimeout(() => {
                    this.initWebSocket();
                }, delay);
            } else {
                console.warn('Max WebSocket retries exceeded, falling back to polling');
                this.fallbackToPolling();
            }
        },
        
        disconnectWebSocket: function() {
            this.stopHeartbeat();
            if (this.ws) {
                this.ws.close();
                this.ws = null;
            }
            this.isConnected = false;
            this.clientId = null;
        },

        handleRealTimeUpdate: function(message) {
            // Handle events based on Phase 3 Event Schema
            const eventType = message.type;
            const eventData = message.data || {};
            
            switch (eventType) {
                // Trade events
                case 'trade.executed':
                    this.addNewTrade(eventData);
                    this.updateMetrics({ total_trades: 'increment' });
                    break;
                
                // Order events
                case 'order.placed':
                case 'order.filled':
                case 'order.cancelled':
                    // Refresh trades table to show updated order status
                    this.loadTrades();
                    break;
                
                // Position events
                case 'position.update':
                    // Update positions display if exists
                    this.updatePosition(eventData);
                    break;
                
                // Account events
                case 'account.update':
                    // Update account metrics
                    this.updateAccountMetrics(eventData);
                    break;
                
                // Strategy events
                case 'strategy.loaded':
                case 'strategy.unloaded':
                case 'strategy.signal':
                case 'strategy.paused':
                case 'strategy.resumed':
                    // Refresh strategies and metrics
                    this.loadOverview();
                    break;
                
                // Market data events
                case 'market.data.update':
                    // Update market data charts if needed
                    if (eventData.symbol && this.currentStrategy) {
                        this.loadChartData();
                    }
                    break;
                
                // Engine events
                case 'engine.initialized':
                case 'engine.started':
                case 'engine.stopped':
                    // Update engine status if displayed
                    this.updateEngineStatus(eventData);
                    break;
                
                // Fallback for unknown events
                default:
                    console.log('Unhandled event type:', eventType, eventData);
                    // For unknown events, do a full refresh
                    if (eventType.startsWith('trade.') || eventType.startsWith('strategy.')) {
                        this.loadDashboardData();
                    }
            }
        },
        
        updatePosition: function(positionData) {
            // Update position display if position widget exists
            // This would update a positions table or widget
            console.log('Position update:', positionData);
        },
        
        updateAccountMetrics: function(accountData) {
            // Update account-related metrics
            const metrics = {
                buying_power: accountData.buying_power,
                cash: accountData.cash,
                equity: accountData.equity,
                portfolio_value: accountData.portfolio_value
            };
            this.updateMetrics(metrics);
        },
        
        updateEngineStatus: function(engineData) {
            // Update engine status indicator if displayed
            console.log('Engine status:', engineData);
        },

        initCharts: function() {
            // Performance Chart (Line)
            const perfCanvas = document.getElementById('performanceChart');
            if (perfCanvas) {
                this.charts.performance = new Chart(
                    perfCanvas.getContext('2d'),
                    {
                        type: 'line',
                        data: { labels: [], datasets: [] },
                        options: this.getChartOptions('Performance', 'line')
                    }
                );
            }

            // Trades Chart (Bar)
            const tradesCanvas = document.getElementById('tradesChart');
            if (tradesCanvas) {
                this.charts.trades = new Chart(
                    tradesCanvas.getContext('2d'),
                    {
                        type: 'bar',
                        data: { labels: [], datasets: [] },
                        options: this.getChartOptions('Trades', 'bar')
                    }
                );
            }

            // Win/Loss Chart (Doughnut)
            const winlossCanvas = document.getElementById('winlossChart');
            if (winlossCanvas) {
                this.charts.winloss = new Chart(
                    winlossCanvas.getContext('2d'),
                    {
                        type: 'doughnut',
                        data: { labels: [], datasets: [] },
                        options: this.getChartOptions('Win/Loss', 'doughnut')
                    }
                );
            }

            // Strategy Comparison Chart (Line)
            const strategyCanvas = document.getElementById('strategyChart');
            if (strategyCanvas) {
                this.charts.strategy = new Chart(
                    strategyCanvas.getContext('2d'),
                    {
                        type: 'line',
                        data: { labels: [], datasets: [] },
                        options: this.getChartOptions('Strategy Comparison', 'line')
                    }
                );
            }
        },

        getChartOptions: function(title, type) {
            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: 'var(--color-text-primary)' }
                    },
                    tooltip: {
                        backgroundColor: 'var(--color-bg-elevated)',
                        titleColor: 'var(--color-text-primary)',
                        bodyColor: 'var(--color-text-primary)',
                        borderColor: 'var(--color-border-primary)',
                        borderWidth: 1
                    }
                },
                scales: type !== 'doughnut' ? {
                    x: {
                        ticks: { color: 'var(--color-text-secondary)' },
                        grid: { color: 'var(--color-chart-grid)' }
                    },
                    y: {
                        ticks: { color: 'var(--color-text-secondary)' },
                        grid: { color: 'var(--color-chart-grid)' }
                    }
                } : {}
            };
            return baseOptions;
        },

        loadDashboardData: function() {
            this.loadOverview();
            this.loadTrades();
        },

        loadOverview: function() {
            $.get(this.apiBase + '/dashboard/overview')
                .done((data) => {
                    this.updateMetrics(data.metrics || {});
                    this.updateActiveStrategies(data.active_strategies || []);
                })
                .fail(() => {
                    console.error('Failed to load dashboard overview');
                });
        },

        updateMetrics: function(metrics) {
            // Handle increment/decrement for real-time updates
            const currentMetrics = this.getCurrentMetrics();
            
            const metricMap = {
                total_strategies: metrics.total_strategies !== undefined ? metrics.total_strategies : currentMetrics.total_strategies,
                active_strategies: metrics.active_strategies !== undefined ? metrics.active_strategies : currentMetrics.active_strategies,
                total_trades: metrics.total_trades === 'increment' 
                    ? (currentMetrics.total_trades + 1)
                    : (metrics.total_trades !== undefined ? metrics.total_trades : currentMetrics.total_trades),
                total_pnl: this.formatCurrency(metrics.total_pnl !== undefined ? metrics.total_pnl : currentMetrics.total_pnl),
                win_rate: this.formatPercent(metrics.win_rate !== undefined ? metrics.win_rate : currentMetrics.win_rate),
                avg_return: this.formatPercent(metrics.avg_return !== undefined ? metrics.avg_return : currentMetrics.avg_return),
                sharpe_ratio: (metrics.sharpe_ratio !== undefined ? metrics.sharpe_ratio : currentMetrics.sharpe_ratio).toFixed(2),
                max_drawdown: this.formatPercent(metrics.max_drawdown !== undefined ? metrics.max_drawdown : currentMetrics.max_drawdown),
                profit_factor: (metrics.profit_factor !== undefined ? metrics.profit_factor : currentMetrics.profit_factor).toFixed(2),
                daily_pnl: this.formatCurrency(metrics.daily_pnl !== undefined ? metrics.daily_pnl : currentMetrics.daily_pnl),
                monthly_pnl: this.formatCurrency(metrics.monthly_pnl !== undefined ? metrics.monthly_pnl : currentMetrics.monthly_pnl),
                roi: this.formatPercent(metrics.roi !== undefined ? metrics.roi : currentMetrics.roi)
            };

            Object.keys(metricMap).forEach(key => {
                const element = $(`[data-metric="${key}"]`);
                if (element.length) {
                    const oldValue = element.text();
                    const newValue = metricMap[key];
                    
                    // Only update if value changed
                    if (oldValue !== newValue) {
                        element.addClass('updated');
                        element.text(newValue);
                        
                        // Remove animation class after animation completes
                        setTimeout(() => {
                            element.removeClass('updated');
                        }, 500);
                    }
                }
            });
            
            // Update change indicators for P&L metrics
            if (metrics.daily_pnl !== undefined) {
                this.updateChangeIndicator('daily_pnl_change', metrics.daily_pnl, 0);
            }
            if (metrics.monthly_pnl !== undefined) {
                this.updateChangeIndicator('monthly_pnl_change', metrics.monthly_pnl, 0);
            }
            if (metrics.total_pnl !== undefined) {
                this.updateChangeIndicator('pnl_change', metrics.total_pnl, 0);
            }
        },

        updateChangeIndicator: function(selector, currentValue, previousValue) {
            const element = $(`[data-change="${selector}"]`);
            if (!element.length) return;
            
            const change = currentValue - previousValue;
            if (change > 0) {
                element.text(`+${this.formatCurrency(change)}`).removeClass('negative').addClass('positive');
            } else if (change < 0) {
                element.text(this.formatCurrency(change)).removeClass('positive').addClass('negative');
            } else {
                element.text('No change').removeClass('positive negative');
            }
        },

        updateActiveStrategies: function(strategies) {
            // Update strategy-related metrics and charts
            if (strategies.length > 0) {
                const previousStrategy = this.currentStrategy;
                if (this.currentStrategy === null) {
                    this.currentStrategy = strategies[0].strategy_id;
                }
                
                // Track strategy selection change
                if (previousStrategy !== this.currentStrategy && this.currentStrategy) {
                    this.trackGA4Event('strategy_selected', {
                        strategy_id: this.currentStrategy,
                        previous_strategy: previousStrategy || 'none',
                        total_strategies: strategies.length
                    });
                }
                
                this.loadChartData();
            }
        },

        loadChartData: function() {
            if (!this.currentStrategy) return;

            // Load performance chart
            $.get(this.apiBase + `/charts/performance/${this.currentStrategy}`)
                .done((data) => {
                    if (this.charts.performance && data.data) {
                        this.updateChart(this.charts.performance, data.data, data.options);
                    }
                });

            // Load trades chart
            $.get(this.apiBase + `/charts/trades/${this.currentStrategy}`)
                .done((data) => {
                    if (this.charts.trades && data.data) {
                        this.updateChart(this.charts.trades, data.data, data.options);
                    }
                });
        },

        updateChart: function(chart, data, options) {
            if (!chart) return;
            
            let needsUpdate = false;
            
            if (data.labels && JSON.stringify(chart.data.labels) !== JSON.stringify(data.labels)) {
                chart.data.labels = data.labels;
                needsUpdate = true;
            }
            
            if (data.datasets) {
                chart.data.datasets = data.datasets;
                needsUpdate = true;
            }
            
            if (options) {
                chart.options = { ...chart.options, ...options };
            }
            
            if (needsUpdate) {
                // Add visual feedback
                const canvas = chart.canvas;
                const container = $(canvas).closest('.chart-container');
                container.addClass('updated');
                
                chart.update('active');
                
                setTimeout(() => {
                    container.removeClass('updated');
                }, 500);
            }
        },

        loadTrades: function() {
            $.get(this.apiBase + '/trades', { limit: 50 })
                .done((data) => {
                    this.renderTradesTable(data.trades || []);
                })
                .fail(() => {
                    $('#tradesTableBody').html(
                        '<tr><td colspan="9" class="trades-loading">Failed to load trades</td></tr>'
                    );
                });
        },

        renderTradesTable: function(trades) {
            const tbody = $('#tradesTableBody');
            if (trades.length === 0) {
                tbody.html('<tr><td colspan="9" class="trades-loading">No trades found</td></tr>');
                return;
            }

            const rows = trades.map(trade => `
                <tr>
                    <td>${trade.trade_id || '-'}</td>
                    <td>${trade.strategy_id || '-'}</td>
                    <td>${trade.symbol || '-'}</td>
                    <td>${trade.side || '-'}</td>
                    <td>${trade.quantity || 0}</td>
                    <td>${this.formatCurrency(trade.price || 0)}</td>
                    <td class="trade-pnl ${(trade.pnl || 0) >= 0 ? 'positive' : 'negative'}">
                        ${this.formatCurrency(trade.pnl || 0)}
                    </td>
                    <td>${this.formatDate(trade.execution_time || trade.created_at)}</td>
                    <td>
                        <span class="trade-status ${this.getTradeStatus(trade)}">
                            ${this.getTradeStatusLabel(trade)}
                        </span>
                    </td>
                </tr>
            `).join('');

            tbody.html(rows);
        },

        getTradeStatus: function(trade) {
            if (trade.pnl > 0) return 'win';
            if (trade.pnl < 0) return 'loss';
            return 'pending';
        },

        getTradeStatusLabel: function(trade) {
            if (trade.pnl > 0) return 'Win';
            if (trade.pnl < 0) return 'Loss';
            return 'Pending';
        },

        formatCurrency: function(value) {
            return '$' + parseFloat(value).toFixed(2);
        },

        formatPercent: function(value) {
            return parseFloat(value).toFixed(2) + '%';
        },

        formatDate: function(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString();
        },

        setupEventListeners: function() {
            $('.chart-period-select').on('change', (e) => {
                const chartType = $(e.target).data('chart');
                const period = $(e.target).val();
                
                // Track chart interaction
                this.trackGA4Event('chart_interaction', {
                    chart_type: chartType,
                    period: period,
                    interaction_type: 'period_change'
                });
                
                this.loadChartData();
            });

            $('#tradesSearch').on('input', () => {
                this.filterTrades();
            });

            $('#tradesFilter').on('change', (e) => {
                const filterValue = $(e.target).val();
                
                // Track strategy selection if filtering by strategy
                if (filterValue !== 'all') {
                    this.trackGA4Event('strategy_selected', {
                        strategy_filter: filterValue,
                        filter_type: 'trades_table'
                    });
                }
                
                this.filterTrades();
            });
            
            // Add chart interaction tracking (zoom/pan)
            this.setupChartInteractionTracking();
            
            // Add trade expansion tracking
            this.setupTradeExpansionTracking();
            
            // Add manual refresh button tracking
            this.setupManualRefreshTracking();
        },
        
        setupChartInteractionTracking: function() {
            // Track Chart.js interactions (zoom, pan)
            Object.keys(this.charts).forEach(chartKey => {
                const chart = this.charts[chartKey];
                if (chart && chart.canvas) {
                    chart.canvas.addEventListener('click', () => {
                        this.trackGA4Event('chart_interaction', {
                            chart_type: chartKey,
                            interaction_type: 'click'
                        });
                    });
                }
            });
        },
        
        setupTradeExpansionTracking: function() {
            // Track when trade rows are expanded for details
            $(document).on('click', '#tradesTableBody tr', function() {
                const tradeId = $(this).data('trade-id');
                if (tradeId) {
                    Dashboard.trackGA4Event('trade_expanded', {
                        trade_id: tradeId,
                        strategy: $(this).find('td').eq(1).text()
                    });
                }
            });
        },
        
        setupManualRefreshTracking: function() {
            // Track manual refresh actions (if refresh button exists)
            $(document).on('click', '[data-action="refresh-dashboard"]', () => {
                this.trackGA4Event('metrics_refresh', {
                    refresh_type: 'manual',
                    timestamp: new Date().toISOString()
                });
                this.loadDashboardData();
            });
        },
        
        trackGA4Event: function(eventName, eventParams) {
            // GA4 event tracking (gtag.js)
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, eventParams);
            }
            
            // Fallback: console log for debugging
            if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                console.log('GA4 Event:', eventName, eventParams);
            }
        },

        filterTrades: function() {
            const search = $('#tradesSearch').val().toLowerCase();
            const filter = $('#tradesFilter').val();

            $('#tradesTableBody tr').each(function() {
                const row = $(this);
                const text = row.text().toLowerCase();
                const status = row.find('.trade-status').text().toLowerCase();

                const matchesSearch = !search || text.includes(search);
                const matchesFilter = filter === 'all' || 
                    (filter === 'win' && status === 'win') ||
                    (filter === 'loss' && status === 'loss') ||
                    (filter === 'pending' && status === 'pending');

                row.toggle(matchesSearch && matchesFilter);
            });
        },

        startRealTimeUpdates: function() {
            // If WebSocket is connected, use it for real-time updates
            // Otherwise, use polling as fallback
            if (this.isConnected && this.ws && this.ws.readyState === WebSocket.OPEN) {
                // WebSocket handles real-time updates via events
                // Only start minimal polling as backup
                this.stopRealTimeUpdates();
                
                // Backup polling (less frequent when WebSocket is active)
                this.updateTimers.backup = setInterval(() => {
                    this.loadDashboardData();
                }, this.updateInterval * 4); // Every 20 seconds as backup
            } else {
                // Full polling mode when WebSocket is not available
                this.stopRealTimeUpdates();

                // Metrics updates (most frequent)
                this.updateTimers.metrics = setInterval(() => {
                    this.loadOverview();
                }, this.metricsUpdateInterval);

                // Chart updates (less frequent to reduce load)
                this.updateTimers.charts = setInterval(() => {
                    this.loadChartData();
                }, this.chartsUpdateInterval);

                // Trades updates
                this.updateTimers.trades = setInterval(() => {
                    this.loadTrades();
                }, this.tradesUpdateInterval);

                // Full dashboard refresh (backup)
                this.updateTimers.full = setInterval(() => {
                    this.loadDashboardData();
                }, this.updateInterval * 2); // Every 10 seconds
            }
        },

        stopRealTimeUpdates: function() {
            Object.values(this.updateTimers).forEach(timer => {
                if (timer) clearInterval(timer);
            });
            this.updateTimers = {};
        },

        showConnectionStatus: function() {
            // Add connection status indicator to dashboard
            const statusHtml = '<div class="dashboard-status" id="dashboardStatus" style="position: fixed; top: 20px; right: 20px; z-index: 1000; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 500; transition: all 0.3s;"></div>';
            $('body').append(statusHtml);
            this.updateConnectionStatus(this.isConnected ? 'connected' : 'polling');
        },

        updateConnectionStatus: function(status) {
            const $status = $('#dashboardStatus');
            if (!$status.length) return;

            const statusConfig = {
                connected: {
                    text: '● Live',
                    class: 'status-connected',
                    bg: 'rgba(0, 230, 118, 0.2)',
                    color: '#00e676',
                    border: '1px solid #00e676'
                },
                polling: {
                    text: '⟳ Syncing',
                    class: 'status-polling',
                    bg: 'rgba(0, 229, 255, 0.2)',
                    color: '#00e5ff',
                    border: '1px solid #00e5ff'
                },
                disconnected: {
                    text: '○ Offline',
                    class: 'status-disconnected',
                    bg: 'rgba(255, 82, 82, 0.2)',
                    color: '#ff5252',
                    border: '1px solid #ff5252'
                },
                error: {
                    text: '⚠ Error',
                    class: 'status-error',
                    bg: 'rgba(255, 193, 7, 0.2)',
                    color: '#ffc107',
                    border: '1px solid #ffc107'
                }
            };

            const config = statusConfig[status] || statusConfig.polling;
            $status
                .text(config.text)
                .attr('class', 'dashboard-status ' + config.class)
                .css({
                    'background-color': config.bg,
                    'color': config.color,
                    'border': config.border
                });
        },

        loadTrades: function() {
            $.get(this.apiBase + '/trades', { limit: 50 })
                .done((data) => {
                    this.renderTradesTable(data.trades || []);
                })
                .fail(() => {
                    // Silent fail for polling updates
                });
        },

        updateChartData: function(chartType, chartData) {
            if (this.charts[chartType] && chartData) {
                this.updateChart(this.charts[chartType], chartData.data || chartData, chartData.options);
            }
        },

        getCurrentMetrics: function() {
            // Get current metric values from DOM
            return {
                total_strategies: parseInt($('[data-metric="total_strategies"]').text()) || 0,
                active_strategies: parseInt($('[data-metric="active_strategies"]').text()) || 0,
                total_trades: parseInt($('[data-metric="total_trades"]').text()) || 0,
                total_pnl: parseFloat($('[data-metric="total_pnl"]').text().replace(/[^0-9.-]/g, '')) || 0,
                win_rate: parseFloat($('[data-metric="win_rate"]').text().replace(/[^0-9.-]/g, '')) || 0,
                avg_return: parseFloat($('[data-metric="avg_return"]').text().replace(/[^0-9.-]/g, '')) || 0,
                sharpe_ratio: parseFloat($('[data-metric="sharpe_ratio"]').text()) || 0,
                max_drawdown: parseFloat($('[data-metric="max_drawdown"]').text().replace(/[^0-9.-]/g, '')) || 0,
                profit_factor: parseFloat($('[data-metric="profit_factor"]').text()) || 0,
                daily_pnl: parseFloat($('[data-metric="daily_pnl"]').text().replace(/[^0-9.-]/g, '')) || 0,
                monthly_pnl: parseFloat($('[data-metric="monthly_pnl"]').text().replace(/[^0-9.-]/g, '')) || 0,
                roi: parseFloat($('[data-metric="roi"]').text().replace(/[^0-9.-]/g, '')) || 0
            };
        },
        
        addNewTrade: function(trade) {
            // Add new trade to top of table (from trade.executed event)
            const tbody = $('#tradesTableBody');
            const existingRows = tbody.find('tr').length;
            const tradeId = trade.trade_id || trade.id;
            
            // Check if trade already exists
            if (tradeId && tbody.find(`tr[data-trade-id="${tradeId}"]`).length > 0) {
                return; // Trade already displayed
            }
            
            // Add new trade row
            const newRow = $(`
                <tr data-trade-id="${tradeId || 'new-' + Date.now()}" style="animation: slideIn 0.3s ease-out;">
                    <td>${tradeId || '-'}</td>
                    <td>${trade.strategy_id || '-'}</td>
                    <td>${trade.symbol || '-'}</td>
                    <td>${trade.side || '-'}</td>
                    <td>${trade.quantity || 0}</td>
                    <td>${this.formatCurrency(trade.price || 0)}</td>
                    <td class="trade-pnl ${(trade.pnl || 0) >= 0 ? 'positive' : 'negative'}">
                        ${this.formatCurrency(trade.pnl || 0)}
                    </td>
                    <td>${this.formatDate(trade.timestamp || trade.execution_time || trade.created_at)}</td>
                    <td>
                        <span class="trade-status ${this.getTradeStatus(trade)}">
                            ${this.getTradeStatusLabel(trade)}
                        </span>
                    </td>
                </tr>
            `);
            tbody.prepend(newRow);
            
            // Limit to 50 rows
            if (existingRows >= 50) {
                tbody.find('tr').slice(50).remove();
            }
            
            // Track trade execution event
            this.trackGA4Event('trade_executed', {
                trade_id: tradeId,
                symbol: trade.symbol,
                strategy_id: trade.strategy_id,
                pnl: trade.pnl || 0
            });
        }
    };

    $(document).ready(function() {
        Dashboard.init();
    });

})(jQuery);

