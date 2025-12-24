<?php

/**
 * Digital Dreamscape Theme Functions
 * 
 * Living, narrative-driven AI world theme
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function digitaldreamscape_setup()
{
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 300,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('automatic-feed-links');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'digitaldreamscape'),
        'footer' => __('Footer Menu', 'digitaldreamscape'),
    ));
}
add_action('after_setup_theme', 'digitaldreamscape_setup');

/**
 * Enqueue Styles and Scripts with Performance Optimizations
 */
function digitaldreamscape_scripts()
{
    // Enqueue theme stylesheet
    wp_enqueue_style('digitaldreamscape-style', get_stylesheet_uri(), array(), '2.0.1');

    // Enqueue theme JavaScript (load in footer for better performance)
    wp_enqueue_script('digitaldreamscape-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '2.0.1', true);

    // Add Digital Dreamscape context to page
    wp_localize_script('digitaldreamscape-script', 'dreamscapeContext', array(
        'isEpisode' => is_single(),
        'isArchive' => is_archive(),
        'narrativeMode' => true,
    ));
}
add_action('wp_enqueue_scripts', 'digitaldreamscape_scripts');

/**
 * Performance Optimizations
 */

// Lazy load images (native WordPress support for WordPress 5.5+)
function digitaldreamscape_lazy_load_images($attr, $attachment, $size)
{
    if (!is_admin()) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'digitaldreamscape_lazy_load_images', 10, 3);

// Remove unnecessary WordPress features for better performance
function digitaldreamscape_performance_cleanup()
{
    // Remove emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    // Remove unnecessary RSS feed links
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');

    // Remove shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('init', 'digitaldreamscape_performance_cleanup');

// Optimize WordPress queries
function digitaldreamscape_optimize_queries($query)
{
    if (!is_admin() && $query->is_main_query()) {
        // Limit post queries to improve performance
        if (is_home() || is_archive()) {
            $query->set('posts_per_page', 12);
        }
    }
}
add_action('pre_get_posts', 'digitaldreamscape_optimize_queries');

/**
 * Register Widget Areas
 */
function digitaldreamscape_widgets_init()
{
    register_sidebar(array(
        'name' => __('Sidebar', 'digitaldreamscape'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'digitaldreamscape'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'digitaldreamscape_widgets_init');

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


/**
 * Enhanced Template Loading Fix
 * Ensures page templates load correctly and handles cache clearing
 * Priority 999 ensures this runs before most other template filters
 * 
 * Applied: 2025-12-23
 */
add_filter('template_include', function ($template) {
    // Skip admin and AJAX requests
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return $template;
    }

    // Get the page slug from URL or post object
    $page_slug = null;

    if (is_page()) {
        global $post;
        if ($post && isset($post->post_name)) {
            $page_slug = $post->post_name;
        }
    }

    // Fallback: Check URL directly
    if (!$page_slug && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $request_parts = explode('/', $request_uri);
        $page_slug = end($request_parts);
    }

    // Map page slugs to templates (customize per site)
    $page_templates = array(
        // Add site-specific page templates here
        // Example: 'about' => 'page-templates/page-about.php',
        // Example: 'blog' => 'page-templates/page-blog.php',
    );

    if ($page_slug && isset($page_templates[$page_slug])) {
        $custom_template = locate_template($page_templates[$page_slug]);

        if ($custom_template && file_exists($custom_template)) {
            // If page exists but template isn't set, update it
            if (is_page()) {
                global $post;
                $current_template = get_page_template_slug($post->ID);
                if ($current_template !== $page_templates[$page_slug]) {
                    update_post_meta($post->ID, '_wp_page_template', $page_templates[$page_slug]);
                }
            }

            return $custom_template;
        }
    }

    // Handle 404 cases (fallback for pages that don't exist yet)
    if (is_404() && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $request_parts = explode('/', $request_uri);
        $uri_slug = end($request_parts);

        if (isset($page_templates[$uri_slug])) {
            $new_template = locate_template($page_templates[$uri_slug]);
            if ($new_template && file_exists($new_template)) {
                // Set up WordPress query to treat this as a page
                global $wp_query;
                $wp_query->is_404 = false;
                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                $wp_query->queried_object = (object) array(
                    'post_type' => 'page',
                    'post_name' => $uri_slug,
                );
                return $new_template;
            }
        }
    }

    return $template;
}, 999);

/**
 * Clear cache when theme is activated or updated
 * This helps ensure template changes take effect immediately
 */
function clear_template_cache_on_theme_change()
{
    // Clear object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }

    // Clear LiteSpeed Cache if active
    if (class_exists('LiteSpeed_Cache') && method_exists('LiteSpeed_Cache', 'purge_all')) {
        LiteSpeed_Cache::purge_all();
    }

    // Clear rewrite rules to ensure permalinks work
    flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'clear_template_cache_on_theme_change');

/**
 * Default menu fallback if no menu is set
 */
function digitaldreamscape_default_menu()
{
?>
    <ul id="primary-menu" class="menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
        <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
        <li><a href="<?php echo esc_url(home_url('/streaming')); ?>">Streaming</a></li>
        <li><a href="<?php echo esc_url(home_url('/community')); ?>">Community</a></li>
        <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
    </ul>
<?php
}
