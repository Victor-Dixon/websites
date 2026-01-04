<?php
/**
 * Reset posts and run full batch import of all 3000+ episodes
 */

require_once 'wp/wp-load.php';

echo "🌌 DIGITAL DREAMSCAPE - FULL RESET & IMPORT\n";
echo "=============================================\n\n";

echo "⚠️  WARNING: This will delete all existing posts!\n";
echo "Press Ctrl+C to cancel, or wait 5 seconds to continue...\n";

for ($i = 5; $i > 0; $i--) {
    echo "{$i}...\n";
    sleep(1);
}

echo "\n🗑️  Deleting existing posts...\n";

// Get all posts
$existing_posts = get_posts(['numberposts' => -1, 'post_type' => 'post']);
$deleted = 0;

foreach ($existing_posts as $post) {
    if (wp_delete_post($post->ID, true)) {
        $deleted++;
        echo "Deleted: {$post->post_title}\n";
    }
}

echo "\n✅ Deleted {$deleted} posts\n\n";

// Clear post files
array_map('unlink', glob('wp-content/posts/*.json'));
array_map('unlink', glob('wp-content/meta/*.json'));

// Clear tracking files
@unlink('processed_artifacts.json');
@unlink('auto_promotion.log');
@unlink('canon_declaration.log');
@unlink('episode_import_progress.json');

echo "🧹 Cleared all cached files\n\n";

echo "🚀 Starting full batch import of 3000+ episodes...\n\n";

// Run the batch importer
require_once 'batch_import_episodes.php';

$importer = new EpisodeBatchImporter();
$importer->run_import();

echo "\n\n🎉 COMPLETE! All 3000+ episodes have been imported!\n\n";

echo "🔄 Run these commands to finish setup:\n";
echo "php canon_declaration_system.php scan\n";
echo "php system_status.php\n\n";

echo "🌐 Visit https://digitaldreamscape.site/blog/ to see all episodes!\n";
?>