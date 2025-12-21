/**
 * AriaJet Theme Main JavaScript
 */

(function() {
    'use strict';
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initNavigation();
        initGameShowcase();
    });
    
    /**
     * Initialize Navigation
     */
    function initNavigation() {
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.querySelector('.main-navigation');
        
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
            });
        }
    }
    
    /**
     * Initialize Game Showcase
     */
    function initGameShowcase() {
        const gameCards = document.querySelectorAll('.game-card');
        
        gameCards.forEach(function(card) {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.game-play-button')) {
                    const gameUrl = card.dataset.gameUrl;
                    if (gameUrl) {
                        window.location.href = gameUrl;
                    }
                }
            });
        });
    }
})();





