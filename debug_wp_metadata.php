<?php
/**
 * Debug WordPress Episode Metadata
 */

require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

echo "🔍 DEBUGGING WORDPRESS EPISODE METADATA\n";
echo "======================================\n\n";

// Get the existing episode post
$existing_posts = get_posts([
    'meta_key' => 'artifact_type',
    'meta_value' => 'episode',
    'numberposts' => -1
]);

echo "Found " . count($existing_posts) . " episode posts:\n\n";

foreach ($existing_posts as $post) {
    echo "Post ID: {$post->ID}\n";
    echo "Title: {$post->post_title}\n";

    // Get all metadata for this post
    echo "Metadata:\n";
    $artifact_type = get_post_meta($post->ID, 'artifact_type', true);
    $episode_number = get_post_meta($post->ID, 'episode_number', true);
    $questline = get_post_meta($post->ID, 'questline', true);
    $artifact_state = get_post_meta($post->ID, 'artifact_state', true);

    echo "  artifact_type: '{$artifact_type}'\n";
    echo "  episode_number: '{$episode_number}' (length: " . strlen($episode_number) . ")\n";
    echo "  questline: '{$questline}'\n";
    echo "  artifact_state: '{$artifact_state}'\n";
    echo "\n";
}

// Test the duplicate checking query that's used in import
echo "TESTING DUPLICATE CHECK QUERY:\n";
echo "===============================\n";

$test_episode_numbers = [145, 146, 147, 1000];

// Get all existing episodes for manual checking
$existing_episodes = get_posts([
    'meta_key' => 'artifact_type',
    'meta_value' => 'episode',
    'numberposts' => -1
]);

foreach ($test_episode_numbers as $ep_num) {
    $found = false;
    $found_post_id = null;

    foreach ($existing_episodes as $existing_post) {
        $existing_ep_num = get_post_meta($existing_post->ID, 'episode_number', true);
        if ($existing_ep_num == $ep_num) {
            $found = true;
            $found_post_id = $existing_post->ID;
            break;
        }
    }

    echo "Episode {$ep_num}: " . ($found ? 'FOUND (Post ID: ' . $found_post_id . ')' : 'NOT FOUND') . "\n";
}

echo "\n🔍 Checking all posts with episode_number metadata:\n";
$all_posts_with_ep_num = get_posts([
    'meta_key' => 'episode_number',
    'numberposts' => -1
]);

foreach ($all_posts_with_ep_num as $post) {
    $ep_num = get_post_meta($post->ID, 'episode_number', true);
    echo "Post {$post->ID}: episode_number = {$ep_num}\n";
}
?>