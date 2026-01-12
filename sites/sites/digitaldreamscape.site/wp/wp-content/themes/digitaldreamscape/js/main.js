/**
 * Digital Dreamscape Theme JavaScript
 * Handles mobile menu toggle and basic interactions
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Mobile menu toggle
        var menuToggle = document.querySelector('.mobile-menu-toggle');
        var navigation = document.querySelector('.main-navigation');
        var header = document.querySelector('.site-header');

        if (menuToggle && navigation) {
            menuToggle.addEventListener('click', function () {
                navigation.classList.toggle('active');
                menuToggle.classList.toggle('active');
                var isExpanded = navigation.classList.contains('active');
                menuToggle.setAttribute('aria-expanded', isExpanded);

                // Prevent body scroll when menu is open
                if (isExpanded) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });

            // Close menu when clicking on a link
            var navLinks = navigation.querySelectorAll('a');
            navLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    navigation.classList.remove('active');
                    menuToggle.classList.remove('active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                });
            });

            // Close menu when clicking outside
            document.addEventListener('click', function (e) {
                if (navigation.classList.contains('active') &&
                    !navigation.contains(e.target) &&
                    !menuToggle.contains(e.target)) {
                    navigation.classList.remove('active');
                    menuToggle.classList.remove('active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                }
            });
        }

        // Smooth scroll for anchor links
        var anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                var href = link.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    var target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });

        // Header scroll effect - add shadow on scroll
        if (header) {
            var lastScrollTop = 0;

            window.addEventListener('scroll', function () {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }

                lastScrollTop = scrollTop;
            }, { passive: true });
        }

        // Add loading class removal for smooth page transitions
        document.body.classList.add('loaded');
    });

    // Handle page visibility for video/audio if needed
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            // Pause any auto-playing content
        } else {
            // Resume content
        }
    });
})();
