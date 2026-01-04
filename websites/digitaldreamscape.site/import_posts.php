<?php
/**
 * Import Posts and Metadata from Backup
 *
 * Usage: php import_posts.php < backup.json
 */

require_once 'wp/wp-load.php';

// Read JSON from stdin
$json_input = file_get_contents('php://stdin');
$import_data = json_decode($json_input, true);

if (!$import_data) {
    die("Error: Invalid JSON input\n");
}

echo "🌌 DIGITAL DREAMSCAPE - DATA IMPORT\n";
echo "==================================\n\n";

$imported_posts = 0;
$imported_metadata = 0;
$skipped_posts = 0;

// Import posts
if (isset($import_data['posts']) && is_array($import_data['posts'])) {
    echo "📝 Importing posts...\n";

    foreach ($import_data['posts'] as $post_data) {
        // Check if post already exists
        $existing_posts = get_posts([
            'meta_key' => 'original_id',
            'meta_value' => $post_data['ID'],
            'numberposts' => 1
        ]);

        if (!empty($existing_posts)) {
            echo "- Skipping existing post: {$post_data['post_title']}\n";
            $skipped_posts++;
            continue;
        }

        // Create new post
        $new_post_id = wp_insert_post([
            'post_title' => $post_data['post_title'],
            'post_content' => $post_data['post_content'],
            'post_excerpt' => $post_data['post_excerpt'],
            'post_status' => $post_data['post_status'],
            'post_date' => $post_data['post_date'],
            'post_modified' => $post_data['post_modified'],
            'post_type' => 'post'
        ]);

        if (is_wp_error($new_post_id)) {
            echo "- Error creating post: {$post_data['post_title']} - {$new_post_id->get_error_message()}\n";
            continue;
        }

        // Mark with original ID to prevent duplicates
        update_post_meta($new_post_id, 'original_id', $post_data['ID']);
        update_post_meta($new_post_id, 'imported_at', time());

        echo "- Imported post: {$post_data['post_title']} (ID: {$new_post_id})\n";
        $imported_posts++;
    }
}

// Import metadata
if (isset($import_data['metadata']) && is_array($import_data['metadata'])) {
    echo "\n🏷️  Importing metadata...\n";

    foreach ($import_data['metadata'] as $key => $metadata) {
        if ($key === 'total_posts' || $key === 'total_metadata_entries' || $key === 'export_date' || $key === 'system_version') {
            continue; // Skip metadata about the export itself
        }

        // Extract post ID from key (format: post-{ID})
        if (preg_match('/post-(\d+)/', $key, $matches)) {
            $original_post_id = $matches[1];

            // Find the new post ID
            $new_posts = get_posts([
                'meta_key' => 'original_id',
                'meta_value' => $original_post_id,
                'numberposts' => 1
            ]);

            if (!empty($new_posts)) {
                $new_post_id = $new_posts[0]->ID;

                // Import metadata
                foreach ($metadata as $meta_key => $meta_value) {
                    update_post_meta($new_post_id, $meta_key, $meta_value);
                    $imported_metadata++;
                }

                echo "- Imported metadata for post ID: {$new_post_id}\n";
            }
        }
    }
}

// Import processed artifacts tracking
if (isset($import_data['processed_artifacts']) && is_array($import_data['processed_artifacts'])) {
    echo "\n📊 Importing processed artifacts tracking...\n";

    if (file_exists('processed_artifacts.json')) {
        $existing = json_decode(file_get_contents('processed_artifacts.json'), true) ?: [];
        $merged = array_merge_recursive($existing, $import_data['processed_artifacts']);
    } else {
        $merged = $import_data['processed_artifacts'];
    }

    file_put_contents('processed_artifacts.json', json_encode($merged, JSON_PRETTY_PRINT));
    echo "- Processed artifacts tracking updated\n";
}

echo "\n✅ IMPORT COMPLETE\n";
echo "==================\n";
echo "Posts imported: {$imported_posts}\n";
echo "Posts skipped: {$skipped_posts}\n";
echo "Metadata entries imported: {$imported_metadata}\n";

if (isset($import_data['metadata']['export_date'])) {
    echo "Source export date: {$import_data['metadata']['export_date']}\n";
}

echo "\n🎯 Next Steps:\n";
echo "- Run promotion cycle: php auto_promotion_daemon.php run\n";
echo "- Check system status: php system_status.php\n";
echo "- Verify imported content in World Archive\n";
?>