/**
 * freerideinvestor.com Menu Navigation Fixes - Theme-Styled Version
 * Added: 2025-12-24
 * Matches existing theme style.css variables and class structure
 */

// Add menu navigation CSS that matches theme style
function freerideinvestor_menu_css_styled() {
    ?>
    <style id="freerideinvestor-menu-css-styled">
    /* Menu Navigation Fixes - Matches Theme Style */
    /* Uses theme CSS variables for consistency */
    
    /* Ensure main navigation is visible and properly styled */
    .main-nav {
        display: flex !important;
        align-items: center !important;
        gap: var(--spacing-lg, 1.5rem) !important;
    }
    
    /* Navigation list styling - matches theme .nav-menu style */
    .main-nav .nav-list,
    .main-nav ul,
    .main-nav .menu {
        display: flex !important;
        list-style: none !important;
        gap: var(--spacing-lg, 1.5rem) !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Navigation links - matches theme .nav-menu li a style */
    .main-nav .nav-list li a,
    .main-nav ul li a,
    .main-nav .menu li a {
        color: var(--text-secondary, #4a4a4a) !important;
        font-weight: 500 !important;
        padding: var(--spacing-xs, 0.5rem) 0 !important;
        text-decoration: none !important;
        transition: color var(--transition-fast, 150ms ease) !important;
    }
    
    /* Navigation link hover - matches theme primary blue */
    .main-nav .nav-list li a:hover,
    .main-nav ul li a:hover,
    .main-nav .menu li a:hover {
        color: var(--primary-blue, #0066ff) !important;
        text-decoration: none !important;
    }
    
    /* Menu toggle button - matches theme style */
    .menu-toggle,
    #mobile-menu-toggle,
    button[aria-label*="menu" i],
    button[name*="menu" i] {
        display: none !important; /* Hidden on desktop, shown on mobile */
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
        
        /* Hide navigation by default on mobile */
        .main-nav .nav-list,
        .main-nav ul,
        .main-nav .menu {
            display: none !important;
            flex-direction: column !important;
            width: 100% !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            background: var(--bg-primary, #ffffff) !important;
            box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1)) !important;
            padding: var(--spacing-md, 1.5rem) !important;
            gap: var(--spacing-sm, 1rem) !important;
            z-index: 999 !important;
        }
        
        /* Show navigation when menu is open */
        .main-nav.is-open .nav-list,
        .main-nav.is-open ul,
        .main-nav.is-open .menu,
        .main-nav.menu-open .nav-list,
        .main-nav.menu-open ul,
        .main-nav.menu-open .menu,
        .main-nav[aria-expanded="true"] .nav-list,
        .main-nav[aria-expanded="true"] ul,
        .main-nav[aria-expanded="true"] .menu {
            display: flex !important;
        }
        
        /* Mobile menu link styling */
        .main-nav .nav-list li,
        .main-nav ul li,
        .main-nav .menu li {
            width: 100% !important;
            text-align: left !important;
        }
        
        .main-nav .nav-list li a,
        .main-nav ul li a,
        .main-nav .menu li a {
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
        .main-nav ul,
        .main-nav .menu {
            display: flex !important;
            flex-direction: row !important;
        }
    }
    
    /* Dark mode support - if theme uses dark mode */
    @media (prefers-color-scheme: dark) {
        .main-nav .nav-list li a,
        .main-nav ul li a,
        .main-nav .menu li a {
            color: var(--dark-text-secondary, #c9d1d9) !important;
        }
        
        .main-nav .nav-list li a:hover,
        .main-nav ul li a:hover,
        .main-nav .menu li a:hover {
            color: var(--primary-blue-light, #3385ff) !important;
        }
        
        .menu-toggle,
        #mobile-menu-toggle {
            color: var(--dark-text-primary, #f0f6fc) !important;
        }
        
        @media (max-width: 768px) {
            .main-nav .nav-list,
            .main-nav ul,
            .main-nav .menu {
                background: var(--dark-bg-primary, #0d1117) !important;
            }
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
add_action('wp_head', 'freerideinvestor_menu_css_styled', 99);

// Add menu navigation JavaScript that matches theme structure
function freerideinvestor_menu_js_styled() {
    ?>
    <script id="freerideinvestor-menu-js-styled">
    (function() {
        'use strict';
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMenu);
        } else {
            initMenu();
        }
        
        function initMenu() {
            // Find menu toggle button - matches theme structure
            const toggleButtons = document.querySelectorAll(
                '.menu-toggle, ' +
                '#mobile-menu-toggle, ' +
                'button[aria-label*="menu" i], ' +
                'button[name*="menu" i]'
            );
            
            // Find navigation element - matches theme .main-nav class
            const navElements = document.querySelectorAll('.main-nav, nav.main-nav, [role="navigation"]');
            
            if (toggleButtons.length === 0 || navElements.length === 0) {
                console.warn('Menu toggle button or nav element not found');
                return;
            }
            
            // Add click handlers to toggle buttons
            toggleButtons.forEach(function(button) {
                // Remove any existing listeners to avoid duplicates
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Toggle menu on all nav elements - matches theme .is-open class
                    navElements.forEach(function(nav) {
                        nav.classList.toggle('is-open');
                        nav.classList.toggle('menu-open');
                        nav.setAttribute('aria-expanded', 
                            nav.classList.contains('is-open') ? 'true' : 'false');
                    });
                    
                    // Toggle button state
                    newButton.classList.toggle('active');
                    newButton.setAttribute('aria-expanded',
                        newButton.classList.contains('active') ? 'true' : 'false');
                });
            });
            
            // Close menu when clicking outside - matches theme behavior
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.main-nav') && 
                    !e.target.closest('.menu-toggle') && 
                    !e.target.closest('#mobile-menu-toggle')) {
                    navElements.forEach(function(nav) {
                        nav.classList.remove('is-open');
                        nav.classList.remove('menu-open');
                        nav.setAttribute('aria-expanded', 'false');
                    });
                    toggleButtons.forEach(function(button) {
                        button.classList.remove('active');
                        button.setAttribute('aria-expanded', 'false');
                    });
                }
            });
            
            // Close menu on escape key - matches theme behavior
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    navElements.forEach(function(nav) {
                        nav.classList.remove('is-open');
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
            const menuLinks = document.querySelectorAll('.main-nav a, nav.main-nav a, [role="navigation"] a');
            menuLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Close mobile menu when link is clicked
                    if (window.innerWidth <= 768) {
                        navElements.forEach(function(nav) {
                            nav.classList.remove('is-open');
                            nav.classList.remove('menu-open');
                            nav.setAttribute('aria-expanded', 'false');
                        });
                        toggleButtons.forEach(function(button) {
                            button.classList.remove('active');
                            button.setAttribute('aria-expanded', 'false');
                        });
                    }
                });
            });
            
            console.log('Menu navigation initialized (theme-styled)');
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'freerideinvestor_menu_js_styled', 99);

