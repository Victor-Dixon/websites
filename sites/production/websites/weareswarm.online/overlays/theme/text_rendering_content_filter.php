<?php
/**
 * We Are Swarm Text Rendering Content Filter
 * Applied: 2026-01-01
 * Based on crosbyultimateevents.com fixes
 */

if (!defined('ABSPATH')) {
    exit;
}

function weareswarm_fix_text_rendering($content) {
    // Fix common text rendering issues found on weareswarm.online
    $fixes = array(
        // Fix "Capabilitie" -> "Capabilities"
        '/\bCapabilitie\b/i' => 'Capabilities',

        // Fix "WordPre" -> "WordPress"
        '/\bWordPre\b/i' => 'WordPress',

        // Fix spaced text patterns
        '/A multi-agent AI\s+y\s+tem\s+howca\s+ing/i' => 'A multi-agent AI system showcasing',
        '/Specialize\s+d\s+in\s+y\s+tem\s+integration/i' => 'Specialized in system integration',
        '/©\s+2025\s+weare\s+warm\.online/i' => '© 2025 weareswarm.online',

        // Fix general spacing issues
        '/(\w)\s{2,}(\w)/' => '$1 $2',  // Multiple spaces between words
        '/(\w)\s+(\w)\s+(\w)/' => '$1 $2 $3',  // Spaced words that should be together

        // Fix domain spacing
        '/weare\s+warm/i' => 'weareswarm',
    );

    // Apply fixes if broken patterns found
    foreach ($fixes as $pattern => $replacement) {
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
        }
    }

    return $content;
}

// Apply to content, titles, excerpts, and bloginfo
add_filter('the_content', 'weareswarm_fix_text_rendering', 999);
add_filter('the_title', 'weareswarm_fix_text_rendering', 999);
add_filter('the_excerpt', 'weareswarm_fix_text_rendering', 999);
add_filter('bloginfo', 'weareswarm_fix_text_rendering', 999);
add_filter('get_bloginfo', 'weareswarm_fix_text_rendering', 999);

// Additional filters for navigation and widgets
add_filter('wp_nav_menu_items', 'weareswarm_fix_text_rendering', 999);
add_filter('widget_text', 'weareswarm_fix_text_rendering', 999);
add_filter('widget_title', 'weareswarm_fix_text_rendering', 999);

function weareswarm_add_inline_css() {
    // High-priority inline CSS that loads after all plugins
    wp_add_inline_style('weareswarm-style', '
        /* CRITICAL FIX: Text rendering issues for weareswarm.online */
        * {
            word-spacing: normal !important;
        }
        body, p, span, div, a, li, h1, h2, h3, h4, h5, h6,
        td, th, label, input, textarea, select, button,
        .site-title, .main-navigation, .hero-content,
        .capabilities-text, .services-content, .about-content,
        .hero-subtitle, .feature-description, .content-area,
        .entry-content, .post-content, .widget, .sidebar,
        .footer-widget, .site-footer, .footer-content {
            word-spacing: normal !important;
            letter-spacing: normal !important;
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            white-space: normal !important;
        }
        /* Specific fixes for known spacing issues */
        .multi-agent-text, .system-architecture-text, .swarm-intelligence-text {
            word-spacing: normal !important;
            letter-spacing: 0 !important;
        }
    ');
}

// Hook into wp_enqueue_scripts with high priority
add_action('wp_enqueue_scripts', 'weareswarm_add_inline_css', 999);
