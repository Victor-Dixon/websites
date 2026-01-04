<?php
/**
 * Activate Digital Dreamscape Theme
 */

require_once 'wp/wp-load.php';

// Check current theme
$current_theme = wp_get_theme();
echo "Current theme: " . $current_theme->get('Name') . "\n";

// Check if our theme exists
$our_theme = wp_get_theme('digitaldreamscape');
if ($our_theme->exists()) {
    echo "Digital Dreamscape theme found: " . $our_theme->get('Name') . "\n";

    // Try to activate our theme
    switch_theme('digitaldreamscape');

    // Verify activation
    $new_theme = wp_get_theme();
    echo "New active theme: " . $new_theme->get('Name') . "\n";

    if ($new_theme->get('Name') === 'Digital Dreamscape') {
        echo "✅ Theme activation successful!\n";
    } else {
        echo "❌ Theme activation failed\n";
    }
} else {
    echo "❌ Digital Dreamscape theme not found\n";
    echo "Available themes:\n";

    $themes = wp_get_themes();
    foreach ($themes as $theme) {
        echo "- " . $theme->get('Name') . "\n";
    }
}
?>