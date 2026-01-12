/**
 * Swarm Chronicle Frontend JavaScript
 */

(function($) {
    'use strict';

    class SwarmChronicle {
        constructor() {
            this.ajaxUrl = swarmChronicleAjax.ajaxurl;
            this.nonce = swarmChronicleAjax.nonce;
            this.currentPage = 1;
            this.isLoading = false;

            this.init();
        }

        init() {
            this.bindEvents();
            this.initializeLazyLoading();
        }

        bindEvents() {
            // Load more button
            $(document).on('click', '.load-more-btn', this.loadMoreEntries.bind(this));

            // Mission filtering
            $(document).on('change', '.mission-filter', this.filterMissions.bind(this));

            // Search functionality
            $(document).on('input', '.chronicle-search', this.debounce(this.searchChronicle.bind(this), 300));

            // Auto-refresh toggle
            $(document).on('change', '.auto-refresh-toggle', this.toggleAutoRefresh.bind(this));
        }

        initializeLazyLoading() {
            // Initialize intersection observer for lazy loading
            if ('IntersectionObserver' in window) {
                const observerOptions = {
                    root: null,
                    rootMargin: '50px',
                    threshold: 0.1
                };

                this.intersectionObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.isLoading) {
                            this.loadMoreEntries();
                        }
                    });
                }, observerOptions);

                // Observe load more triggers
                $('.load-more-trigger').each((index, element) => {
                    this.intersectionObserver.observe(element);
                });
            }
        }

        async loadMoreEntries() {
            if (this.isLoading) return;

            this.isLoading = true;
            const $button = $('.load-more-btn');
            const originalText = $button.text();

            $button.text('Loading...').prop('disabled', true);

            try {
                const response = await this.fetchChronicleData({
                    page: this.currentPage + 1,
                    append: true
                });

                if (response.entries && response.entries.length > 0) {
                    this.appendEntries(response.entries);
                    this.currentPage++;

                    if (!response.has_more) {
                        $button.hide();
                    }
                } else {
                    $button.text('No more entries').prop('disabled', true);
                }

            } catch (error) {
                console.error('Error loading more entries:', error);
                $button.text('Error loading more').prop('disabled', false);
                this.showNotification('Error loading more entries. Please try again.', 'error');
            } finally {
                this.isLoading = false;
                if ($button.is(':visible')) {
                    $button.text(originalText).prop('disabled', false);
                }
            }
        }

        async fetchChronicleData(params = {}) {
            const defaultParams = {
                type: 'chronicle',
                limit: 50,
                agent: 'all',
                page: 1
            };

            const queryParams = { ...defaultParams, ...params };

            return new Promise((resolve, reject) => {
                $.ajax({
                    url: this.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'swarm_get_chronicle_data',
                        nonce: this.nonce,
                        ...queryParams
                    },
                    success: (response) => {
                        if (response.success) {
                            resolve(response.data);
                        } else {
                            reject(new Error(response.data || 'Unknown error'));
                        }
                    },
                    error: (xhr, status, error) => {
                        reject(new Error(`AJAX error: ${error}`));
                    }
                });
            });
        }

        appendEntries(entries) {
            const $container = $('.chronicle-content');
            const $entries = entries.map(entry => this.createEntryElement(entry));

            $container.append($entries);
            this.animateNewEntries($entries);
        }

        createEntryElement(entry) {
            const $entry = $(`
                <div class="chronicle-entry ${entry.type}" style="opacity: 0; transform: translateY(20px);">
                    <div class="entry-header">
                        <span class="entry-type">${this.escapeHtml(entry.icon)}</span>
                        <span class="entry-agent">${this.escapeHtml(entry.agent)}</span>
                        <span class="entry-date">${this.escapeHtml(entry.date)}</span>
                    </div>
                    <div class="entry-content">
                        ${entry.content}
                    </div>
                </div>
            `);

            return $entry;
        }

        animateNewEntries($entries) {
            $entries.each((index, element) => {
                setTimeout(() => {
                    $(element).animate({
                        opacity: 1,
                        transform: 'translateY(0)'
                    }, 300);
                }, index * 50);
            });
        }

        async filterMissions() {
            const status = $('.mission-status-filter').val();
            const agent = $('.mission-agent-filter').val();

            try {
                const response = await this.fetchChronicleData({
                    type: 'missions',
                    status: status,
                    agent: agent
                });

                this.updateMissionsDisplay(response);
            } catch (error) {
                console.error('Error filtering missions:', error);
                this.showNotification('Error filtering missions. Please try again.', 'error');
            }
        }

        updateMissionsDisplay(data) {
            const $container = $('.swarm-missions-container .missions-list');

            if (!data.length) {
                $container.html('<p>No missions found matching the selected criteria.</p>');
                return;
            }

            const $missions = data.map(mission => this.createMissionElement(mission));
            $container.html($missions);

            // Animate new missions
            $missions.hide().fadeIn(300);
        }

        createMissionElement(mission) {
            const progressHtml = mission.progress !== null ? `
                <div class="mission-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${mission.progress}%"></div>
                    </div>
                    <span class="progress-text">${mission.progress}% Complete</span>
                </div>
            ` : '';

            return $(`
                <div class="mission-item ${mission.status}">
                    <div class="mission-header">
                        <span class="mission-priority ${mission.priority}">
                            ${mission.priority}
                        </span>
                        <span class="mission-agent">${this.escapeHtml(mission.agent)}</span>
                    </div>
                    <div class="mission-content">
                        <h4>${this.escapeHtml(mission.title)}</h4>
                        <p>${this.escapeHtml(mission.description)}</p>
                        ${progressHtml}
                    </div>
                </div>
            `);
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        async searchChronicle(event) {
            const query = $(event.target).val().trim();

            if (query.length < 3) {
                this.clearSearchResults();
                return;
            }

            try {
                const response = await this.fetchChronicleData({
                    type: 'search',
                    q: query
                });

                this.displaySearchResults(response);
            } catch (error) {
                console.error('Error searching chronicle:', error);
                this.showNotification('Error performing search. Please try again.', 'error');
            }
        }

        displaySearchResults(results) {
            const $container = $('.chronicle-content');

            if (!results.entries || results.entries.length === 0) {
                $container.html('<div class="no-results"><p>No entries found matching your search.</p></div>');
                return;
            }

            const $entries = results.entries.map(entry => this.createEntryElement(entry));
            $container.html($entries);

            // Highlight search terms
            this.highlightSearchTerms($container);
        }

        highlightSearchTerms($container) {
            const searchTerm = $('.chronicle-search').val().trim();
            if (!searchTerm) return;

            const regex = new RegExp(`(${this.escapeRegex(searchTerm)})`, 'gi');

            $container.find('.entry-content').each(function() {
                const html = $(this).html();
                const highlighted = html.replace(regex, '<mark>$1</mark>');
                $(this).html(highlighted);
            });
        }

        clearSearchResults() {
            // Reset to original view
            this.loadInitialEntries();
        }

        async loadInitialEntries() {
            try {
                const response = await this.fetchChronicleData({ page: 1 });
                const $entries = response.entries.map(entry => this.createEntryElement(entry));
                $('.chronicle-content').html($entries);
            } catch (error) {
                console.error('Error loading initial entries:', error);
            }
        }

        toggleAutoRefresh(event) {
            const isEnabled = $(event.target).is(':checked');

            if (isEnabled) {
                this.startAutoRefresh();
                this.showNotification('Auto-refresh enabled', 'success');
            } else {
                this.stopAutoRefresh();
                this.showNotification('Auto-refresh disabled', 'info');
            }
        }

        startAutoRefresh() {
            this.autoRefreshInterval = setInterval(() => {
                this.refreshChronicle();
            }, 30000); // Refresh every 30 seconds
        }

        stopAutoRefresh() {
            if (this.autoRefreshInterval) {
                clearInterval(this.autoRefreshInterval);
                this.autoRefreshInterval = null;
            }
        }

        async refreshChronicle() {
            try {
                const response = await this.fetchChronicleData({
                    page: 1,
                    refresh: true
                });

                if (response.entries && response.entries.length > 0) {
                    this.updateChronicleDisplay(response.entries);
                    this.showNotification('Chronicle updated', 'success');
                }
            } catch (error) {
                console.error('Error refreshing chronicle:', error);
            }
        }

        updateChronicleDisplay(entries) {
            const $container = $('.chronicle-content');
            const $newEntries = entries.slice(0, 10).map(entry => this.createEntryElement(entry));

            // Animate update
            $container.fadeOut(200, () => {
                $container.html($newEntries).fadeIn(200);
            });
        }

        showNotification(message, type = 'info') {
            // Simple notification system - could be enhanced with a proper notification plugin
            const $notification = $(`
                <div class="chronicle-notification ${type}" style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
                    color: white;
                    padding: 15px 20px;
                    border-radius: 5px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                    z-index: 10000;
                    opacity: 0;
                    transform: translateY(-10px);
                ">
                    ${this.escapeHtml(message)}
                </div>
            `);

            $('body').append($notification);

            // Animate in
            $notification.animate({
                opacity: 1,
                transform: 'translateY(0)'
            }, 300);

            // Auto remove after 3 seconds
            setTimeout(() => {
                $notification.animate({
                    opacity: 0,
                    transform: 'translateY(-10px)'
                }, 300, function() {
                    $notification.remove();
                });
            }, 3000);
        }

        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return (text || '').replace(/[&<>"']/g, m => map[m]);
        }

        escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        if (typeof swarmChronicleAjax !== 'undefined') {
            new SwarmChronicle();
        }
    });

})(jQuery);