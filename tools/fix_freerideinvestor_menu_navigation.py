#!/usr/bin/env python3
"""
Fix freerideinvestor.com Menu Navigation Issues
===============================================

Diagnoses and fixes menu navigation problems on freerideinvestor.com.
Checks for:
- Menu toggle button functionality
- Navigation link functionality
- CSS/JavaScript issues
- Mobile menu responsiveness

Agent-7: Web Development Specialist
Task: Fix menu navigation issues on freerideinvestor.com
"""

import json
import requests
from pathlib import Path
from datetime import datetime
import re

def get_site_info():
    """Get freerideinvestor.com site information."""
    project_root = Path(__file__).parent.parent
    registry_file = project_root / "configs" / "sites_registry.json"
    
    with open(registry_file, 'r', encoding='utf-8') as f:
        registry = json.load(f)
    
    return registry.get("freerideinvestor.com", {})

def fetch_site_html():
    """Fetch the site HTML to analyze menu structure."""
    url = "https://freerideinvestor.com"
    try:
        response = requests.get(url, timeout=10)
        if response.status_code == 200:
            return response.text
        else:
            print(f"❌ Failed to fetch site: HTTP {response.status_code}")
            return None
    except Exception as e:
        print(f"❌ Error fetching site: {e}")
        return None

def analyze_menu_structure(html):
    """Analyze the menu structure in HTML."""
    issues = []
    fixes_needed = []
    
    # Check for navigation menu
    has_nav = bool(re.search(r'<nav[^>]*>', html, re.IGNORECASE))
    if not has_nav:
        issues.append("No <nav> element found")
        fixes_needed.append("Add proper <nav> element")
    
    # Check for menu toggle button
    has_toggle = bool(re.search(r'toggle.*menu|hamburger|menu.*button', html, re.IGNORECASE))
    if not has_toggle:
        issues.append("No menu toggle button found")
        fixes_needed.append("Add menu toggle button for mobile")
    
    # Check for wp_nav_menu (WordPress menu)
    has_wp_menu = bool(re.search(r'wp_nav_menu|wp-menu', html, re.IGNORECASE))
    if not has_wp_menu:
        issues.append("WordPress menu function not detected")
        fixes_needed.append("Ensure wp_nav_menu() is used in theme")
    
    # Check for menu JavaScript
    has_menu_js = bool(re.search(r'menu.*toggle|toggle.*menu|\.menu|nav.*toggle', html, re.IGNORECASE))
    if not has_menu_js:
        issues.append("Menu toggle JavaScript not detected")
        fixes_needed.append("Add menu toggle JavaScript")
    
    # Check for proper menu structure
    menu_items = len(re.findall(r'<li[^>]*>.*?</li>', html, re.IGNORECASE | re.DOTALL))
    if menu_items < 4:
        issues.append(f"Only {menu_items} menu items found (expected at least 4)")
    
    return {
        "has_nav": has_nav,
        "has_toggle": has_toggle,
        "has_wp_menu": has_wp_menu,
        "has_menu_js": has_menu_js,
        "menu_items_count": menu_items,
        "issues": issues,
        "fixes_needed": fixes_needed
    }

def create_menu_fix_css():
    """Create CSS fixes for menu navigation."""
    css = """
/* freerideinvestor.com Menu Navigation Fixes */
/* Added: 2025-12-24 */

/* Ensure menu is visible and functional */
nav {
    display: block !important;
    visibility: visible !important;
}

/* Menu toggle button styling */
.menu-toggle,
button[aria-label*="menu"],
button[aria-label*="Menu"],
button[name*="menu"],
button[name*="Menu"] {
    display: block !important;
    cursor: pointer !important;
    background: transparent !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    color: inherit !important;
    padding: 0.5rem 1rem !important;
    z-index: 1000 !important;
}

.menu-toggle:hover,
button[aria-label*="menu"]:hover {
    background: rgba(255, 255, 255, 0.1) !important;
}

/* Menu list styling */
nav ul,
nav .menu,
.menu-primary,
.primary-menu {
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
    display: flex !important;
    flex-direction: column !important;
}

nav ul li,
nav .menu li,
.menu-primary li,
.primary-menu li {
    margin: 0 !important;
    padding: 0.5rem 0 !important;
}

nav ul li a,
nav .menu li a,
.menu-primary li a,
.primary-menu li a {
    display: block !important;
    padding: 0.5rem 1rem !important;
    text-decoration: none !important;
    color: inherit !important;
    transition: opacity 0.2s ease !important;
}

nav ul li a:hover,
nav .menu li a:hover {
    opacity: 0.8 !important;
}

/* Mobile menu toggle functionality */
@media (max-width: 768px) {
    nav ul,
    nav .menu {
        display: none !important;
    }
    
    nav.menu-open ul,
    nav.menu-open .menu,
    nav[aria-expanded="true"] ul,
    nav[aria-expanded="true"] .menu {
        display: flex !important;
        flex-direction: column !important;
    }
}

/* Desktop menu - horizontal */
@media (min-width: 769px) {
    nav ul,
    nav .menu {
        flex-direction: row !important;
        gap: 1rem !important;
    }
}

/* Ensure menu links are clickable */
nav a {
    pointer-events: auto !important;
    cursor: pointer !important;
}

/* Fix any z-index issues */
nav {
    position: relative !important;
    z-index: 100 !important;
}
"""
    return css

