/**
 * Unified Developer Tools JavaScript
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Handle tool button clicks
        $('.fri-run-tool').on('click', function() {
            const tool = $(this).data('tool');
            const resultsDiv = $('#' + tool + '-results');
            
            resultsDiv.show().addClass('show').html('<div class="fri-loading">Running...</div>');
            
            $.ajax({
                url: friDevTools.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fri_' + tool.replace('-', '_'),
                    nonce: friDevTools.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayResults(resultsDiv, response.data, 'success');
                    } else {
                        displayResults(resultsDiv, response.data, 'error');
                    }
                },
                error: function() {
                    resultsDiv.html('<div class="fri-error">Error running tool</div>');
                }
            });
        });
        
        // Handle Fintech form submission
        $('#fintech-form').on('submit', function(e) {
            e.preventDefault();
            const resultsDiv = $('#fintech-results');
            const formData = $(this).serialize();
            
            resultsDiv.show().addClass('show').html('<div class="fri-loading">Generating...</div>');
            
            $.ajax({
                url: friDevTools.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fri_generate_historical_data',
                    nonce: friDevTools.nonce,
                    symbol: $('input[name="symbol"]').val(),
                    interval: $('select[name="interval"]').val(),
                    output_size: $('input[name="output_size"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        displayResults(resultsDiv, response.data, 'success');
                    } else {
                        displayResults(resultsDiv, response.data, 'error');
                    }
                },
                error: function() {
                    resultsDiv.html('<div class="fri-error">Error generating data</div>');
                }
            });
        });
        
        /**
         * Display results
         */
        function displayResults(container, data, type) {
            let html = '';
            
            if (type === 'success') {
                html += '<div class="fri-success">✅ Success!</div>';
                
                if (data.output) {
                    html += '<pre>' + escapeHtml(data.output) + '</pre>';
                }
                
                if (data.results) {
                    html += '<pre>' + JSON.stringify(data.results, null, 2) + '</pre>';
                }
                
                if (data.message) {
                    html += '<div class="fri-success">' + escapeHtml(data.message) + '</div>';
                }
            } else {
                html += '<div class="fri-error">❌ Error</div>';
                if (data) {
                    html += '<pre>' + escapeHtml(JSON.stringify(data, null, 2)) + '</pre>';
                }
            }
            
            container.html(html);
        }
        
        /**
         * Escape HTML
         */
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    });
    
})(jQuery);

