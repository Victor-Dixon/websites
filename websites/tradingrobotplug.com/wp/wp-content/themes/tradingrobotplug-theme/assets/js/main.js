/*
C:\TheTradingRobotPlugWeb\my-custom-theme\assets\js\main.js
Description: This JavaScript file handles front-end functionalities for The Trading Robot Plug Plugin, including smooth scrolling for anchor links and fetching/rendering stock data charts from various APIs using Chart.js.
Version: 1.0.0
Author: Victor Dixon
*/

/**
 * Main JavaScript file for The Trading Robot Plug Plugin
 */
(function($) {
    $(document).ready(function() {
        // Smooth scrolling for anchor links
        $('a[href*="#"]').on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: $($(this).attr('href')).offset().top }, 500);
        });

        // Mobile menu toggle
        $('.mobile-menu-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('.main-navigation').toggleClass('active');
        });

        // Close mobile menu when clicking on a link
        $('.nav-menu a').on('click', function() {
            $('.mobile-menu-toggle').removeClass('active');
            $('.main-navigation').removeClass('active');
        });

        // Fetch and render charts for Alpha Vantage, Polygon, Real-Time data, and Query Stock Data
        fetchAndRenderChart('/wp-json/tradingrobotplug/v1/fetchdata', 'myChart', 'Alpha Vantage Stock Prices');
        fetchAndRenderChart('/wp-json/tradingrobotplug/v1/fetchpolygondata', 'polygonChart', 'Polygon Stock Prices');
        fetchAndRenderChart('/wp-json/tradingrobotplug/v1/fetchrealtime', 'realtimeChart', 'Real-Time Stock Prices');
        fetchAndRenderChart('/wp-json/tradingrobotplug/v1/querystockdata?symbol=AAPL&start_date=2023-01-01&end_date=2024-01-01', 'queryChart', 'AAPL Stock Prices');
    });

    /**
     * Fetch data from the API and render the chart.
     * @param {string} apiUrl - The API endpoint to fetch data from.
     * @param {string} chartElementId - The ID of the HTML canvas element to render the chart.
     * @param {string} chartLabel - The label for the chart.
     */
    function fetchAndRenderChart(apiUrl, chartElementId, chartLabel) {
        $.ajax({
            url: apiUrl,
            method: 'GET',
            success: function(data) {
                if (data) {
                    if (data.dates && data.prices) {
                        // If data contains 'dates' and 'prices', it's from the original fetch APIs
                        renderChart(chartElementId, chartLabel, data.dates, data.prices);
                    } else if (Array.isArray(data) && data.length && data[0].date && data[0].close) {
                        // If data is an array with 'date' and 'close', it's from the query API
                        renderChart(chartElementId, chartLabel, data.map(item => item.date), data.map(item => item.close));
                    } else {
                        console.error("API returned unexpected data format.");
                    }
                } else {
                    console.error("No data returned from API.");
                }
            },
            error: function(xhr, status, error) {
                console.error("API Request Failed: " + error);
                alert("Failed to load chart data. Please try again later.");
            }
        });
    }

    /**
     * Render a chart using Chart.js.
     * @param {string} elementId - The ID of the canvas element where the chart will be rendered.
     * @param {string} label - The label for the chart dataset.
     * @param {Array} dates - The array of dates for the x-axis.
     * @param {Array} prices - The array of prices for the y-axis.
     */
    function renderChart(elementId, label, dates, prices) {
        var ctx = document.getElementById(elementId).getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: label,
                    data: prices,
                    borderColor: '#1e73be',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    xAxes: [{
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'll'
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                }
            }
        });
    }
})(jQuery);
