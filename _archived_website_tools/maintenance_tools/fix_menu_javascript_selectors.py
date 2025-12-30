#!/usr/bin/env python3
"""
Fix Menu JavaScript Selectors - freerideinvestor.com
====================================================

Updates the menu fix JavaScript to use more robust selectors that match
the actual HTML structure.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

def fix_javascript_selectors(deploy_file: Path) -> bool:
    """Fix JavaScript selectors to be more robust."""
    if not deploy_file.exists():
        print(f"❌ File not found: {deploy_file}")
        return False
    
    content = deploy_file.read_text(encoding="utf-8", errors="ignore")
    original_content = content
    
    # Update the selector to be more flexible
    # Change from strict check to more flexible approach
    old_pattern = r"if \(toggleButtons\.length === 0 \|\| navElements\.length === 0\) \{[^}]+console\.warn\('Menu toggle button or nav element not found'\);[\s\S]*?return;[\s\S]*?\}"
    
    new_code = """if (toggleButtons.length === 0 && navElements.length === 0) {
                // If neither found, try alternative selectors
                console.warn('Menu toggle button or nav element not found, trying alternative selectors');
                
                // Try alternative selectors
                const altToggleButtons = document.querySelectorAll('button[aria-label*="Toggle" i], button[aria-label*="menu" i]');
                const altNavElements = document.querySelectorAll('nav, [role="navigation"]');
                
                if (altToggleButtons.length > 0 && altNavElements.length > 0) {
                    // Use alternative selectors
                    setupMenuHandlers(altToggleButtons, altNavElements);
                    return;
                }
                
                // If still not found, just return (menu might be handled by theme)
                return;
            }"""
    
    # More flexible approach - handle cases where one might exist but not the other
    better_fix = """
            // More flexible selector matching
            let toggleBtn = null;
            let navEl = null;
            
            // Try multiple selector patterns for toggle button
            const toggleSelectors = [
                '.menu-toggle',
                '#mobile-menu-toggle',
                'button[aria-label*="Toggle" i]',
                'button[aria-label*="menu" i]',
                'button[name*="menu" i]',
                'button[class*="toggle"]'
            ];
            
            for (const selector of toggleSelectors) {
                const btn = document.querySelector(selector);
                if (btn) {
                    toggleBtn = btn;
                    break;
                }
            }
            
            // Try multiple selector patterns for nav
            const navSelectors = [
                '.main-nav',
                'nav.main-nav',
                '[role="navigation"]',
                'nav'
            ];
            
            for (const selector of navSelectors) {
                const nav = document.querySelector(selector);
                if (nav) {
                    navEl = nav;
                    break;
                }
            }
            
            // Setup handlers if we found the elements
            if (toggleBtn && navEl) {
                setupMenuHandlers([toggleBtn], [navEl]);
            } else {
                // Silently fail - menu might be handled by theme or CSS-only
                console.debug('Menu toggle not initialized - may be handled by theme');
            }
            """
    
    # Find the initMenu function and replace the problematic check
    # Look for the pattern more carefully
    pattern_to_find = r"if \(toggleButtons\.length === 0 \|\| navElements\.length === 0\) \{"
    
    if re.search(pattern_to_find, content):
        # Replace with more flexible version
        new_init = """function initMenu() {
            // Find menu toggle button - matches theme structure
            const toggleButtons = document.querySelectorAll(
                '.menu-toggle, ' +
                '#mobile-menu-toggle, ' +
                'button[aria-label*="Toggle" i], ' +
                'button[aria-label*="menu" i], ' +
                'button[name*="menu" i]'
            );
            
            // Find navigation element - matches theme .main-nav class
            const navElements = document.querySelectorAll('.main-nav, nav.main-nav, [role="navigation"]');
            
            // Setup menu handlers function
            function setupMenuHandlers(buttons, navs) {
                buttons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        navs.forEach(function(nav) {
                            nav.classList.toggle('is-open');
                            nav.classList.toggle('menu-open');
                            const isOpen = nav.classList.contains('is-open');
                            nav.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        });
                    });
                });
                
                // Close menu when clicking menu links (mobile)
                navs.forEach(function(nav) {
                    const links = nav.querySelectorAll('a');
                    links.forEach(function(link) {
                        link.addEventListener('click', function() {
                            if (window.innerWidth <= 768) {
                                nav.classList.remove('is-open');
                                nav.classList.remove('menu-open');
                                nav.setAttribute('aria-expanded', 'false');
                            }
                        });
                    });
                });
                
                // Close menu when pressing Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        navs.forEach(function(nav) {
                            nav.classList.remove('is-open');
                            nav.classList.remove('menu-open');
                            nav.setAttribute('aria-expanded', 'false');
                        });
                    }
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    const clickedInsideNav = e.target.closest('.main-nav') || 
                                             e.target.closest('nav') ||
                                             Array.from(buttons).some(btn => btn.contains(e.target));
                    if (!clickedInsideNav) {
                        navs.forEach(function(nav) {
                            nav.classList.remove('is-open');
                            nav.classList.remove('menu-open');
                            nav.setAttribute('aria-expanded', 'false');
                        });
                    }
                });
            }
            
            // Setup handlers if elements found
            if (toggleButtons.length > 0 && navElements.length > 0) {
                setupMenuHandlers(toggleButtons, navElements);
            } else {
                // Try to find elements with more flexible selectors
                const altToggle = document.querySelector('button[aria-label*="Toggle" i], button[aria-label*="menu" i]');
                const altNav = document.querySelector('nav, [role="navigation"]');
                if (altToggle && altNav) {
                    setupMenuHandlers([altToggle], [altNav]);
                }
                // If still not found, menu may be CSS-only or handled by theme
            }
        }"""
        
        # Replace the entire initMenu function
        old_init_pattern = r"function initMenu\(\) \{[\s\S]*?\n        \}"
        content = re.sub(old_init_pattern, new_init, content, flags=re.MULTILINE)
    
    if content != original_content:
        deploy_file.write_text(content, encoding="utf-8")
        print(f"✅ Updated JavaScript selectors to be more robust")
        return True
    else:
        print(f"⚠️  No changes needed or pattern not found")
        return False

def main():
    project_root = Path(__file__).parent.parent
    deploy_file = project_root / "docs" / "freerideinvestor" / "freerideinvestor_menu_fix_DEPLOY.php"
    
    if fix_javascript_selectors(deploy_file):
        print(f"\n✅ JavaScript selectors fixed")
        print(f"📋 Next: Redeploy the updated fix")
    else:
        print(f"\n⚠️  Fix may not have been applied")

if __name__ == "__main__":
    sys.exit(0 if main() else 1)

