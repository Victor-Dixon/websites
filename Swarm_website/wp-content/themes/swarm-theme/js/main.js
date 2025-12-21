/**
 * Swarm Theme Main JavaScript
 * 
 * Handles real-time activity feed updates and interactive features
 * 
 * @package Swarm_Theme
 */

(function($) {
    'use strict';

    let activityFeedInterval;
    let lastActivityTimestamp = 0;

    // Wait for DOM ready
    $(document).ready(function() {
        
        // Initialize real-time activity feed
        initActivityFeed();
        
        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            const href = $(this).attr('href');
            if (href !== '#' && href.length > 1) {
                const target = $(href);
                if (target.length) {
                    e.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            }
        });

        // Agent card hover effects
        $('.agent-card').hover(
            function() {
                $(this).addClass('hovered');
            },
            function() {
                $(this).removeClass('hovered');
            }
        );

        // Mobile menu toggle
        $('.menu-toggle').on('click', function() {
            $('#mainNav').toggleClass('active');
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-nav, .menu-toggle').length) {
                $('#mainNav').removeClass('active');
            }
        });

        // Scroll animations
        initScrollAnimations();
    });

    /**
     * Initialize real-time activity feed
     */
    function initActivityFeed() {
        const $activityFeed = $('#activityFeed');
        
        if ($activityFeed.length) {
            // Refresh activity feed every 15 seconds
            activityFeedInterval = setInterval(function() {
                refreshActivityFeed();
            }, 15000);
            
            // Initial refresh after 5 seconds
            setTimeout(refreshActivityFeed, 5000);
        }
    }

    /**
     * Refresh activity feed via REST API
     */
    function refreshActivityFeed() {
        const restUrl = '/wp-json/swarm/v2/mission-log?limit=20';
        
        $.ajax({
            url: restUrl,
            type: 'GET',
            dataType: 'json',
            success: function(logs) {
                if (Array.isArray(logs) && logs.length > 0) {
                    updateActivityFeed(logs);
                }
            },
            error: function(xhr, status, error) {
                console.log('Activity feed refresh failed:', error);
            }
        });
    }

    /**
     * Update activity feed display
     */
    function updateActivityFeed(logs) {
        const $feed = $('#activityFeed');
        if (!$feed.length) return;

        // Check if we have new entries
        const newestLog = logs[0];
        const newestTimestamp = newestLog.unix_timestamp || 
                               (newestLog.timestamp ? new Date(newestLog.timestamp).getTime() / 1000 : 0);
        
        // Only update if there are new entries
        if (newestTimestamp <= lastActivityTimestamp) {
            return;
        }
        
        lastActivityTimestamp = newestTimestamp;

        // Clear existing feed
        $feed.empty();

        // Add new entries
        logs.forEach(function(log) {
            const $item = createActivityItem(log);
            $feed.append($item);
        });

        // Scroll to top if user is at top of feed
        const scrollTop = $feed.scrollTop();
        if (scrollTop < 100) {
            $feed.scrollTop(0);
        }
    }

    /**
     * Create activity item element
     */
    function createActivityItem(log) {
        const timestamp = log.unix_timestamp || 
                         (log.timestamp ? new Date(log.timestamp).getTime() / 1000 : Date.now() / 1000);
        const timeAgo = getTimeAgo(timestamp);
        
        const $item = $('<div>').addClass('activity-item');
        
        const $header = $('<div>').addClass('activity-header');
        $header.append($('<span>').addClass('activity-agent').text(log.agent || 'Unknown'));
        $header.append($('<span>').addClass('activity-time').text(timeAgo));
        
        const $message = $('<p>').addClass('activity-message').text(log.message || 'No message');
        
        $item.append($header);
        $item.append($message);
        
        // Add tags if present
        if (log.tags && Array.isArray(log.tags) && log.tags.length > 0) {
            const $tags = $('<div>').addClass('activity-tags');
            log.tags.forEach(function(tag) {
                $tags.append($('<span>').addClass('activity-tag').text(tag));
            });
            $item.append($tags);
        }
        
        return $item;
    }

    /**
     * Get human-readable time ago
     */
    function getTimeAgo(timestamp) {
        const now = Math.floor(Date.now() / 1000);
        const diff = now - timestamp;
        
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
        
        return new Date(timestamp * 1000).toLocaleDateString();
    }

    /**
     * Initialize scroll animations
     */
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe capability cards and agent cards
        $('.capability-card, .agent-card, .stat-card').each(function() {
            observer.observe(this);
        });
    }

    /**
     * Refresh agent stats
     */
    function refreshAgentStats() {
        $.ajax({
            url: '/wp-json/swarm/v2/stats',
            type: 'GET',
            dataType: 'json',
            success: function(stats) {
                updateStatsDisplay(stats);
            }
        });
    }

    /**
     * Update stats display
     */
    function updateStatsDisplay(stats) {
        $('.hero-stat-value').each(function() {
            const $stat = $(this);
            const label = $stat.next('.hero-stat-label').text().toLowerCase();
            
            if (label.includes('agents') && !label.includes('active')) {
                $stat.text(stats.total_agents || 8);
            } else if (label.includes('active')) {
                $stat.text(stats.active_agents || 0);
            } else if (label.includes('points')) {
                $stat.text(formatNumber(stats.total_points || 0));
            }
        });
    }

    /**
     * Format number with commas
     */
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (activityFeedInterval) {
            clearInterval(activityFeedInterval);
        }
    });

})(jQuery);
