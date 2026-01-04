<?php
/**
 * Episode Import Automation Script
 *
 * Automatically imports all episode HTML files into WordPress posts
 * Usage: php import_episodes.php [--dry-run] [--limit=N] [--start-from=EP-NNNN]
 */

// Prevent web access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line');
}

// Check if WordPress environment is available
$wordpress_available = file_exists(dirname(__FILE__) . '/wp/wp-load.php');

if ($wordpress_available) {
    require_once(dirname(__FILE__) . '/wp/wp-load.php');
    echo "✅ WordPress environment loaded\n";
} else {
    echo "⚠️  WordPress environment not found - running in export mode\n";
    echo "   Episodes will be parsed and exported to JSON for later import\n\n";
}

// Parse command line arguments
$options = getopt('', ['dry-run', 'limit:', 'start-from:', 'batch-size:', 'help']);
$dry_run = isset($options['dry-run']);
$limit = isset($options['limit']) ? (int)$options['limit'] : null;
$start_from = isset($options['start-from']) ? $options['start-from'] : null;
$batch_size = isset($options['batch-size']) ? (int)$options['batch-size'] : 100;

if (isset($options['help'])) {
    show_help();
    exit(0);
}

echo "🌌 Digital Dreamscape - Episode Import Automation\n";
echo "=================================================\n\n";

if ($dry_run) {
    echo "🔍 DRY RUN MODE - No posts will be created\n\n";
}

// Get all episode files
$episodes_dir = dirname(__FILE__) . '/episodes';
$episode_files = glob($episodes_dir . '/ep_*.html');

if (empty($episode_files)) {
    echo "❌ No episode files found in {$episodes_dir}\n";
    exit(1);
}

echo "📂 Found " . count($episode_files) . " episode files\n\n";

// Sort files by episode number
usort($episode_files, function($a, $b) {
    $num_a = (int)preg_replace('/.*ep_(\d+).*/', '$1', basename($a));
    $num_b = (int)preg_replace('/.*ep_(\d+).*/', '$1', basename($b));
    return $num_a - $num_b;
});

// Filter to start from specific episode if requested
if ($start_from) {
    $start_num = (int)preg_replace('/EP-?(\d+)/i', '$1', $start_from);
    $episode_files = array_filter($episode_files, function($file) use ($start_num) {
        $file_num = (int)preg_replace('/.*ep_(\d+).*/', '$1', basename($file));
        return $file_num >= $start_num;
    });
    echo "▶️  Starting from episode EP-{$start_num}\n";
}

// Apply limit if specified
if ($limit) {
    $episode_files = array_slice($episode_files, 0, $limit);
    echo "📏 Limited to {$limit} episodes\n";

if ($batch_size && $batch_size > 0) {
    echo "📦 Processing in batches of {$batch_size}\n";
}
}

echo "\n🚀 Processing " . count($episode_files) . " episodes...\n\n";

$processed = 0;
$imported = 0;
$skipped = 0;
$errors = 0;
$export_data = [];
$start_time = microtime(true);

foreach ($episode_files as $episode_file) {
    $processed++;

    echo "[$processed/" . count($episode_files) . "] Processing: " . basename($episode_file) . "\n";

    try {
        $episode_data = parse_episode_file($episode_file);

        if (!$episode_data) {
            echo "  ❌ Failed to parse episode data\n";
            $errors++;
            continue;
        }

        // Check if episode already exists (only if WordPress is available)
        if ($wordpress_available) {
            $existing_posts = get_posts([
                'meta_key' => 'internal_source',
                'meta_value' => $episode_file,
                'post_type' => 'post',
                'posts_per_page' => 1
            ]);

            if (!empty($existing_posts)) {
                echo "  ⏭️  Already imported: {$existing_posts[0]->post_title}\n";
                $skipped++;
                continue;
            }
        }

        // Import the episode
        if ($dry_run || !$wordpress_available) {
            echo "  📝 Would import: {$episode_data['title']}\n";
            echo "     Questline: {$episode_data['questline']}\n";
            echo "     Agent: {$episode_data['agent_id']}\n";
            $imported++;

            // Collect data for export
            $export_data[] = $episode_data;
        } else {
            $result = digitaldreamscape_promote_artifact($episode_data);

            if (is_wp_error($result)) {
                echo "  ❌ Import failed: " . $result->get_error_message() . "\n";
                $errors++;
            } else {
                echo "  ✅ Imported: {$episode_data['title']}\n";
                echo "     URL: " . get_permalink($result) . "\n";
                $imported++;
            }
        }

    } catch (Exception $e) {
        echo "  💥 Error processing " . basename($episode_file) . ": " . $e->getMessage() . "\n";
        $errors++;
    }

    // Progress indicator and batch processing
    if ($processed % $batch_size === 0 && $processed > 0) {
        $elapsed = microtime(true) - $start_time;
        $rate = $processed / $elapsed;
        $estimated_total = count($episode_files) / $rate;
        $remaining = $estimated_total - $elapsed;

        echo "\n📊 Progress: $processed/" . count($episode_files) . " processed\n";
        echo "   Rate: " . round($rate, 2) . " episodes/second\n";
        echo "   Elapsed: " . round($elapsed, 2) . "s\n";
        echo "   Remaining: " . round($remaining, 2) . "s\n\n";

        // Optional: Add a small delay between batches to prevent overwhelming the system
        if (!$dry_run && $wordpress_available) {
            usleep(100000); // 0.1 second delay
        }
    }
}

echo "\n🎉 Import Complete!\n";
echo "==================\n";
echo "Processed: {$processed}\n";
echo "Imported:  {$imported}\n";
echo "Skipped:   {$skipped}\n";
echo "Errors:    {$errors}\n";

