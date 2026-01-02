<?php
/**
 * Theme functions for dadudekc.com.
 *
 * @package DaDudeKC
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/inc/post-types/experiment.php';
require_once __DIR__ . '/inc/post-types/project.php';
require_once __DIR__ . '/inc/post-types/resume-item.php';
require_once __DIR__ . '/inc/functions/proof-metrics.php';
require_once __DIR__ . '/inc/post-types/note.php';

function dadudekc_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');

    register_nav_menus([
        'primary' => __('Primary Navigation', 'dadudekc'),
    ]);
}
add_action('after_setup_theme', 'dadudekc_theme_setup');

function dadudekc_enqueue_assets() {
    wp_enqueue_style('dadudekc-style', get_stylesheet_uri(), [], '1.0.0');
}
add_action('wp_enqueue_scripts', 'dadudekc_enqueue_assets');

function dadudekc_get_reading_time($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(wp_strip_all_tags($content));
    $minutes = max(1, ceil($word_count / 200));

    return sprintf(_n('%d min read', '%d min read', $minutes, 'dadudekc'), $minutes);
}

function dadudekc_is_swarm_intro_post($post_id = null) {
    if (!is_singular('post')) {
        return false;
    }

    $post_id = $post_id ?: get_the_ID();
    if (!$post_id) {
        return false;
    }

    $post = get_post($post_id);
    if (!$post) {
        return false;
    }

    return $post->post_name === 'introducing-the-swarm-a-new-paradigm-in-collaborative-development';
}

function dadudekc_demote_swarm_headings($content) {
    if (!dadudekc_is_swarm_intro_post()) {
        return $content;
    }

    $content = preg_replace('/<h1([^>]*)>/i', '<h2$1>', $content);
    $content = preg_replace('/<\\/h1>/i', '</h2>', $content);

    return $content;
}
add_filter('the_content', 'dadudekc_demote_swarm_headings', 20);

function dadudekc_get_blog_page_url() {
    $page_for_posts = get_option('page_for_posts');
    if ($page_for_posts) {
        return get_permalink($page_for_posts);
    }

    return home_url('/blog/');
}

function dadudekc_get_portfolio_url() {
    $archive = get_post_type_archive_link('project');
    return $archive ?: home_url('/projects/');
}

function dadudekc_get_idea_lab_url() {
    $page = get_page_by_path('idea-lab');
    if ($page) {
        return get_permalink($page->ID);
    }

    return home_url('/idea-lab/');
}

function dadudekc_get_now_url() {
    $page = get_page_by_path('now');
    if ($page) {
        return get_permalink($page->ID);
    }

    return home_url('/now/');
}

function dadudekc_get_contact_url() {
    $page = get_page_by_path('contact');
    if ($page) {
        return get_permalink($page->ID);
    }

    return home_url('/contact/');
}
