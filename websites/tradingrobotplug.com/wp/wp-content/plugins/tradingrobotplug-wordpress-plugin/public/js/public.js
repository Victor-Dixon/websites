// Public JS placeholder
console.log('Trading Robot Plug Public Scripts Loaded');

// #region API Configuration
// Load centralized API configuration utilities
// Note: This file should be loaded before this script via WordPress enqueue
// #endregion

// #region agent log
(function () {
    const logData = {
        location: 'public.js:4',
        message: 'Public script loaded',
        data: { timestamp: Date.now(), userAgent: navigator.userAgent },
        timestamp: Date.now(),
        sessionId: 'debug-session',
        runId: 'run1',
        hypothesisId: 'E'
    };
    fetch(getIngestionApiUrl(), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(logData)
    }).catch(() => { });
})();
// #endregion

// Chart loading functionality
(function () {
    // #region agent log
    (function () {
        const logData = {
            location: 'public.js:chart-init',
            message: 'Chart initialization started',
            data: { chartPlaceholderExists: !!document.querySelector('.trp-chart-placeholder') },
            timestamp: Date.now(),
            sessionId: 'debug-session',
            runId: 'run1',
            hypothesisId: 'B'
        };
        fetch(getIngestionApiUrl(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logData)
        }).catch(() => { });
    })();
    // #endregion

    // Initialize trading chart
    const chartContainer = document.querySelector('.trp-chart-container');
    if (chartContainer) {
        initializeTradingChart(chartContainer);
    }
    }

    // Check if Chart.js is loaded
    // #region agent log
    (function () {
        const logData = {
            location: 'public.js:chart-library-check',
            message: 'Checking for chart library',
            data: {
                chartJsExists: typeof Chart !== 'undefined',
                windowChart: typeof window.Chart !== 'undefined'
            },
            timestamp: Date.now(),
            sessionId: 'debug-session',
            runId: 'run1',
            hypothesisId: 'B'
        };
        fetch(getIngestionApiUrl(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logData)
        }).catch(() => { });
    })();
    // #endregion

    // Try to load chart data
    // Get REST API URL from localized script or fallback
    let restUrl;
    if (window.tradingRobotPlug && window.tradingRobotPlug.restUrl) {
        restUrl = window.tradingRobotPlug.restUrl;
    } else if (window.wpApiSettings && window.wpApiSettings.root) {
        // Remove /wp/v2/ from the root URL and add our namespace
        restUrl = window.wpApiSettings.root.replace(/\/wp\/v2\/?$/, '') + 'tradingrobotplug/v1/';
    } else {
        restUrl = '/wp-json/tradingrobotplug/v1/';
    }

    // Ensure restUrl ends with /
    if (!restUrl.endsWith('/')) {
        restUrl += '/';
    }

    const chartDataUrl = restUrl + 'chart-data';

    // #region agent log
    (function () {
        const logData = {
            location: 'public.js:before-fetch',
            message: 'Before fetching chart data',
            data: {
                restUrl: restUrl,
                chartDataUrl: chartDataUrl,
                wpApiSettingsExists: !!window.wpApiSettings
            },
            timestamp: Date.now(),
            sessionId: 'debug-session',
            runId: 'run1',
            hypothesisId: 'A'
        };
        fetch(getIngestionApiUrl(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logData)
        }).catch(() => { });
    })();
    // #endregion

    // Test endpoint first
    console.log('[DEBUG] Testing endpoint accessibility:', chartDataUrl);

    fetch(chartDataUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        },
        credentials: 'same-origin',
        cache: 'no-cache'
    })
        .then(response => {
            // #region agent log
            console.log('[DEBUG] Fetch response:', response.status, response.statusText);
            (function () {
                const logData = {
                    location: 'public.js:fetch-response',
                    message: 'Fetch response received',
                    data: {
                        status: response.status,
                        statusText: response.statusText,
                        ok: response.ok,
                        url: response.url
                    },
                    timestamp: Date.now(),
                    sessionId: 'debug-session',
                    runId: 'run1',
                    hypothesisId: 'A'
                };
                fetch(getIngestionApiUrl(), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(logData)
                }).catch(() => { });
            })();
            // #endregion

            if (!response.ok) {
                // Try to get error message from response
                return response.text().then(text => {
                    console.error('[DEBUG] API Error Response:', text);
                    throw new Error(`HTTP ${response.status}: ${response.statusText}. Response: ${text.substring(0, 100)}`);
                });
            }
            return response.json();
        })
        .then(data => {
            // #region agent log
            (function () {
                const logData = {
                    location: 'public.js:fetch-success',
                    message: 'Chart data received',
                    data: {
                        hasData: !!data,
                        dataKeys: data ? Object.keys(data) : [],
                        dataLength: data && data.length ? data.length : 0
                    },
                    timestamp: Date.now(),
                    sessionId: 'debug-session',
                    runId: 'run1',
                    hypothesisId: 'A'
                };
                fetch(getIngestionApiUrl(), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(logData)
                }).catch(() => { });
            })();
            // #endregion

            // Render chart if Chart.js is available
            if (typeof Chart !== 'undefined' && data) {
                renderChart(data);
            } else {
                chartPlaceholder.innerHTML = 'Chart Visualization Loading...';
            }
        })
        .catch(error => {
            // #region agent log
            console.error('[DEBUG] Chart data fetch error:', error);
            console.error('[DEBUG] Error message:', error.message);
            console.error('[DEBUG] Error stack:', error.stack);
            console.error('[DEBUG] Chart data URL attempted:', chartDataUrl);
            (function () {
                const logData = {
                    location: 'public.js:fetch-error',
                    message: 'Chart data fetch failed',
                    data: {
                        errorMessage: error.message,
                        errorStack: error.stack,
                        errorName: error.name,
                        chartDataUrl: chartDataUrl
                    },
                    timestamp: Date.now(),
                    sessionId: 'debug-session',
                    runId: 'run1',
                    hypothesisId: 'A'
                };
                fetch(getIngestionApiUrl(), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(logData)
                }).catch(() => { });
            })();
            // #endregion

            // Show detailed error message and try fallback
            const errorMsg = error.message || 'Unknown error';
            console.warn('[DEBUG] Attempting fallback with mock data');

            // Generate fallback mock data
            const fallbackData = generateMockChartData();
            if (typeof Chart !== 'undefined' && fallbackData) {
                console.log('[DEBUG] Rendering chart with fallback data');
                renderChart(fallbackData);
            } else {
                chartPlaceholder.innerHTML = 'FAILED TO LOAD CHART DATA<br><small style="font-size: 12px; color: #999;">' +
                    errorMsg.substring(0, 100) + '<br>Endpoint: ' + chartDataUrl + '</small>';
                chartPlaceholder.style.color = '#dc3545';
                chartPlaceholder.style.textAlign = 'center';
            }
        });

    function generateMockChartData() {
        // Generate 30 days of mock performance data
        const labels = [];
        const data = [];
        let cumulative = 0;

        for (let i = 29; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));

            const dailyPnl = 100 + Math.random() * 50 - 25; // Random between 75-125
            cumulative += dailyPnl;
            data.push(Math.round(cumulative * 100) / 100);
        }

        return {
            labels: labels,
            datasets: [{
                label: 'Cumulative P&L',
                data: data,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        };
    }

    function renderChart(data) {
        // #region agent log
        console.log('[DEBUG] Rendering chart with data:', data);
        (function () {
            const logData = {
                location: 'public.js:render-chart',
                message: 'Rendering chart',
                data: {
                    chartType: typeof Chart,
                    dataProvided: !!data,
                    hasLabels: data && data.labels ? data.labels.length : 0,
                    hasDatasets: data && data.datasets ? data.datasets.length : 0
                },
                timestamp: Date.now(),
                sessionId: 'debug-session',
                runId: 'run1',
                hypothesisId: 'B'
            };
            fetch(getIngestionApiUrl(), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(logData)
            }).catch(() => { });
        })();
        // #endregion

        if (typeof Chart === 'undefined') {
            console.error('[DEBUG] Chart.js not loaded');
            chartPlaceholder.innerHTML = 'Chart library not available';
            return;
        }

        if (!data || !data.labels || !data.datasets) {
            console.error('[DEBUG] Invalid chart data:', data);
            chartPlaceholder.innerHTML = 'Invalid chart data';
            return;
        }

        // Create canvas element
        const canvas = document.createElement('canvas');
        chartPlaceholder.innerHTML = '';
        chartPlaceholder.appendChild(canvas);
        chartPlaceholder.style.background = 'transparent';
        chartPlaceholder.style.height = 'auto';

        // Render chart
        try {
            new Chart(canvas, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Performance History'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function (value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
            console.log('[DEBUG] Chart rendered successfully');
        } catch (error) {
            console.error('[DEBUG] Chart rendering error:', error);
            chartPlaceholder.innerHTML = 'Error rendering chart: ' + error.message;
        }
    }
})();

