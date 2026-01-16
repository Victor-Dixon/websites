// Performance Validation JavaScript - Tracks user engagement and behavior
(function($) {
    'use strict';

    // Configuration
    const config = {
        trackPageViews: true,
        trackClicks: true,
        trackScroll: true,
        trackTimeOnPage: true,
        scrollThresholds: [25, 50, 75, 100],
        heartbeatInterval: 30000, // 30 seconds
    };

    // Track page view when DOM is ready
    $(document).ready(function() {
        if (config.trackPageViews) {
            trackUserAction('page_view', {
                page_title: document.title,
                page_url: window.location.href,
                referrer: document.referrer,
                viewport_width: window.innerWidth,
                viewport_height: window.innerHeight,
                user_agent: navigator.userAgent
            });
        }

        // Set up other tracking
        setupClickTracking();
        setupScrollTracking();
        setupTimeTracking();
        setupHeartbeat();
        setupFormTracking();
        setupStrategyTracking();
    });

    // Track user actions
    function trackUserAction(actionType, actionData) {
        $.ajax({
            url: performanceValidationAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'track_user_action',
                nonce: performanceValidationAjax.nonce,
                action_type: actionType,
                action_data: JSON.stringify(actionData),
                page_url: window.location.href,
                session_id: performanceValidationAjax.session_id,
                user_id: performanceValidationAjax.user_id
            },
            success: function(response) {
                // Optional: log successful tracking
                console.log('Tracked:', actionType);
            },
            error: function(xhr, status, error) {
                console.error('Tracking failed:', actionType, error);
            }
        });
    }

    // Click tracking
    function setupClickTracking() {
        if (!config.trackClicks) return;

        $(document).on('click', function(e) {
            const target = e.target;
            const clickableElement = $(target).closest('a, button, [role="button"], .btn, .cta-button');

            if (clickableElement.length > 0) {
                const elementData = {
                    element_type: clickableElement.prop('tagName').toLowerCase(),
                    element_class: clickableElement.attr('class') || '',
                    element_id: clickableElement.attr('id') || '',
                    element_text: clickableElement.text().trim().substring(0, 100),
                    href: clickableElement.attr('href') || '',
                    position_x: e.pageX,
                    position_y: e.pageY
                };

                trackUserAction('element_click', elementData);
            }
        });
    }

    // Scroll depth tracking
    function setupScrollTracking() {
        if (!config.trackScroll) return;

        let maxScrollDepth = 0;
        const trackedThresholds = new Set();

        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const documentHeight = $(document).height();
            const windowHeight = $(window).height();
            const scrollPercent = Math.round((scrollTop / (documentHeight - windowHeight)) * 100);

            // Track maximum scroll depth
            if (scrollPercent > maxScrollDepth) {
                maxScrollDepth = scrollPercent;
            }

            // Track threshold crossings
            config.scrollThresholds.forEach(threshold => {
                if (scrollPercent >= threshold && !trackedThresholds.has(threshold)) {
                    trackedThresholds.add(threshold);
                    trackUserAction('scroll_threshold', {
                        threshold: threshold,
                        max_scroll_depth: maxScrollDepth,
                        time_to_threshold: Date.now() - window.performance.timing.navigationStart
                    });
                }
            });
        });
    }

    // Time on page tracking
    function setupTimeTracking() {
        if (!config.trackTimeOnPage) return;

        const pageStartTime = Date.now();
        let timeOnPage = 0;

        // Track time on page every 30 seconds
        const timeInterval = setInterval(function() {
            timeOnPage = Math.floor((Date.now() - pageStartTime) / 1000);

            if (timeOnPage > 0 && timeOnPage % 30 === 0) { // Every 30 seconds
                trackUserAction('time_on_page', {
                    seconds: timeOnPage,
                    page_title: document.title
                });
            }
        }, 1000);

        // Track when user leaves the page
        $(window).on('beforeunload', function() {
            clearInterval(timeInterval);
            const finalTime = Math.floor((Date.now() - pageStartTime) / 1000);

            trackUserAction('page_exit', {
                total_time_seconds: finalTime,
                max_scroll_depth: getMaxScrollDepth(),
                page_title: document.title
            });
        });
    }

    // Heartbeat to track active users
    function setupHeartbeat() {
        setInterval(function() {
            trackUserAction('heartbeat', {
                timestamp: Date.now(),
                page_visible: !document.hidden
            });
        }, config.heartbeatInterval);
    }

    // Form interaction tracking
    function setupFormTracking() {
        $(document).on('focus', 'input, textarea, select', function() {
            const field = $(this);
            trackUserAction('form_field_focus', {
                field_name: field.attr('name') || field.attr('id') || 'unnamed',
                field_type: field.prop('tagName').toLowerCase(),
                form_context: field.closest('form').attr('id') || 'unknown'
            });
        });

        $(document).on('submit', 'form', function(e) {
            const form = $(this);
            const formData = {};

            // Collect form field values (anonymized)
            form.find('input, textarea, select').each(function() {
                const field = $(this);
                const fieldName = field.attr('name') || field.attr('id') || 'unnamed';
                const fieldType = field.attr('type') || field.prop('tagName').toLowerCase();

                // Don't track sensitive data
                if (fieldType !== 'password' && fieldName.toLowerCase().indexOf('password') === -1) {
                    formData[fieldName] = fieldType === 'checkbox' ? field.prop('checked') : '[value]';
                }
            });

            trackUserAction('form_submission', {
                form_id: form.attr('id') || 'unknown',
                form_action: form.attr('action') || '',
                field_count: Object.keys(formData).length,
                form_data: formData
            });
        });
    }

    // Strategy marketplace specific tracking
    function setupStrategyTracking() {
        // Track strategy views
        $(document).on('click', '.view-details, .strategy-card', function() {
            const card = $(this).closest('.strategy-card');
            const strategyId = card.data('strategy-id') || card.find('[data-strategy-id]').data('strategy-id');

            if (strategyId) {
                trackUserAction('strategy_view', {
                    strategy_id: strategyId,
                    strategy_name: card.find('.strategy-name').text().trim(),
                    context: 'marketplace'
                });
            }
        });

        // Track strategy deployments
        $(document).on('click', '.deploy-strategy, .deploy-mini-btn', function() {
            const strategyId = $(this).data('strategy-id');

            if (strategyId) {
                trackUserAction('strategy_deployment_click', {
                    strategy_id: strategyId,
                    context: $(this).hasClass('deploy-mini-btn') ? 'shortcode' : 'full_marketplace'
                });
            }
        });

        // Track filter usage
        $(document).on('change', '#category-filter, #performance-filter, #risk-filter, #mini-category-filter, #mini-risk-filter', function() {
            trackUserAction('filter_usage', {
                filter_type: $(this).attr('id'),
                filter_value: $(this).val(),
                context: 'strategy_marketplace'
            });
        });

        // Track search usage
        $(document).on('input', '#strategy-search', function() {
            const searchTerm = $(this).val().trim();
            if (searchTerm.length > 2) { // Only track meaningful searches
                trackUserAction('search_usage', {
                    search_term: searchTerm,
                    context: 'strategy_marketplace'
                });
            }
        });
    }

    // Utility functions
    function getMaxScrollDepth() {
        const scrollTop = $(window).scrollTop();
        const documentHeight = $(document).height();
        const windowHeight = $(window).height();
        return Math.round((scrollTop / (documentHeight - windowHeight)) * 100);
    }

    // Track engagement with specific elements
    window.trackEngagement = function(elementType, elementId, additionalData = {}) {
        trackUserAction('element_engagement', {
            element_type: elementType,
            element_id: elementId,
            ...additionalData
        });
    };

    // Track conversions
    window.trackConversion = function(conversionType, value = null, metadata = {}) {
        trackUserAction('conversion', {
            conversion_type: conversionType,
            value: value,
            metadata: metadata
        });
    };

})(jQuery);