def create_menu_fix_javascript():
    """Create JavaScript fixes for menu navigation."""
    js = """
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
"""
    return js

def create_functions_php_fix():
    """Create PHP code to add menu fixes to functions.php."""
    php = """
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
"""
    return php

def main():
    """Main execution."""
    print("=" * 70)
    print("FREERIDEINVESTOR.COM MENU NAVIGATION FIX")
    print("=" * 70)
    print()
    
    # Get site info
    site_info = get_site_info()
    print(f"Site: freerideinvestor.com")
    print(f"URL: {site_info.get('url', 'https://freerideinvestor.com')}")
    print()
    
    # Fetch and analyze HTML
    print("Fetching site HTML...")
    html = fetch_site_html()
    if not html:
        print("❌ Could not fetch site HTML")
        return
    
    print("✅ Site HTML fetched")
    print()
    
    # Analyze menu structure
    print("Analyzing menu structure...")
    analysis = analyze_menu_structure(html)
    
    print(f"Navigation element: {'✅' if analysis['has_nav'] else '❌'}")
    print(f"Menu toggle button: {'✅' if analysis['has_toggle'] else '❌'}")
    print(f"WordPress menu: {'✅' if analysis['has_wp_menu'] else '❌'}")
    print(f"Menu JavaScript: {'✅' if analysis['has_menu_js'] else '❌'}")
    print(f"Menu items: {analysis['menu_items_count']}")
    print()
    
    if analysis['issues']:
        print("Issues found:")
        for issue in analysis['issues']:
            print(f"  - {issue}")
        print()
    
    # Generate fixes
    print("Generating fixes...")
    project_root = Path(__file__).parent.parent
    docs_dir = project_root / "docs" / "freerideinvestor"
    docs_dir.mkdir(parents=True, exist_ok=True)
    
    # Save CSS fix
    css_file = docs_dir / "freerideinvestor_menu_navigation_fix.css"
    with open(css_file, 'w', encoding='utf-8') as f:
        f.write(create_menu_fix_css())
    print(f"✅ CSS fix saved: {css_file}")
    
    # Save JavaScript fix
    js_file = docs_dir / "freerideinvestor_menu_navigation_fix.js"
    with open(js_file, 'w', encoding='utf-8') as f:
        f.write(create_menu_fix_javascript())
    print(f"✅ JavaScript fix saved: {js_file}")
    
    # Save PHP fix
    php_file = docs_dir / "freerideinvestor_menu_navigation_fix.php"
    with open(php_file, 'w', encoding='utf-8') as f:
        f.write(create_functions_php_fix())
    print(f"✅ PHP fix saved: {php_file}")
    
    # Create deployment instructions
    instructions = f"""
# freerideinvestor.com Menu Navigation Fix
Generated: {datetime.now().isoformat()}

## Issues Found
{chr(10).join('- ' + issue for issue in analysis['issues']) if analysis['issues'] else 'No critical issues found'}

## Fixes Generated

### 1. CSS Fix
File: `{css_file.relative_to(project_root)}`
- Ensures menu is visible and functional
- Adds responsive menu toggle styles
- Fixes mobile menu display

### 2. JavaScript Fix
File: `{js_file.relative_to(project_root)}`
- Adds menu toggle functionality
- Handles click events for menu button
- Closes menu when clicking outside

### 3. PHP Fix (for functions.php)
File: `{php_file.relative_to(project_root)}`
- Adds CSS and JavaScript via WordPress hooks
- Can be added to theme's functions.php

## Deployment Instructions

### Option 1: Add to functions.php (Recommended)
1. Open WordPress theme's functions.php file
2. Add the contents of `{php_file.name}` to the end of functions.php
3. Save and clear cache

### Option 2: Add CSS and JS separately
1. Add CSS from `{css_file.name}` to theme's style.css or via Customizer
2. Add JavaScript from `{js_file.name}` to theme's footer or via Customizer
3. Save and clear cache

## Testing
After deployment:
1. Test menu toggle button on mobile/tablet
2. Test navigation links (Home, Blog, About, Contact)
3. Test menu closes when clicking outside
4. Verify menu works on desktop and mobile

## Next Steps
- Deploy fixes to WordPress theme
- Test menu functionality
- Verify all navigation links work correctly
"""
    
    instructions_file = docs_dir / "freerideinvestor_menu_navigation_fix_INSTRUCTIONS.md"
    with open(instructions_file, 'w', encoding='utf-8') as f:
        f.write(instructions)
    print(f"✅ Instructions saved: {instructions_file}")
    
    print()
    print("=" * 70)
    print("✅ MENU NAVIGATION FIX GENERATED")
    print("=" * 70)
    print()
    print("Next steps:")
    print("1. Review the generated fixes in docs/freerideinvestor/")
    print("2. Deploy the PHP fix to WordPress theme's functions.php")
    print("3. Test menu navigation functionality")
    print("4. Clear WordPress cache")

if __name__ == "__main__":
    main()

