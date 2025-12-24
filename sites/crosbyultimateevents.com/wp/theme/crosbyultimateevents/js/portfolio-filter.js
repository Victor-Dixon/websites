/**
 * Portfolio Filter JavaScript
 * Handles filtering of portfolio items by category
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var filterButtons = document.querySelectorAll('.filter-btn');
        var portfolioItems = document.querySelectorAll('.portfolio-item');

        if (filterButtons.length === 0 || portfolioItems.length === 0) {
            return; // Exit if elements don't exist
        }

        // Add click handlers to filter buttons
        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var filter = this.getAttribute('data-filter');

                // Update active state
                filterButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });
                this.classList.add('active');

                // Filter portfolio items
                portfolioItems.forEach(function(item) {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = 'block';
                        // Add fade-in animation
                        item.style.opacity = '0';
                        setTimeout(function() {
                            item.style.transition = 'opacity 0.3s ease-in';
                            item.style.opacity = '1';
                        }, 10);
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
})();

