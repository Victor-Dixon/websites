<?php
/**
 * Import Sample Projects for Portfolio
 *
 * This script reads sample_projects.json and creates project posts
 * Run this once to populate the portfolio with sample content
 *
 * Usage: php import_sample_projects.php
 */

require_once '../../../wp-load.php';

if (!defined('ABSPATH')) {
    die('WordPress not loaded');
}

if (!current_user_can('administrator')) {
    die('Administrator access required');
}

$json_file = __DIR__ . '/sample_projects.json';

if (!file_exists($json_file)) {
    die("Sample projects JSON file not found: $json_file\n");
}

$projects = json_decode(file_get_contents($json_file), true);

if (!$projects) {
    die("Failed to parse JSON file\n");
}

$imported_count = 0;
$skipped_count = 0;

foreach ($projects as $project_data) {
    // Check if project already exists
    $existing_post = get_page_by_title($project_data['title'], OBJECT, 'project');

    if ($existing_post) {
        echo "Skipping existing project: {$project_data['title']}\n";
        $skipped_count++;
        continue;
    }

    // Create the project post
    $post_id = wp_insert_post([
        'post_title' => $project_data['title'],
        'post_content' => $project_data['content'],
        'post_excerpt' => $project_data['excerpt'],
        'post_status' => 'publish',
        'post_type' => 'project',
        'post_author' => 1,
    ]);

    if (!$post_id) {
        echo "Failed to create project: {$project_data['title']}\n";
        continue;
    }

    // Add meta fields
    foreach ($project_data['meta'] as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    // Handle featured image if specified
    if (isset($project_data['featured_image'])) {
        // Note: This would require actual image files in uploads directory
        // For now, we'll skip this as images aren't included in the sample data
    }

    echo "Imported project: {$project_data['title']}\n";
    $imported_count++;
}

// Clear any caches
wp_cache_flush();

echo "\nImport complete!\n";
echo "Imported: $imported_count projects\n";
echo "Skipped: $skipped_count existing projects\n";
echo "\nVisit your portfolio at: " . home_url('/portfolio') . "\n";