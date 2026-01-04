<?php
/**
 * Export Posts and Metadata for Backup/Migration
 *
 * Usage: php export_posts.php > backup.json
 */

require_once 'wp/wp-load.php';

$export_data = [
    'metadata' => [
        'export_date' => date('Y-m-d H:i:s'),
        'system_version' => 'Digital Dreamscape v1.0',
        'total_posts' => 0,
        'total_metadata_entries' => 0,
    ],
    'posts' => [],
    'metadata' => [],
    'processed_artifacts' => []
];

// Get all posts
$posts = get_posts(['numberposts' => -1]);
$export_data['metadata']['total_posts'] = count($posts);

// Export posts
foreach ($posts as $post) {
    $post_data = [
        'ID' => $post->ID,
        'post_title' => $post->post_title,
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_status' => $post->post_status,
        'post_date' => $post->post_date,
        'post_modified' => $post->post_modified,
    ];

    $export_data['posts'][] = $post_data;

    // Get metadata for this post
    $meta_file = "wp-content/meta/post-{$post->ID}-meta.json";
    if (file_exists($meta_file)) {
        $metadata = json_decode(file_get_contents($meta_file), true);
        if ($metadata) {
            $export_data['metadata']["post-{$post->ID}"] = $metadata;
            $export_data['metadata']['total_metadata_entries'] += count($metadata);
        }
    }
}

// Include processed artifacts tracking
if (file_exists('processed_artifacts.json')) {
    $export_data['processed_artifacts'] = json_decode(file_get_contents('processed_artifacts.json'), true);
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>