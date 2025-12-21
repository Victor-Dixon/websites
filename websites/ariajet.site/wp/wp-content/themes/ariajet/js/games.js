/**
 * Game Interaction JavaScript
 */

(function() {
    'use strict';
    
    // Game embed handlers
    document.addEventListener('DOMContentLoaded', function() {
        initGameEmbeds();
        initGameFilters();
    });
    
    /**
     * Initialize Game Embeds
     */
    function initGameEmbeds() {
        const gameEmbeds = document.querySelectorAll('.game-embed');
        
        gameEmbeds.forEach(function(embed) {
            const gameUrl = embed.dataset.gameUrl;
            if (gameUrl) {
                const iframe = document.createElement('iframe');
                iframe.src = gameUrl;
                iframe.setAttribute('allowfullscreen', 'true');
                embed.appendChild(iframe);
            }
        });
    }
    
    /**
     * Initialize Game Filters
     */
    function initGameFilters() {
        const filterButtons = document.querySelectorAll('.game-filter');
        
        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const filter = this.dataset.filter;
                filterGames(filter);
            });
        });
    }
    
    /**
     * Filter Games
     */
    function filterGames(filter) {
        const gameCards = document.querySelectorAll('.game-card');
        
        gameCards.forEach(function(card) {
            if (filter === 'all' || card.dataset.gameType === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
})();





