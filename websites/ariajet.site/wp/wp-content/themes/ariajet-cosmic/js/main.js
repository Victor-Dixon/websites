/**
 * AriaJet Cosmic - Main JavaScript
 * 
 * Handles navigation, star generation, and cosmic animations.
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initNavigation();
        initStarField();
        initScrollEffects();
        initCosmicCards();
    });
    
    /**
     * Initialize Mobile Navigation
     */
    function initNavigation() {
        const navToggle = document.querySelector('.nav-toggle');
        const navMenu = document.querySelector('.main-navigation');
        
        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function() {
                const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';
                navToggle.setAttribute('aria-expanded', !isExpanded);
                navMenu.classList.toggle('active');
                
                // Animate hamburger icon
                navToggle.classList.toggle('active');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
                    navMenu.classList.remove('active');
                    navToggle.classList.remove('active');
                    navToggle.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    navToggle.classList.remove('active');
                    navToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }
    
    /**
     * Generate Animated Star Field
     */
    function initStarField() {
        const starsContainer = document.getElementById('stars-container');
        if (!starsContainer) return;
        
        const starCount = getStarCount();
        const fragment = document.createDocumentFragment();
        
        for (let i = 0; i < starCount; i++) {
            const star = createStar();
            fragment.appendChild(star);
        }
        
        starsContainer.appendChild(fragment);
    }
    
    /**
     * Get star count based on screen size
     */
    function getStarCount() {
        const width = window.innerWidth;
        if (width < 768) return 50;
        if (width < 1024) return 100;
        return 150;
    }
    
    /**
     * Create a single star element
     */
    function createStar() {
        const star = document.createElement('div');
        star.className = 'star';
        
        // Random position
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 100 + '%';
        
        // Random size (1-3px)
        const size = Math.random() * 2 + 1;
        star.style.width = size + 'px';
        star.style.height = size + 'px';
        
        // Random animation delay and duration
        star.style.animationDelay = Math.random() * 3 + 's';
        star.style.animationDuration = (Math.random() * 2 + 2) + 's';
        
        // Random color (white, blue, or gold)
        const colors = ['#ffffff', '#a8d8ff', '#ffd700'];
        const colorIndex = Math.floor(Math.random() * 10);
        star.style.background = colorIndex < 7 ? colors[0] : (colorIndex < 9 ? colors[1] : colors[2]);
        
        // Add glow effect for brighter stars
        if (size > 2) {
            star.style.boxShadow = '0 0 ' + (size * 2) + 'px currentColor';
        }
        
        return star;
    }
    
    /**
     * Initialize Scroll Effects
     */
    function initScrollEffects() {
        // Parallax effect for nebula elements
        const nebulas = document.querySelectorAll('.nebula');
        
        if (nebulas.length > 0) {
            let ticking = false;
            
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        const scrollY = window.scrollY;
                        
                        nebulas.forEach(function(nebula, index) {
                            const speed = 0.05 + (index * 0.02);
                            const yPos = scrollY * speed;
                            nebula.style.transform = 'translateY(' + yPos + 'px)';
                        });
                        
                        ticking = false;
                    });
                    
                    ticking = true;
                }
            });
        }
        
        // Fade in elements on scroll
        const fadeElements = document.querySelectorAll('.cosmic-card, .game-card, .post-card');
        
        if (fadeElements.length > 0 && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            fadeElements.forEach(function(el) {
                el.classList.add('fade-in');
                observer.observe(el);
            });
        }
    }
    
    /**
     * Initialize Cosmic Card Interactions
     */
    function initCosmicCards() {
        const cards = document.querySelectorAll('.cosmic-card, .game-card');
        
        cards.forEach(function(card) {
            // Tilt effect on mouse move
            card.addEventListener('mousemove', function(e) {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;
                
                card.style.transform = 'perspective(1000px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                card.style.transform = '';
            });
        });
    }
    
    /**
     * Add CSS for fade-in animation
     */
    (function addFadeStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .fade-in {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }
            .fade-in-visible {
                opacity: 1;
                transform: translateY(0);
            }
        `;
        document.head.appendChild(style);
    })();
    
})();
