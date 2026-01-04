<?php
/**
 * World Archive Promotion Pipeline
 *
 * Command-line tool to promote internal system artifacts to public archive
 * Usage: php promote_artifacts.php [command] [options]
 */

// Prevent web access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line');
}

// Load WordPress environment
require_once(dirname(__FILE__) . '/wp/wp-load.php');

// Commands
$command = $argv[1] ?? 'help';

switch ($command) {
    case 'devlog':
        promote_devlog($argv[2] ?? null);
        break;

    case 'tasklist':
        promote_tasklist($argv[2] ?? null);
        break;

    case 'status':
        show_promotion_status();
        break;

    case 'help':
    default:
        show_help();
        break;
}

function promote_devlog($devlog_path = null) {
    if (!$devlog_path) {
        echo "Usage: php promote_artifacts.php devlog <path/to/devlog.md>\n";
        return;
    }

    if (!file_exists($devlog_path)) {
        echo "Error: Devlog file not found: $devlog_path\n";
        return;
    }

    echo "🔍 Processing devlog: $devlog_path\n";

    $content = file_get_contents($devlog_path);
    $filename = basename($devlog_path);

    // Extract metadata from filename (YYYY-MM-DD_description.md)
    if (!preg_match('/(\d{4}-\d{2}-\d{2})_(.+)\.md$/', $filename, $matches)) {
        echo "Error: Invalid filename format. Expected: YYYY-MM-DD_description.md\n";
        return;
    }

    $date = $matches[1];
    $description = str_replace(['_', '-'], ' ', $matches[2]);

    // Extract title from content (first # header)
    $title = $description; // fallback
    if (preg_match('/^#\s+(.+)$/m', $content, $title_match)) {
        $title = trim($title_match[1]);
    }

    // Extract excerpt (first paragraph after title)
    $excerpt = '';
    if (preg_match('/^#\s+.+\n\n(.+?)(?:\n\n|---|$)/s', $content, $excerpt_match)) {
        $excerpt = trim($excerpt_match[1]);
    }

    // Determine questline from content or filename
    $questline = 'general';
    if (preg_match('/questline:\s*(.+)/i', $content, $ql_match)) {
        $questline = trim($ql_match[1]);
    }

    // Check if already promoted
    $existing_posts = get_posts([
        'meta_key' => 'internal_source',
        'meta_value' => $devlog_path,
        'post_type' => 'post',
        'posts_per_page' => 1
    ]);

    if (!empty($existing_posts)) {
        echo "⚠️  Already promoted as: {$existing_posts[0]->post_title}\n";
        echo "   URL: " . get_permalink($existing_posts[0]) . "\n";
        return;
    }

    $artifact_data = [
        'title' => $title,
        'content' => $content,
        'excerpt' => $excerpt,
        'artifact_type' => 'episode',
        'questline' => $questline,
        'artifact_state' => 'active',
        'era' => date('Y', strtotime($date)),
        'source_system' => 'devlog',
        'internal_source' => $devlog_path
    ];

    $result = digitaldreamscape_promote_artifact($artifact_data);

    if (is_wp_error($result)) {
        echo "❌ Failed to promote devlog: " . $result->get_error_message() . "\n";
    } else {
        echo "✅ Promoted devlog to episode\n";
        echo "   Title: $title\n";
        echo "   Questline: $questline\n";
        echo "   URL: " . get_permalink($result) . "\n";
    }
}

