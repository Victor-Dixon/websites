/**
 * Mobile Navigation Menu Toggle
 * Author: Agent-3 (Infrastructure & DevOps)
 * Date: October 27, 2025
 * Purpose: Hamburger menu functionality for mobile responsiveness
 */

(function() {
  'use strict';
  
  /**
   * Initialize mobile menu toggle
   */
  function initMobileMenu() {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const primaryMenu = document.getElementById('primary-menu');
    const mainNav = menuToggle ? menuToggle.closest('.main-navigation') : null;
    
    if (!menuToggle || !primaryMenu || !mainNav) {
      return; // Exit if elements don't exist
    }
    
    // Toggle menu visibility on button click
    menuToggle.addEventListener('click', function() {
      const isActive = mainNav.classList.contains('active');
      
      if (isActive) {
        // Close menu
        mainNav.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.innerHTML = '<span>☰ Menu</span>';
      } else {
        // Open menu
        mainNav.classList.add('active');
        menuToggle.setAttribute('aria-expanded', 'true');
        menuToggle.innerHTML = '<span>✕ Close</span>';
      }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      const isClickInside = menuToggle.contains(event.target) || mainNav.contains(event.target);
      
      if (!isClickInside && mainNav.classList.contains('active')) {
        mainNav.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.innerHTML = '<span>☰ Menu</span>';
      }
    });
    
    // Close menu on window resize to desktop
    let resizeTimer;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        if (window.innerWidth > 768 && mainNav.classList.contains('active')) {
          mainNav.classList.remove('active');
          menuToggle.setAttribute('aria-expanded', 'false');
          menuToggle.innerHTML = '<span>☰ Menu</span>';
        }
      }, 250);
    });
    
    // Close menu when ESC key is pressed
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && mainNav.classList.contains('active')) {
        mainNav.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.innerHTML = '<span>☰ Menu</span>';
        menuToggle.focus(); // Return focus to toggle button
      }
    });

    // Close menu when a link is clicked (mobile UX)
    primaryMenu.addEventListener('click', function(event) {
      if (event.target.matches('a') && window.innerWidth <= 768) {
        mainNav.classList.remove('active');
        menuToggle.setAttribute('aria-expanded', 'false');
        menuToggle.innerHTML = '<span>☰ Menu</span>';
      }
    });
  }
  
  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileMenu);
  } else {
    initMobileMenu();
  }
})();

