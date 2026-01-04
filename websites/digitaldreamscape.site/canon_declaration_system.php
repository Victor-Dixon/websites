<?php
/**
 * Canon Declaration System
 *
 * Automatically declares canon based on reuse patterns
 * No vibes, no ceremony - just reuse = canon
 */

// Load WordPress environment
require_once dirname(__FILE__) . '/wp/wp-load.php';

// Verify WordPress environment loaded
if (!defined('ABSPATH')) {
    die('Failed to load WordPress environment');
}

class CanonDeclarationSystem {

    private $log_file;

    public function __construct() {
        $this->log_file = dirname(__FILE__) . '/canon_declaration.log';
    }

    /**
     * Scan for artifacts that should become canon
     */
    public function scan_and_declare_canon() {
        $this->log("Canon declaration scan started");

        $canon_declarations = 0;

        // Rule 1: Referenced twice or more
        $canon_declarations += $this->declare_canon_from_references();

        // Rule 2: Imported by system
        $canon_declarations += $this->declare_canon_from_system_imports();

        // Rule 3: Agent dependency
        $canon_declarations += $this->declare_canon_from_agent_dependency();

        // Rule 4: Questline foundation
        $canon_declarations += $this->declare_canon_from_questline_foundation();

        $this->log("Canon declaration scan complete. Declared: {$canon_declarations} new canon artifacts");
        return $canon_declarations;
    }

    /**
     * Rule 1: Referenced twice or more = Canon
     */
    private function declare_canon_from_references() {
        global $wpdb;

        $referenced_artifacts = $wpdb->get_results("
            SELECT
                p.ID,
                p.post_title,
                COUNT(DISTINCT ref.post_id) as reference_count
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} ref ON (
                ref.meta_value LIKE CONCAT('%', p.post_title, '%')
                AND ref.meta_key IN ('internal_source', 'questline')
                AND ref.post_id != p.ID
            )
            WHERE p.post_status = 'publish'
            AND p.post_type = 'post'
            AND p.ID NOT IN (
                SELECT post_id FROM {$wpdb->postmeta}
                WHERE meta_key = 'artifact_state'
                AND meta_value = 'canon'
            )
            GROUP BY p.ID
            HAVING reference_count >= 2
            ORDER BY reference_count DESC
        ");

        $declarations = 0;

        foreach ($referenced_artifacts as $artifact) {
            $this->declare_canon($artifact->ID, 'referenced', $artifact->reference_count);
            $declarations++;
        }

        return $declarations;
    }

    /**
     * Rule 2: Imported by system = Canon
     */
    private function declare_canon_from_system_imports() {
        global $wpdb;

        // Find artifacts referenced in system files
        $system_files = [
            'functions.php',
            'auto_promotion_daemon.php',
            'canon_declaration_system.php'
        ];

        $declarations = 0;

        foreach ($system_files as $system_file) {
            $system_path = dirname(__FILE__) . '/' . $system_file;

            if (!file_exists($system_path)) continue;

            $system_content = file_get_contents($system_path);
            $artifact_titles = $this->extract_artifact_titles_from_system($system_content);

            foreach ($artifact_titles as $title) {
                $artifact_id = $this->find_artifact_by_title($title);

                if ($artifact_id && !$this->is_canon($artifact_id)) {
                    $this->declare_canon($artifact_id, 'system_import', $system_file);
                    $declarations++;
                }
            }
        }

        return $declarations;
    }

    /**
     * Rule 3: Agent dependency = Canon
     */
    private function declare_canon_from_agent_dependency() {
        $agent_dir = dirname(__FILE__) . '/agents';
        if (!is_dir($agent_dir)) return 0;

        $declarations = 0;

        // Scan agent status files for dependencies
        $status_files = glob($agent_dir . '/**/status.json');

        foreach ($status_files as $status_file) {
            $status_data = json_decode(file_get_contents($status_file), true);
            if (!$status_data) continue;

            // Check dependencies in current_tasks, completed_tasks, etc.
            $dependency_fields = ['current_tasks', 'completed_tasks', 'dependencies'];

            foreach ($dependency_fields as $field) {
                if (isset($status_data[$field]) && is_array($status_data[$field])) {
                    foreach ($status_data[$field] as $task) {
                        if (isset($task['depends_on'])) {
                            $artifact_id = $this->find_artifact_by_title($task['depends_on']);

                            if ($artifact_id && !$this->is_canon($artifact_id)) {
                                $this->declare_canon($artifact_id, 'agent_dependency', basename(dirname($status_file)));
                                $declarations++;
                            }
                        }
                    }
                }
            }
        }

        return $declarations;
    }

