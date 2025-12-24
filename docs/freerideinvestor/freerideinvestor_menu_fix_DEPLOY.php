/**
 * freerideinvestor.com Menu Navigation - Theme-Consistent Styling
 * ===============================================================
 * 
 * This fix ensures menu navigation styling matches the theme EXACTLY
 * across all pages, using the theme's component CSS patterns.
 * 
 * Matches: css/styles/components/_navigation.css
 * Header uses: .main-nav and .nav-list
 * 
 * Added: 2025-12-22
 */

// Add theme-consistent menu navigation CSS
function freerideinvestor_menu_theme_consistent_css() {
    ?>
    <style id="freerideinvestor-menu-theme-consistent">
    /* ============================================
       MENU NAVIGATION - THEME CONSISTENT STYLING
       Matches: css/styles/components/_navigation.css
       ============================================ */
    
    /* Navigation Container - matches theme .main-nav */
    .main-nav {
        display: flex !important;
        align-items: center !important;
    }
    
    /* Navigation List Styling - EXACT match to theme component CSS */
    .main-nav .nav-list {
        list-style: none !important;
        display: flex !important;
        gap: var(--spacing-sm, 1rem) !important; /* Theme uses --spacing-sm, not --spacing-lg */
        padding: 0 !important;
        margin: 0 !important;
        flex-wrap: wrap !important;
        align-items: center !important;
    }
    
    /* Navigation Links - EXACT match to theme component CSS */
    .main-nav .nav-list li a {
        display: inline-block !important;
        padding: var(--spacing-xs, 0.5rem) var(--spacing-sm, 1rem) !important; /* Theme has horizontal padding */
        border-radius: 4px !important;
        color: var(--color-text-base, var(--text-secondary, #4a4a4a)) !important; /* Theme uses --color-text-base */
        text-decoration: none !important;
        font-weight: 600 !important; /* Theme uses 600, not 500 */
        transition: background-color var(--transition-fast, 0.2s ease), 
                    color var(--transition-fast, 0.2s ease) !important;
    }
    
    /* Hover and Focus States - EXACT match to theme component CSS */
    .main-nav .nav-list li a:hover,
    .main-nav .nav-list li a:focus {
        background: var(--color-nav-hover-bg, rgba(255, 255, 255, 0.1)) !important;
        color: var(--color-text-base, var(--text-secondary, #4a4a4a)) !important; /* Theme maintains text color on hover */
        outline: 2px solid var(--color-accent, var(--primary-blue, #0066ff)) !important;
        outline-offset: 2px !important;
    }
    
    /* Active Navigation Link - matches theme component CSS */
    .main-nav .nav-list li a.active {
        background: var(--color-accent, var(--primary-blue, #0066ff)) !important;
        color: var(--color-background, var(--bg-primary, #ffffff)) !important;
        font-weight: 700 !important;
    }
    
    /* Menu Toggle Button - consistent styling */
    .main-nav .menu-toggle,
    .main-nav #mobile-menu-toggle,
    .main-nav button[aria-label*="Toggle" i],
    .main-nav button[aria-label*="menu" i] {
        display: none !important; /* Hidden on desktop */
        background: none !important;
        border: none !important;
        font-size: 1.5rem !important;
        cursor: pointer !important;
        padding: var(--spacing-xs, 0.5rem) !important;
        color: var(--color-text-base, var(--text-primary, #1a1a1a)) !important;
        transition: color var(--transition-fast, 0.2s ease) !important;
    }
    
    .main-nav .menu-toggle:hover,
    .main-nav #mobile-menu-toggle:hover {
        color: var(--color-accent, var(--primary-blue, #0066ff)) !important;
    }
    
    /* ============================================
       RESPONSIVE - Mobile Menu
       Matches theme responsive patterns
       ============================================ */
    @media (max-width: 768px) {
        /* Show menu toggle on mobile */
        .main-nav .menu-toggle,
        .main-nav #mobile-menu-toggle {
            display: block !important;
        }
        
        /* Navigation List for Smaller Screens - EXACT match to theme component CSS */
        .main-nav .nav-list {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: var(--spacing-xs, 0.5rem) !important;
            display: none !important; /* Hidden by default on mobile */
            width: 100% !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            background: var(--color-dark-grey, var(--bg-primary, #ffffff)) !important;
            padding: var(--spacing-sm, 1rem) !important;
            border-radius: 4px !important;
            margin-top: 10px !important;
            box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1)) !important;
            z-index: 999 !important;
        }
        
        /* Show navigation when menu is open */
        .main-nav.is-open .nav-list,
        .main-nav.menu-open .nav-list,
        .main-nav[aria-expanded="true"] .nav-list,
        .main-nav .nav-list.active {
            display: flex !important;
        }
        
        /* Mobile menu link styling - EXACT match to theme component CSS */
        .main-nav .nav-list li a {
            width: 100% !important;
            text-align: left !important;
            padding: var(--spacing-xs, 0.5rem) var(--spacing-sm, 1rem) !important;
        }
    }
    
    /* Optional: Adjust for Very Small Screens - matches theme component CSS */
    @media (max-width: 480px) {
        .main-nav .nav-list {
            gap: var(--spacing-xs, 0.5rem) !important;
        }
        
        .main-nav .nav-list li a {
            font-size: 0.9rem !important;
            padding: var(--spacing-xs, 0.5rem) var(--spacing-sm, 0.75rem) !important;
        }
    }
    
    /* Desktop menu - horizontal layout */
    @media (min-width: 769px) {
        .main-nav .menu-toggle,
        .main-nav #mobile-menu-toggle {
            display: none !important;
        }
        
        .main-nav .nav-list {
            display: flex !important;
            flex-direction: row !important;
        }
    }
    
    /* Ensure menu links are clickable */
    .main-nav a {
        pointer-events: auto !important;
        cursor: pointer !important;
    }
    
    /* Fallback for theme variables that might not be defined */
    /* These match the theme's style.css root variables */
    </style>
    <?php
}
add_action('wp_head', 'freerideinvestor_menu_theme_consistent_css', 100); /* Higher priority to ensure it loads after theme CSS */

// JavaScript remains the same (already fixed)
// Using the corrected JavaScript from the previous fix
function freerideinvestor_menu_theme_consistent_js() {
    ?>
    <script id="freerideinvestor-menu-theme-consistent-js">
    (function() {
        'use strict';
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMenu);
        } else {
            initMenu();
        }
        
        function initMenu() {
            // Find menu toggle button - matches theme structure
            let toggleButtons = document.querySelectorAll(
                '.main-nav .menu-toggle, ' +
                '.main-nav #mobile-menu-toggle, ' +
                '.main-nav button[aria-label*="Toggle" i], ' +
                '.main-nav button[aria-label*="menu" i]'
            );
            
            // Find navigation element - matches theme .main-nav class
            let navElements = document.querySelectorAll('.main-nav');
            
            // If not found, try alternative selectors as fallback
            if (toggleButtons.length === 0 || navElements.length === 0) {
                const altToggle = document.querySelector('button[aria-label*="Toggle" i], button[aria-label*="menu" i]');
                const altNav = document.querySelector('.main-nav, nav');
                if (altToggle && altNav) {
                    toggleButtons = [altToggle];
                    navElements = [altNav];
                } else {
                    console.debug('Menu toggle not initialized - may be handled by theme');
                    return;
                }
            }
            
            // Add click handlers to toggle buttons
            toggleButtons.forEach(function(button) {
                // Clone button to remove existing listeners
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
                        
                        // Also toggle .active class on nav-list (theme uses .active)
                        const navList = nav.querySelector('.nav-list');
                        if (navList) {
                            navList.classList.toggle('active');
                        }
                    });
                    
                    // Toggle button state
                    newButton.classList.toggle('active');
                    newButton.setAttribute('aria-expanded',
                        newButton.classList.contains('active') ? 'true' : 'false');
                });
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                const clickedInsideNav = e.target.closest('.main-nav');
                if (!clickedInsideNav) {
                    navElements.forEach(function(nav) {
                        nav.classList.remove('is-open');
                        nav.classList.remove('menu-open');
                        nav.setAttribute('aria-expanded', 'false');
                        const navList = nav.querySelector('.nav-list');
                        if (navList) {
                            navList.classList.remove('active');
                        }
                    });
                    toggleButtons.forEach(function(button) {
                        button.classList.remove('active');
                        button.setAttribute('aria-expanded', 'false');
                    });
                }
            });
            
            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    navElements.forEach(function(nav) {
                        nav.classList.remove('is-open');
                        nav.classList.remove('menu-open');
                        nav.setAttribute('aria-expanded', 'false');
                        const navList = nav.querySelector('.nav-list');
                        if (navList) {
                            navList.classList.remove('active');
                        }
                    });
                    toggleButtons.forEach(function(button) {
                        button.classList.remove('active');
                        button.setAttribute('aria-expanded', 'false');
                    });
                }
            });
            
            // Close menu when clicking menu links (mobile)
            navElements.forEach(function(nav) {
                const links = nav.querySelectorAll('.nav-list a');
                links.forEach(function(link) {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            nav.classList.remove('is-open');
                            nav.classList.remove('menu-open');
                            nav.setAttribute('aria-expanded', 'false');
                            const navList = nav.querySelector('.nav-list');
                            if (navList) {
                                navList.classList.remove('active');
                            }
                        }
                    });
                });
            });
            
            console.log('Menu navigation initialized (theme-consistent)');
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'freerideinvestor_menu_theme_consistent_js', 99);

