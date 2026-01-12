/**
 * WeAreSwarm Theme JavaScript
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

        // Swarm particle animation
        initSwarmParticles();

        // Neural network animation
        initNeuralNetwork();
    }

    /**
     * Initialize swarm particle animations
     */
    function initSwarmParticles() {
        const particles = $('.agent-node');
        const connections = $('.connection-line');

        // Add random movement to particles
        particles.each(function(index) {
            const particle = $(this);
            const delay = index * 500;

            setTimeout(function() {
                animateParticle(particle);
            }, delay);
        });

        function animateParticle(particle) {
            const randomX = Math.random() * 20 - 10;
            const randomY = Math.random() * 20 - 10;

            particle.animate({
                left: `+=${randomX}px`,
                top: `+=${randomY}px`
            }, 3000, 'easeInOutQuad', function() {
                // Reverse animation
                particle.animate({
                    left: `-=${randomX}px`,
                    top: `-=${randomY}px`
                }, 3000, 'easeInOutQuad', function() {
                    animateParticle(particle);
                });
            });
        }
    }

    /**
     * Initialize neural network animations
     */
    function initNeuralNetwork() {
        const neuralLines = $('.neural-line');
        const neuralNodes = $('.neural-node');

        // Pulse animation for neural network
        const pulseNetwork = () => {
            neuralLines.each(function(index) {
                const line = $(this);
                setTimeout(() => {
                    line.animate({
                        opacity: 1,
                        strokeWidth: 2
                    }, 1000, function() {
                        line.animate({
                            opacity: 0.3,
                            strokeWidth: 1
                        }, 1000);
                    });
                }, index * 200);
            });

            neuralNodes.each(function(index) {
                const node = $(this);
                setTimeout(() => {
                    node.animate({
                        r: 4,
                        opacity: 1
                    }, 500, function() {
                        node.animate({
                            r: 2,
                            opacity: 0.7
                        }, 500);
                    });
                }, index * 200);
            });
        };

        // Run pulse animation every 5 seconds
        pulseNetwork();
        setInterval(pulseNetwork, 5000);
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