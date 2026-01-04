<?php
/**
 * Auto-Promotion Daemon
 *
 * Monitors internal systems and promotes artifacts automatically
 * Runs as scheduled task or daemon process
 */

// Load WordPress environment
require_once dirname(__FILE__) . '/wp/wp-load.php';

// Verify WordPress environment loaded
if (!defined('ABSPATH')) {
    die('Failed to load WordPress environment');
}

class AutoPromotionDaemon {

    private $log_file;
    private $processed_file;

    public function __construct() {
        $this->log_file = dirname(__FILE__) . '/auto_promotion.log';
        $this->processed_file = dirname(__FILE__) . '/processed_artifacts.json';
        $this->processed = $this->load_processed_artifacts();
    }

    /**
     * Main daemon loop
     */
    public function run() {
        $this->log("Auto-promotion daemon started");

        $promoted_count = 0;

        // Process devlogs
        $promoted_count += $this->process_devlogs();

        // Process agent outputs
        $promoted_count += $this->process_agent_outputs();

        // Process task completions
        $promoted_count += $this->process_task_completions();

        // Update canon based on reuse
        $this->update_canon_from_reuse();

        $this->log("Auto-promotion cycle complete. Promoted: {$promoted_count} artifacts");
        $this->save_processed_artifacts();
    }

    /**
     * Process new devlogs automatically
     */
    private function process_devlogs() {
        $devlog_dir = dirname(__FILE__) . '/devlogs';
        if (!is_dir($devlog_dir)) {
            return 0;
        }

        $promoted = 0;
        $files = glob($devlog_dir . '/*.md');

        foreach ($files as $file) {
            $filename = basename($file);

            // Skip already processed
            if (isset($this->processed['devlogs'][$filename])) {
                continue;
            }

            // Check if devlog is "complete" (has proper structure)
            if ($this->is_devlog_complete($file)) {
                $result = digitaldreamscape_promote_devlog($file);

                if (!is_wp_error($result)) {
                    $this->log("Promoted devlog: {$filename} → Episode #{$result}");
                    $this->processed['devlogs'][$filename] = [
                        'promoted_at' => time(),
                        'episode_id' => $result
                    ];
                    $promoted++;
                } else {
                    $this->log("Failed to promote devlog {$filename}: " . $result->get_error_message());
                }
            }
        }

        return $promoted;
    }

    /**
     * Check if devlog is complete and ready for promotion
     */
    private function is_devlog_complete($file_path) {
        $content = file_get_contents($file_path);

        // Must have proper frontmatter
        if (!preg_match('/^---\s*$/m', $content)) {
            return false;
        }

        // Must have required sections or be explicitly marked complete
        $has_questline = preg_match('/\*\*Questline\*\*:\s*(.+)/i', $content);
        $has_status = preg_match('/\*\*Status\*\*:\s*completed/i', $content);

        return $has_questline && $has_status;
    }

    /**
     * Process agent output files automatically
     */
    private function process_agent_outputs() {
        $agent_dir = dirname(__FILE__) . '/agents/output';
        if (!is_dir($agent_dir)) {
            return 0;
        }

        $promoted = 0;
        $files = glob($agent_dir . '/*.json');

        foreach ($files as $file) {
            $filename = basename($file);

            // Skip already processed
            if (isset($this->processed['agent_outputs'][$filename])) {
                continue;
            }

            $data = json_decode(file_get_contents($file), true);
            if (!$data) continue;

            // Check if agent marked this for auto-promotion
            if (!isset($data['auto_promote']) || $data['auto_promote'] !== true) {
                continue;
            }

            // Determine artifact type based on content
            $artifact_type = $this->classify_agent_output($data);

            $artifact_data = [
                'title' => $data['title'] ?? $data['summary'] ?? 'Agent Output',
                'content' => json_encode($data, JSON_PRETTY_PRINT),
                'excerpt' => $data['summary'] ?? substr(json_encode($data), 0, 200),
                'artifact_type' => $artifact_type,
                'questline' => $data['questline'] ?? 'agent-operations',
                'artifact_state' => 'active',
                'era' => date('Y'),
                'source_system' => 'agent',
                'agent_id' => $data['agent_id'] ?? 'unknown',
                'internal_source' => $file
            ];

            $result = digitaldreamscape_promote_artifact($artifact_data);

            if (!is_wp_error($result)) {
                $this->log("Promoted agent output: {$filename} → {$artifact_type} #{$result}");
                $this->processed['agent_outputs'][$filename] = [
                    'promoted_at' => time(),
                    'artifact_id' => $result,
                    'type' => $artifact_type
                ];
                $promoted++;
            }
        }

        return $promoted;
    }

    /**
     * Classify agent output type
     */
    private function classify_agent_output($data) {
        if (isset($data['canon_candidate']) && $data['canon_candidate']) {
            return 'canon';
        }

        if (isset($data['tool_created']) || isset($data['code_generated'])) {
            return 'artifact';
        }

        return 'episode';
    }

