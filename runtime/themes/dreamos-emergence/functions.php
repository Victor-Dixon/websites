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
        (string) filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'dreamos_emergence_assets');

function dreamos_emergence_placeholder_needles(): array {
    return [
        'trans-' . 'menu',
        'trans-' . 'contacts',
        'email@' . 'email.com',
        '+' . '123456789',
        'trans-' . 'socials',
        'trans-' . 'newsletter',
    ];
}

function dreamos_emergence_cleanup_placeholder_text(string $content): string {
    return str_replace(dreamos_emergence_placeholder_needles(), '', $content);
}
add_filter('the_content', 'dreamos_emergence_cleanup_placeholder_text', 20);

function dreamos_emergence_shortcode_frame(string $content): string {
    if (is_admin()) {
        return $content;
    }

    $shortcodes = [
        'spark_generator',
        'spark_battle_sim',
        'spark_battle',
        'emergence_character_generator',
    ];

    foreach ($shortcodes as $shortcode) {
        if (has_shortcode($content, $shortcode)) {
            return '<div class="dreamos-plugin-frame">' . $content . '</div>';
        }
    }

    return $content;
}
add_filter('the_content', 'dreamos_emergence_shortcode_frame', 8);
