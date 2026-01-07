/**
 * FreeRideInvestor V2 Theme JavaScript
 *
 * @package FreeRideInvestor_V2
 * @version 2.0.0
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
        // Mobile menu toggle
        initMobileMenu();

        // Smooth scrolling for anchor links
        initSmoothScroll();

        // Search form enhancements
        initSearchForm();

        // Post card hover effects
        initPostCards();

        // Keyboard navigation
        initKeyboardNav();

        // Lazy loading for images
        initLazyLoading();

        // Form validation
        initFormValidation();
    }

    /**
     * Mobile menu functionality
     */
    function initMobileMenu() {
        // Create mobile menu toggle button if it doesn't exist
        if (!$('.mobile-menu-toggle').length) {
            $('.main-navigation').before('<button class="mobile-menu-toggle" aria-expanded="false"><span class="screen-reader-text">Toggle navigation menu</span></button>');
        }

        // Toggle mobile menu
        $('.mobile-menu-toggle').on('click', function() {
            var $this = $(this);
            var $nav = $('.main-navigation');
            var expanded = $this.attr('aria-expanded') === 'true';

            $this.attr('aria-expanded', !expanded);
            $nav.toggleClass('is-open');

            // Update button text for screen readers
            $this.find('.screen-reader-text').text(
                expanded ? 'Toggle navigation menu' : 'Close navigation menu'
            );
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.site-header').length) {
                $('.main-navigation').removeClass('is-open');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
            }
        });

        // Close mobile menu on escape key
        $(document).on('keydown', function(event) {
            if (event.keyCode === 27) { // Escape key
                $('.main-navigation').removeClass('is-open');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
            }
        });
    }

    /**
     * Smooth scrolling for anchor links
     */
    function initSmoothScroll() {
        $('a[href*="#"]:not([href="#"])').on('click', function(event) {
            var target = $(this.hash);
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    }

    /**
     * Search form enhancements
     */
    function initSearchForm() {
        var $searchInput = $('.search-form input[type="search"]');

        // Add placeholder text if not present
        if (!$searchInput.attr('placeholder')) {
            $searchInput.attr('placeholder', 'Search articles...');
        }

        // Auto-focus search input when search form is visible
        $('.search-form').on('show', function() {
            $(this).find('input[type="search"]').focus();
        });

        // Clear search on escape
        $searchInput.on('keydown', function(event) {
            if (event.keyCode === 27) { // Escape key
                $(this).val('');
                $(this).blur();
            }
        });
    }

    /**
     * Post card hover effects
     */
    function initPostCards() {
        $('.post-card').hover(
            function() {
                $(this).addClass('hover');
            },
            function() {
                $(this).removeClass('hover');
            }
        );
    }

    /**
     * Keyboard navigation enhancements
     */
    function initKeyboardNav() {
        // Focus management for modal dialogs
        $(document).on('keydown', function(event) {
            if (event.keyCode === 9) { // Tab key
                var $focusable = $(':focusable');
                var $first = $focusable.first();
                var $last = $focusable.last();

                if (event.shiftKey) {
                    // Shift + Tab
                    if (document.activeElement === $first[0]) {
                        $last.focus();
                        event.preventDefault();
                    }
                } else {
                    // Tab
                    if (document.activeElement === $last[0]) {
                        $first.focus();
                        event.preventDefault();
                    }
                }
            }
        });
    }

    /**
     * Lazy loading for images
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            // Observe all images with data-src attribute
            $('img[data-src]').each(function() {
                imageObserver.observe(this);
            });
        }
    }

    /**
     * Form validation
     */
    function initFormValidation() {
        // Comment form validation
        $('#commentform').on('submit', function(event) {
            var $form = $(this);
            var isValid = true;

            // Check required fields
            $form.find('[required]').each(function() {
                var $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });

            // Email validation
            var $email = $form.find('input[type="email"]');
            if ($email.length && $email.val()) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test($email.val())) {
                    $email.addClass('error');
                    isValid = false;
                } else {
                    $email.removeClass('error');
                }
            }

            if (!isValid) {
                event.preventDefault();

                // Show error message
                if (!$form.find('.form-error').length) {
                    $form.prepend('<div class="form-error" style="color: #dc3545; margin-bottom: 1rem;">Please fill in all required fields correctly.</div>');
                }

                // Scroll to first error
                var $firstError = $form.find('.error').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 300);
                }
            } else {
                $form.find('.form-error').remove();
            }
        });

        // Remove error class on input
        $('input, textarea').on('input', function() {
            $(this).removeClass('error');
        });
    }

    /**
     * AJAX functionality for dynamic content
     */
    window.freerideinvestor = {
        /**
         * Load more posts via AJAX
         */
        loadMorePosts: function(page) {
            $.ajax({
                url: freerideinvestor_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_posts',
                    page: page,
                    nonce: freerideinvestor_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.blog-grid').append(response.data.html);

                        // Re-initialize post cards
                        initPostCards();

                        // Update pagination
                        if (response.data.has_more) {
                            $('.load-more-btn').data('page', page + 1);
                        } else {
                            $('.load-more-btn').hide();
                        }
                    }
                },
                error: function() {
                    console.error('Error loading more posts');
                }
            });
        },

        /**
         * Handle search suggestions
         */
        searchSuggestions: function(query) {
            if (query.length < 3) {
                return;
            }

            $.ajax({
                url: freerideinvestor_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'search_suggestions',
                    query: query,
                    nonce: freerideinvestor_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.suggestions.length) {
                        // Show suggestions dropdown
                        var $suggestions = $('.search-suggestions');
                        if (!$suggestions.length) {
                            $('.search-form').append('<div class="search-suggestions"></div>');
                            $suggestions = $('.search-suggestions');
                        }

                        var html = '<ul>';
                        response.data.suggestions.forEach(function(suggestion) {
                            html += '<li><a href="' + suggestion.url + '">' + suggestion.title + '</a></li>';
                        });
                        html += '</ul>';

                        $suggestions.html(html).show();
                    }
                }
            });
        }
    };

})(jQuery);

