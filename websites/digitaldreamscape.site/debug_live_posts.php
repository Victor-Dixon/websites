<?php
/**
 * Debug what's happening with posts on the live site
 */

require_once 'wp/wp-load.php';

echo "=== LOCAL WORDPRESS POSTS ===\n";
$local_posts = get_posts(['numberposts' => -1]);
echo "Found " . count($local_posts) . " posts in our system:\n";

foreach ($local_posts as $post) {
    echo "- ID {$post->ID}: {$post->post_title}\n";
}

echo "\n=== CHECKING LIVE SITE ===\n";

// Check if we can access the live site's posts
// This is a bit tricky since we can't directly query the live database
// Let's check if our posts exist in our system vs what the live site shows

echo "Our system has posts, but the live site doesn't show them.\n";
echo "This suggests the live site is using a different WordPress installation\n";
echo "or database than our file-based system.\n\n";

echo "=== POSSIBLE SOLUTIONS ===\n";
echo "1. Export our posts and import them into the live WordPress database\n";
echo "2. Configure our system to use the live WordPress database\n";
echo "3. Set up our theme on the live WordPress installation\n";
echo "4. Sync the two systems\n\n";

// Check if we can connect to a real database
echo "=== DATABASE CONNECTION TEST ===\n";
global $wpdb;
if ($wpdb && method_exists($wpdb, 'get_results')) {
    try {
        $test_query = $wpdb->get_results("SELECT COUNT(*) as post_count FROM {$wpdb->posts} WHERE post_status = 'publish'");
        if ($test_query) {
            echo "✅ Database connection working\n";
            echo "Live posts in database: " . $test_query[0]->post_count . "\n";
        } else {
            echo "❌ Database query failed\n";
        }
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ No database connection (file-based mode)\n";
}

echo "\n=== RECOMMENDATION ===\n";
echo "We need to either:\n";
echo "A) Import our posts into the live WordPress database, OR\n";
echo "B) Configure our automation to work with the live database\n\n";

echo "Let's check if we can import our posts to the live site...\n";
?>