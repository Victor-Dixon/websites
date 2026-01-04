<?php
/**
 * Automated Episode Deployment Pipeline
 *
 * 1. Sync new episodes from development to deployment
 * 2. Import episodes into WordPress
 * 3. Update canon declarations if needed
 * 4. Generate deployment report
 */

class AutomatedEpisodeDeployer {
    private $base_dir;
    private $deploy_dir;
    private $reports = [];

    public function __construct() {
        $this->base_dir = 'D:/websites';
        $this->deploy_dir = 'D:/websites/websites/digitaldreamscape.site';
    }

    public function run_full_deployment() {
        echo "🚀 AUTOMATED EPISODE DEPLOYMENT\n";
        echo "===============================\n\n";

        $start_time = time();
        $success = true;

        try {
            // Step 1: Sync episodes
            echo "📂 STEP 1: Syncing episodes from development\n";
            $this->sync_episodes();

            // Step 2: Import to WordPress
            echo "\n📝 STEP 2: Importing episodes to WordPress\n";
            $this->import_episodes();

            // Step 3: Update canon declarations
            echo "\n🏛️  STEP 3: Updating canon declarations\n";
            $this->update_canon();

            // Step 4: Generate report
            echo "\n📊 STEP 4: Generating deployment report\n";
            $this->generate_report($start_time);

            echo "\n🎉 DEPLOYMENT COMPLETE!\n";
            echo "======================\n";
            echo "✅ All episodes deployed successfully\n";
            echo "🌐 Visit: https://digitaldreamscape.site/blog/\n\n";

        } catch (Exception $e) {
            $success = false;
            echo "\n❌ DEPLOYMENT FAILED\n";
            echo "===================\n";
            echo "Error: " . $e->getMessage() . "\n\n";
        }

        $this->log_deployment($success, time() - $start_time);
    }

    private function sync_episodes() {
        $sync_script = $this->base_dir . '/sync_episodes_to_deployment.php';

        if (!file_exists($sync_script)) {
            throw new Exception("Sync script not found: {$sync_script}");
        }

        $command = "php \"{$sync_script}\"";
        $output = shell_exec($command);

        if ($output === null) {
            throw new Exception("Failed to execute sync script");
        }

        echo $output;

        // Check if any episodes were synced
        $sync_log_file = $this->base_dir . '/episode_sync_log.json';
        if (file_exists($sync_log_file)) {
            $sync_log = json_decode(file_get_contents($sync_log_file), true);
            $synced_count = count(array_filter($sync_log, function($item) {
                return $item['status'] === 'synced';
            }));
            $this->reports['synced_episodes'] = $synced_count;
        }
    }

    private function import_episodes() {
        $import_script = $this->deploy_dir . '/batch_import_episodes.php';

        if (!file_exists($import_script)) {
            throw new Exception("Import script not found: {$import_script}");
        }

        // Change to deployment directory and run import
        $command = "cd \"{$this->deploy_dir}\" && php batch_import_episodes.php";
        $output = shell_exec($command);

        if ($output === null) {
            throw new Exception("Failed to execute import script");
        }

        echo $output;

        // Check import results
        $progress_file = $this->deploy_dir . '/episode_import_progress.json';
        if (file_exists($progress_file)) {
            $progress = json_decode(file_get_contents($progress_file), true);
            $this->reports['imported_episodes'] = $progress['processed'] ?? 0;
            $this->reports['skipped_episodes'] = $progress['skipped'] ?? 0;
            $this->reports['error_episodes'] = $progress['errors'] ?? 0;
        }
    }

    private function update_canon() {
        $canon_script = $this->deploy_dir . '/canon_declaration_system.php';

        if (file_exists($canon_script)) {
            // Run canon scan
            $command = "cd \"{$this->deploy_dir}\" && php canon_declaration_system.php scan";
            $output = shell_exec($command);

            if ($output) {
                echo $output;
            }
        }
    }

    private function generate_report($start_time) {
        $total_time = time() - $start_time;
        $report_file = $this->base_dir . '/deployment_reports/episode_deployment_' . date('Y-m-d_H-i-s') . '.json';

        // Ensure reports directory exists
        $reports_dir = dirname($report_file);
        if (!is_dir($reports_dir)) {
            mkdir($reports_dir, 0755, true);
        }

        $report = [
            'timestamp' => time(),
            'date' => date('Y-m-d H:i:s'),
            'duration_seconds' => $total_time,
            'synced_episodes' => $this->reports['synced_episodes'] ?? 0,
            'imported_episodes' => $this->reports['imported_episodes'] ?? 0,
            'skipped_episodes' => $this->reports['skipped_episodes'] ?? 0,
            'error_episodes' => $this->reports['error_episodes'] ?? 0,
            'status' => 'completed'
        ];

        file_put_contents($report_file, json_encode($report, JSON_PRETTY_PRINT));

        echo "📄 Report saved: " . basename($report_file) . "\n";
        echo "⏱️  Total time: " . gmdate('H:i:s', $total_time) . "\n";
    }

    private function log_deployment($success, $duration) {
        $log_file = $this->base_dir . '/deployment.log';
        $log_entry = sprintf(
            "[%s] Episode deployment %s - Duration: %s - Synced: %d, Imported: %d\n",
            date('Y-m-d H:i:s'),
            $success ? 'SUCCESS' : 'FAILED',
            gmdate('H:i:s', $duration),
            $this->reports['synced_episodes'] ?? 0,
            $this->reports['imported_episodes'] ?? 0
        );

        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }
}

// Run the automated deployment
$deployer = new AutomatedEpisodeDeployer();
$deployer->run_full_deployment();
?>