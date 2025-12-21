/**
 * FreeRideInvestor Modern Theme JavaScript
 */

(function () {
    'use strict';

    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mainNavigation = document.getElementById('main-navigation');

    if (mobileMenuToggle && mainNavigation) {
        mobileMenuToggle.addEventListener('click', function () {
            mainNavigation.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            if (!mainNavigation.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                mainNavigation.classList.remove('active');
            }
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Add animation on scroll (optional enhancement)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe cards for animation
    document.querySelectorAll('.card, .post-card').forEach(card => {
        observer.observe(card);
    });

})();
