<?php
/**
 * Sync Episodes from Development to Deployment Directory
 *
 * This script moves newly generated episodes from the development directory
 * to the deployment directory for processing and WordPress import.
 */

class EpisodeSyncManager {
    private $dev_dir;
    private $deploy_dir;
    private $sync_log = [];

    public function __construct() {
        $this->dev_dir = 'D:/websites/digitaldreamscape.site/episodes';
        $this->deploy_dir = 'D:/websites/websites/digitaldreamscape.site/episodes';
        $this->sync_log_file = dirname(__FILE__) . '/episode_sync_log.json';
    }

    public function run_sync() {
        echo "🔄 SYNCING EPISODES: Development → Deployment\n";
        echo "==============================================\n\n";

        // Ensure directories exist
        if (!is_dir($this->dev_dir)) {
            die("❌ Development directory not found: {$this->dev_dir}\n");
        }

        if (!is_dir($this->deploy_dir)) {
            mkdir($this->deploy_dir, 0755, true);
            echo "📁 Created deployment episodes directory\n";
        }

        // Load previous sync log
        $this->load_sync_log();

        // Get all episodes from development
        $dev_episodes = glob($this->dev_dir . '/ep_*.html');
        $deploy_episodes = glob($this->deploy_dir . '/ep_*.html');

        echo "📂 Development: " . count($dev_episodes) . " episodes\n";
        echo "📂 Deployment: " . count($deploy_episodes) . " episodes\n\n";

        // Find new episodes to sync
        $existing_in_deploy = [];
        foreach ($deploy_episodes as $deploy_file) {
            $existing_in_deploy[] = basename($deploy_file);
        }

        $new_episodes = [];
        foreach ($dev_episodes as $dev_file) {
            $filename = basename($dev_file);
            if (!in_array($filename, $existing_in_deploy)) {
                $new_episodes[] = $dev_file;
            }
        }

        if (empty($new_episodes)) {
            echo "✅ No new episodes to sync\n\n";
            return;
        }

        echo "🚀 Syncing " . count($new_episodes) . " new episodes...\n\n";

        $synced = 0;
        $failed = 0;

        foreach ($new_episodes as $dev_file) {
            $filename = basename($dev_file);
            $deploy_file = $this->deploy_dir . '/' . $filename;

            try {
                if (copy($dev_file, $deploy_file)) {
                    $synced++;

                    // Extract episode info
                    $content = file_get_contents($dev_file);
                    $episode_info = $this->extract_episode_info($content, $filename);

                    $this->sync_log[$filename] = [
                        'status' => 'synced',
                        'synced_at' => time(),
                        'episode_number' => $episode_info['number'],
                        'title' => $episode_info['title'],
                        'size' => filesize($dev_file)
                    ];

                    echo "✅ Synced: {$filename} (EP-{$episode_info['number']})\n";

                } else {
                    throw new Exception("Failed to copy file");
                }

            } catch (Exception $e) {
                $failed++;
                $this->sync_log[$filename] = [
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                    'failed_at' => time()
                ];
                echo "❌ Failed: {$filename} - {$e->getMessage()}\n";
            }
        }

        // Save sync log
        $this->save_sync_log();

        // Show summary
        echo "\n🎉 SYNC COMPLETE!\n";
        echo "================\n";
        echo "✅ Synced: {$synced}\n";
        echo "❌ Failed: {$failed}\n";
        echo "📊 Total in deployment: " . (count($deploy_episodes) + $synced) . "\n\n";

        if ($synced > 0) {
            echo "🔄 NEXT STEP: Run episode import\n";
            echo "   php D:/websites/websites/digitaldreamscape.site/batch_import_episodes.php\n\n";
        }
    }

    private function extract_episode_info($content, $filename) {
        // Extract episode number
        $number = 'unknown';
        if (preg_match('/ep_(\d+)_/', $filename, $matches)) {
            $number = $matches[1];
        }

        // Extract title
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

// Run the sync
$syncer = new EpisodeSyncManager();
$syncer->run_sync();
?>