    /**
     * Process completed tasks from task lists
     */
    private function process_task_completions() {
        $task_dir = dirname(__FILE__) . '/tasks';
        if (!is_dir($task_dir)) {
            return 0;
        }

        $promoted = 0;
        $files = glob($task_dir . '/*.json');

        foreach ($files as $file) {
            $filename = basename($file);

            $data = json_decode(file_get_contents($file), true);
            if (!$data || !isset($data['tasks'])) continue;

            $questline = $data['questline'] ?? 'general';

            foreach ($data['tasks'] as $task) {
                if (empty($task['title'])) continue;

                $task_key = $questline . '_' . sanitize_title($task['title']);

                // Skip if already processed and completed status hasn't changed
                $was_completed = $this->processed['tasks'][$task_key]['completed'] ?? false;
                $is_completed = $task['completed'] ?? false;

                if (isset($this->processed['tasks'][$task_key]) && $was_completed === $is_completed) {
                    continue;
                }

                // Only promote completed tasks
                if (!$is_completed) continue;

                $artifact_data = [
                    'title' => $task['title'],
                    'content' => $task['description'] ?? '',
                    'excerpt' => substr($task['description'] ?? $task['title'], 0, 150) . '...',
                    'artifact_type' => 'episode',
                    'questline' => $questline,
                    'artifact_state' => 'resolved',
                    'era' => date('Y'),
                    'source_system' => 'tasklist',
                    'quest_progress' => $task['progress'] ?? 'completed',
                    'internal_source' => $file
                ];

                $result = digitaldreamscape_promote_artifact($artifact_data);

                if (!is_wp_error($result)) {
                    $this->log("Promoted task completion: {$task['title']} → Episode #{$result}");
                    $this->processed['tasks'][$task_key] = [
                        'promoted_at' => time(),
                        'artifact_id' => $result,
                        'completed' => true
                    ];
                    $promoted++;
                }
            }
        }

        return $promoted;
    }

    /**
     * Update canon status based on reuse patterns
     */
    private function update_canon_from_reuse() {
        global $wpdb;

        // Find artifacts referenced multiple times
        $referenced_artifacts = $wpdb->get_results("
            SELECT
                p.ID,
                p.post_title,
                COUNT(DISTINCT ref.post_id) as reference_count
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} ref ON (
                ref.meta_value LIKE CONCAT('%', p.post_title, '%')
                AND ref.meta_key = 'internal_source'
                AND ref.post_id != p.ID
            )
            WHERE p.post_status = 'publish'
            AND p.post_type = 'post'
            GROUP BY p.ID
            HAVING reference_count >= 2
        ");

        foreach ($referenced_artifacts as $artifact) {
            $current_state = get_post_meta($artifact->ID, 'artifact_state', true);

            if ($current_state !== 'canon') {
                update_post_meta($artifact->ID, 'artifact_state', 'canon');
                update_post_meta($artifact->ID, 'canonical', 'true');
                $this->log("Elevated to canon: {$artifact->post_title} (referenced {$artifact->reference_count} times)");
            }
        }

        // Also check for system imports
        $system_imports = $wpdb->get_results("
            SELECT p.ID, p.post_title
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE pm.meta_key = 'artifact_type'
            AND pm.meta_value = 'artifact'
            AND p.post_status = 'publish'
            AND EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} pm2
                WHERE pm2.meta_key = 'internal_source'
                AND pm2.meta_value LIKE CONCAT('%', p.post_title, '%')
            )
        ");

        foreach ($system_imports as $imported) {
            $current_state = get_post_meta($imported->ID, 'artifact_state', true);

            if ($current_state !== 'canon') {
                update_post_meta($imported->ID, 'artifact_state', 'canon');
                update_post_meta($imported->ID, 'canonical', 'true');
                $this->log("Elevated to canon: {$imported->post_title} (system import detected)");
            }
        }
    }

    /**
     * Load processed artifacts tracking
     */
    private function load_processed_artifacts() {
        if (file_exists($this->processed_file)) {
            $data = json_decode(file_get_contents($this->processed_file), true);
            return $data ?: ['devlogs' => [], 'agent_outputs' => [], 'tasks' => []];
        }
        return ['devlogs' => [], 'agent_outputs' => [], 'tasks' => []];
    }

    /**
     * Save processed artifacts tracking
     */
    private function save_processed_artifacts() {
        file_put_contents($this->processed_file, json_encode($this->processed, JSON_PRETTY_PRINT));
    }

    /**
     * Log activity
     */
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] {$message}\n";
        file_put_contents($this->log_file, $log_entry, FILE_APPEND);
        echo $log_entry;
    }
}

// Run the daemon
if ($argc > 1 && $argv[1] === 'run') {
    $daemon = new AutoPromotionDaemon();
    $daemon->run();
} else {
    echo "Auto-Promotion Daemon for Digital Dreamscape\n";
    echo "Usage: php auto_promotion_daemon.php run\n\n";
    echo "This daemon will:\n";
    echo "- Monitor devlogs/ for completed entries\n";
    echo "- Process agent outputs marked for auto-promotion\n";
    echo "- Promote completed tasks from task lists\n";
    echo "- Update canon status based on reuse patterns\n\n";
    echo "Run this as a scheduled task or cron job for automatic promotion.\n";
}