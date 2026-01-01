<?php
/**
 * Houston Sip Queen Hero A/B Test
 * Applied: 2025-12-21
 * 
 * Implements A/B testing for hero headlines with benefit focus and urgency.
 */

if (!defined('ABSPATH')) {
    exit;
}

function houstonsipqueen_com_hero_ab_test() {
    // Get user session ID for consistent variant assignment
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $session_id = session_id();
    $variant_index = abs(crc32($session_id)) % 3;
    
    $variants = array(
        0 => "Your Event Deserves More Than Basic Drinks. Experience Luxury Bartending.",
        1 => "Elevate Your Event With Premium Cocktails & Service That Impresses.",
        2 => "Luxury Bartending That Makes Your Event Unforgettable.",
    );
    
    $selected_variant = $variants[$variant_index];
    
    // Add urgency element
    $urgency_text = "Limited Availability - Book Now";
    
    return array(
        'headline' => $selected_variant,
        'urgency' => $urgency_text,
        'variant' => $variant_index
    );
}

// Hook into theme header
add_action('wp_head', function() {
    $hero_data = houstonsipqueen_com_hero_ab_test();
    ?>
    <script>
    // Hero A/B Test Data
    window.heroABTest = {
        headline: <?php echo json_encode($hero_data['headline']); ?>,
        urgency: <?php echo json_encode($hero_data['urgency']); ?>,
        variant: <?php echo $hero_data['variant']; ?>
    };
    </script>
    <?php
}, 1);
