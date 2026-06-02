<?php
/**
 * DreamOS Emergence theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

function dreamos_emergence_setup(): void {
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'dreamos_emergence_setup');

function dreamos_emergence_assets(): void {
    wp_enqueue_style(
        'dreamos-emergence-style',
        get_stylesheet_uri(),
        [],
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'dreamos_emergence_assets');

function dreamos_emergence_cleanup_placeholder_text(string $content): string {
    return str_replace(
        [
            'trans-menu',
            'trans-contacts',
            'email@email.com',
            '+123456789',
            'trans-socials',
            'trans-newsletter',
        ],
        '',
        $content
    );
}
add_filter('the_content', 'dreamos_emergence_cleanup_placeholder_text', 20);

function dreamos_emergence_shortcode_frame(string $content): string {
    if (is_admin()) {
        return $content;
    }

    if (has_shortcode($content, 'spark_generator') ||
        has_shortcode($content, 'spark_battle_sim') ||
        has_shortcode($content, 'spark_battle') ||
        has_shortcode($content, 'emergence_character_generator')) {
        return '<div class="dreamos-plugin-frame">' . $content . '</div>';
    }

    return $content;
}
add_filter('the_content', 'dreamos_emergence_shortcode_frame', 8);
