<?php
/**
 * Batch Episode Sync - Processes episodes in smaller chunks to avoid timeouts
 */

class BatchEpisodeSyncer {
    private $dev_dir;
    private $deploy_dir;
    private $batch_size = 500; // Process 500 episodes at a time
    private $sync_log = [];

    public function __construct() {
        $this->dev_dir = 'D:/websites/digitaldreamscape.site/episodes';
        $this->deploy_dir = 'D:/websites/websites/digitaldreamscape.site/episodes';
        $this->sync_log_file = dirname(__FILE__) . '/episode_sync_log.json';
        $this->load_sync_log();
    }

    public function run_batch_sync() {
        echo "🔄 BATCH EPISODE SYNC\n";
        echo "===================\n\n";

        // Get all episodes from development
        $dev_episodes = glob($this->dev_dir . '/ep_*.html');
        $deploy_episodes = glob($this->deploy_dir . '/ep_*.html');

        echo "📂 Development: " . count($dev_episodes) . " episodes\n";
        echo "📂 Deployment: " . count($deploy_episodes) . " episodes\n\n";

        // Find episodes not yet synced
        $existing_in_deploy = [];
        foreach ($deploy_episodes as $deploy_file) {
            $existing_in_deploy[] = basename($deploy_file);
        }

        $to_sync = [];
        foreach ($dev_episodes as $dev_file) {
            $filename = basename($dev_file);
            if (!in_array($filename, $existing_in_deploy) &&
                !isset($this->sync_log[$filename])) {
                $to_sync[] = $dev_file;
            }
        }

        if (empty($to_sync)) {
            echo "✅ All episodes already synced!\n\n";
            return;
        }

        // Sort by episode number for consistent processing
        usort($to_sync, function($a, $b) {
            preg_match('/ep_(\d+)_/', basename($a), $match_a);
            preg_match('/ep_(\d+)_/', basename($b), $match_b);
            return ($match_a[1] ?? 0) - ($match_b[1] ?? 0);
        });

        $total_to_sync = count($to_sync);
        echo "🚀 Syncing {$total_to_sync} episodes in batches of {$this->batch_size}\n\n";

        $batches = array_chunk($to_sync, $this->batch_size);
        $total_synced = 0;
        $total_failed = 0;

        foreach ($batches as $batch_index => $batch) {
            $batch_num = $batch_index + 1;
            $total_batches = count($batches);

            echo "🔄 Processing batch {$batch_num}/{$total_batches} (" . count($batch) . " episodes)\n";

            $batch_synced = 0;
            $batch_failed = 0;

            foreach ($batch as $dev_file) {
                $filename = basename($dev_file);
                $deploy_file = $this->deploy_dir . '/' . $filename;

                try {
                    if (copy($dev_file, $deploy_file)) {
                        $batch_synced++;
                        $total_synced++;

                        // Extract episode info
                        $content = file_get_contents($dev_file);
                        $episode_info = $this->extract_episode_info($content, $filename);

                        $this->sync_log[$filename] = [
                            'status' => 'synced',
                            'synced_at' => time(),
                            'episode_number' => $episode_info['number'],
                            'title' => $episode_info['title'],
                            'batch' => $batch_num
                        ];
                    } else {
                        throw new Exception("Failed to copy file");
                    }
                } catch (Exception $e) {
                    $batch_failed++;
                    $total_failed++;

                    $this->sync_log[$filename] = [
                        'status' => 'failed',
                        'error' => $e->getMessage(),
                        'failed_at' => time(),
                        'batch' => $batch_num
                    ];
                }
            }

            // Save progress after each batch
            $this->save_sync_log();

            echo "   ✅ Synced: {$batch_synced}\n";
            echo "   ❌ Failed: {$batch_failed}\n";
            echo "   📊 Progress: " . round(($total_synced / $total_to_sync) * 100, 1) . "%\n\n";

            // Small delay between batches
            if ($batch_index < count($batches) - 1) {
                sleep(1);
            }
        }

        // Final summary
        echo "🎉 BATCH SYNC COMPLETE!\n";
        echo "=====================\n";
        echo "✅ Total synced: {$total_synced}\n";
        echo "❌ Total failed: {$total_failed}\n";
        echo "📊 Success rate: " . round(($total_synced / ($total_synced + $total_failed)) * 100, 1) . "%\n\n";

        if ($total_synced > 0) {
            echo "🔄 NEXT STEP: Run episode import\n";
            echo "   cd websites/digitaldreamscape.site && php batch_import_episodes.php\n\n";
        }
    }

    private function extract_episode_info($content, $filename) {
        $number = 'unknown';
        if (preg_match('/ep_(\d+)_/', $filename, $matches)) {
            $number = $matches[1];
        }

        $title = 'Unknown Episode';
        if (preg_match('/<title>(.+?)<\/title>/', $content, $title_match)) {
            $title = trim($title_match[1]);
            $title = preg_replace('/ - Digital Dreamscape$/', '', $title);
        }

        return ['number' => $number, 'title' => $title];
    }

    private function load_sync_log() {
        if (file_exists($this->sync_log_file)) {
            $data = json_decode(file_get_contents($this->sync_log_file), true);
            if ($data) {
                $this->sync_log = $data;
            }
        }
    }

    private function save_sync_log() {
        file_put_contents($this->sync_log_file, json_encode($this->sync_log, JSON_PRETTY_PRINT));
    }
}

// Run the batch sync
$syncer = new BatchEpisodeSyncer();
$syncer->run_batch_sync();
?>