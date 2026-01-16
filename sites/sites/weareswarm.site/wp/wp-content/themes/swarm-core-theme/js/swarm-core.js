/**
 * Swarm Core Theme JavaScript
 * Modern interactive functionality for Swarm Intelligence Platform
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initMobileNavigation();
        initScrollEffects();
        initSmoothScrolling();
        initAnimations();
    });

    /**
     * Mobile Navigation Functionality
     */
    function initMobileNavigation() {
        const navToggle = $('#nav-toggle');
        const navMenu = $('.nav-menu');
        const overlay = $('#mobile-menu-overlay');
        const body = $('body');

        // Toggle mobile menu
        navToggle.on('click', function() {
            const isActive = navMenu.hasClass('active');

            if (isActive) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });

        // Close menu when clicking overlay
        overlay.on('click', function() {
            closeMobileMenu();
        });

        // Close menu when clicking menu links
        navMenu.find('a').on('click', function() {
            closeMobileMenu();
        });

        // Close menu on window resize (if desktop view)
        $(window).on('resize', function() {
            if ($(window).width() > 768) {
                closeMobileMenu();
            }
        });

        function openMobileMenu() {
            navMenu.addClass('active');
            overlay.addClass('active');
            navToggle.addClass('active');
            body.addClass('mobile-menu-open');
        }

        function closeMobileMenu() {
            navMenu.removeClass('active');
            overlay.removeClass('active');
            navToggle.removeClass('active');
            body.removeClass('mobile-menu-open');
        }

        // Handle escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && navMenu.hasClass('active')) {
                closeMobileMenu();
            }
        });
    }

    /**
     * Scroll Effects
     */
    function initScrollEffects() {
        const header = $('.modern-header');
        let lastScrollTop = 0;

        $(window).on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            const scrollDirection = scrollTop > lastScrollTop ? 'down' : 'up';

            // Header background opacity based on scroll
            if (scrollTop > 50) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }

            // Hide/show header on scroll (optional - can be enabled if desired)
            // if (scrollTop > 200) {
            //     if (scrollDirection === 'down') {
            //         header.addClass('hidden');
            //     } else {
            //         header.removeClass('hidden');
            //     }
            // }

            lastScrollTop = scrollTop;
        });
    }

    /**
     * Smooth Scrolling for Anchor Links
     */
    function initSmoothScrolling() {
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            const target = $(this.hash);

            if (target.length) {
                e.preventDefault();

                const headerHeight = $('.modern-header').outerHeight();
                const targetOffset = target.offset().top - headerHeight - 20;

                $('html, body').animate({
                    scrollTop: targetOffset
                }, 800, 'easeInOutQuart');

                // Update URL hash without triggering scroll
                history.pushState(null, null, this.hash);
            }
        });
    }

    /**
     * Animation Effects
     */
    function initAnimations() {
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    $(entry.target).addClass('animate-in');
                }
            });
        }, observerOptions);

        // Observe elements that should animate in
        $('.feature-card, .definition-content, .vision-text, .connection-content').each(function() {
            observer.observe(this);
        });

        // Floating particle animations for about page
        animateFloatingParticles();

        // Agent network pulse animation
        setInterval(function() {
            $('.network-node').each(function(index) {
                setTimeout(() => {
                    $(this).addClass('pulse');
                    setTimeout(() => {
                        $(this).removeClass('pulse');
                    }, 1000);
                }, index * 200);
            });
        }, 4000);
    }

    /**
     * Floating Particle Animation
     */
    function animateFloatingParticles() {
        $('.floating-particle').each(function(index) {
            const particle = $(this);
            const delay = index * 1000;

            setTimeout(function animateParticle() {
                particle.animate({
                    top: '+=20',
                    left: '+=10',
                    opacity: 0.3
                }, 2000, 'easeInOutSine').animate({
                    top: '-=20',
                    left: '-=10',
                    opacity: 0.8
                }, 2000, 'easeInOutSine', animateParticle);
            }, delay);
        });
    }

    /**
     * Utility Functions
     */

    // Easing function for smooth scrolling
    $.easing.easeInOutQuart = function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
        return -c/2 * ((t-=2)*t*t*t - 2) + b;
    };

    // Debounce function for performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Performance optimization for scroll events
    const optimizedScrollHandler = debounce(function() {
        // Any heavy scroll operations can go here
    }, 16);

    $(window).on('scroll', optimizedScrollHandler);

})(jQuery);