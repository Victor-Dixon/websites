<?php
require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

echo "CHECKING BLOG PAGE TEMPLATE\n";
echo "==========================\n\n";

// Check reading settings
$page_for_posts = get_option('page_for_posts');
echo "page_for_posts option: " . ($page_for_posts ?: 'NOT SET') . "\n";

if ($page_for_posts) {
    $page = get_post($page_for_posts);
    if ($page) {
        echo "Page exists:\n";
        echo "  ID: {$page->ID}\n";
        echo "  Title: {$page->post_title}\n";
        echo "  Slug: {$page->post_name}\n";
        echo "  Status: {$page->post_status}\n";

        $template = get_post_meta($page->ID, '_wp_page_template', true);
        echo "  Template: " . ($template ?: 'default') . "\n";
        echo "  URL: " . get_permalink($page) . "\n";
    } else {
        echo "❌ Page with ID {$page_for_posts} not found!\n";
    }
} else {
    echo "❌ No posts page set!\n";
}

// Check if page-blog.php exists
$template_path = 'websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/page-blog.php';
if (file_exists($template_path)) {
    echo "\n✅ page-blog.php template exists\n";
} else {
    echo "\n❌ page-blog.php template NOT found\n";
}

// Check what template is actually being loaded for /blog/
echo "\nTEMPLATE RESOLUTION:\n";
echo "===================\n";

// Try to determine what template WordPress would use for /blog/
$blog_page = get_page_by_path('blog');
if ($blog_page) {
    echo "Found blog page by path: {$blog_page->post_title} (ID: {$blog_page->ID})\n";
    $resolved_template = get_page_template_slug($blog_page->ID);
    echo "Resolved template: " . ($resolved_template ?: 'default (index.php)') . "\n";
} else {
    echo "No page found with slug 'blog'\n";
    echo "WordPress is using index.php for blog/archive pages\n";
}
?>