/**
 * Trading Chart Implementation
 * Uses Canvas API for lightweight charting without external dependencies
 */
function initializeTradingChart(container) {
    if (!container) return;

    // Create canvas element
    const canvas = document.createElement('canvas');
    canvas.width = container.clientWidth || 800;
    canvas.height = container.clientHeight || 400;
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    container.innerHTML = '';
    container.appendChild(canvas);

    const ctx = canvas.getContext('2d');

    // Sample trading data (replace with real API data)
    const sampleData = generateSampleTradingData();

    // Chart configuration
    const config = {
        data: sampleData,
        colors: {
            background: '#1a1a1a',
            grid: '#333',
            bullish: '#10b981',
            bearish: '#ef4444',
            volume: 'rgba(59, 130, 246, 0.3)'
        }
    };

    // Render chart
    renderTradingChart(ctx, config);

    // Auto-refresh every 30 seconds
    setInterval(() => {
        const newData = generateSampleTradingData();
        config.data = newData;
        renderTradingChart(ctx, config);
    }, 30000);
}

function generateSampleTradingData() {
    const data = [];
    const basePrice = 100;
    let currentPrice = basePrice;

    for (let i = 0; i < 100; i++) {
        const change = (Math.random() - 0.5) * 4; // Random change between -2 and +2
        currentPrice += change;
        currentPrice = Math.max(50, Math.min(150, currentPrice)); // Keep within bounds

        data.push({
            time: new Date(Date.now() - (99 - i) * 60000), // 1 minute intervals
            price: currentPrice,
            volume: Math.floor(Math.random() * 1000) + 100
        });
    }

    return data;
}

