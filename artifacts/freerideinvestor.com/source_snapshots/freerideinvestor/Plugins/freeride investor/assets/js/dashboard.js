// Self-invoking function to avoid polluting the global namespace
(function ($) {
    'use strict';

    // DOM Elements
    const $form = $('#stock-research-form');
    const $input = $form.find('input');
    const $button = $form.find('button');
    const $stocksContainer = $('#stocks-container');
    const $spinner = $('<div class="spinner"></div>');

    /**
     * Initialize the Dashboard
     */
    function init() {
        // Attach event listeners
        $form.on('submit', handleFormSubmit);
    }

    /**
     * Handle Form Submission
     * @param {Event} e
     */
    function handleFormSubmit(e) {
        e.preventDefault();

        // Clear previous results
        $stocksContainer.empty();

        // Basic validation
        const symbols = $input.val().trim();
        if (!symbols) {
            displayError('Please enter at least one stock symbol.');
            return;
        }

        // Show spinner
        $stocksContainer.append($spinner);

        // Prepare AJAX request
        $.ajax({
            url: freerideAjax.ajax_url, // Provided by wp_localize_script
            method: 'POST',
            data: {
                action: 'fri_fetch_stock_data',
                security: freerideAjax.nonce,
                stock_symbols: symbols
            },
            success: handleSuccess,
            error: handleError
        });
    }

    /**
     * Handle AJAX Success
     * @param {Object} response
     */
    function handleSuccess(response) {
        $spinner.remove();

        if (!response.success) {
            displayError(response.data || 'Unexpected error occurred.');
            return;
        }

        const stocks = response.data;
        for (const symbol in stocks) {
            if (stocks.hasOwnProperty(symbol)) {
                renderStockData(symbol, stocks[symbol]);
            }
        }
    }

    /**
     * Handle AJAX Error
     * @param {Object} jqXHR
     * @param {String} textStatus
     * @param {String} errorThrown
     */
    function handleError(jqXHR, textStatus, errorThrown) {
        $spinner.remove();
        displayError(`Error: ${textStatus}. ${errorThrown}`);
    }

    /**
     * Render Stock Data
     * @param {String} symbol
     * @param {Object} data
     */
    function renderStockData(symbol, data) {
        const $stockSection = $(`
            <div class="stock-section">
                <h2>${symbol}</h2>
                <p><strong>Price:</strong> ${data.stock_data['05. price'] || 'N/A'}</p>
                <p><strong>Change Percent:</strong> ${data.stock_data['10. change percent'] || 'N/A'}</p>
                <p><strong>Sentiment Score:</strong> ${data.sentiment || 'N/A'}</p>
                <div class="news">
                    <h3>Recent News</h3>
                    <ul></ul>
                </div>
                <div class="historical-chart">
                    <canvas id="chart-${symbol}"></canvas>
                </div>
                <div class="trade-plan">
                    <h3>AI Trade Plan</h3>
                    <pre>${data.plan || 'N/A'}</pre>
                </div>
            </div>
        `);

        // Add news items
        const $newsList = $stockSection.find('.news ul');
        if (data.news && data.news.length > 0) {
            data.news.forEach(newsItem => {
                $newsList.append(`<li><a href="${newsItem.url}" target="_blank">${newsItem.headline}</a></li>`);
            });
        } else {
            $newsList.append('<li>No recent news available.</li>');
        }

        // Append the stock section to the container
        $stocksContainer.append($stockSection);

        // Render the chart
        renderChart(`chart-${symbol}`, data.historical_data || []);
    }

    /**
     * Render Historical Data Chart
     * @param {String} canvasId
     * @param {Array} data
     */
    function renderChart(canvasId, data) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        const labels = data.map(entry => entry.date);
        const prices = data.map(entry => entry.close);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Closing Price',
                    data: prices,
                    fill: false,
                    borderColor: 'rgba(34, 136, 34, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { display: true, title: { display: true, text: 'Date' } },
                    y: { display: true, title: { display: true, text: 'Price (USD)' } }
                }
            }
        });
    }

    /**
     * Display Error Message
     * @param {String} message
     */
    function displayError(message) {
        $stocksContainer.append(`<div class="notice notice-error">${message}</div>`);
    }

    // Initialize the dashboard script
    $(document).ready(init);

})(jQuery);
