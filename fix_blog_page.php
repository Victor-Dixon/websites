<?php
/**
 * Fix Blog Page Configuration
 */

define('WP_USE_THEMES', false);
require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

// Check current settings
$page_for_posts = get_option('page_for_posts');
echo "Current posts page ID: " . ($page_for_posts ?: 'NOT SET') . "\n";

// Find or create blog page
$blog_page = get_page_by_path('blog');

if (!$blog_page) {
    echo "Creating blog page...\n";

    // Create the blog page
    $page_id = wp_insert_post([
        'post_title' => 'Blog',
        'post_name' => 'blog',
        'post_content' => 'Welcome to the Digital Dreamscape Blog - our central archive of episodes, lore, and canonical content.',
        'post_status' => 'publish',
        'post_type' => 'page'
    ]);

    if (is_wp_error($page_id)) {
        die("Failed to create blog page: " . $page_id->get_error_message());
    }

    $blog_page = get_post($page_id);
    echo "Created blog page with ID: {$page_id}\n";
} else {
    echo "Found existing blog page with ID: {$blog_page->ID}\n";
}

// Set the page template
update_post_meta($blog_page->ID, '_wp_page_template', 'page-blog.php');
echo "Set page template to: page-blog.php\n";

// Set as posts page if not already set
if (!$page_for_posts || $page_for_posts != $blog_page->ID) {
    update_option('page_for_posts', $blog_page->ID);
    echo "Set as posts page in WordPress settings\n";
}

// Verify settings
$page_for_posts = get_option('page_for_posts');
$template = get_post_meta($blog_page->ID, '_wp_page_template', true);

echo "\n✅ BLOG PAGE CONFIGURATION:\n";
echo "=========================\n";
echo "Posts page ID: {$page_for_posts}\n";
echo "Blog page ID: {$blog_page->ID}\n";
echo "Page template: {$template}\n";
echo "Page URL: " . get_permalink($blog_page) . "\n\n";

echo "🎉 Blog page is now configured correctly!\n";
echo "Visit: https://digitaldreamscape.site/blog/\n\n";

// Clear any caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "Cache cleared\n";
}
?>