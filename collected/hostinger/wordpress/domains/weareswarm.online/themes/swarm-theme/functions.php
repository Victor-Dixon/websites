<?php
/**
 * We Are Swarm Text Rendering Content Filter
 * Applied: 2026-01-01
 * Based on crosbyultimateevents.com fixes
 */

if (!defined('ABSPATH')) {
    exit;
}

$swarm_api_enhanced = get_template_directory() . '/swarm-api-enhanced.php';
if (file_exists($swarm_api_enhanced)) {
    require_once $swarm_api_enhanced;
}

function swarm_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'swarm_theme_setup');

function swarm_theme_enqueue_assets() {
    wp_enqueue_style('weareswarm-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'swarm_theme_enqueue_assets');

if (!function_exists('get_swarm_agents')) {
    function get_swarm_agents() {
        $agents = get_option('swarm_agents_data', array());
        if (empty($agents) && function_exists('get_swarm_default_agents')) {
            $agents = get_swarm_default_agents();
        }

        foreach ($agents as $id => &$agent) {
            $agent['id'] = $agent['agent_id'] ?? $id;
            $agent['description'] = $agent['description'] ?? ($agent['mission'] ?? 'Ready for the next coordinated swarm mission.');
            $agent['coordinates'] = $agent['coordinates'] ?? 'Dream.OS';
            $agent['specialties'] = $agent['specialties'] ?? array($agent['role'] ?? 'Swarm Operations');
        }
        unset($agent);

        return $agents;
    }
}

if (!function_exists('get_swarm_stats')) {
    function get_swarm_stats() {
        $agents = get_swarm_agents();
        $total_agents = count($agents);
        $active_agents = 0;
        $total_points = 0;

        foreach ($agents as $agent) {
            if (($agent['status'] ?? '') === 'active') {
                $active_agents++;
            }
            $total_points += (int) ($agent['points'] ?? 0);
        }

        return array(
            'total_agents' => $total_agents,
            'active_agents' => $active_agents,
            'total_points' => $total_points,
            'avg_points' => $total_agents > 0 ? round($total_points / $total_agents) : 0,
        );
    }
}

if (!function_exists('get_swarm_mission_logs')) {
    function get_swarm_mission_logs($limit = 20) {
        $logs = get_option('swarm_mission_logs', array());
        return array_slice($logs, 0, max(0, (int) $limit));
    }
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
