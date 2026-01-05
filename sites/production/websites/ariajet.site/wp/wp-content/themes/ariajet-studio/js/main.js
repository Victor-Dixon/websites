/**
 * AriaJet Studio - Main JavaScript
 * 
 * Subtle, smooth interactions that feel natural and premium.
 */

(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        initNavigation();
        initScrollReveal();
        initSmoothScroll();
    });
    
    /**
     * Mobile Navigation Toggle
     */
    function initNavigation() {
        const toggle = document.querySelector('.nav-toggle');
        const nav = document.querySelector('.main-navigation');
        
        if (!toggle || !nav) return;
        
        toggle.addEventListener('click', function() {
            const isOpen = toggle.classList.contains('active');
            
            toggle.classList.toggle('active');
            nav.classList.toggle('active');
            toggle.setAttribute('aria-expanded', !isOpen);
            
            // Prevent body scroll when menu is open
            document.body.style.overflow = isOpen ? '' : 'hidden';
        });
        
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !nav.contains(e.target) && nav.classList.contains('active')) {
                toggle.classList.remove('active');
                nav.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
        
        // Close on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && nav.classList.contains('active')) {
                toggle.classList.remove('active');
                nav.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
    }
    
    /**
     * Scroll Reveal Animations
     * Subtle fade-in as elements enter viewport
     */
    function initScrollReveal() {
        const reveals = document.querySelectorAll('.reveal');
        
        if (reveals.length === 0) return;
        
        // Check if Intersection Observer is supported
        if (!('IntersectionObserver' in window)) {
            // Fallback: just show everything
            reveals.forEach(function(el) {
                el.classList.add('visible');
            });
            return;
        }
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    // Stagger animation if there are siblings
                    const siblings = entry.target.parentElement.querySelectorAll('.reveal');
                    const index = Array.from(siblings).indexOf(entry.target);
                    
                    setTimeout(function() {
                        entry.target.classList.add('visible');
                    }, index * 100);
                    
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        reveals.forEach(function(el) {
            observer.observe(el);
        });
    }
    
    /**
     * Smooth Scroll for Anchor Links
     */
    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                if (href === '#') return;
                
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    
                    const headerHeight = document.querySelector('.site-header')?.offsetHeight || 0;
                    const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without jumping
                    history.pushState(null, null, href);
                }
            });
        });
    }
    
})();