function renderTradingChart(ctx, config) {
    const { data, colors } = config;
    const { width, height } = ctx.canvas;

    // Clear canvas
    ctx.fillStyle = colors.background;
    ctx.fillRect(0, 0, width, height);

    if (!data || data.length === 0) return;

    // Calculate price range
    const prices = data.map(d => d.price);
    const minPrice = Math.min(...prices);
    const maxPrice = Math.max(...prices);
    const priceRange = maxPrice - minPrice;
    const padding = priceRange * 0.1;

    // Chart dimensions
    const chartHeight = height * 0.7;
    const volumeHeight = height * 0.2;
    const chartTop = 20;
    const volumeTop = chartTop + chartHeight + 10;

    // Draw grid
    ctx.strokeStyle = colors.grid;
    ctx.lineWidth = 0.5;

    // Horizontal grid lines
    for (let i = 0; i <= 5; i++) {
        const y = chartTop + (chartHeight * i) / 5;
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(width, y);
        ctx.stroke();

        // Price labels
        const price = maxPrice - (priceRange * i) / 5;
        ctx.fillStyle = '#999';
        ctx.font = '12px monospace';
        ctx.fillText(price.toFixed(2), 5, y - 5);
    }

    // Draw price line
    ctx.strokeStyle = colors.bullish;
    ctx.lineWidth = 2;
    ctx.beginPath();

    data.forEach((point, index) => {
        const x = (index / (data.length - 1)) * width;
        const y = chartTop + chartHeight - ((point.price - minPrice) / priceRange) * chartHeight;

        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });

    ctx.stroke();

    // Draw volume bars
    const maxVolume = Math.max(...data.map(d => d.volume));

    data.forEach((point, index) => {
        const x = (index / (data.length - 1)) * width;
        const barWidth = width / data.length;
        const barHeight = (point.volume / maxVolume) * volumeHeight;

        ctx.fillStyle = colors.volume;
        ctx.fillRect(x - barWidth/2, volumeTop + volumeHeight - barHeight, barWidth, barHeight);
    });

    // Draw current price indicator
    const currentPrice = data[data.length - 1].price;
    const currentY = chartTop + chartHeight - ((currentPrice - minPrice) / priceRange) * chartHeight;

    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 1;
    ctx.setLineDash([5, 5]);
    ctx.beginPath();
    ctx.moveTo(0, currentY);
    ctx.lineTo(width, currentY);
    ctx.stroke();
    ctx.setLineDash([]);

    // Current price label
    ctx.fillStyle = '#fff';
    ctx.font = 'bold 14px monospace';
    ctx.fillText(`$${currentPrice.toFixed(2)}`, width - 80, currentY - 10);
}

// Export for potential external use
if (typeof window !== 'undefined') {
    window.initializeTradingChart = initializeTradingChart;
}
