/**
 * AriaJet Cosmic - Game Interaction JavaScript
 * 
 * Handles game embeds, filters, and interactive game features.
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initGameEmbeds();
        initGameFilters();
        initGameCards();
    });
    
    /**
     * Initialize Game Embeds
     * Creates iframes for game containers with loading states
     */
    function initGameEmbeds() {
        const gameEmbeds = document.querySelectorAll('.game-embed[data-game-url]');
        
        gameEmbeds.forEach(function(embed) {
            const gameUrl = embed.dataset.gameUrl;
            if (!gameUrl) return;
            
            // Create loading spinner
            const spinner = document.createElement('div');
            spinner.className = 'loading-spinner';
            embed.appendChild(spinner);
            
            // Create iframe
            const iframe = document.createElement('iframe');
            iframe.src = gameUrl;
            iframe.setAttribute('allowfullscreen', 'true');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            iframe.setAttribute('loading', 'lazy');
            iframe.style.opacity = '0';
            iframe.style.transition = 'opacity 0.5s ease';
            
            // Remove spinner when loaded
            iframe.addEventListener('load', function() {
                spinner.remove();
                iframe.style.opacity = '1';
                
                // Add loaded class for any additional styling
                embed.classList.add('game-loaded');
            });
            
            // Handle load errors
            iframe.addEventListener('error', function() {
                spinner.remove();
                embed.innerHTML = '<div class="game-error"><p>Unable to load game. Please try again later.</p></div>';
            });
            
            embed.appendChild(iframe);
        });
    }
    
    /**
     * Initialize Game Filters
     * Handles filtering games by type/category
     */
    function initGameFilters() {
        const filterButtons = document.querySelectorAll('.game-filter');
        const gameCards = document.querySelectorAll('.game-card[data-game-type]');
        
        if (filterButtons.length === 0 || gameCards.length === 0) return;
        
        // Set initial active state on "All" filter
        const allFilter = document.querySelector('.game-filter[data-filter="all"]');
        if (allFilter) {
            allFilter.classList.add('active');
        }
        
        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                // Update active state
                filterButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filter games with animation
                filterGames(filter, gameCards);
            });
        });
    }
    
    /**
     * Filter Games with Animation
     */
    function filterGames(filter, cards) {
        cards.forEach(function(card, index) {
            const gameType = card.dataset.gameType;
            const shouldShow = filter === 'all' || gameType === filter;
            
            if (shouldShow) {
                // Stagger animation
                setTimeout(function() {
                    card.style.display = '';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9) translateY(20px)';
                    
                    // Trigger reflow
                    card.offsetHeight;
                    
                    card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = '';
                }, index * 50);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                
                setTimeout(function() {
                    card.style.display = 'none';
                }, 300);
            }
        });
    }
    
    /**
     * Initialize Game Card Interactions
     */
    function initGameCards() {
        const gameCards = document.querySelectorAll('.game-card');
        
        gameCards.forEach(function(card) {
            const gameUrl = card.dataset.gameUrl;
            const playButton = card.querySelector('.game-play-button, .cosmic-button.accent');
            
            // Make card clickable (except buttons)
            if (gameUrl && !playButton) {
                card.style.cursor = 'pointer';
                
                card.addEventListener('click', function(e) {
                    // Don't navigate if clicking a button or link
                    if (e.target.closest('a, button')) return;
                    
                    window.location.href = gameUrl;
                });
            }
            
            // Add keyboard accessibility
            card.setAttribute('tabindex', '0');
            card.setAttribute('role', 'article');
            
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    const link = card.querySelector('a.cosmic-button.accent, a.game-play-button');
                    if (link) {
                        e.preventDefault();
                        link.click();
                    }
                }
            });
        });
    }
    
    /**
     * Fullscreen Game Toggle
     */
    window.toggleGameFullscreen = function(gameUrl) {
        // Create fullscreen overlay
        const overlay = document.createElement('div');
        overlay.className = 'game-fullscreen-overlay';
        overlay.innerHTML = `
            <div class="fullscreen-container">
                <button class="close-fullscreen" aria-label="Close fullscreen">
                    <span>&times;</span>
                </button>
                <iframe src="${gameUrl}" allowfullscreen></iframe>
            </div>
        `;
        
        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .game-fullscreen-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 10000;
                background: rgba(0, 0, 0, 0.95);
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.3s ease;
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .fullscreen-container {
                position: relative;
                width: 95vw;
                height: 95vh;
                max-width: 1600px;
            }
            .fullscreen-container iframe {
                width: 100%;
                height: 100%;
                border: none;
                border-radius: 12px;
            }
            .close-fullscreen {
                position: absolute;
                top: -50px;
                right: 0;
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                color: #fff;
                font-size: 24px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }
            .close-fullscreen:hover {
                background: rgba(255, 45, 149, 0.5);
                border-color: #ff2d95;
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(overlay);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Close handlers
        const closeBtn = overlay.querySelector('.close-fullscreen');
        closeBtn.addEventListener('click', closeFullscreen);
        
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeFullscreen();
            }
        });
        
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeFullscreen();
                document.removeEventListener('keydown', escHandler);
            }
        });
        
        function closeFullscreen() {
            overlay.style.animation = 'fadeIn 0.3s ease reverse';
            setTimeout(function() {
                overlay.remove();
                style.remove();
                document.body.style.overflow = '';
            }, 300);
        }
    };
    
})();