    /**
     * Rule 4: Questline foundation = Canon
     */
    private function declare_canon_from_questline_foundation() {
        $questlines = $this->get_active_questlines();

        $declarations = 0;

        foreach ($questlines as $questline => $data) {
            // If questline has 5+ artifacts and is 80%+ resolved, its foundation becomes canon
            if ($data['total'] >= 5 && ($data['resolved'] / $data['total']) >= 0.8) {
                // Find the earliest artifact in this questline
                $foundation_artifact = get_posts([
                    'meta_key' => 'questline',
                    'meta_value' => $questline,
                    'posts_per_page' => 1,
                    'orderby' => 'date',
                    'order' => 'ASC'
                ]);

                if (!empty($foundation_artifact)) {
                    $artifact_id = $foundation_artifact[0]->ID;

                    if (!$this->is_canon($artifact_id)) {
                        $this->declare_canon($artifact_id, 'questline_foundation', $questline);
                        $declarations++;
                    }
                }
            }
        }

        return $declarations;
    }

    /**
     * Declare an artifact as canon
     */
    private function declare_canon($artifact_id, $reason, $evidence) {
        update_post_meta($artifact_id, 'artifact_state', 'canon');
        update_post_meta($artifact_id, 'canonical', 'true');
        update_post_meta($artifact_id, 'canon_reason', $reason);
        update_post_meta($artifact_id, 'canon_evidence', $evidence);
        update_post_meta($artifact_id, 'canon_declared_at', time());

        $title = get_the_title($artifact_id);
        $this->log("Declared canon: {$title} (reason: {$reason}, evidence: {$evidence})");
    }

    /**
     * Check if artifact is already canon
     */
    private function is_canon($artifact_id) {
        return get_post_meta($artifact_id, 'artifact_state', true) === 'canon';
    }

    /**
     * Find artifact by title
     */
    private function find_artifact_by_title($title) {
        $posts = get_posts([
            'title' => $title,
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1
        ]);

        return !empty($posts) ? $posts[0]->ID : false;
    }

    /**
     * Extract artifact titles from system code
     */
    private function extract_artifact_titles_from_system($content) {
        $titles = [];

        // Look for quoted strings that might be artifact titles
        preg_match_all('/["\']([^"\']+)["\']/', $content, $matches);

        foreach ($matches[1] as $potential_title) {
            // Filter for likely artifact titles (length, no special chars, etc.)
            if (strlen($potential_title) > 10 && strlen($potential_title) < 100) {
                // Check if this title exists as an artifact
                if ($this->find_artifact_by_title($potential_title)) {
                    $titles[] = $potential_title;
                }
            }
        }

        return array_unique($titles);
    }

    /**
     * Get active questlines data
     */
    private function get_active_questlines() {
        return digitaldreamscape_get_active_questlines();
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

// Command line interface
if ($argc > 1 && $argv[1] === 'scan') {
    $system = new CanonDeclarationSystem();
    $declared = $system->scan_and_declare_canon();
    echo "\nScan complete. {$declared} artifacts elevated to canon.\n";
} else {
    echo "Canon Declaration System - Digital Dreamscape\n";
    echo "Automatically declares canon based on reuse patterns\n\n";
    echo "Usage: php canon_declaration_system.php scan\n\n";
    echo "Canon Rules:\n";
    echo "- Referenced 2+ times = canon\n";
    echo "- System import = canon\n";
    echo "- Agent dependency = canon\n";
    echo "- Questline foundation (5+ artifacts, 80%+ resolved) = canon\n\n";
    echo "No vibes. No ceremony. Just reuse = canon.\n";
}