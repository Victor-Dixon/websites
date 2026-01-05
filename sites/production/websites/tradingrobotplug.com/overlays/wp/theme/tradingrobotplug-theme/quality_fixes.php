<?php
/**
 * Trading Robot Plug Quality Fixes
 * Applied: 2026-01-01
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fix navigation menu typos
 */
function tradingrobotplug_fix_menu_typos($items, $args) {
    if (!is_array($items)) {
        return $items;
    }

    foreach ($items as &$item) {
        if (isset($item->title)) {
            // Fix "Capabilitie" -> "Capabilities"
            $item->title = str_replace('Capabilitie', 'Capabilities', $item->title);
            // Fix any other common typos
            $item->title = str_replace('Capabilites', 'Capabilities', $item->title);
        }

        if (isset($item->attr_title)) {
            $item->attr_title = str_replace('Capabilitie', 'Capabilities', $item->attr_title);
        }

        if (isset($item->description)) {
            $item->description = str_replace('Capabilitie', 'Capabilities', $item->description);
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'tradingrobotplug_fix_menu_typos', 999, 2);

/**
 * Fix footer content typos
 */
function tradingrobotplug_fix_footer_content($content) {
    // Fix "All right re erved" -> "All rights reserved"
    $content = str_replace('All right re erved', 'All rights reserved', $content);
    $content = str_replace('All right reserved', 'All rights reserved', $content);

    return $content;
}
add_filter('the_content', 'tradingrobotplug_fix_footer_content', 999);
add_filter('widget_text', 'tradingrobotplug_fix_footer_content', 999);

/**
 * Fix footer text in theme footer
 */
function tradingrobotplug_fix_footer_text($text) {
    $text = str_replace('All right re erved', 'All rights reserved', $text);
    $text = str_replace('All right reserved', 'All rights reserved', $text);
    return $text;
}
add_filter('gettext', 'tradingrobotplug_fix_footer_text', 999);
add_filter('ngettext', 'tradingrobotplug_fix_footer_text', 999);

/**
 * Add quality improvements CSS
 */
function tradingrobotplug_quality_css() {
    wp_add_inline_style('tradingrobotplug-style', '
        /* Quality improvements for tradingrobotplug.com */

        /* Ensure navigation text is properly formatted */
        .main-navigation a,
        .site-navigation a {
            text-transform: capitalize;
            letter-spacing: normal;
            word-spacing: normal;
        }

        /* Footer improvements */
        .site-footer,
        .footer-content {
            word-spacing: normal;
            letter-spacing: normal;
        }

        /* Content spacing improvements */
        .site-main,
        .content-area {
            line-height: 1.6;
        }

        /* Button improvements */
        .btn {
            text-transform: none;
            letter-spacing: normal;
        }
    ');
}
add_action('wp_enqueue_scripts', 'tradingrobotplug_quality_css', 999);

/**
 * Ensure homepage has substantial content
 */
function tradingrobotplug_enhance_homepage_content($content) {
    if (is_front_page() && is_home()) {
        // Add additional content validation
        // This ensures the homepage always has content
        if (strlen(strip_tags($content)) < 500) {
            // Content is too minimal, add more from template
            ob_start();
            locate_template('front-page.php', true, false);
            $template_content = ob_get_clean();

            if (!empty($template_content) && strlen(strip_tags($template_content)) > strlen(strip_tags($content))) {
                return $template_content;
            }
        }
    }

    return $content;
}
add_filter('the_content', 'tradingrobotplug_enhance_homepage_content', 1); // Run early
