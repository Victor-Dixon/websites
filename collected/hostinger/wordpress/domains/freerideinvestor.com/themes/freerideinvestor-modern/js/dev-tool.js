/**
 * FreeRideInvestor Developer Tool - Single Unified Tool
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Run complete analysis
        $('#fri-run-all').on('click', function() {
            const btn = $(this);
            const resultsPanel = $('#fri-results');
            const resultsContent = $('#fri-results-content');
            
            btn.prop('disabled', true).text('🔄 Running Analysis...');
            resultsPanel.show();
            resultsContent.html('<div class="fri-loading">Running complete developer analysis...</div>');
            
            $.ajax({
                url: friDevTool.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fri_run_complete_analysis',
                    nonce: friDevTool.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayResults(response.data);
                    } else {
                        resultsContent.html('<div class="fri-error">❌ Error: ' + (response.data || 'Unknown error') + '</div>');
                    }
                    btn.prop('disabled', false).text('🚀 Run Complete Analysis');
                },
                error: function() {
                    resultsContent.html('<div class="fri-error">❌ Failed to run analysis</div>');
                    btn.prop('disabled', false).text('🚀 Run Complete Analysis');
                }
            });
        });
        
        // Fintech form
        $('#fri-fintech-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const symbol = form.find('input[name="symbol"]').val();
            const interval = form.find('select[name="interval"]').val();
            const output_size = form.find('input[name="output_size"]').val();
            
            form.find('button').prop('disabled', true).text('Generating...');
            
            $.ajax({
                url: friDevTool.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'fri_generate_data',
                    nonce: friDevTool.nonce,
                    symbol: symbol,
                    interval: interval,
                    output_size: output_size
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ ' + response.data.message);
                    } else {
                        alert('❌ ' + (response.data || 'Failed to generate data'));
                    }
                    form.find('button').prop('disabled', false).text('Generate');
                },
                error: function() {
                    alert('❌ Error generating data');
                    form.find('button').prop('disabled', false).text('Generate');
                }
            });
        });
        
        /**
         * Display comprehensive results
         */
        function displayResults(data) {
            const summary = data.summary;
            const results = data.results;
            
            let html = '<div class="fri-summary">';
            html += '<h3>📊 Summary</h3>';
            html += '<ul>';
            html += '<li><strong>Plugins:</strong> ' + summary.health.total_plugins + ' total, ' + summary.health.healthy + ' healthy</li>';
            html += '<li><strong>Security:</strong> ' + summary.security.critical_issues + ' critical issues, ' + summary.security.warnings + ' warnings</li>';
            html += '<li><strong>Performance:</strong> ' + summary.performance.transients_cleared + ' transients cleared</li>';
            html += '<li><strong>Cache:</strong> ' + summary.cache.pages_warmed + ' pages warmed (avg ' + summary.cache.avg_time_ms + 'ms)</li>';
            html += '<li><strong>Cleanup:</strong> ' + summary.cleanup.debug_logs_deleted + ' debug logs deleted</li>';
            html += '</ul>';
            html += '</div>';
            
            // Health Check Section
            html += '<div class="fri-section">';
            html += '<h3>🔍 Plugin Health Check</h3>';
            html += '<p><span class="fri-success">✅ ' + summary.health.healthy + ' healthy</span> | ';
            html += '<span class="fri-warning">⚠️ ' + summary.health.warnings + ' warnings</span> | ';
            html += '<span class="fri-error">❌ ' + summary.health.critical + ' critical</span></p>';
            html += '<pre>' + JSON.stringify(results.health_check.plugins.slice(0, 5), null, 2) + '</pre>';
            html += '</div>';
            
            // Security Section
            html += '<div class="fri-section">';
            html += '<h3>🔒 Security Analysis</h3>';
            if (summary.security.critical_issues > 0) {
                html += '<p class="fri-error">❌ ' + summary.security.critical_issues + ' plugins with critical security issues</p>';
            } else {
                html += '<p class="fri-success">✅ No critical security issues found</p>';
            }
            if (summary.security.warnings > 0) {
                html += '<p class="fri-warning">⚠️ ' + summary.security.warnings + ' security warnings</p>';
            }
            html += '</div>';
            
            // Performance Section
            html += '<div class="fri-section">';
            html += '<h3>⚡ Performance Optimization</h3>';
            html += '<p class="fri-success">✅ ' + summary.performance.transients_cleared + ' transients cleared</p>';
            html += '<p class="fri-success">✅ Cache flushed</p>';
            html += '</div>';
            
            // Cache Section
            html += '<div class="fri-section">';
            html += '<h3>🌐 Cache Warming</h3>';
            html += '<pre>' + JSON.stringify(results.cache_warming, null, 2) + '</pre>';
            html += '</div>';
            
            // Cleanup Section
            html += '<div class="fri-section">';
            html += '<h3>🧹 Cleanup</h3>';
            html += '<p class="fri-success">✅ ' + summary.cleanup.debug_logs_deleted + ' debug log(s) deleted</p>';
            html += '</div>';
            
            $('#fri-results-content').html(html);
        }
    });
    
})(jQuery);

