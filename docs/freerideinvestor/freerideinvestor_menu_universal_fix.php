/**
 * freerideinvestor.com Menu Navigation - UNIVERSAL Theme-Consistent Styling
 * =========================================================================
 * 
 * This fix ensures menu navigation styling matches the theme EXACTLY
 * across ALL pages, overriding any page-specific styles with maximum specificity.
 * 
 * Uses !important strategically to ensure consistency across all pages.
 * Matches: css/styles/components/_navigation.css AND style.css
 * 
 * Added: 2025-12-22
 * Priority: Maximum specificity to override conflicting styles
 */

// Add universal theme-consistent menu navigation CSS
function freerideinvestor_menu_universal_theme_css() {
    ?>
    <style id="freerideinvestor-menu-universal-theme">
    /* ============================================
       UNIVERSAL MENU NAVIGATION - THEME CONSISTENT
       Maximum specificity to override page-specific styles
       ============================================ */
    
    /* Navigation Container - Universal selector */
    header .main-nav,
    .site-header .main-nav,
    .header-content .main-nav,
    nav.main-nav,
    .main-nav {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
    }
    
    /* Navigation List - Universal with maximum specificity */
    header .main-nav .nav-list,
    .site-header .main-nav .nav-list,
    .header-content .main-nav .nav-list,
    nav.main-nav .nav-list,
    .main-nav .nav-list,
    .main-nav ul,
    .main-nav .menu {
        list-style: none !important;
        display: flex !important;
        gap: var(--spacing-sm, 1rem) !important;
        padding: 0 !important;
        margin: 0 !important;
        flex-wrap: wrap !important;
        align-items: center !important;
    }
    
    /* Navigation Links - Universal with maximum specificity */
    header .main-nav .nav-list li a,
    .site-header .main-nav .nav-list li a,
    .header-content .main-nav .nav-list li a,
    nav.main-nav .nav-list li a,
    .main-nav .nav-list li a,
    .main-nav ul li a,
    .main-nav .menu li a {
        display: inline-block !important;
        padding: var(--spacing-xs, 0.5rem) var(--spacing-sm, 1rem) !important;
        border-radius: 4px !important;
        /* Support all theme variable naming conventions */
        color: var(--color-text-base, var(--text-secondary, var(--text-primary, #4a4a4a))) !important;
        text-decoration: none !important;
        font-weight: 600 !important;
        font-size: 1rem !important;
        line-height: 1.5 !important;
        transition: background-color 0.2s ease, color 0.2s ease !important;
    }
    
    /* Hover States - Universal with maximum specificity */
    header .main-nav .nav-list li a:hover,
    .site-header .main-nav .nav-list li a:hover,
    .header-content .main-nav .nav-list li a:hover,
    nav.main-nav .nav-list li a:hover,
    .main-nav .nav-list li a:hover,
    .main-nav ul li a:hover,
    .main-nav .menu li a:hover,
    header .main-nav .nav-list li a:focus,
    .site-header .main-nav .nav-list li a:focus,
    .header-content .main-nav .nav-list li a:focus,
    nav.main-nav .nav-list li a:focus,
    .main-nav .nav-list li a:focus,
    .main-nav ul li a:focus,
    .main-nav .menu li a:focus {
        background: var(--color-nav-hover-bg, rgba(0, 102, 255, 0.1)) !important;
        /* Maintain text color on hover (theme pattern) */
        color: var(--color-text-base, var(--text-secondary, var(--text-primary, #4a4a4a))) !important;
        outline: 2px solid var(--color-accent, var(--primary-blue, #0066ff)) !important;
        outline-offset: 2px !important;
        text-decoration: none !important;
    }
    
    /* Active Link - Universal */
    header .main-nav .nav-list li a.active,
    .site-header .main-nav .nav-list li a.active,
    .header-content .main-nav .nav-list li a.active,
    nav.main-nav .nav-list li a.active,
    .main-nav .nav-list li a.active,
    .main-nav ul li a.active,
    .main-nav .menu li a.active,
    header .main-nav .nav-list li.current-menu-item > a,
    .site-header .main-nav .nav-list li.current-menu-item > a,
    .header-content .main-nav .nav-list li.current-menu-item > a,
    nav.main-nav .nav-list li.current-menu-item > a,
    .main-nav .nav-list li.current-menu-item > a {
        background: var(--color-accent, var(--primary-blue, #0066ff)) !important;
        color: var(--color-background, var(--bg-primary, #ffffff)) !important;
        font-weight: 700 !important;
    }
    
    /* Menu Toggle Button - Universal */
    header .main-nav .menu-toggle,
    .site-header .main-nav .menu-toggle,
    .header-content .main-nav .menu-toggle,
    nav.main-nav .menu-toggle,
    .main-nav .menu-toggle,
    header .main-nav #mobile-menu-toggle,
    .site-header .main-nav #mobile-menu-toggle,
    .header-content .main-nav #mobile-menu-toggle,
    nav.main-nav #mobile-menu-toggle,
    .main-nav #mobile-menu-toggle {
        display: none !important;
        background: none !important;
        border: none !important;
        font-size: 1.5rem !important;
        cursor: pointer !important;
        padding: var(--spacing-xs, 0.5rem) !important;
        color: var(--color-text-base, var(--text-primary, #1a1a1a)) !important;
        transition: color 0.2s ease !important;
    }
    
    header .main-nav .menu-toggle:hover,
    .site-header .main-nav .menu-toggle:hover,
    .header-content .main-nav .menu-toggle:hover,
    nav.main-nav .menu-toggle:hover,
    .main-nav .menu-toggle:hover,
    header .main-nav #mobile-menu-toggle:hover,
    .site-header .main-nav #mobile-menu-toggle:hover,
    .header-content .main-nav #mobile-menu-toggle:hover,
    nav.main-nav #mobile-menu-toggle:hover,
    .main-nav #mobile-menu-toggle:hover {
        color: var(--color-accent, var(--primary-blue, #0066ff)) !important;
    }
    
    /* ============================================
       RESPONSIVE - Mobile Menu
       Universal across all pages
       ============================================ */
    @media (max-width: 768px) {
        /* Show menu toggle on mobile */
        header .main-nav .menu-toggle,
        .site-header .main-nav .menu-toggle,
        .header-content .main-nav .menu-toggle,
        nav.main-nav .menu-toggle,
        .main-nav .menu-toggle,
        header .main-nav #mobile-menu-toggle,
        .site-header .main-nav #mobile-menu-toggle,
        .header-content .main-nav #mobile-menu-toggle,
        nav.main-nav #mobile-menu-toggle,
        .main-nav #mobile-menu-toggle {
            display: block !important;
        }
        
        /* Navigation List Mobile - Universal */
        header .main-nav .nav-list,
        .site-header .main-nav .nav-list,
        .header-content .main-nav .nav-list,
        nav.main-nav .nav-list,
        .main-nav .nav-list,
        .main-nav ul,
        .main-nav .menu {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: var(--spacing-xs, 0.5rem) !important;
            display: none !important;
            width: 100% !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            background: var(--color-dark-grey, var(--bg-primary, #ffffff)) !important;
            padding: var(--spacing-sm, 1rem) !important;
            border-radius: 4px !important;
            margin-top: 10px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            z-index: 999 !important;
        }
        
        /* Show navigation when menu is open - Universal */
        header .main-nav.is-open .nav-list,
        .site-header .main-nav.is-open .nav-list,
        .header-content .main-nav.is-open .nav-list,
        nav.main-nav.is-open .nav-list,
        .main-nav.is-open .nav-list,
        header .main-nav.menu-open .nav-list,
        .site-header .main-nav.menu-open .nav-list,
        .header-content .main-nav.menu-open .nav-list,
        nav.main-nav.menu-open .nav-list,
        .main-nav.menu-open .nav-list,
        header .main-nav[aria-expanded="true"] .nav-list,
        .site-header .main-nav[aria-expanded="true"] .nav-list,
        .header-content .main-nav[aria-expanded="true"] .nav-list,
        nav.main-nav[aria-expanded="true"] .nav-list,
        .main-nav[aria-expanded="true"] .nav-list,
        header .main-nav .nav-list.active,
        .site-header .main-nav .nav-list.active,
        .header-content .main-nav .nav-list.active,
        nav.main-nav .nav-list.active,
        .main-nav .nav-list.active {
            display: flex !important;
        }
        
        /* Mobile menu link styling - Universal */
        header .main-nav .nav-list li a,
        .site-header .main-nav .nav-list li a,
        .header-content .main-nav .nav-list li a,
        nav.main-nav .nav-list li a,
        .main-nav .nav-list li a,
        .main-nav ul li a,
        .main-nav .menu li a {
            width: 100% !important;
            text-align: left !important;
            padding: var(--spacing-xs, 0.5rem) var(--spacing-sm, 1rem) !important;
        }
    }
    
    /* Very Small Screens - Universal */
    @media (max-width: 480px) {
        header .main-nav .nav-list,
        .site-header .main-nav .nav-list,
        .header-content .main-nav .nav-list,
        nav.main-nav .nav-list,
        .main-nav .nav-list {
            gap: var(--spacing-xs, 0.5rem) !important;
        }
        
        header .main-nav .nav-list li a,
        .site-header .main-nav .nav-list li a,
        .header-content .main-nav .nav-list li a,
        nav.main-nav .nav-list li a,
        .main-nav .nav-list li a {
            font-size: 0.9rem !important;
            padding: var(--spacing-xs, 0.5rem) 0.75rem !important;
        }
    }
    
    /* Desktop - Ensure horizontal layout - Universal */
    @media (min-width: 769px) {
        header .main-nav .menu-toggle,
        .site-header .main-nav .menu-toggle,
        .header-content .main-nav .menu-toggle,
        nav.main-nav .menu-toggle,
        .main-nav .menu-toggle,
        header .main-nav #mobile-menu-toggle,
        .site-header .main-nav #mobile-menu-toggle,
        .header-content .main-nav #mobile-menu-toggle,
        nav.main-nav #mobile-menu-toggle,
        .main-nav #mobile-menu-toggle {
            display: none !important;
        }
        
        header .main-nav .nav-list,
        .site-header .main-nav .nav-list,
        .header-content .main-nav .nav-list,
        nav.main-nav .nav-list,
        .main-nav .nav-list,
        .main-nav ul,
        .main-nav .menu {
            display: flex !important;
            flex-direction: row !important;
        }
    }
    
    /* Ensure menu links are clickable - Universal */
    header .main-nav a,
    .site-header .main-nav a,
    .header-content .main-nav a,
    nav.main-nav a,
    .main-nav a {
        pointer-events: auto !important;
        cursor: pointer !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'freerideinvestor_menu_universal_theme_css', 999); /* Very high priority to override everything */

// JavaScript remains the same (already correct)
function freerideinvestor_menu_universal_theme_js() {
    ?>
    <script id="freerideinvestor-menu-universal-js">
    (function() {
        'use strict';
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMenu);
        } else {
            initMenu();
        }
        
        function initMenu() {
            // Find menu toggle button - universal selectors
            let toggleButtons = document.querySelectorAll(
                '.main-nav .menu-toggle, ' +
                '.main-nav #mobile-menu-toggle, ' +
                'button[aria-label*="Toggle" i], ' +
                'button[aria-label*="menu" i]'
            );
            
            // Find navigation element - universal selectors
            let navElements = document.querySelectorAll('.main-nav, nav.main-nav, [role="navigation"]');
            
            // If not found, try alternative selectors
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
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    navElements.forEach(function(nav) {
                        nav.classList.toggle('is-open');
                        nav.classList.toggle('menu-open');
                        nav.setAttribute('aria-expanded', 
                            nav.classList.contains('is-open') ? 'true' : 'false');
                        
                        const navList = nav.querySelector('.nav-list');
                        if (navList) {
                            navList.classList.toggle('active');
                        }
                    });
                    
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
            
            console.log('Menu navigation initialized (universal theme-consistent)');
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'freerideinvestor_menu_universal_theme_js', 99);

