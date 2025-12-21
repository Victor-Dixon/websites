/**
 * TRP Swarm Status - Frontend JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize all swarm status widgets
        $('.trp-swarm-status').each(function() {
            const $widget = $(this);
            const mode = $widget.data('mode') || 'full';
            const refreshInterval = parseInt($widget.data('refresh')) || 30;
            
            // Load status immediately
            loadSwarmStatus($widget, mode);
            
            // Auto-refresh if interval is set
            if (refreshInterval > 0) {
                setInterval(function() {
                    loadSwarmStatus($widget, mode);
                }, refreshInterval * 1000);
            }
        });
    });
    
    /**
     * Load swarm status from REST API
     */
    function loadSwarmStatus($widget, mode) {
        const $loading = $widget.find('.trp-swarm-loading');
        const $content = $widget.find('.trp-swarm-content');
        const $error = $widget.find('.trp-swarm-error');
        
        // Show loading
        $loading.show();
        $content.hide();
        $error.hide();
        
        $.ajax({
            url: trpSwarm.restUrl,
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', trpSwarm.nonce);
            },
            success: function(data) {
                $loading.hide();
                
                if (data.status === 'error') {
                    renderError($widget, data);
                } else {
                    renderSwarmStatus($widget, data, mode);
                    $content.show();
                }
            },
            error: function(xhr, status, error) {
                $loading.hide();
                renderError($widget, {
                    status: 'error',
                    error: error || 'Failed to load swarm status'
                });
            }
        });
    }
    
    /**
     * Render swarm status in widget
     */
    function renderSwarmStatus($widget, data, mode) {
        const metrics = data.swarm_metrics || {};
        const agents = data.agents || {};
        const $content = $widget.find('.trp-swarm-content');
        
        let html = '';
        
        // Header
        html += '<div class="trp-swarm-header">';
        html += '<h2>🐝 Swarm Status - Real-Time</h2>';
        
        // Health badge
        const health = metrics.swarm_health || 0;
        let healthClass = 'critical';
        if (health >= 75) healthClass = 'excellent';
        else if (health >= 50) healthClass = 'good';
        else if (health >= 25) healthClass = 'warning';
        
        html += '<span class="trp-swarm-health-badge ' + healthClass + '">';
        html += health + '% Health';
        html += '</span>';
        html += '</div>';
        
        // Metrics grid
        html += '<div class="trp-swarm-metrics">';
        
        html += '<div class="trp-swarm-metric-card">';
        html += '<div class="trp-swarm-metric-label">Active Agents</div>';
        html += '<div class="trp-swarm-metric-value">' + metrics.active_agents + '/8</div>';
        html += '<div class="trp-swarm-metric-subvalue">' + metrics.total_agents + ' total agents</div>';
        html += '</div>';
        
        html += '<div class="trp-swarm-metric-card">';
        html += '<div class="trp-swarm-metric-label">Gas Pipeline</div>';
        html += '<div class="trp-swarm-metric-value">' + metrics.total_gas_sent + '</div>';
        html += '<div class="trp-swarm-metric-subvalue">' + metrics.total_gas_received + ' received today</div>';
        html += '</div>';
        
        html += '<div class="trp-swarm-metric-card">';
        html += '<div class="trp-swarm-metric-label">Partnerships</div>';
        html += '<div class="trp-swarm-metric-value">' + metrics.active_partnerships + '</div>';
        html += '<div class="trp-swarm-metric-subvalue">Bilateral pairs active</div>';
        html += '</div>';
        
        html += '</div>';
        
        // Agents grid (full mode only)
        if (mode === 'full') {
            html += '<div class="trp-swarm-agents">';
            
            // Sort agents by status (active first)
            const agentList = Object.values(agents).sort((a, b) => {
                if (a.is_active && !b.is_active) return -1;
                if (!a.is_active && b.is_active) return 1;
                return a.agent_id.localeCompare(b.agent_id);
            });
            
            agentList.forEach(function(agent) {
                const statusClass = agent.is_active ? 'active' : 'inactive';
                const statusLabel = agent.is_active ? 'active' : (agent.minutes_since_update > 60 ? 'offline' : 'idle');
                
                html += '<div class="trp-swarm-agent-card ' + statusClass + '">';
                html += '<div class="trp-swarm-agent-header">';
                html += '<span class="trp-swarm-agent-id">' + agent.agent_id + '</span>';
                html += '<span class="trp-swarm-agent-status ' + statusLabel + '">' + statusLabel + '</span>';
                html += '</div>';
                
                html += '<div class="trp-swarm-agent-name">' + (agent.agent_name || 'Unknown') + '</div>';
                
                if (agent.current_phase && agent.current_phase !== 'Unknown') {
                    html += '<div class="trp-swarm-agent-phase"><strong>Phase:</strong> ' + escapeHtml(agent.current_phase) + '</div>';
                }
                
                if (agent.current_mission && agent.current_mission !== 'No active mission') {
                    html += '<div class="trp-swarm-agent-mission">' + escapeHtml(agent.current_mission) + '</div>';
                }
                
                html += '<div class="trp-swarm-agent-stats">';
                html += '<span>⚡ Gas: ' + agent.gas_sent_today + ' sent</span>';
                html += '<span>📋 Tasks: ' + agent.current_tasks_count + ' active</span>';
                if (agent.minutes_since_update !== null) {
                    html += '<span>🕐 Updated: ' + agent.minutes_since_update + 'm ago</span>';
                }
                html += '</div>';
                
                html += '</div>';
            });
            
            html += '</div>';
        }
        
        // Footer
        html += '<div class="trp-swarm-footer">';
        html += '<p>Last updated: ' + formatDate(data.last_updated) + '</p>';
        html += '<p><em>Real-time swarm intelligence - building trading robots in parallel</em></p>';
        html += '</div>';
        
        $content.html(html);
    }
    
    /**
     * Render error message
     */
    function renderError($widget, data) {
        const $error = $widget.find('.trp-swarm-error');
        let message = 'Unable to load swarm status.';
        
        if (data.error) {
            message = 'Error: ' + data.error;
        }
        
        $error.html('<p>' + message + '</p>');
        $error.show();
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
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
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

