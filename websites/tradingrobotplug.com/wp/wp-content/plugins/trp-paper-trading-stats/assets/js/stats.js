/**
 * TRP Paper Trading Stats - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize all stats widgets
        $('.trp-trading-stats').each(function() {
            const $widget = $(this);
            const mode = $widget.data('mode') || 'full';
            const refreshInterval = parseInt($widget.data('refresh')) || 60;
            
            // Load stats immediately
            loadStats($widget, mode);
            
            // Auto-refresh if interval is set
            if (refreshInterval > 0) {
                setInterval(function() {
                    loadStats($widget, mode);
                }, refreshInterval * 1000);
            }
        });
    });
    
    /**
     * Load trading stats from REST API
     */
    function loadStats($widget, mode) {
        const $loading = $widget.find('.trp-stats-loading');
        const $content = $widget.find('.trp-stats-content');
        const $error = $widget.find('.trp-stats-error');
        
        // Show loading
        $loading.show();
        $content.hide();
        $error.hide();
        
        $.ajax({
            url: trpStats.restUrl,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', trpStats.nonce);
            },
            success: function(data) {
                $loading.hide();
                
                if (data.status === 'error' || data.status === 'no_data') {
                    renderError($widget, data);
                } else {
                    renderStats($widget, data, mode);
                    $content.show();
                }
            },
            error: function(xhr, status, error) {
                $loading.hide();
                renderError($widget, {
                    status: 'error',
                    error: error || 'Failed to load stats'
                });
            }
        });
    }
    
    /**
     * Render stats in widget
     */
    function renderStats($widget, data, mode) {
        const stats = data.stats || {};
        const $content = $widget.find('.trp-stats-content');
        
        let html = '';
        
        // Header
        html += '<div class="trp-stats-header">';
        html += '<h2>Trading Bot Performance</h2>';
        html += '<span class="trp-stats-mode-badge">' + (data.mode === 'live_trading' ? 'Live' : 'Paper Trading') + '</span>';
        html += '</div>';
        
        // Stats grid
        html += '<div class="trp-stats-grid">';
        
        // Total PnL
        const pnlClass = stats.total_pnl >= 0 ? 'positive' : 'negative';
        const pnlSign = stats.total_pnl >= 0 ? '+' : '';
        html += '<div class="trp-stat-card ' + pnlClass + '">';
        html += '<div class="trp-stat-label">Total P&L</div>';
        html += '<div class="trp-stat-value">' + pnlSign + '$' + formatCurrency(stats.total_pnl) + '</div>';
        html += '<div class="trp-stat-subvalue">' + formatPercent((stats.total_pnl / stats.starting_balance) * 100) + '% return</div>';
        html += '</div>';
        
        // Win Rate
        html += '<div class="trp-stat-card">';
        html += '<div class="trp-stat-label">Win Rate</div>';
        html += '<div class="trp-stat-value">' + formatPercent(stats.win_rate) + '</div>';
        html += '<div class="trp-stat-subvalue">' + stats.winning_trades + ' wins / ' + stats.total_trades + ' trades</div>';
        html += '</div>';
        
        // Total Trades
        html += '<div class="trp-stat-card">';
        html += '<div class="trp-stat-label">Total Trades</div>';
        html += '<div class="trp-stat-value">' + stats.total_trades + '</div>';
        html += '<div class="trp-stat-subvalue">' + stats.open_positions + ' open, ' + stats.closed_positions + ' closed</div>';
        html += '</div>';
        
        // Current Balance
        html += '<div class="trp-stat-card">';
        html += '<div class="trp-stat-label">Current Balance</div>';
        html += '<div class="trp-stat-value">$' + formatCurrency(stats.current_balance) + '</div>';
        html += '<div class="trp-stat-subvalue">Started: $' + formatCurrency(stats.starting_balance) + '</div>';
        html += '</div>';
        
        html += '</div>';
        
        // Detailed table (full mode only)
        if (mode === 'full' && stats.total_trades > 0) {
            html += '<table class="trp-stats-table">';
            html += '<thead><tr>';
            html += '<th>Metric</th><th>Value</th>';
            html += '</tr></thead>';
            html += '<tbody>';
            html += '<tr><td>Average Win</td><td class="trp-pnl-positive">$' + formatCurrency(stats.average_win) + '</td></tr>';
            html += '<tr><td>Average Loss</td><td class="trp-pnl-negative">$' + formatCurrency(stats.average_loss) + '</td></tr>';
            html += '<tr><td>Winning Trades</td><td>' + stats.winning_trades + '</td></tr>';
            html += '<tr><td>Losing Trades</td><td>' + stats.losing_trades + '</td></tr>';
            html += '</tbody>';
            html += '</table>';
        }
        
        // Footer
        html += '<div class="trp-stats-footer">';
        html += '<p>Last updated: ' + formatDate(data.last_updated) + '</p>';
        if (data.mode === 'paper_trading') {
            html += '<p><em>These are paper trading results. Live trading coming soon!</em></p>';
        }
        html += '</div>';
        
        $content.html(html);
    }
    
    /**
     * Render error message
     */
    function renderError($widget, data) {
        const $error = $widget.find('.trp-stats-error');
        let message = 'Unable to load trading statistics.';
        
        if (data.status === 'no_data') {
            message = data.message || 'Paper trading bot not yet active. Stats will appear here once trading begins.';
        } else if (data.error) {
            message = 'Error: ' + data.error;
        }
        
        $error.html('<p>' + message + '</p>');
        $error.show();
    }
    
    /**
     * Format currency
     */
    function formatCurrency(value) {
        return parseFloat(value || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    /**
     * Format percentage
     */
    function formatPercent(value) {
        return parseFloat(value || 0).toFixed(2) + '%';
    }
    
    /**
     * Format date
     */
    function formatDate(isoString) {
        if (!isoString) return 'Unknown';
        const date = new Date(isoString);
        return date.toLocaleString();
    }
    
})(jQuery);

