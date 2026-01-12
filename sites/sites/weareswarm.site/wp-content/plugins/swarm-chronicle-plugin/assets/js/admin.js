/**
 * Swarm Chronicle Admin JavaScript
 */

(function($) {
    'use strict';

    class SwarmChronicleAdmin {
        constructor() {
            this.ajaxUrl = ajaxurl;
            this.init();
        }

        init() {
            this.bindEvents();
            this.initializeCharts();
        }

        bindEvents() {
            // Manual sync button
            $('#manual-sync-btn').on('click', this.handleManualSync.bind(this));

            // Quick sync button
            $('#quick-sync-btn').on('click', this.handleQuickSync.bind(this));

            // Settings form validation
            $('#swarm-chronicle-settings-form').on('submit', this.validateSettings.bind(this));

            // Test API connection
            $('#test-api-connection').on('click', this.testApiConnection.bind(this));
        }

        initializeCharts() {
            // Initialize any charts or data visualizations
            this.updateDashboardStats();
        }

        async handleManualSync() {
            const $btn = $('#manual-sync-btn');
            const $result = $('#sync-result');

            // Disable button and show loading
            $btn.prop('disabled', true).text('Syncing...');
            $result.html('<p style="color: #007cba;">🔄 Synchronizing data from Swarm systems...</p>');

            try {
                const response = await this.makeAjaxRequest('swarm_sync_chronicle', {});

                if (response.success) {
                    $result.html('<p style="color: #46b450;">✅ Synchronization completed successfully!</p>');
                    this.updateDashboardStats();
                    this.showSuccessMessage('Data synchronized successfully');
                } else {
                    $result.html(`<p style="color: #dc3232;">❌ Synchronization failed: ${response.data?.error || 'Unknown error'}</p>`);
                    this.showErrorMessage('Synchronization failed');
                }

            } catch (error) {
                console.error('Sync error:', error);
                $result.html('<p style="color: #dc3232;">❌ Network error occurred during synchronization</p>');
                this.showErrorMessage('Network error during sync');
            } finally {
                $btn.prop('disabled', false).text('Sync Now');
            }
        }

        async handleQuickSync() {
            const $btn = $('#quick-sync-btn');

            $btn.prop('disabled', true).text('Syncing...');

            try {
                const response = await this.makeAjaxRequest('swarm_sync_chronicle', { quick: true });

                if (response.success) {
                    this.showSuccessMessage('Quick sync completed');
                    this.updateDashboardStats();
                } else {
                    this.showErrorMessage('Quick sync failed');
                }

            } catch (error) {
                console.error('Quick sync error:', error);
                this.showErrorMessage('Quick sync error');
            } finally {
                $btn.prop('disabled', false).text('Quick Sync');
            }
        }

        validateSettings(event) {
            const apiEndpoint = $('#swarm_chronicle_api_endpoint').val();
            const apiKey = $('#swarm_chronicle_api_key').val();

            if (apiEndpoint && !this.isValidUrl(apiEndpoint)) {
                event.preventDefault();
                this.showErrorMessage('Please enter a valid API endpoint URL');
                $('#swarm_chronicle_api_endpoint').focus();
                return false;
            }

            if (apiKey && apiKey.length < 10) {
                event.preventDefault();
                this.showErrorMessage('API key should be at least 10 characters long');
                $('#swarm_chronicle_api_key').focus();
                return false;
            }

            return true;
        }

        async testApiConnection() {
            const apiEndpoint = $('#swarm_chronicle_api_endpoint').val();
            const apiKey = $('#swarm_chronicle_api_key').val();

            if (!apiEndpoint) {
                this.showErrorMessage('Please enter an API endpoint first');
                return;
            }

            $('#test-api-connection').prop('disabled', true).text('Testing...');
            const $result = $('#test-connection-result');

            $result.html('<p style="color: #007cba;">🔄 Testing API connection...</p>');

            try {
                // For now, just test if the endpoint is reachable
                const response = await fetch(apiEndpoint + '/health', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${apiKey}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    $result.html('<p style="color: #46b450;">✅ API connection successful!</p>');
                    this.showSuccessMessage('API connection successful!');
                } else {
                    $result.html(`<p style="color: #dc3232;">❌ API connection failed: HTTP ${response.status}</p>`);
                    this.showErrorMessage(`API connection failed: HTTP ${response.status}`);
                }

            } catch (error) {
                console.error('API test error:', error);
                $result.html('<p style="color: #dc3232;">❌ API connection failed: Network error</p>');
                this.showErrorMessage('API connection failed: Network error');
            } finally {
                $('#test-api-connection').prop('disabled', false).text('Test Connection');
            }
        }

        async updateDashboardStats() {
            try {
                const stats = await this.makeAjaxRequest('swarm_get_dashboard_stats', {});

                if (stats.success) {
                    this.updateStatDisplays(stats.data);
                }
            } catch (error) {
                console.error('Error updating dashboard stats:', error);
            }
        }

        updateStatDisplays(data) {
            // Update system status indicators
            $('.status-value.connected').toggleClass('connected', data.api_connected);
            $('.status-value.has-data').toggleClass('has-data', data.has_data);

            // Update last sync time
            if (data.last_sync) {
                $('.status-value:contains("Last Sync")').next().text(
                    new Date(data.last_sync * 1000).toLocaleString()
                );
            }

            // Update metrics
            if (data.metrics) {
                $('#total-entries').text(data.metrics.total_entries || 0);
                $('#active-agents').text(data.metrics.active_agents || 0);
                $('#pending-tasks').text(data.metrics.pending_tasks || 0);
            }
        }

        async makeAjaxRequest(action, data = {}) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('nonce', this.getNonce());

            Object.keys(data).forEach(key => {
                formData.append(key, data[key]);
            });

            const response = await fetch(this.ajaxUrl, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }

        getNonce() {
            // Try to get nonce from various sources
            return (window.wpApiSettings && window.wpApiSettings.nonce) ||
                   (window.swarmChronicleAjax && window.swarmChronicleAjax.nonce) ||
                   '';
        }

        showSuccessMessage(message) {
            this.showMessage(message, 'success');
        }

        showErrorMessage(message) {
            this.showMessage(message, 'error');
        }

        showMessage(message, type = 'info') {
            // Use WordPress notices if available, otherwise create our own
            if (window.wp && window.wp.data && window.wp.data.dispatch) {
                window.wp.data.dispatch('core/notices').createNotice(
                    type,
                    message,
                    { type: type, isDismissible: true }
                );
            } else {
                // Fallback notification
                const noticeClass = type === 'success' ? 'notice-success' :
                                  type === 'error' ? 'notice-error' : 'notice-info';

                const $notice = $(`
                    <div class="notice ${noticeClass} is-dismissible">
                        <p>${message}</p>
                        <button type="button" class="notice-dismiss">
                            <span class="screen-reader-text">Dismiss this notice.</span>
                        </button>
                    </div>
                `);

                $('.wp-header-end').after($notice);

                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    $notice.fadeOut(() => $notice.remove());
                }, 5000);
            }
        }

        isValidUrl(string) {
            try {
                const url = new URL(string);
                return url.protocol === 'http:' || url.protocol === 'https:';
            } catch {
                return false;
            }
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        if (typeof ajaxurl !== 'undefined') {
            new SwarmChronicleAdmin();
        }
    });

})(jQuery);