if ($dry_run) {
    echo "\n🔄 This was a dry run. Run without --dry-run to perform actual import.\n";
}

if (!$wordpress_available && !empty($export_data)) {
    $export_file = dirname(__FILE__) . '/episodes_export_' . date('Y-m-d_H-i-s') . '.json';
    if (file_put_contents($export_file, json_encode($export_data, JSON_PRETTY_PRINT))) {
        echo "\n💾 Exported episode data to: {$export_file}\n";
        echo "   Use this file with a WordPress import script when the site is available.\n";
    } else {
        echo "\n❌ Failed to export episode data to file.\n";
    }
}

/**
 * Parse episode HTML file and extract structured data
 */
function parse_episode_file($file_path) {
    $html = file_get_contents($file_path);

    if (!$html) {
        return false;
    }

    $data = [
        'title' => '',
        'content' => '',
        'excerpt' => '',
        'questline' => '',
        'agent_id' => '',
        'artifact_state' => 'active',
        'era' => '2026',
        'source_system' => 'episode_import',
        'internal_source' => $file_path,
        'artifact_type' => 'episode'
    ];

    // Extract episode ID from filename
    $filename = basename($file_path);
    if (preg_match('/ep_(\d+)/', $filename, $matches)) {
        $episode_num = $matches[1];
        $data['episode_id'] = 'EP-' . $episode_num;
    }

    // Extract title from <title> tag or h1.episode-title
    if (preg_match('/<title>(.+?)<\/title>/', $html, $matches)) {
        $title_text = $matches[1];
        // Remove " - Digital Dreamscape" suffix
        $title_text = preg_replace('/ - Digital Dreamscape$/', '', $title_text);
        // Extract title after "EP-XXXX: "
        if (preg_match('/EP-\d+:\s*(.+)/', $title_text, $title_matches)) {
            $data['title'] = trim($title_matches[1]);
        } else {
            $data['title'] = trim($title_text);
        }
    }

    // Fallback to h1.episode-title if title extraction failed
    if (empty($data['title']) && preg_match('/<h1[^>]*class="[^"]*episode-title[^"]*"[^>]*>(.+?)<\/h1>/s', $html, $matches)) {
        $data['title'] = trim(strip_tags($matches[1]));
    }

    // Extract questline from meta section
    if (preg_match('/Questline:\s*([^<]+)/i', $html, $matches)) {
        $data['questline'] = trim($matches[1]);
    }

    // Extract agent from meta section
    if (preg_match('/Agent:\s*([^<]+)/i', $html, $matches)) {
        $agent_text = trim($matches[1]);
        // Normalize agent names
        $agent_text = str_replace(['Agent-', 'Agent ', '(Captain)', '(Coordination & Communication Specialist)', '(SSOT & System Integration Specialist)', '(Web Development Specialist)'], '', $agent_text);
        $data['agent_id'] = trim($agent_text);
    }

    // Extract state from status indicator or episode closure
    if (preg_match('/episode state:\s*([^<\n]+)/i', $html, $matches)) {
        $status = strtolower(trim($matches[1]));
        if (in_array($status, ['canon', 'resolved', 'active'])) {
            $data['artifact_state'] = $status;
        }
    } elseif (preg_match('/<span[^>]*class="[^"]*episode-status[^"]*"[^>]*>(.+?)<\/span>/s', $html, $matches)) {
        $status = strtolower(trim(strip_tags($matches[1])));
        if (in_array($status, ['canon', 'resolved', 'active'])) {
            $data['artifact_state'] = $status;
        }
    }

    // Extract content (everything after episode-meta until footer)
    if (preg_match('/<div class="episode-meta">.+?<\/div>\s*(.+?)\s*<footer/s', $html, $matches)) {
        $content_html = $matches[1];

        // Clean up content - remove navigation, styling, etc.
        $content_html = preg_replace('/<nav[^>]*>.*?<\/nav>/s', '', $content_html);
        $content_html = preg_replace('/<style[^>]*>.*?<\/style>/s', '', $content_html);
        $content_html = preg_replace('/<script[^>]*>.*?<\/script>/s', '', $content_html);

        // Convert to plain text for excerpt
        $plain_text = strip_tags($content_html);
        $data['excerpt'] = substr($plain_text, 0, 200) . (strlen($plain_text) > 200 ? '...' : '');

        // Keep some HTML structure for content
        $data['content'] = $content_html;
    }

    // Validate required fields
    if (empty($data['title'])) {
        return false;
    }

    // Set default questline if not found
    if (empty($data['questline'])) {
        $data['questline'] = 'general';
    }

    return $data;
}

/**
 * Show help information
 */
function show_help() {
    echo "🌌 Digital Dreamscape - Episode Import Automation\n";
    echo "=================================================\n\n";

    echo "Automatically imports episode HTML files as WordPress posts\n\n";

    echo "USAGE:\n";
    echo "  php import_episodes.php [options]\n\n";

    echo "OPTIONS:\n";
    echo "  --dry-run           Show what would be imported without making changes\n";
    echo "  --limit=N           Process only the first N episodes\n";
    echo "  --start-from=EP-NNNN Start processing from specific episode number\n";
    echo "  --batch-size=N      Process episodes in batches of N (default: 100)\n";
    echo "  --help              Show this help message\n\n";

    echo "EXAMPLES:\n";
    echo "  php import_episodes.php --dry-run --limit=10\n";
    echo "  php import_episodes.php --start-from=EP-1000\n";
    echo "  php import_episodes.php\n\n";

    echo "The script processes all HTML files in the 'episodes/' directory.\n";
    echo "Each episode becomes a published WordPress post with proper metadata.\n";
}