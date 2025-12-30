<?php
/**
 * freerideinvestor.com Menu Navigation Fix - Final Version
 * Added: 2025-12-24
 * Matches header.php structure (.main-nav, .nav-list) and theme style.css
 */

// Add menu navigation CSS that matches theme exactly
function freerideinvestor_menu_css_final() {
    ?>
    <style id="freerideinvestor-menu-css-final">
    /* Menu Navigation Fixes - Matches Theme Style Exactly */
    /* Uses theme CSS variables and matches header.php structure */
    
    /* Ensure main navigation matches theme .main-navigation style */
    .main-nav {
        display: flex !important;
        align-items: center !important;
        gap: var(--spacing-lg, 1.5rem) !important;
    }
    
    /* Navigation list - matches theme .nav-menu style */
    .main-nav .nav-list,
    .main-nav ul {
        display: flex !important;
        list-style: none !important;
        gap: var(--spacing-lg, 1.5rem) !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Navigation links - matches theme .nav-menu li a style */
    .main-nav .nav-list li a,
    .main-nav ul li a {
        color: var(--text-secondary, #4a4a4a) !important;
        font-weight: 500 !important;
        padding: var(--spacing-xs, 0.5rem) 0 !important;
        text-decoration: none !important;
        transition: color var(--transition-fast, 150ms ease) !important;
    }
    
    /* Navigation link hover - matches theme primary blue */
    .main-nav .nav-list li a:hover,
    .main-nav ul li a:hover {
        color: var(--primary-blue, #0066ff) !important;
        text-decoration: none !important;
    }
    
    /* Menu toggle button - matches theme .mobile-menu-toggle style */
    .menu-toggle,
    #mobile-menu-toggle {
        display: none !important; /* Hidden on desktop */
        background: none !important;
        border: none !important;
        font-size: 1.5rem !important;
        cursor: pointer !important;
        padding: var(--spacing-xs, 0.5rem) !important;
        color: var(--text-primary, #1a1a1a) !important;
        transition: color var(--transition-fast, 150ms ease) !important;
    }
    
    .menu-toggle:hover,
    #mobile-menu-toggle:hover {
        color: var(--primary-blue, #0066ff) !important;
    }
    
    /* Mobile menu styles - matches theme responsive design */
    @media (max-width: 768px) {
        /* Show menu toggle on mobile */
        .menu-toggle,
        #mobile-menu-toggle {
            display: block !important;
        }
        
        /* Hide navigation by default on mobile - matches theme */
        .main-nav .nav-list,
        .main-nav ul {
            display: none !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            background: var(--bg-primary, #ffffff) !important;
            border-top: 1px solid var(--border-color, #dee2e6) !important;
            flex-direction: column !important;
            padding: var(--spacing-md, 1.5rem) !important;
            box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1)) !important;
            z-index: 999 !important;
        }
        
        /* Show navigation when menu is open - matches theme .active class */
        .main-nav.is-open .nav-list,
        .main-nav.is-open ul,
        .main-nav.active .nav-list,
        .main-nav.active ul,
        .main-nav[aria-expanded="true"] .nav-list,
        .main-nav[aria-expanded="true"] ul {
            display: flex !important;
        }
        
        /* Mobile menu link styling */
        .main-nav .nav-list li,
        .main-nav ul li {
            width: 100% !important;
        }
        
        .main-nav .nav-list li a,
        .main-nav ul li a {
            display: block !important;
            padding: var(--spacing-sm, 1rem) 0 !important;
            width: 100% !important;
        }
    }
    
    /* Desktop menu - horizontal layout */
    @media (min-width: 769px) {
        .menu-toggle,
        #mobile-menu-toggle {
            display: none !important;
        }
        
        .main-nav .nav-list,
        .main-nav ul {
            display: flex !important;
            flex-direction: row !important;
        }
    }
    
    /* Ensure menu links are clickable */
    .main-nav a {
        pointer-events: auto !important;
        cursor: pointer !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'freerideinvestor_menu_css_final', 99);

// Add menu navigation JavaScript - simple and safe
function freerideinvestor_menu_js_final() {
    ?>
    <script id="freerideinvestor-menu-js-final">
    (function() {
        'use strict';
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMenu);
        } else {
            initMenu();
        }
        
        function initMenu() {
            // Find menu toggle button - matches header.php structure
            const toggleButtons = document.querySelectorAll(
                '.menu-toggle, ' +
                '#mobile-menu-toggle, ' +
                'button[aria-label*="menu" i]'
            );
            
            // Find navigation element - matches header.php .main-nav class
            const navElements = document.querySelectorAll('.main-nav');
            
            if (toggleButtons.length === 0 || navElements.length === 0) {
                return; // No menu found, exit silently
            }
            
            // Add click handlers to toggle buttons
            toggleButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Toggle menu - matches theme .is-open and .active classes
                    navElements.forEach(function(nav) {
                        nav.classList.toggle('is-open');
                        nav.classList.toggle('active');
                        nav.setAttribute('aria-expanded', 
                            nav.classList.contains('is-open') ? 'true' : 'false');
                    });
                    
                    // Toggle button state
                    button.setAttribute('aria-expanded',
                        navElements[0].classList.contains('is-open') ? 'true' : 'false');
                });
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.main-nav') && 
                    !e.target.closest('.menu-toggle') && 
                    !e.target.closest('#mobile-menu-toggle')) {
                    navElements.forEach(function(nav) {
                        nav.classList.remove('is-open');
                        nav.classList.remove('active');
                        nav.setAttribute('aria-expanded', 'false');
                    });
                    toggleButtons.forEach(function(button) {
                        button.setAttribute('aria-expanded', 'false');
                    });
                }
            });
            
            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    navElements.forEach(function(nav) {
                        nav.classList.remove('is-open');
                        nav.classList.remove('active');
                        nav.setAttribute('aria-expanded', 'false');
                    });
                    toggleButtons.forEach(function(button) {
                        button.setAttribute('aria-expanded', 'false');
                    });
                }
            });
            
            // Close mobile menu when link is clicked
            const menuLinks = document.querySelectorAll('.main-nav a');
            menuLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        navElements.forEach(function(nav) {
                            nav.classList.remove('is-open');
                            nav.classList.remove('active');
                            nav.setAttribute('aria-expanded', 'false');
                        });
                        toggleButtons.forEach(function(button) {
                            button.setAttribute('aria-expanded', 'false');
                        });
                    }
                });
            });
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'freerideinvestor_menu_js_final', 99);

