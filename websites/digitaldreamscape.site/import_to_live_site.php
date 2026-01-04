<?php
/**
 * Import our posts to the live WordPress site
 */

require_once 'wp/wp-load.php';

echo "🌐 IMPORTING POSTS TO LIVE SITE\n";
echo "================================\n\n";

// Get our posts
$our_posts = get_posts(['numberposts' => -1]);
echo "Found " . count($our_posts) . " posts to import\n\n";

// Method 1: Try WordPress REST API
echo "=== METHOD 1: REST API IMPORT ===\n";

// First, let's try to get authentication. We'll need to check if we can authenticate
// For now, let's try a simple approach - check if the live site has our posts already

$live_posts_url = 'https://digitaldreamscape.site/wp-json/wp/v2/posts?per_page=100';
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: DigitalDreamscape-Importer/1.0',
        'timeout' => 10
    ]
]);

$live_posts_response = file_get_contents($live_posts_url, false, $context);
if ($live_posts_response) {
    $live_posts = json_decode($live_posts_response, true);
    if (is_array($live_posts)) {
        echo "✅ Live site accessible via REST API\n";
        echo "Live site has " . count($live_posts) . " posts\n\n";

        // Check if our posts are there
        $our_titles = array_map(function($post) { return $post->post_title; }, $our_posts);
        $live_titles = array_map(function($post) { return $post['title']['rendered']; }, $live_posts);

        $missing_posts = array_diff($our_titles, $live_titles);
        if (empty($missing_posts)) {
            echo "✅ All our posts are already on the live site!\n";
        } else {
            echo "❌ Missing posts on live site:\n";
            foreach ($missing_posts as $title) {
                echo "  - $title\n";
            }
            echo "\n";

            // Try to import via REST API (this would need authentication)
            echo "To import missing posts, we need:\n";
            echo "1. WordPress REST API authentication (Application Passwords)\n";
            echo "2. Or direct database access to the live site\n";
            echo "3. Or manual import via WordPress admin\n\n";

            echo "=== MANUAL IMPORT INSTRUCTIONS ===\n";
            echo "1. Go to https://digitaldreamscape.site/wp-admin/\n";
            echo "2. For each missing post, create it manually or\n";
            echo "3. Use the export/import functionality\n\n";

            // Generate import commands
            echo "=== AUTOMATED IMPORT SCRIPT ===\n";
            echo "Run this on the live server:\n\n";

            foreach ($our_posts as $post) {
                $title = addslashes($post->post_title);
                $content = addslashes($post->post_content);
                $excerpt = addslashes($post->post_excerpt);

                // Get metadata
                $artifact_type = get_post_meta($post->ID, 'artifact_type', true) ?: 'episode';
                $questline = get_post_meta($post->ID, 'questline', true) ?: '';

                echo "wp post create --post_title=\"$title\" --post_content=\"$content\" --post_excerpt=\"$excerpt\" --post_status=publish\n";
                if ($artifact_type) {
                    echo "wp post meta set [POST_ID] artifact_type $artifact_type\n";
                }
                if ($questline) {
                    echo "wp post meta set [POST_ID] questline $questline\n";
                }
                echo "\n";
            }
        }
    } else {
        echo "❌ Could not parse live site posts\n";
    }
} else {
    echo "❌ Could not access live site REST API\n";
    echo "This might be due to:\n";
    echo "- REST API disabled\n";
    echo "- Authentication required\n";
    echo "- Network issues\n\n";
}

echo "=== METHOD 2: DATABASE SYNC ===\n";
echo "If we had direct database access, we could sync the posts directly.\n";
echo "This would require database credentials for the live site.\n\n";

echo "=== METHOD 3: FILE TRANSFER ===\n";
echo "We could export our posts as WordPress XML and import via Tools > Import\n\n";

echo "=== RECOMMENDED APPROACH ===\n";
echo "1. Export our posts: php export_posts.php > posts_backup.json\n";
echo "2. Import to live site manually via WordPress admin\n";
echo "3. Or set up WP-CLI on the live server for automated import\n\n";

echo "Once posts are imported, our theme will display them properly!\n";
?>