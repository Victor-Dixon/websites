/**
 * We Are Swarm - Main JavaScript
 * ==============================
 *
 * Core functionality for the futuristic Swarm Intelligence website.
 * Handles navigation, animations, and interactive elements.
 *
 * Author: Swarm Intelligence - Agent-7 (Web Development Specialist)
 * Date: 2026-01-15
 */

(function($) {
    'use strict';

    // DOM ready
    $(document).ready(function() {
        initTheme();
        initNavigation();
        initScrollEffects();
        initAccessibility();
        initPerformance();
    });

    /**
     * Initialize theme functionality
     */
    function initTheme() {
        // Theme toggle (if implemented)
        initThemeToggle();

        // Mobile menu toggle
        initMobileMenu();

        // Smooth scrolling for anchor links
        initSmoothScrolling();

        // Initialize counters and stats
        initCounters();

        // Initialize forms
        initForms();
    }

    /**
     * Theme toggle functionality
     */
    function initThemeToggle() {
        const themeToggle = $('#theme-toggle');
        if (!themeToggle.length) return;

        // Check for saved theme preference
        const savedTheme = localStorage.getItem('weareswarm-theme') || 'dark';
        setTheme(savedTheme);

        themeToggle.on('click', function(e) {
            e.preventDefault();
            const currentTheme = $('body').hasClass('light-theme') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
    }

    /**
     * Set theme
     */
    function setTheme(theme) {
        $('body').removeClass('light-theme dark-theme').addClass(theme + '-theme');
        localStorage.setItem('weareswarm-theme', theme);

        const themeToggle = $('#theme-toggle');
        if (themeToggle.length) {
            const icon = theme === 'dark' ? '🌙' : '☀️';
            const text = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
            themeToggle.html(`${icon} ${text}`);
        }
    }

    /**
     * Mobile menu functionality
     */
    function initMobileMenu() {
        const mobileToggle = $('.mobile-menu-toggle');
        const navMenu = $('.nav-menu');

        if (!mobileToggle.length || !navMenu.length) return;

        mobileToggle.on('click', function() {
            navMenu.toggleClass('active');
            $(this).toggleClass('active');
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.nav-menu, .mobile-menu-toggle').length) {
                navMenu.removeClass('active');
                mobileToggle.removeClass('active');
            }
        });
    }

    /**
     * Smooth scrolling for anchor links
     */
    function initSmoothScrolling() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                const offset = target.offset().top - 80; // Account for fixed header

                $('html, body').animate({
                    scrollTop: offset
                }, 800, 'easeInOutCubic');
            }
        });
    }

    /**
     * Scroll effects and animations
     */
    function initScrollEffects() {
        // Intersection Observer for scroll animations
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

        // Observe elements for animation
        $('.animate-on-scroll').each(function() {
            observer.observe(this);
        });

        // Parallax effects
        $(window).on('scroll', function() {
            const scrolled = $(window).scrollTop();
            const rate = scrolled * -0.5;

            $('.parallax-bg').css('transform', `translateY(${rate}px)`);
        });

        // Header background on scroll
        $(window).on('scroll', function() {
            const scrollTop = $(window).scrollTop();
            const header = $('header');

            if (scrollTop > 100) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }
        });
    }

    /**
     * Accessibility enhancements
     */
    function initAccessibility() {
        // Keyboard navigation for dropdowns
        $('.nav-item.has-dropdown > a').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).siblings('.dropdown-menu').toggleClass('active');
            }
        });

        // Focus management
        $('button, a, input, select, textarea').on('focus', function() {
            $(this).addClass('focused');
        }).on('blur', function() {
            $(this).removeClass('focused');
        });

        // Skip links
        $('.skip-link').on('keydown', function(e) {
            if (e.key === 'Enter') {
                const target = $(this).attr('href');
                $(target).attr('tabindex', '-1').focus();
            }
        });
    }

    /**
     * Performance optimizations
     */
    function initPerformance() {
        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            $('.lazy').each(function() {
                imageObserver.observe(this);
            });
        }

        // Debounced scroll events
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

        // Apply debouncing to scroll events
        $(window).on('scroll', debounce(function() {
            // Throttled scroll handlers
        }, 16));
    }

    /**
     * Counter animations
     */
    function initCounters() {
        $('.stat-number').each(function() {
            const $this = $(this);
            const target = parseFloat($this.data('target')) || 0;
            const isCurrency = $this.siblings('.stat-unit').text() === '$';

            // Start counter when element is visible
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter($this, target, isCurrency);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(this);
        });
    }

    /**
     * Animate counter
     */
    function animateCounter($element, target, isCurrency = false) {
        const duration = 2000;
        const start = 0;
        const startTime = performance.now();

        function updateCounter(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // Easing function
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(start + (target - start) * easeOutCubic);

            $element.text(isCurrency ? `$${current.toLocaleString()}` : current.toLocaleString());

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                $element.text(isCurrency ? `$${target.toLocaleString()}` : target.toLocaleString());
            }
        }

        requestAnimationFrame(updateCounter);
    }

    /**
     * Form handling
     */
    function initForms() {
        // Contact form AJAX submission
        $('.contact-form').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalText = $submitBtn.text();

            // Disable button
            $submitBtn.prop('disabled', true).text('Sending...');

            // Collect form data
            const formData = new FormData(this);
            formData.append('action', 'submit_contact');
            formData.append('nonce', weareswarmAjax.nonce);

            // Submit via AJAX
            $.ajax({
                url: weareswarmAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showMessage($form, response.data, 'success');
                        $form[0].reset();
                    } else {
                        showMessage($form, response.data, 'error');
                    }
                },
                error: function() {
                    showMessage($form, 'An error occurred. Please try again.', 'error');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    /**
     * Show form message
     */
    function showMessage($form, message, type) {
        // Remove existing message
        $form.find('.form-message').remove();

        // Create new message
        const $message = $('<div>', {
            'class': `form-message form-message-${type}`,
            'text': message
        });

        $form.append($message);

        // Auto-remove after 5 seconds
        setTimeout(function() {
            $message.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Navigation functionality
     */
    function initNavigation() {
        // Active menu item highlighting
        const currentPath = window.location.pathname;

        $('.nav-menu a').each(function() {
            const href = $(this).attr('href');
            if (href && currentPath.includes(href.replace('#', ''))) {
                $(this).addClass('active');
            }
        });

        // Dropdown menus
        $('.nav-item.has-dropdown').on('mouseenter', function() {
            $(this).find('.dropdown-menu').stop(true, true).slideDown(200);
        }).on('mouseleave', function() {
            $(this).find('.dropdown-menu').stop(true, true).slideUp(200);
        });
    }

    /**
     * Utility functions
     */
    function isMobile() {
        return window.innerWidth < 768;
    }

    function isTablet() {
        return window.innerWidth >= 768 && window.innerWidth < 1024;
    }

    function isDesktop() {
        return window.innerWidth >= 1024;
    }

    // Expose some functions globally for use in other scripts
    window.WeAreSwarm = {
        setTheme: setTheme,
        showMessage: showMessage,
        animateCounter: animateCounter,
        isMobile: isMobile,
        isTablet: isTablet,
        isDesktop: isDesktop
    };

})(jQuery);