/**
 * Crosby Ultimate Events Theme JavaScript
 * Handles theme interactions, animations, and dynamic functionality
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        initTheme();
    });

    /**
     * Initialize theme functionality
     */
    function initTheme() {
        // Initialize scroll animations
        initScrollAnimations();

        // Initialize mobile menu
        initMobileMenu();

        // Initialize hero animations
        initHeroAnimations();

        // Initialize smooth scrolling
        initSmoothScrolling();

        // Initialize form enhancements
        initFormEnhancements();

        // Initialize lazy loading
        initLazyLoading();

        // Initialize event interactions
        initEventInteractions();
    }

    /**
     * Initialize scroll-based animations
     */
    function initScrollAnimations() {
        // Fade in elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    $(entry.target).addClass('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe elements with fade-in-up class
        $('.fade-in-up').each(function() {
            observer.observe(this);
        });
    }

    /**
     * Initialize mobile menu functionality
     */
    function initMobileMenu() {
        $('.mobile-menu-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('.main-navigation-nav').toggleClass('active');

            // Toggle aria-expanded
            const expanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !expanded);
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.main-navigation, .mobile-menu-toggle').length) {
                $('.mobile-menu-toggle').removeClass('active');
                $('.main-navigation-nav').removeClass('active');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
            }
        });
    }

    /**
     * Initialize hero section animations
     */
    function initHeroAnimations() {
        // Typewriter effect for hero text
        const typewriterElement = $('.hero-typewriter');
        if (typewriterElement.length) {
            const text = typewriterElement.text();
            typewriterElement.text('');
            let i = 0;

            const typeWriter = () => {
                if (i < text.length) {
                    typewriterElement.append(text.charAt(i));
                    i++;
                    setTimeout(typeWriter, 50);
                }
            };

            // Start typewriter effect after a delay
            setTimeout(typeWriter, 1000);
        }

        // Frisbee animations
        initFrisbeeAnimations();

        // Field animations
        initFieldAnimations();
    }

    /**
     * Initialize frisbee animations
     */
    function initFrisbeeAnimations() {
        const frisbees = $('.frisbee-throw');
        const crowdElements = $('.crowd-wave');

        // Add random movement to frisbees
        frisbees.each(function(index) {
            const frisbee = $(this);
            const delay = index * 1000;

            setTimeout(function() {
                animateFrisbee(frisbee);
            }, delay);
        });

        // Add wave animation to crowd
        crowdElements.each(function(index) {
            const element = $(this);
            const delay = index * 500;

            setTimeout(function() {
                element.addClass('animate-pulse');
            }, delay);
        });

        function animateFrisbee(frisbee) {
            const randomX = Math.random() * 30 - 15;
            const randomY = Math.random() * 20 - 10;

            frisbee.animate({
                left: `+=${randomX}px`,
                top: `+=${randomY}px`
            }, 2000, 'easeInOutQuad', function() {
                // Reverse animation
                frisbee.animate({
                    left: `-=${randomX}px`,
                    top: `-=${randomY}px`
                }, 2000, 'easeInOutQuad', function() {
                    animateFrisbee(frisbee);
                });
            });
        }
    }

    /**
     * Initialize field animations
     */
    function initFieldAnimations() {
        // Add subtle field glow effects
        const fieldElements = $('.field-markings');
        if (fieldElements.length) {
            setInterval(function() {
                fieldElements.toggleClass('field-glow');
            }, 3000);
        }
    }

    /**
     * Initialize event interactions
     */
    function initEventInteractions() {
        // Event card hover effects
        $('.event-card').on('mouseenter', function() {
            $(this).find('.event-card-header').addClass('scale-105');
        }).on('mouseleave', function() {
            $(this).find('.event-card-header').removeClass('scale-105');
        });

        // Event registration buttons
        $('.event-card a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $(this).attr('href');
            const message = target === '#register' ? 'Registration for this event will be available soon!' :
                           target === '#learn-more' ? 'More information about this clinic coming soon!' :
                           'Join us for our next community pickup game!';

            showNotification(message, 'info');
        });
    }

    /**
     * Initialize smooth scrolling for anchor links
     */
    function initSmoothScrolling() {
        $('a[href*="#"]:not([href="#"])').on('click', function(event) {
            if (this.hash !== '') {
                event.preventDefault();

                const hash = this.hash;
                const target = $(hash);

                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800, 'easeInOutQuad');
                }
            }
        });
    }

    /**
     * Initialize form enhancements
     */
    function initFormEnhancements() {
        // Add focus states to form inputs
        $('input, textarea, select').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).parent().removeClass('focused');
        });

        // Form validation
        $('form').on('submit', function(event) {
            const form = $(this);
            const requiredFields = form.find('[required]');
            let isValid = true;

            requiredFields.each(function() {
                if ($(this).val().trim() === '') {
                    $(this).addClass('error');
                    isValid = false;
                } else {
                    $(this).removeClass('error');
                }
            });

            if (!isValid) {
                event.preventDefault();
                showFormError('Please fill in all required fields.');
            }
        });
    }

    /**
     * Initialize lazy loading for images
     */
    function initLazyLoading() {
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

            $('img[data-src]').each(function() {
                imageObserver.observe(this);
            });
        }
    }

    /**
     * Show notification message
     */
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        $('.notification').remove();

        // Create notification
        const notification = $('<div class="notification notification-' + type + '">' + message + '</div>');
        $('body').append(notification);

        // Show notification
        setTimeout(function() {
            notification.addClass('show');
        }, 100);

        // Auto-hide after 3 seconds
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }

    /**
     * Show form error message
     */
    function showFormError(message) {
        // Remove existing error messages
        $('.form-error').remove();

        // Add new error message
        const errorDiv = $('<div class="form-error"></div>').text(message);
        $('form').prepend(errorDiv);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorDiv.fadeOut();
        }, 5000);
    }

    /**
     * Utility function for debouncing
     */
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

    /**
     * Handle window resize events
     */
    $(window).on('resize', debounce(function() {
        // Reinitialize mobile menu on resize
        if ($(window).width() > 768) {
            $('.mobile-menu-toggle').removeClass('active');
            $('.main-navigation-nav').removeClass('active');
        }
    }, 250));

    /**
     * Handle AJAX loading states
     */
    $(document).on('ajaxStart', function() {
        $('body').addClass('loading');
    }).on('ajaxStop', function() {
        $('body').removeClass('loading');
    });

})(jQuery);

// Fallback for jQuery not loaded
if (typeof jQuery === 'undefined') {
    console.warn('jQuery is not loaded. Some theme features may not work properly.');
}

// Add notification styles
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #52b788;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification-info {
        background: #52b788;
    }

    .notification-error {
        background: #dc3545;
    }

    .field-glow {
        box-shadow: 0 0 30px rgba(82, 183, 136, 0.2) !important;
    }

    .scale-105 {
        transform: scale(1.05);
    }
`;
document.head.appendChild(style);