<?php
require_once 'wp/wp-load.php';

echo "Checking existing posts...\n\n";

$posts = get_posts(['numberposts' => 10]);
echo "Found " . count($posts) . " posts\n\n";

foreach ($posts as $post) {
    $episode_num = get_post_meta($post->ID, 'episode_number', true);
    $artifact_type = get_post_meta($post->ID, 'artifact_type', true);
    echo "ID {$post->ID}: {$post->post_title}\n";
    echo "  Episode: {$episode_num} | Type: {$artifact_type}\n";
    echo "  Meta keys: " . implode(', ', array_keys(get_post_meta($post->ID))) . "\n\n";
}
?>