<?php
/**
 * Digital Dreamscape System Status Checker
 *
 * Displays comprehensive status of the blog system
 * Usage: php system_status.php
 */

// Load WordPress environment
require_once('wp/wp-content/themes/digitaldreamscape/functions.php');

class SystemStatusChecker {

    public function display_status() {
        echo "🌌 DIGITAL DREAMSCAPE SYSTEM STATUS\n";
        echo "====================================\n\n";

        $this->check_wordpress_status();
        $this->check_post_statistics();
        $this->check_canon_system();
        $this->check_theme_status();
        $this->check_server_health();
        $this->display_recent_posts();

        echo "\n🎯 SYSTEM STATUS COMPLETE\n";
    }

    private function check_wordpress_status() {
        echo "🔧 WORDPRESS CORE STATUS\n";
        echo "-----------------------\n";

        global $wp_version;
        echo "📦 WordPress Version: $wp_version\n";

        // Check if WordPress is loaded
        if (function_exists('wp_count_posts')) {
            echo "✅ WordPress Core: Loaded\n";
        } else {
            echo "❌ WordPress Core: Failed to load\n";
        }

        // Check database connection
        global $wpdb;
        if ($wpdb->check_connection()) {
            echo "🗄️ Database: Connected\n";
        } else {
            echo "❌ Database: Connection failed\n";
        }

        echo "\n";
    }

    private function check_post_statistics() {
        echo "📊 POST STATISTICS\n";
        echo "-----------------\n";

        $post_counts = wp_count_posts();
        $total_posts = $post_counts->publish;
        $draft_posts = $post_counts->draft;

        echo "📝 Published Posts: $total_posts\n";
        echo "📝 Draft Posts: $draft_posts\n";

        // Check for episode posts (assuming they have specific metadata)
        $episode_posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'episode_type',
                    'compare' => 'EXISTS'
                ]
            ],
            'numberposts' => -1
        ]);

        echo "🎭 Episode Posts: " . count($episode_posts) . "\n";

        // Calculate expected vs actual
        $expected_episodes = 3258; // Based on user's report
        $progress_percentage = $total_posts > 0 ? round(($total_posts / $expected_episodes) * 100, 1) : 0;

        echo "🎯 Expected Episodes: $expected_episodes\n";
        echo "📈 Import Progress: {$progress_percentage}%\n";

        if ($total_posts >= $expected_episodes) {
            echo "✅ Status: Import Complete!\n";
        } elseif ($total_posts > 0) {
            echo "🔄 Status: Import In Progress\n";
        } else {
            echo "❌ Status: Import Not Started\n";
        }

        echo "\n";
    }

    private function check_canon_system() {
        echo "🏛️ CANON SYSTEM STATUS\n";
        echo "---------------------\n";

        $canon_file = __DIR__ . '/canon_data.json';

        if (file_exists($canon_file)) {
            $canon_data = json_decode(file_get_contents($canon_file), true);

            if ($canon_data) {
                $canon_count = isset($canon_data['canon_terms']) ? count($canon_data['canon_terms']) : 0;
                $last_scan = isset($canon_data['last_scan']) ? $canon_data['last_scan'] : 'Never';

                echo "✅ Canon System: Active\n";
                echo "🏛️ Canon Elements: $canon_count\n";
                echo "🕒 Last Scan: $last_scan\n";

                if (isset($canon_data['total_posts_scanned'])) {
                    echo "📊 Posts Scanned: {$canon_data['total_posts_scanned']}\n";
                }
            } else {
                echo "❌ Canon System: Data file corrupted\n";
            }
        } else {
            echo "❌ Canon System: Not initialized\n";
            echo "💡 Run: php canon_declaration_system.php scan\n";
        }

        echo "\n";
    }

    private function check_theme_status() {
        echo "🎨 THEME STATUS\n";
        echo "--------------\n";

        $current_theme = wp_get_theme();
        echo "📱 Active Theme: " . $current_theme->get('Name') . "\n";
        echo "🏷️ Theme Version: " . $current_theme->get('Version') . "\n";

        // Check if theme files exist
        $theme_dir = get_template_directory();
        if (is_dir($theme_dir)) {
            echo "✅ Theme Directory: Exists\n";

            // Check key template files
            $key_files = ['index.php', 'single.php', 'archive.php', 'functions.php', 'style.css'];
            $missing_files = [];

            foreach ($key_files as $file) {
                if (!file_exists($theme_dir . '/' . $file)) {
                    $missing_files[] = $file;
                }
            }

            if (empty($missing_files)) {
                echo "✅ Theme Files: All present\n";
            } else {
                echo "⚠️ Missing Files: " . implode(', ', $missing_files) . "\n";
            }
        } else {
            echo "❌ Theme Directory: Missing\n";
        }

        echo "\n";
    }

    private function check_server_health() {
        echo "🖥️ SERVER HEALTH\n";
        echo "---------------\n";

        // PHP Version
        echo "🐘 PHP Version: " . phpversion() . "\n";

        // Memory limit
        $memory_limit = ini_get('memory_limit');
        echo "🧠 Memory Limit: $memory_limit\n";

        // Max execution time
        $max_execution_time = ini_get('max_execution_time');
        echo "⏱️ Max Execution Time: {$max_execution_time}s\n";

        // Check if required extensions are loaded
        $required_extensions = ['json', 'mbstring', 'curl'];
        $missing_extensions = [];

        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $missing_extensions[] = $ext;
            }
        }

        if (empty($missing_extensions)) {
            echo "✅ PHP Extensions: All required loaded\n";
        } else {
            echo "❌ Missing Extensions: " . implode(', ', $missing_extensions) . "\n";
        }

        // Disk space (if available)
        $disk_free = @disk_free_space(__DIR__);
        if ($disk_free) {
            $disk_free_gb = round($disk_free / 1024 / 1024 / 1024, 2);
            echo "💾 Free Disk Space: {$disk_free_gb}GB\n";
        }

        echo "\n";
    }

    private function display_recent_posts() {
        echo "📰 RECENT POSTS\n";
        echo "---------------\n";

        $recent_posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        if (!empty($recent_posts)) {
            foreach ($recent_posts as $post) {
                $date = get_the_date('Y-m-d', $post->ID);
                $title = substr($post->post_title, 0, 60);
                if (strlen($post->post_title) > 60) {
                    $title .= '...';
                }
                echo "📅 $date: $title\n";
            }
        } else {
            echo "❌ No published posts found\n";
        }

        echo "\n";
    }
}

// Execute status check
$checker = new SystemStatusChecker();
$checker->display_status();