<?php
/**
 * Check WordPress Settings for Blog Page
 */

// Load WordPress
define('WP_USE_THEMES', false);
require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

echo "🔧 WORDPRESS BLOG SETTINGS CHECK\n";
echo "================================\n\n";

// Check reading settings
$page_for_posts = get_option('page_for_posts');
$page_on_front = get_option('page_on_front');
$show_on_front = get_option('show_on_front');

echo "📖 Reading Settings:\n";
echo "==================\n";
echo "Show on front: {$show_on_front}\n";
echo "Posts page ID: " . ($page_for_posts ?: 'NOT SET') . "\n";
echo "Front page ID: " . ($page_on_front ?: 'NOT SET') . "\n\n";

// Check if posts page exists
if ($page_for_posts) {
    $posts_page = get_post($page_for_posts);
    if ($posts_page) {
        echo "📄 Posts Page Details:\n";
        echo "===================\n";
        echo "ID: {$posts_page->ID}\n";
        echo "Title: {$posts_page->post_title}\n";
        echo "Slug: {$posts_page->post_name}\n";
        echo "Status: {$posts_page->post_status}\n";
        echo "URL: " . get_permalink($posts_page) . "\n\n";
    } else {
        echo "❌ Posts page ID {$page_for_posts} not found!\n\n";
    }
} else {
    echo "❌ NO POSTS PAGE SET!\n";
    echo "This is why the blog isn't working.\n\n";
    echo "🔧 TO FIX:\n";
    echo "1. Go to WordPress Admin > Settings > Reading\n";
    echo "2. Set 'Posts page' to a page with slug 'blog'\n";
    echo "3. Or create a page called 'Blog' and set it as Posts page\n\n";
}

// Check if there's a page with slug 'blog'
$blog_page = get_page_by_path('blog');
if ($blog_page) {
    echo "📄 Found page with slug 'blog':\n";
    echo "============================\n";
    echo "ID: {$blog_page->ID}\n";
    echo "Title: {$blog_page->post_title}\n";
    echo "Template: " . get_page_template_slug($blog_page->ID) . "\n";
    echo "URL: " . get_permalink($blog_page) . "\n\n";
} else {
    echo "❌ No page with slug 'blog' found!\n\n";
}

// Check total posts
$total_posts = wp_count_posts()->publish;
echo "📊 Content Status:\n";
echo "=================\n";
echo "Total published posts: {$total_posts}\n";

// Check episode posts
$episode_posts = get_posts([
    'meta_key' => 'artifact_type',
    'meta_value' => 'episode',
    'numberposts' => -1
]);
echo "Episode posts: " . count($episode_posts) . "\n\n";

if (!$page_for_posts && $blog_page) {
    echo "💡 RECOMMENDED FIX:\n";
    echo "==================\n";
    echo "Set the 'blog' page (ID: {$blog_page->ID}) as your Posts page in Settings > Reading\n\n";
}
?>