
/**
 * freerideinvestor.com Menu Navigation Fix
 * Added: 2025-12-24
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMenu);
    } else {
        initMenu();
    }
    
    function initMenu() {
        // Find menu toggle button
        const toggleButtons = document.querySelectorAll(
            'button[aria-label*="menu" i], ' +
            'button[name*="menu" i], ' +
            '.menu-toggle, ' +
            'button.toggle-menu, ' +
            '[class*="menu-toggle"]'
        );
        
        // Find menu/navigation element
        const navElements = document.querySelectorAll('nav, [role="navigation"]');
        
        if (toggleButtons.length === 0 || navElements.length === 0) {
            console.warn('Menu toggle button or nav element not found');
            return;
        }
        
        // Add click handlers to toggle buttons
        toggleButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle menu on all nav elements
                navElements.forEach(function(nav) {
                    nav.classList.toggle('menu-open');
                    nav.setAttribute('aria-expanded', 
                        nav.classList.contains('menu-open') ? 'true' : 'false');
                });
                
                // Toggle button state
                button.classList.toggle('active');
                button.setAttribute('aria-expanded',
                    button.classList.contains('active') ? 'true' : 'false');
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('nav') && !e.target.closest('button[aria-label*="menu" i]')) {
                navElements.forEach(function(nav) {
                    nav.classList.remove('menu-open');
                    nav.setAttribute('aria-expanded', 'false');
                });
                toggleButtons.forEach(function(button) {
                    button.classList.remove('active');
                    button.setAttribute('aria-expanded', 'false');
                });
            }
        });
        
        // Ensure menu links work
        const menuLinks = document.querySelectorAll('nav a, [role="navigation"] a');
        menuLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Ensure link works
                if (this.href && this.href !== '#' && this.href !== window.location.href) {
                    // Link is valid, allow navigation
                    return true;
                }
            });
        });
        
        console.log('Menu navigation initialized');
    }
})();
