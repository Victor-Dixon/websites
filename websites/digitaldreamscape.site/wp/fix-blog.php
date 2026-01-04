<?php
/**
 * Fix Blog Configuration - Place in wp/ directory
 * Access at: https://digitaldreamscape.site/wp/fix-blog.php
 */

require_once '../wp/wp-load.php';

// Only allow access from localhost/CLI
if (!isset($_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
    echo "<h1>🔧 Fixing Blog Configuration</h1>\n";
    echo "<pre>\n";

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

    // Clear any caches
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
        echo "Cache cleared\n";
    }

    echo "</pre>\n";
    echo "<h2>🎉 Blog page configuration complete!</h2>\n";
    echo "<p><a href='/blog/' target='_blank'>Visit the blog</a> to see your episodes.</p>\n";

} else {
    http_response_code(403);
    echo "<h1>Access Denied</h1>\n";
    echo "<p>This script can only be accessed locally.</p>\n";
}
?>