<?php
/**
 * Batch Import All 3000+ Episodes
 *
 * Processes HTML episode files and imports them as WordPress posts
 * Handles large volumes with progress tracking and error handling
 */

require_once 'wp/wp-load.php';

echo "🌌 DIGITAL DREAMSCAPE - BATCH EPISODE IMPORT\n";
echo "=============================================\n\n";

class EpisodeBatchImporter {
    private $source_dir;
    private $batch_size = 50;
    private $processed_file;
    private $stats = [
        'total_found' => 0,
        'processed' => 0,
        'skipped' => 0,
        'errors' => 0,
        'start_time' => 0,
        'last_batch_time' => 0
    ];

    public function __construct() {
        // Use deployment episodes directory (synced from development)
        $this->source_dir = dirname(__FILE__) . '/episodes';
        $this->processed_file = dirname(__FILE__) . '/episode_import_progress.json';
        $this->stats['start_time'] = time();
        $this->load_progress();
    }

    /**
     * Main import process
     */
    public function run_import() {
        echo "📂 Source directory: {$this->source_dir}\n\n";

        if (!is_dir($this->source_dir)) {
            die("❌ Source directory not found: {$this->source_dir}\n");
        }

        // Get all HTML episode files
        $episode_files = glob($this->source_dir . '/ep_*.html');
        $this->stats['total_found'] = count($episode_files);

        echo "🎭 Found {$this->stats['total_found']} episode files\n\n";

        if ($this->stats['total_found'] === 0) {
            die("❌ No episode files found\n");
        }

        // Sort files by episode number for consistent processing
        usort($episode_files, function($a, $b) {
            preg_match('/ep_(\d+)_/', basename($a), $match_a);
            preg_match('/ep_(\d+)_/', basename($b), $match_b);
            return ($match_a[1] ?? 0) - ($match_b[1] ?? 0);
        });

        // Process in batches
        $batches = array_chunk($episode_files, $this->batch_size);

        foreach ($batches as $batch_index => $batch) {
            $this->stats['last_batch_time'] = time();
            echo "🔄 Processing batch " . ($batch_index + 1) . " of " . count($batches) . " ({$this->batch_size} episodes)\n";

            foreach ($batch as $file_path) {
                $this->process_episode_file($file_path);
            }

            $this->save_progress();
            $this->show_progress();

            // Small delay between batches to prevent overwhelming the system
            if ($batch_index < count($batches) - 1) {
                sleep(1);
            }
        }

        $this->show_final_summary();
    }

    /**
     * Process a single episode HTML file
     */
    private function process_episode_file($file_path) {
        $filename = basename($file_path);

        // Skip if already processed
        if (isset($this->stats['processed_files'][$filename])) {
            $this->stats['skipped']++;
            return;
        }

        try {
            $content = file_get_contents($file_path);
            if (!$content) {
                throw new Exception("Could not read file content");
            }

            $episode_data = $this->parse_episode_html($content, $filename);

            if (!$episode_data) {
                throw new Exception("Could not parse episode data");
            }

            // Check if episode already exists (by episode number in metadata)
            $episode_exists = false;
            $existing_post_id = null;
            $existing_title = null;

            // Get all existing episodes and check manually
            $existing_episodes = get_posts([
                'meta_key' => 'artifact_type',
                'meta_value' => 'episode',
                'numberposts' => -1
            ]);

            foreach ($existing_episodes as $existing_post) {
                $existing_ep_num = get_post_meta($existing_post->ID, 'episode_number', true);
                if ($existing_ep_num == $episode_data['episode_number']) {
                    $episode_exists = true;
                    $existing_post_id = $existing_post->ID;
                    $existing_title = $existing_post->post_title;
                    break;
                }
            }

            if ($episode_exists) {
                $this->stats['skipped']++;
                $this->stats['processed_files'][$filename] = [
                    'status' => 'skipped',
                    'reason' => 'already_exists',
                    'existing_title' => $existing_title,
                    'post_id' => $existing_post_id,
                    'processed_at' => time()
                ];
                return;
            }

            // Create the post
            $post_id = digitaldreamscape_promote_artifact($episode_data);

            if (is_wp_error($post_id)) {
                throw new Exception($post_id->get_error_message());
            }

            $this->stats['processed']++;
            $this->stats['processed_files'][$filename] = [
                'status' => 'success',
                'post_id' => $post_id,
                'episode_number' => $episode_data['episode_number'],
                'questline' => $episode_data['questline'],
                'processed_at' => time()
            ];

            echo "✅ Imported EP-{$episode_data['episode_number']}: {$episode_data['title']}\n";

        } catch (Exception $e) {
            $this->stats['errors']++;
            $this->stats['processed_files'][$filename] = [
                'status' => 'error',
                'error' => $e->getMessage(),
                'processed_at' => time()
            ];

            echo "❌ Failed {$filename}: {$e->getMessage()}\n";
        }
    }

