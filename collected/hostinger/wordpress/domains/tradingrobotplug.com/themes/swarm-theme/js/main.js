/**
 * Swarm Theme Main JavaScript
 * 
 * @package Swarm_Theme
 */

(function($) {
    'use strict';

    // Wait for DOM ready
    $(document).ready(function() {
        
        // Smooth scrolling for anchor links
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 800);
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

        // Auto-refresh mission log every 30 seconds
        setInterval(function() {
            refreshMissionLog();
        }, 30000);

    });

    /**
     * Refresh mission log via AJAX
     */
    function refreshMissionLog() {
        $.ajax({
            url: swarmData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_mission_logs',
                nonce: swarmData.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateMissionLogDisplay(response.data);
                }
            }
        });
    }

    /**
     * Update mission log display
     */
    function updateMissionLogDisplay(logs) {
        const $logContainer = $('.mission-log .log-container');
        if ($logContainer.length && logs.length > 0) {
            // Update only if there are new entries
            // Implementation depends on your needs
        }
    }

})(jQuery);

