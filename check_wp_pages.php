<?php
require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

echo "WORDPRESS PAGE AND BLOG CONFIGURATION\n";
echo "=====================================\n\n";

// Check existing pages
$pages = get_posts([
    'post_type' => 'page',
    'post_status' => 'publish',
    'numberposts' => -1
]);

echo "EXISTING PAGES:\n";
echo "===============\n";
if (empty($pages)) {
    echo "No published pages found.\n\n";
} else {
    foreach ($pages as $page) {
        $template = get_post_meta($page->ID, '_wp_page_template', true);
        echo "ID: {$page->ID}\n";
        echo "Title: {$page->post_title}\n";
        echo "Slug: {$page->post_name}\n";
        echo "Template: " . ($template ?: 'default') . "\n";
        echo "URL: " . get_permalink($page) . "\n\n";
    }
}

// Check reading settings
$page_for_posts = get_option('page_for_posts');
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');

echo "READING SETTINGS:\n";
echo "=================\n";
echo "Show on front: {$show_on_front}\n";
echo "Posts page ID: " . ($page_for_posts ?: 'NOT SET') . "\n";
echo "Front page ID: " . ($page_on_front ?: 'NOT SET') . "\n\n";

// Check if we need to create/fix the blog page
if (!$page_for_posts) {
    echo "❌ NO POSTS PAGE SET!\n";
    echo "This is why the blog shows the wrong content.\n\n";

    // Try to find a page with slug 'blog'
    $blog_page = get_page_by_path('blog');

    if ($blog_page) {
        echo "✅ Found existing blog page: {$blog_page->post_title} (ID: {$blog_page->ID})\n";
        echo "Setting it as the posts page...\n";

        // Set template and posts page
        update_post_meta($blog_page->ID, '_wp_page_template', 'page-blog.php');
        update_option('page_for_posts', $blog_page->ID);

        echo "✅ FIXED: Blog page configured!\n";
        echo "Template: page-blog.php\n";
        echo "Posts page: {$blog_page->ID}\n";
        echo "URL: " . get_permalink($blog_page) . "\n\n";

    } else {
        echo "Creating new blog page...\n";

        $page_id = wp_insert_post([
            'post_title' => 'Blog',
            'post_name' => 'blog',
            'post_content' => 'Welcome to the Digital Dreamscape Blog - our central archive of episodes, lore, and canonical content.',
            'post_status' => 'publish',
            'post_type' => 'page'
        ]);

        if (!is_wp_error($page_id)) {
            // Set template and posts page
            update_post_meta($page_id, '_wp_page_template', 'page-blog.php');
            update_option('page_for_posts', $page_id);

            echo "✅ CREATED AND CONFIGURED: Blog page (ID: {$page_id})\n";
            echo "Template: page-blog.php\n";
            echo "URL: " . get_permalink($page_id) . "\n\n";
        } else {
            echo "❌ FAILED to create blog page: " . $page_id->get_error_message() . "\n\n";
        }
    }
} else {
    $posts_page = get_post($page_for_posts);
    if ($posts_page) {
        $template = get_post_meta($posts_page->ID, '_wp_page_template', true);
        echo "✅ Posts page is set: {$posts_page->post_title} (ID: {$posts_page->ID})\n";
        echo "Template: " . ($template ?: 'default') . "\n";
        echo "URL: " . get_permalink($posts_page) . "\n\n";

        if ($template !== 'page-blog.php') {
            echo "⚠️  WARNING: Template is not set to page-blog.php\n";
            echo "Setting template to page-blog.php...\n";
            update_post_meta($posts_page->ID, '_wp_page_template', 'page-blog.php');
            echo "✅ Template updated!\n\n";
        }
    } else {
        echo "❌ Posts page ID {$page_for_posts} not found!\n\n";
    }
}

// Clear caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "Cache cleared.\n";
}

echo "🎉 BLOG CONFIGURATION COMPLETE!\n";
echo "===============================\n";
echo "Visit: https://digitaldreamscape.site/blog/ to see your episodes.\n\n";
?>