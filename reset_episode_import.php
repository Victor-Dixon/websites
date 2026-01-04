<?php
/**
 * Reset Episode Import Process
 *
 * Fixes metadata issues and resets import progress for a clean restart
 */

require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

echo "🔄 RESETTING EPISODE IMPORT PROCESS\n";
echo "===================================\n\n";

// 1. Fix the existing episode post metadata
$existing_episodes = get_posts([
    'meta_key' => 'artifact_type',
    'meta_value' => 'episode',
    'numberposts' => -1
]);

echo "📝 Found " . count($existing_episodes) . " existing episode posts\n";

foreach ($existing_episodes as $post) {
    echo "Fixing Post ID: {$post->ID} - {$post->post_title}\n";

    // Extract episode number from title
    if (preg_match('/EP-(\d+):/', $post->post_title, $matches)) {
        $episode_number = (int) $matches[1];
        update_post_meta($post->ID, 'episode_number', $episode_number);
        echo "  ✅ Set episode_number to: {$episode_number}\n";
    } else {
        echo "  ❌ Could not extract episode number from title\n";
    }
}

// 2. Reset import progress file
$progress_file = 'websites/digitaldreamscape.site/episode_import_progress.json';
if (file_exists($progress_file)) {
    echo "\n📊 Resetting import progress file...\n";
    $progress = json_decode(file_get_contents($progress_file), true);

    // Keep only successfully imported episodes
    $reset_progress = [
        'total_found' => $progress['total_found'] ?? 0,
        'processed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'start_time' => time(),
        'last_batch_time' => time(),
        'processed_files' => []
    ];

    // Only keep successful imports
    foreach (($progress['processed_files'] ?? []) as $filename => $data) {
        if (($data['status'] ?? '') === 'success') {
            $reset_progress['processed_files'][$filename] = $data;
            $reset_progress['processed']++;
        }
    }

    file_put_contents($progress_file, json_encode($reset_progress, JSON_PRETTY_PRINT));
    echo "✅ Import progress reset (keeping " . $reset_progress['processed'] . " successful imports)\n";
}

// 3. Test the metadata fix
echo "\n🧪 TESTING METADATA FIX\n";
echo "=======================\n";

$test_episodes = [145, 146, 147, 1000];
foreach ($test_episodes as $ep_num) {
    $existing = get_posts([
        'meta_key' => 'episode_number',
        'meta_value' => $ep_num,
        'numberposts' => 1
    ]);

    $status = empty($existing) ? 'NOT FOUND' : 'FOUND (Post ID: ' . $existing[0]->ID . ')';
    echo "Episode {$ep_num}: {$status}\n";
}

echo "\n🎯 RESET COMPLETE!\n";
echo "=================\n";
echo "• Fixed existing episode metadata\n";
echo "• Reset import progress\n";
echo "• Ready for clean import restart\n\n";
echo "🚀 Run: cd websites/digitaldreamscape.site && php batch_import_episodes.php\n";
?>