<?php
/**
 * Add custom rewrite rules for blog pagination
 * 
 * This ensures /blog/page/2/ works correctly with page templates
 */
function freerideinvestor_add_blog_rewrite_rules() {
    // Add rewrite rule for blog pagination
    add_rewrite_rule(
        '^blog/page/([0-9]+)/?$',
        'index.php?pagename=blog&paged=$matches[1]',
        'top'
    );
}
add_action('init', 'freerideinvestor_add_blog_rewrite_rules');

/**
 * Flush rewrite rules on theme activation
 */
function freerideinvestor_flush_rewrite_rules() {
    freerideinvestor_add_blog_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'freerideinvestor_flush_rewrite_rules');


/**
 * Fix Text Rendering Issues
 * Fixes broken text patterns (missing spaces, corrupted characters)
 */
function fix_text_rendering_issues($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Common broken patterns: "ha  been" -> "has been", "thi  web" -> "this web", etc.
    $patterns = [
        // Fix double spaces in common words
        '/ha\s{2,}been/i' => 'has been',
        '/thi\s{2,}web/i' => 'this web',
        '/trouble\s{2,}hooting/i' => 'troubleshooting',
        '/WordPre\s{2,}/i' => 'WordPress',
        '/web\s{2,}ite/i' => 'website',
        
        // Fix corrupted spacing in common phrases
        '/\s{3,}/' => ' ', // Replace 3+ spaces with single space
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    return $content;
}
add_filter('the_content', 'fix_text_rendering_issues');
add_filter('widget_text', 'fix_text_rendering_issues');
add_filter('get_the_excerpt', 'fix_text_rendering_issues');

/**
 * Enqueue inline CSS for text rendering fixes
 */
function freerideinvestor_text_rendering_styles() {
    $css = '
        body, p, h1, h2, h3, h4, h5, h6, a, span, div, li, td, th {
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            font-variant-ligatures: none !important;
            font-feature-settings: normal !important;
        }
    ';
    wp_add_inline_style('main-css', $css);
}
add_action('wp_enqueue_scripts', 'freerideinvestor_text_rendering_styles', 999);

/**
 * Force header and menu styling to match stunning homepage design
 */
function freerideinvestor_force_menu_styles() {
    $css = '
        /* Force Header Styling - Override all conflicting styles */
        header.site-header,
        .site-header {
            background: linear-gradient(135deg, #010409 0%, #0d1117 100%) !important;
            border-bottom: 1px solid rgba(240, 246, 252, 0.1) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Force Navigation Container */
        .main-nav,
        nav.main-nav {
            display: flex !important;
            align-items: center !important;
            gap: 20px !important;
        }
        
        /* Force Navigation List */
        .main-nav .nav-list,
        .nav-list,
        nav .nav-list {
            list-style: none !important;
            display: flex !important;
            gap: 10px !important;
            padding: 0 !important;
            margin: 0 !important;
            align-items: center !important;
        }
        
        /* Force Navigation Links */
        .main-nav .nav-list li a,
        .nav-list li a,
        nav .nav-list li a,
        .nav-menu li a {
            padding: 12px 24px !important;
            color: #f0f6fc !important;
            background: rgba(240, 246, 252, 0.03) !important;
            border: 1px solid transparent !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            display: inline-block !important;
        }
        
        .main-nav .nav-list li a:hover,
        .nav-list li a:hover,
        nav .nav-list li a:hover,
        .nav-menu li a:hover {
            background: rgba(240, 246, 252, 0.08) !important;
            border-color: rgba(240, 246, 252, 0.1) !important;
            color: #0066ff !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.2) !important;
            text-decoration: none !important;
        }
        
        .main-nav .nav-list li.current-menu-item > a,
        .main-nav .nav-list li.current_page_item > a,
        .nav-list li.current-menu-item > a,
        .nav-menu li.current-menu-item > a {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%) !important;
            color: white !important;
            border-color: #0066ff !important;
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.4) !important;
        }
        
        /* Force Logo Styling */
        .site-logo a,
        .logo-link,
        a.logo-link {
            color: #f0f6fc !important;
            text-decoration: none !important;
        }
        
        .site-logo a:hover,
        .logo-link:hover,
        a.logo-link:hover {
            color: #0066ff !important;
            text-decoration: none !important;
        }
        
        /* Remove bullet points */
        .main-nav .nav-list li::before,
        .nav-list li::before {
            content: none !important;
            display: none !important;
        }
        
        /* Body background */
        body {
            background: linear-gradient(135deg, #010409 0%, #0d1117 100%) !important;
            color: #f0f6fc !important;
        }
    ';
    wp_add_inline_style('main-css', $css);
}
add_action('wp_enqueue_scripts', 'freerideinvestor_force_menu_styles', 9999);

/**
 * Enqueue theme styles and scripts
 * CRITICAL: This function ensures CSS files are loaded
 */
function freerideinvestor_enqueue_assets() {
    // Main stylesheet
    wp_enqueue_style(
        'main-css',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get('Version')
    );
    
    // Navigation CSS - CRITICAL for menu styling
    wp_enqueue_style(
        'freeride-navigation-css',
        get_template_directory_uri() . '/css/styles/components/_navigation.css',
        ['main-css'],
        wp_get_theme()->get('Version')
    );
    
    // Header/Footer CSS - CRITICAL for header styling
    wp_enqueue_style(
        'freeride-header-footer-css',
        get_template_directory_uri() . '/css/styles/layout/_header-footer.css',
        ['main-css'],
        wp_get_theme()->get('Version')
    );
    
    // Google Fonts
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap',
        [],
        null
    );
    
    // Font Awesome
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
        [],
        '5.15.4'
    );
    
    // Custom CSS
    wp_enqueue_style(
        'custom-css',
        get_template_directory_uri() . '/css/custom.css',
        ['main-css', 'freeride-navigation-css', 'freeride-header-footer-css'],
        '1.1'
    );
    
    // Brand Core Responsive CSS (Phase 1 P0 Fixes)
    wp_enqueue_style(
        'brand-core-responsive-css',
        get_template_directory_uri() . '/css/styles/components/_brand-core-responsive.css',
        ['main-css', 'custom-css'],
        '1.0'
    );
    
    // Lead Magnet Landing Pages CSS (Phase 1 P0 Fixes - FUN-01)
    if (is_page_template(['page-roadmap-landing.php', 'page-mindset-journal-landing.php', 'page-thank-you-roadmap.php', 'page-thank-you-mindset-journal.php'])) {
        wp_enqueue_style(
            'lead-magnet-landing-css',
            get_template_directory_uri() . '/css/styles/pages/_lead-magnet-landing.css',
            ['main-css'],
            '1.0'
        );
    }
    
    // Hero Optimization CSS (Tier 1 Quick Win - WEB-01)
    wp_enqueue_style(
        'hero-optimization-css',
        get_template_directory_uri() . '/css/styles/pages/_hero-optimization.css',
        ['main-css'],
        '1.0'
    );
    
    // Custom JS
    wp_enqueue_script(
        'custom-js',
        get_template_directory_uri() . '/js/custom.js',
        ['jquery'],
        '1.1',
        true
    );
    
    // Mobile Menu JS
    wp_enqueue_script(
        'mobile-menu-js',
        get_template_directory_uri() . '/js/mobile-menu.js',
        [],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'freerideinvestor_enqueue_assets', 5);

/**
 * Load Brand Core Meta Boxes (Phase 1 P0 Fixes)
 */
require_once get_template_directory() . '/inc/meta-boxes/brand-core-meta-boxes.php';

/**
 * Load Lead Magnet Handlers (Phase 1 P0 Fixes - FUN-01)
 */
require_once get_template_directory() . '/inc/lead-magnet-handlers.php';