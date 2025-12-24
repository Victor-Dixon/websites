
/**
 * freerideinvestor.com Menu Navigation Fixes
 * Added: 2025-12-24
 */

// Add menu navigation CSS
function freerideinvestor_menu_css() {
    ?>
    <style id="freerideinvestor-menu-css">
    /* Menu Navigation Fixes */
    nav {
        display: block !important;
        visibility: visible !important;
    }
    
    .menu-toggle,
    button[aria-label*="menu"],
    button[name*="menu"] {
        display: block !important;
        cursor: pointer !important;
        background: transparent !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: inherit !important;
        padding: 0.5rem 1rem !important;
        z-index: 1000 !important;
    }
    
    nav ul,
    nav .menu {
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    nav ul li a,
    nav .menu li a {
        display: block !important;
        padding: 0.5rem 1rem !important;
        text-decoration: none !important;
        color: inherit !important;
    }
    
    @media (max-width: 768px) {
        nav ul,
        nav .menu {
            display: none !important;
        }
        
        nav.menu-open ul,
        nav.menu-open .menu {
            display: flex !important;
            flex-direction: column !important;
        }
    }
    
    @media (min-width: 769px) {
        nav ul,
        nav .menu {
            display: flex !important;
            flex-direction: row !important;
            gap: 1rem !important;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'freerideinvestor_menu_css', 99);

// Add menu navigation JavaScript
function freerideinvestor_menu_js() {
    ?>
    <script id="freerideinvestor-menu-js">
    (function() {
        'use strict';
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMenu);
        } else {
            initMenu();
        }
        
        function initMenu() {
            const toggleButtons = document.querySelectorAll(
                'button[aria-label*="menu" i], ' +
                'button[name*="menu" i], ' +
                '.menu-toggle'
            );
            const navElements = document.querySelectorAll('nav, [role="navigation"]');
            
            if (toggleButtons.length === 0 || navElements.length === 0) return;
            
            toggleButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    navElements.forEach(function(nav) {
                        nav.classList.toggle('menu-open');
                        nav.setAttribute('aria-expanded', 
                            nav.classList.contains('menu-open') ? 'true' : 'false');
                    });
                    button.classList.toggle('active');
                });
            });
            
            document.addEventListener('click', function(e) {
                if (!e.target.closest('nav') && !e.target.closest('button[aria-label*="menu" i]')) {
                    navElements.forEach(function(nav) {
                        nav.classList.remove('menu-open');
                        nav.setAttribute('aria-expanded', 'false');
                    });
                    toggleButtons.forEach(function(button) {
                        button.classList.remove('active');
                    });
                }
            });
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'freerideinvestor_menu_js', 99);