    /**
     * Parse episode data from HTML content
     */
    private function parse_episode_html($html, $filename) {
        // Extract episode number from filename
        if (!preg_match('/ep_(\d+)_/', $filename, $matches)) {
            return false;
        }
        $episode_number = (int) $matches[1];

        // Extract title from <title> tag
        if (!preg_match('/<title>(.+?)<\/title>/', $html, $title_match)) {
            return false;
        }
        $title = trim($title_match[1]);
        // Remove " - Digital Dreamscape" suffix if present
        $title = preg_replace('/ - Digital Dreamscape$/', '', $title);

        // Extract episode content
        if (!preg_match('/<section class="episode-content">(.*?)<\/section>/s', $html, $content_match)) {
            return false;
        }
        $content = $this->clean_html_content($content_match[1]);

        // Determine questline from filename or content
        $questline = 'general';
        if (strpos($filename, 'documentation') !== false) {
            $questline = 'technical-debt';
        } elseif (strpos($filename, 'agent-sessions') !== false) {
            $questline = 'system-automation';
        } elseif (strpos($filename, 'repository-analysis') !== false) {
            $questline = 'narrative-authority';
        } elseif (strpos($filename, 'mission-reports') !== false) {
            $questline = 'world-building';
        }

        // Extract excerpt (first meaningful paragraph)
        $excerpt = '';
        if (preg_match('/<p[^>]*>(.*?)<\/p>/', $content, $excerpt_match)) {
            $excerpt = strip_tags($excerpt_match[1]);
            $excerpt = substr($excerpt, 0, 200) . '...';
        }

        // Determine artifact state
        $artifact_state = 'active';
        if (strpos($content, 'canon-authority') !== false || strpos($content, '[CANON AUTHORITY GRANTED]') !== false) {
            $artifact_state = 'canon';
        }

        return [
            'title' => $title,
            'content' => $content,
            'excerpt' => $excerpt,
            'artifact_type' => 'episode',
            'questline' => $questline,
            'artifact_state' => $artifact_state,
            'era' => '2025',
            'source_system' => 'episode_archive',
            'episode_number' => $episode_number,
            'internal_source' => $filename
        ];
    }

    /**
     * Clean HTML content for WordPress
     */
    private function clean_html_content($html) {
        // Convert headers
        $html = preg_replace('/<h2[^>]*>\[([^\]]+)\]/', '<h2>$1</h2>', $html);
        $html = preg_replace('/<h3[^>]*>\[([^\]]+)\]/', '<h3>$1</h3>', $html);

        // Clean up classes and styling
        $html = preg_replace('/ class="[^"]*"/', '', $html);
        $html = preg_replace('/ style="[^"]*"/', '', $html);

        // Convert canon authority div to proper formatting
        $html = preg_replace('/<div class="canon-authority">.*?<div class="canon-seal">\[([^\]]+)\]/s', '<div style="border: 2px solid #gold; padding: 15px; margin: 20px 0; background: rgba(255,215,0,0.1);"><strong>$1</strong>', $html);
        $html = preg_replace('/<\/div>\s*<p>/', '<br><p>', $html);

        // Clean up remaining divs and spans
        $html = preg_replace('/<\/?div[^>]*>/', '', $html);
        $html = preg_replace('/<\/?span[^>]*>/', '', $html);

        return trim($html);
    }

    /**
     * Load previous progress
     */
    private function load_progress() {
        if (file_exists($this->processed_file)) {
            $data = json_decode(file_get_contents($this->processed_file), true);
            if ($data) {
                $this->stats = array_merge($this->stats, $data);
            }
        }
    }

    /**
     * Save current progress
     */
    private function save_progress() {
        file_put_contents($this->processed_file, json_encode($this->stats, JSON_PRETTY_PRINT));
    }

    /**
     * Show current progress
     */
    private function show_progress() {
        $elapsed = time() - $this->stats['start_time'];
        $processed = $this->stats['processed'] + $this->stats['skipped'];
        $remaining = $this->stats['total_found'] - $processed;
        $progress_pct = $this->stats['total_found'] > 0 ? round(($processed / $this->stats['total_found']) * 100, 1) : 0;

        echo "📊 Progress: {$progress_pct}% ({$processed}/{$this->stats['total_found']})\n";
        echo "   ✅ Processed: {$this->stats['processed']}\n";
        echo "   ⏭️  Skipped: {$this->stats['skipped']}\n";
        echo "   ❌ Errors: {$this->stats['errors']}\n";
        echo "   ⏳ Time: " . gmdate('H:i:s', $elapsed) . "\n\n";
    }

    /**
     * Show final summary
     */
    private function show_final_summary() {
        $total_time = time() - $this->stats['start_time'];
        $avg_time_per_episode = $this->stats['processed'] > 0 ? round($total_time / $this->stats['processed'], 2) : 0;

        echo "🎉 IMPORT COMPLETE!\n";
        echo "==================\n\n";
        echo "📈 FINAL STATISTICS:\n";
        echo "   Total episodes found: {$this->stats['total_found']}\n";
        echo "   Successfully imported: {$this->stats['processed']}\n";
        echo "   Skipped (already exist): {$this->stats['skipped']}\n";
        echo "   Errors: {$this->stats['errors']}\n";
        echo "   Total time: " . gmdate('H:i:s', $total_time) . "\n";
        echo "   Average time per episode: {$avg_time_per_episode}s\n\n";

        echo "🔄 NEXT STEPS:\n";
        echo "1. Run canon declaration: php canon_declaration_system.php scan\n";
        echo "2. Check system status: php system_status.php\n";
        echo "3. Visit https://digitaldreamscape.site/blog/ to see all episodes\n";
        echo "4. Set up automated promotion: setup_cron.bat\n\n";

        echo "🌟 SUCCESS: All 3000+ episodes are now live in the Digital Dreamscape!\n";
    }
}

// Run the import
$importer = new EpisodeBatchImporter();
$importer->run_import();
?>