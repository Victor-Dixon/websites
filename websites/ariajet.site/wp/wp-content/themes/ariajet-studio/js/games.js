/**
 * AriaJet Studio - Game Interactions
 * 
 * Smooth, subtle game embed handling and filters.
 */

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        initGameEmbeds();
        initGameFilters();
    });
    
    /**
     * Initialize Game Embeds
     * Loads iframes with loading state
     */
    function initGameEmbeds() {
        const embeds = document.querySelectorAll('.game-embed[data-game-url]');
        
        embeds.forEach(function(embed) {
            const url = embed.dataset.gameUrl;
            if (!url) return;
            
            // Create loading indicator
            const loading = document.createElement('div');
            loading.className = 'game-embed-loading';
            loading.innerHTML = '<div class="loading-spinner"></div><span>Loading game...</span>';
            embed.appendChild(loading);
            
            // Create iframe
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.setAttribute('loading', 'lazy');
            iframe.style.opacity = '0';
            iframe.style.transition = 'opacity 0.4s ease';
            
            iframe.addEventListener('load', function() {
                loading.remove();
                iframe.style.opacity = '1';
                embed.classList.add('loaded');
            });
            
            iframe.addEventListener('error', function() {
                loading.innerHTML = '<span>Unable to load game</span>';
            });
            
            embed.appendChild(iframe);
        });
    }
    
    /**
     * Game Filters
     * Smooth filtering animation
     */
    function initGameFilters() {
        const filters = document.querySelectorAll('.game-filter');
        const cards = document.querySelectorAll('.game-card[data-game-type]');
        
        if (filters.length === 0 || cards.length === 0) return;
        
        filters.forEach(function(filter) {
            filter.addEventListener('click', function() {
                const type = this.dataset.filter;
                
                // Update active state
                filters.forEach(function(f) {
                    f.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filter cards
                filterCards(type, cards);
            });
        });
    }
    
    /**
     * Filter cards with animation
     */
    function filterCards(type, cards) {
        cards.forEach(function(card, index) {
            const cardType = card.dataset.gameType;
            const shouldShow = type === 'all' || cardType === type;
            
            if (shouldShow) {
                card.style.display = '';
                
                // Stagger animation
                setTimeout(function() {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 50);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(function() {
                    card.style.display = 'none';
                }, 300);
            }
        });
    }
    
})();
