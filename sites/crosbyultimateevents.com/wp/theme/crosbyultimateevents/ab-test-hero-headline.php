<?php
/**
 * A/B Test Hero Headline
 * 
 * Implements A/B testing for hero headline with benefit-focused variants and urgency
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

/**
 * Get A/B test variant for hero headline
 * Uses cookie-based persistence for consistent experience
 * 
 * @return string 'variant_a' or 'variant_b'
 */
function crosby_get_hero_ab_variant() {
    // Check if variant already set in cookie
    if (isset($_COOKIE['crosby_hero_variant'])) {
        return sanitize_text_field($_COOKIE['crosby_hero_variant']);
    }
    
    // Randomly assign variant (50/50 split)
    $variant = (rand(0, 1) === 0) ? 'variant_a' : 'variant_b';
    
    // Set cookie for 30 days
    setcookie('crosby_hero_variant', $variant, time() + (30 * 24 * 60 * 60), '/');
    
    return $variant;
}

/**
 * Get hero headline based on A/B test variant
 * 
 * @return array Array with 'headline' and 'subtitle' keys
 */
function crosby_get_hero_content() {
    $variant = crosby_get_hero_ab_variant();
    
    $variants = array(
        'variant_a' => array(
            'headline' => 'Extraordinary Culinary Experiences & Flawless Event Planning',
            'subtitle' => 'Premier private chef services and comprehensive event coordination for memorable occasions',
        ),
        'variant_b' => array(
            'headline' => 'Create Unforgettable Events That Your Guests Will Talk About For Years',
            'subtitle' => 'Limited availability this season—book your consultation today and let us handle every detail so you can enjoy your perfect event',
        ),
    );
    
    return isset($variants[$variant]) ? $variants[$variant] : $variants['variant_a'];
}

/**
 * Enqueue A/B test tracking script
 */
function crosby_enqueue_ab_tracking() {
    if (is_front_page()) {
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                // Track which variant is shown
                var variant = document.cookie.match(/crosby_hero_variant=([^;]+)/);
                if (variant) {
                    variant = variant[1];
                    // Send to analytics (Google Analytics example)
                    if (typeof gtag !== "undefined") {
                        gtag("event", "ab_test_view", {
                            "test_name": "hero_headline",
                            "variant": variant
                        });
                    }
                    // Also track in dataLayer for GTM
                    if (typeof dataLayer !== "undefined") {
                        dataLayer.push({
                            "event": "ab_test_view",
                            "test_name": "hero_headline",
                            "variant": variant
                        });
                    }
                }
            });
        ');
    }
}
add_action('wp_enqueue_scripts', 'crosby_enqueue_ab_tracking');

