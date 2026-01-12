/**
 * Houston Sip Queen Theme JavaScript
 * Handles theme interactions, animations, and luxury mobile bartending features
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

        // Initialize cocktail interactions
        initCocktailInteractions();

        // Initialize package interactions
        initPackageInteractions();
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
        // Champagne glass floating animation
        const champagneGlasses = $('.champagne-glass');
        champagneGlasses.each(function(index) {
            const glass = $(this);
            const delay = index * 500;

            setTimeout(function() {
                animateChampagneGlass(glass);
            }, delay);
        });

        function animateChampagneGlass(glass) {
            const randomDuration = 3000 + Math.random() * 2000;

            glass.animate({
                opacity: 0.8
            }, {
                duration: randomDuration / 2,
                easing: 'easeInOutQuad',
                complete: function() {
                    glass.animate({
                        opacity: 0.6
                    }, {
                        duration: randomDuration / 2,
                        easing: 'easeInOutQuad',
                        complete: animateChampagneGlass.bind(null, glass)
                    });
                }
            });
        }

        // Scroll indicator animation
        const scrollIndicator = $('.scroll-indicator');
        if (scrollIndicator.length) {
            setInterval(function() {
                scrollIndicator.find('svg').animate({
                    transform: 'translateY(-5px)'
                }, 500, function() {
                    $(this).animate({
                        transform: 'translateY(0px)'
                    }, 500);
                });
            }, 2000);
        }
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

        // Form validation and submission
        $('.quote-form').on('submit', function(event) {
            event.preventDefault();

            const form = $(this);
            const submitBtn = form.find('.form-submit-btn');
            const originalText = submitBtn.text();

            // Basic validation
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
                showNotification('Please fill in all required fields.', 'error');
                return;
            }

            // Submit form
            submitBtn.text('Sending...').prop('disabled', true);

            // Simulate form submission (in real implementation, this would send to server)
            setTimeout(function() {
                submitBtn.text('Quote Request Sent!').removeClass('btn').addClass('btn-success');
                showNotification('Thank you! Your quote request has been sent. We\'ll be in touch within 24 hours.', 'success');

                // Reset form after 3 seconds
                setTimeout(function() {
                    form[0].reset();
                    submitBtn.text(originalText).removeClass('btn-success').addClass('btn').prop('disabled', false);
                }, 3000);
            }, 2000);
        });
    }

    /**
     * Initialize cocktail card interactions
     */
    function initCocktailInteractions() {
        $('.cocktail-card').on('mouseenter', function() {
            const card = $(this);
            const glass = card.find('.cocktail-glass');

            // Add sparkle effect
            createSparkles(card, 3);

            // Animate glass
            glass.animate({
                transform: 'scale(1.1) rotate(5deg)'
            }, 300);
        }).on('mouseleave', function() {
            const card = $(this);
            const glass = card.find('.cocktail-glass');

            glass.animate({
                transform: 'scale(1) rotate(0deg)'
            }, 300);
        });
    }

    /**
     * Initialize package card interactions
     */
    function initPackageInteractions() {
        $('.package-card').on('mouseenter', function() {
            const card = $(this);
            const isFeatured = card.hasClass('package-featured');

            if (!isFeatured) {
                card.css('border-color', 'var(--rosegold)');
            }

            // Add subtle glow
            card.css('box-shadow', '0 10px 30px rgba(201, 162, 106, 0.2)');
        }).on('mouseleave', function() {
            const card = $(this);
            const isFeatured = card.hasClass('package-featured');

            if (!isFeatured) {
                card.css('border-color', 'transparent');
            }

            card.css('box-shadow', '');
        });

        // Package selection
        $('.package-btn').on('click', function(e) {
            e.preventDefault();

            const packageCard = $(this).closest('.package-card');
            const packageName = packageCard.find('.package-title').text();
            const packagePrice = packageCard.find('.package-price').text();

            // Scroll to quote form and pre-fill
            const quoteSection = $('#quote');
            if (quoteSection.length) {
                $('html, body').animate({
                    scrollTop: quoteSection.offset().top - 100
                }, 800, 'easeInOutQuad');

                // Pre-fill form with package info
                setTimeout(function() {
                    const messageField = $('#message');
                    const currentMessage = messageField.val();
                    const packageInfo = `\n\nInterested in: ${packageName} (${packagePrice})`;
                    messageField.val(currentMessage + packageInfo);
                }, 1000);
            }
        });
    }

    /**
     * Create sparkle effects
     */
    function createSparkles(container, count) {
        for (let i = 0; i < count; i++) {
            const sparkle = $('<div class="sparkle">✨</div>');
            sparkle.css({
                position: 'absolute',
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                fontSize: (Math.random() * 20 + 10) + 'px',
                pointerEvents: 'none',
                zIndex: 1000,
                animation: 'sparkleFade 1s ease-out forwards'
            });

            container.append(sparkle);

            setTimeout(function() {
                sparkle.remove();
            }, 1000);
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

        // Position at top of page
        notification.css({
            position: 'fixed',
            top: '20px',
            left: '50%',
            transform: 'translateX(-50%)',
            zIndex: 10000,
            maxWidth: '500px',
            textAlign: 'center'
        });

        // Show notification
        setTimeout(function() {
            notification.addClass('show');
        }, 100);

        // Auto-hide after 5 seconds
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
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

// Add notification styles and sparkle animation
const style = document.createElement('style');
style.textContent = `
    .notification {
        background: var(--rosegold);
        color: var(--onyx);
        padding: 1rem 2rem;
        border-radius: 50px;
        box-shadow: 0 4px 12px rgba(201, 162, 106, 0.3);
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        opacity: 0;
        transform: translateX(-50%) translateY(-20px);
        transition: all 0.3s ease;
    }

    .notification.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .notification-error {
        background: #dc3545;
        color: white;
    }

    .notification-success {
        background: var(--rosegold);
        color: var(--onyx);
    }

    @keyframes sparkleFade {
        0% { transform: scale(0); opacity: 1; }
        50% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(2); opacity: 0; }
    }

    .btn-success {
        background: #28a745 !important;
        color: white !important;
    }

    .error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2) !important;
    }

    .focused {
        border-color: var(--rosegold) !important;
        box-shadow: 0 0 0 2px rgba(201, 162, 106, 0.2) !important;
    }
`;
document.head.appendChild(style);