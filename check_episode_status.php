<?php
/**
 * Check Episode Status Across Directories
 *
 * Shows the current state of episodes in development and deployment directories
 */

class EpisodeStatusChecker {
    private $dev_dir;
    private $deploy_dir;
    private $wordpress_posts;

    public function __construct() {
        $this->dev_dir = 'D:/websites/digitaldreamscape.site/episodes';
        $this->deploy_dir = 'D:/websites/websites/digitaldreamscape.site/episodes';
    }

    public function show_status() {
        echo "📊 EPISODE STATUS REPORT\n";
        echo "========================\n\n";

        // Check development directory
        $dev_episodes = $this->get_episode_files($this->dev_dir);
        echo "🔧 DEVELOPMENT DIRECTORY\n";
        echo "------------------------\n";
        echo "📂 Location: {$this->dev_dir}\n";
        echo "📄 Episodes: " . count($dev_episodes) . "\n";

        if (!empty($dev_episodes)) {
            echo "📋 Latest episodes:\n";
            $latest = array_slice($dev_episodes, -5, 5, true);
            foreach ($latest as $filename => $info) {
                echo "   • EP-{$info['number']}: {$info['title']}\n";
            }
        }
        echo "\n";

        // Check deployment directory
        $deploy_episodes = $this->get_episode_files($this->deploy_dir);
        echo "🌐 DEPLOYMENT DIRECTORY\n";
        echo "-----------------------\n";
        echo "📂 Location: {$this->deploy_dir}\n";
        echo "📄 Episodes: " . count($deploy_episodes) . "\n";

        if (!empty($deploy_episodes)) {
            echo "📋 Latest episodes:\n";
            $latest = array_slice($deploy_episodes, -5, 5, true);
            foreach ($latest as $filename => $info) {
                echo "   • EP-{$info['number']}: {$info['title']}\n";
            }
        }
        echo "\n";

        // Check WordPress posts
        $this->check_wordpress_status();

        // Show sync status
        $this->show_sync_status($dev_episodes, $deploy_episodes);
    }

    private function get_episode_files($directory) {
        if (!is_dir($directory)) {
            return [];
        }

        $files = glob($directory . '/ep_*.html');
        $episodes = [];

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $filename = basename($file);

            // Extract episode info
            $number = 'unknown';
            if (preg_match('/ep_(\d+)_/', $filename, $matches)) {
                $number = $matches[1];
            }

            $title = 'Unknown Episode';
            if (preg_match('/<title>(.+?)<\/title>/', $content, $title_match)) {
                $title = trim($title_match[1]);
                $title = preg_replace('/ - Digital Dreamscape$/', '', $title);
            }

            $episodes[$filename] = [
                'number' => $number,
                'title' => $title,
                'path' => $file,
                'size' => filesize($file),
                'modified' => filemtime($file)
            ];
        }

        // Sort by episode number
        uasort($episodes, function($a, $b) {
            return $a['number'] <=> $b['number'];
        });

        return $episodes;
    }

    private function check_wordpress_status() {
        // Try to load WordPress to check posts
        $wp_load = $this->deploy_dir . '/wp/wp-load.php';

        if (file_exists($wp_load)) {
            require_once $wp_load;

            $episode_posts = get_posts([
                'meta_key' => 'artifact_type',
                'meta_value' => 'episode',
                'numberposts' => -1
            ]);

            echo "📝 WORDPRESS STATUS\n";
            echo "------------------\n";
            echo "📰 Published episodes: " . count($episode_posts) . "\n";

            if (!empty($episode_posts)) {
                echo "📋 Latest published:\n";
                $latest = array_slice($episode_posts, 0, 5);
                foreach ($latest as $post) {
                    $episode_num = get_post_meta($post->ID, 'episode_number', true);
                    echo "   • EP-{$episode_num}: {$post->post_title}\n";
                }
            }
            echo "\n";
        }
    }

    private function show_sync_status($dev_episodes, $deploy_episodes) {
        echo "🔄 SYNC STATUS\n";
        echo "--------------\n";

        $dev_count = count($dev_episodes);
        $deploy_count = count($deploy_episodes);

        $new_in_dev = $dev_count - $deploy_count;

        if ($new_in_dev > 0) {
            echo "📤 {$new_in_dev} episodes ready to sync from development\n";
            echo "💡 Run: php sync_episodes_to_deployment.php\n";
        } elseif ($new_in_dev < 0) {
            echo "⚠️  More episodes in deployment than development ({$deploy_count} vs {$dev_count})\n";
        } else {
            echo "✅ All episodes synced\n";
        }

        // Check for import needed
        $progress_file = $this->deploy_dir . '/episode_import_progress.json';
        if (file_exists($progress_file)) {
            $progress = json_decode(file_get_contents($progress_file), true);
            $imported = $progress['processed'] ?? 0;
            $total_deployed = $deploy_count;

            $unimported = $total_deployed - $imported;
            if ($unimported > 0) {
                echo "📝 {$unimported} episodes ready to import to WordPress\n";
                echo "💡 Run: cd websites/digitaldreamscape.site && php batch_import_episodes.php\n";
            }
        }

        echo "\n🚀 QUICK ACTIONS\n";
        echo "----------------\n";
        echo "• Check this status: php check_episode_status.php\n";
        echo "• Sync episodes: php sync_episodes_to_deployment.php\n";
        echo "• Import to WordPress: cd websites/digitaldreamscape.site && php batch_import_episodes.php\n";
        echo "• Full deployment: php automated_episode_deployment.php\n";
        echo "• Full deployment (batch): deploy_episodes.bat\n\n";
    }
}

// Run the status check
$checker = new EpisodeStatusChecker();
$checker->show_status();
?>