<?php
/**
 * Trading Robot Plug Hero A/B Test
 * Applied: 2025-12-21
 * 
 * Implements A/B testing for hero headlines with benefit focus and urgency.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tradingrobotplug_com_hero_ab_test() {
    // Get user session ID for consistent variant assignment
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $session_id = session_id();
    $variant_index = abs(crc32($session_id)) % 3;
    
    $variants = array(
        0 => "Automated Trading Robots That Actually Work. Join the Waitlist.",
        1 => "Stop Guessing. Let AI-Powered Trading Robots Do the Work for You.",
        2 => "Trading Robots With Real Results. Join the Waitlist Today.",
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
    $hero_data = tradingrobotplug_com_hero_ab_test();
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
