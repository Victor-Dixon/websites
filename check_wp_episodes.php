<?php
/**
 * Check WordPress Episode Status
 */

require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

echo "📝 WORDPRESS EPISODE STATUS\n";
echo "==========================\n\n";

// Get all posts
$all_posts = get_posts(['numberposts' => -1]);
echo "📄 Total posts: " . count($all_posts) . "\n";

// Get episode posts
$episode_posts = get_posts([
    'meta_key' => 'artifact_type',
    'meta_value' => 'episode',
    'numberposts' => -1
]);

echo "🎭 Episode posts: " . count($episode_posts) . "\n\n";

if (!empty($episode_posts)) {
    echo "📋 Latest episodes:\n";

    // Sort by episode number
    usort($episode_posts, function($a, $b) {
        $num_a = get_post_meta($a->ID, 'episode_number', true) ?: 0;
        $num_b = get_post_meta($b->ID, 'episode_number', true) ?: 0;
        return $num_b - $num_a; // Descending
    });

    $latest = array_slice($episode_posts, 0, 10);
    foreach ($latest as $post) {
        $episode_num = get_post_meta($post->ID, 'episode_number', true);
        $questline = get_post_meta($post->ID, 'questline', true);
        echo "   • EP-{$episode_num} [{$questline}]: {$post->post_title}\n";
    }
} else {
    echo "❌ No episode posts found in WordPress\n";
}

echo "\n🌐 Site URL: https://digitaldreamscape.site/blog/\n";
?>