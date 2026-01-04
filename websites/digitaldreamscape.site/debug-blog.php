<?php
/**
 * Debug Blog Template - Web accessible
 * Access at: https://digitaldreamscape.site/debug-blog.php
 */

require_once 'wp/wp-load.php';

echo "<h1>🔍 BLOG TEMPLATE DEBUG</h1>";
echo "<pre>";

// Check reading settings
$page_for_posts = get_option('page_for_posts');
echo "page_for_posts: " . ($page_for_posts ?: 'NOT SET') . "\n\n";

if ($page_for_posts) {
    $page = get_post($page_for_posts);
    if ($page) {
        echo "POSTS PAGE DETAILS:\n";
        echo "==================\n";
        echo "ID: {$page->ID}\n";
        echo "Title: {$page->post_title}\n";
        echo "Slug: {$page->post_name}\n";
        echo "Status: {$page->post_status}\n";

        $template = get_post_meta($page->ID, '_wp_page_template', true);
        echo "Template: " . ($template ?: 'default') . "\n";
        echo "URL: " . get_permalink($page) . "\n\n";
    }
}

// Check if blog page exists
$blog_page = get_page_by_path('blog');
if ($blog_page) {
    echo "BLOG PAGE FOUND:\n";
    echo "================\n";
    echo "ID: {$blog_page->ID}\n";
    echo "Title: {$blog_page->post_title}\n";
    echo "Slug: {$blog_page->post_name}\n";

    $template = get_post_meta($blog_page->ID, '_wp_page_template', true);
    echo "Template: " . ($template ?: 'default') . "\n";
    echo "URL: " . get_permalink($blog_page) . "\n\n";

    // If template is not page-blog.php, fix it
    if ($template !== 'page-blog.php') {
        echo "🔧 FIXING TEMPLATE...\n";
        update_post_meta($blog_page->ID, '_wp_page_template', 'page-blog.php');
        echo "✅ Set template to page-blog.php\n";

        // Also set as posts page if not already
        if (get_option('page_for_posts') != $blog_page->ID) {
            update_option('page_for_posts', $blog_page->ID);
            echo "✅ Set as posts page\n";
        }

        echo "\n🎉 FIXED! Refresh the blog page.\n";
    }
} else {
    echo "❌ NO BLOG PAGE FOUND\n";
    echo "Creating blog page...\n";

    $page_id = wp_insert_post([
        'post_title' => 'Blog',
        'post_name' => 'blog',
        'post_content' => 'Welcome to the Digital Dreamscape Blog.',
        'post_status' => 'publish',
        'post_type' => 'page'
    ]);

    if (!is_wp_error($page_id)) {
        update_post_meta($page_id, '_wp_page_template', 'page-blog.php');
        update_option('page_for_posts', $page_id);
        echo "✅ Created blog page (ID: {$page_id}) with page-blog.php template\n";
    } else {
        echo "❌ Failed to create blog page\n";
    }
}

// Check template file
$template_file = __DIR__ . '/wp/wp-content/themes/digitaldreamscape/page-blog.php';
if (file_exists($template_file)) {
    echo "\n✅ page-blog.php template file exists\n";
} else {
    echo "\n❌ page-blog.php template file NOT found\n";
}

echo "</pre>";
echo "<p><a href='/blog/' target='_blank'>View Blog</a></p>";
?>