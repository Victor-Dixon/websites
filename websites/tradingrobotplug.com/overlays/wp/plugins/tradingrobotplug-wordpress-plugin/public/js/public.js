// Public JS placeholder
console.log('Trading Robot Plug Public Scripts Loaded');

// #region agent log
(function() {
    const logData = {
        location: 'public.js:4',
        message: 'Public script loaded',
        data: { timestamp: Date.now(), userAgent: navigator.userAgent },
        timestamp: Date.now(),
        sessionId: 'debug-session',
        runId: 'run1',
        hypothesisId: 'E'
    };
    fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(logData)
    }).catch(() => {});
})();
// #endregion

// Chart loading functionality
(function() {
    // #region agent log
    (function() {
        const logData = {
            location: 'public.js:chart-init',
            message: 'Chart initialization started',
            data: { chartPlaceholderExists: !!document.querySelector('.trp-chart-placeholder') },
            timestamp: Date.now(),
            sessionId: 'debug-session',
            runId: 'run1',
            hypothesisId: 'B'
        };
        fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logData)
        }).catch(() => {});
    })();
    // #endregion

    const chartPlaceholder = document.querySelector('.trp-chart-placeholder');
    if (!chartPlaceholder) {
        // #region agent log
        (function() {
            const logData = {
                location: 'public.js:chart-check',
                message: 'Chart placeholder not found',
                data: {},
                timestamp: Date.now(),
                sessionId: 'debug-session',
                runId: 'run1',
                hypothesisId: 'B'
            };
            fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(logData)
            }).catch(() => {});
        })();
        // #endregion
        return;
    }

    // Check if Chart.js is loaded
    // #region agent log
    (function() {
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
        fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logData)
        }).catch(() => {});
    })();
    // #endregion

    // Try to load chart data
    // #region agent log
    console.log('[DEBUG] Chart loading started');
    // #endregion
    
    // Get REST API URL from localized script or fallback
    let restUrl;
    if (window.tradingRobotPlug && window.tradingRobotPlug.restUrl) {
        restUrl = window.tradingRobotPlug.restUrl;
        console.log('[DEBUG] Using localized REST URL:', restUrl);
    } else if (window.wpApiSettings && window.wpApiSettings.root) {
        // Remove /wp/v2/ from the root URL and add our namespace
        restUrl = window.wpApiSettings.root.replace(/\/wp\/v2\/?$/, '') + 'tradingrobotplug/v1/';
        console.log('[DEBUG] Using wpApiSettings REST URL:', restUrl);
    } else {
        restUrl = '/wp-json/tradingrobotplug/v1/';
        console.log('[DEBUG] Using fallback REST URL:', restUrl);
    }
    
    // Ensure restUrl ends with /
    if (!restUrl.endsWith('/')) {
        restUrl += '/';
    }
    
    const chartDataUrl = restUrl + 'chart-data';
    console.log('[DEBUG] Final chart data URL:', chartDataUrl);
    
    // #region agent log
    console.log('[DEBUG] REST URL:', restUrl);
    console.log('[DEBUG] Chart data URL:', chartDataUrl);
    console.log('[DEBUG] wpApiSettings exists:', !!window.wpApiSettings);
    // #endregion
    
    // #region agent log
    (function() {
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
        fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(logData)
        }).catch(() => {});
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
            (function() {
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
                fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(logData)
                }).catch(() => {});
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
            (function() {
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
                fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(logData)
                }).catch(() => {});
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
            (function() {
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
                fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(logData)
                }).catch(() => {});
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
        (function() {
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
            fetch('http://127.0.0.1:7245/ingest/e9bfe30e-6503-4d21-94d5-4e3bf96c6b89', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(logData)
            }).catch(() => {});
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
                                callback: function(value) {
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