function promote_tasklist($tasklist_path = null) {
    if (!$tasklist_path) {
        echo "Usage: php promote_artifacts.php tasklist <path/to/tasklist.json>\n";
        return;
    }

    if (!file_exists($tasklist_path)) {
        echo "Error: Tasklist file not found: $tasklist_path\n";
        return;
    }

    echo "🔍 Processing tasklist: $tasklist_path\n";

    $json_content = file_get_contents($tasklist_path);
    $tasks = json_decode($json_content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error: Invalid JSON in tasklist file\n";
        return;
    }

    $questline_name = $tasks['questline'] ?? 'general';
    $task_items = $tasks['tasks'] ?? [];

    echo "📋 Promoting questline: $questline_name\n";
    echo "📝 Tasks: " . count($task_items) . "\n";

    $created_posts = [];
    $completed_count = 0;

    foreach ($task_items as $task) {
        if (empty($task['title'])) continue;

        $artifact_data = [
            'title' => $task['title'],
            'content' => $task['description'] ?? '',
            'excerpt' => substr($task['description'] ?? $task['title'], 0, 150) . '...',
            'artifact_type' => 'episode',
            'questline' => $questline_name,
            'artifact_state' => $task['completed'] ? 'resolved' : 'active',
            'era' => date('Y'),
            'source_system' => 'tasklist',
            'quest_progress' => $task['progress'] ?? ''
        ];

        $result = digitaldreamscape_promote_artifact($artifact_data);

        if (!is_wp_error($result)) {
            $created_posts[] = $result;
            if ($task['completed']) $completed_count++;
        } else {
            echo "❌ Failed to promote task: {$task['title']} - " . $result->get_error_message() . "\n";
        }
    }

    echo "✅ Promoted " . count($created_posts) . " tasks to questline '$questline_name'\n";
    echo "   Completed: $completed_count/" . count($task_items) . "\n";

    if (!empty($created_posts)) {
        echo "   Questline URL: " . home_url("/blog/?questline=" . sanitize_title($questline_name)) . "\n";
    }
}

function show_promotion_status() {
    echo "📊 World Archive Promotion Status\n";
    echo "================================\n\n";

    // Total artifacts
    $total_posts = wp_count_posts()->publish;
    echo "📦 Total Artifacts: {$total_posts['publish']}\n\n";

    // By type
    $types = ['episode', 'artifact', 'canon', 'devlog'];
    echo "🎭 By Type:\n";
    foreach ($types as $type) {
        $count = count(get_posts([
            'meta_key' => 'artifact_type',
            'meta_value' => $type,
            'posts_per_page' => -1
        ]));
        echo "   $type: $count\n";
    }

    // By state
    $states = ['active', 'resolved', 'canon', 'ruins'];
    echo "\n🎯 By State:\n";
    foreach ($states as $state) {
        $count = count(get_posts([
            'meta_key' => 'artifact_state',
            'meta_value' => $state,
            'posts_per_page' => -1
        ]));
        echo "   $state: $count\n";
    }

    // Questlines
    $questlines = get_categories(['hide_empty' => true]);
    echo "\n⚔️ Active Questlines:\n";
    foreach ($questlines as $ql) {
        echo "   {$ql->name}: {$ql->count} artifacts\n";
    }

    // Recent promotions
    $recent = get_posts([
        'meta_key' => 'source_system',
        'meta_value' => ['devlog', 'tasklist', 'agent'],
        'meta_compare' => 'IN',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);

    echo "\n🕒 Recent Promotions:\n";
    foreach ($recent as $post) {
        $source = get_post_meta($post->ID, 'source_system', true);
        $type = get_post_meta($post->ID, 'artifact_type', true);
        echo "   [$source → $type] {$post->post_title}\n";
    }
}

function show_help() {
    echo "🌌 Digital Dreamscape - World Archive Promotion Pipeline\n";
    echo "======================================================\n\n";

    echo "Promote internal system artifacts to the public World Archive\n\n";

    echo "COMMANDS:\n";
    echo "  devlog <path>     Promote a devlog file to episode artifact\n";
    echo "  tasklist <path>   Promote a tasklist JSON to questline artifacts\n";
    echo "  status           Show current promotion statistics\n";
    echo "  help             Show this help message\n\n";

    echo "EXAMPLES:\n";
    echo "  php promote_artifacts.php devlog devlogs/2026-01-03_agent_cleanup.md\n";
    echo "  php promote_artifacts.php tasklist tasks/debt-purge.json\n";
    echo "  php promote_artifacts.php status\n\n";

    echo "FILE FORMATS:\n";
    echo "  Devlogs: YYYY-MM-DD_description.md (Markdown with optional frontmatter)\n";
    echo "  Tasklists: JSON with {questline, tasks[]} structure\n\n";

    echo "The promotion pipeline turns internal system state into public world artifacts.\n";
    echo "No extra writing required - just classification and framing.\n";
}

// Utility function for questline slug generation
function sanitize_title($title) {
    return strtolower(str_replace([' ', '_'], '-', $title));
}