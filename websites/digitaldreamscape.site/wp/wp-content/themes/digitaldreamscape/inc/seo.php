<?php
/**
 * SEO Functions
 * 
 * Meta tags, structured data, and SEO enhancements
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enhanced SEO Meta Tags
 */
function digitaldreamscape_seo_meta_tags()
{
    // Get site name and description
    $site_name = get_bloginfo('name');
    $site_description = get_bloginfo('description');

    if (is_single() || is_page()) {
        global $post;
        $title = get_the_title();
        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30);
        $url = get_permalink();
        $image = has_post_thumbnail() ? get_the_post_thumbnail_url($post->ID, 'large') : '';
    } elseif (is_home() || is_front_page()) {
        $title = $site_name;
        $excerpt = $site_description ? $site_description : 'Build-in-public & streaming hub for Digital Dreamscape. Watch live streams, read updates, and be part of the community.';
        $url = home_url('/');
        $image = '';
    } else {
        $title = wp_get_document_title();
        $excerpt = $site_description ? $site_description : 'Digital Dreamscape is a living, narrative-driven AI world where real actions become story, and story feeds back into execution.';
        $url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $image = '';
    }

    // Meta description
    echo '<meta name="description" content="' . esc_attr($excerpt) . '">' . "\n";

    // Open Graph Meta Tags
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($excerpt) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_single() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    if ($image) {
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    }

    // Twitter Card Meta Tags
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($excerpt) . '">' . "\n";
    if ($image) {
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    }
}
add_action('wp_head', 'digitaldreamscape_seo_meta_tags', 1);

/**
 * Add structured data (JSON-LD) for better SEO
 */
function digitaldreamscape_structured_data()
{
    if (is_single()) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title(),
            'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url('/'),
            ),
        );

        if (has_post_thumbnail()) {
            $schema['image'] = get_the_post_thumbnail_url($post->ID, 'large');
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    } elseif (is_home() || is_front_page()) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'description' => get_bloginfo('description') ? get_bloginfo('description') : 'Build-in-public & streaming hub for Digital Dreamscape',
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'digitaldreamscape_structured_data', 2);

