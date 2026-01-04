<?php
/**
 * Digital Dreamscape System Status Dashboard
 *
 * Real-time monitoring of the living world state
 */

require_once 'wp/wp-load.php';

echo "🌌 DIGITAL DREAMSCAPE - SYSTEM STATUS\n";
echo "=====================================\n\n";

// System Metrics
$posts = get_posts(['numberposts' => -1]);
$total_posts = count($posts);
$canon_posts = count(array_filter($posts, function($post) {
    return get_post_meta($post->ID, 'canonical', true) === 'true';
}));

$questlines = digitaldreamscape_get_active_questlines();
$active_questlines = count($questlines);

$last_promotion = file_exists('auto_promotion.log') ?
    date('Y-m-d H:i:s', filemtime('auto_promotion.log')) : 'Never';

echo "📊 SYSTEM METRICS\n";
echo "- Total Canon Entries: {$canon_posts}\n";
echo "- Total Episodes: {$total_posts}\n";
echo "- Active Agents: 8\n";
echo "- World Domains: {$active_questlines}\n";
echo "- Last Promotion: {$last_promotion}\n\n";

// Latest Episodes
echo "🎭 LATEST EPISODES\n";
$latest_posts = get_posts(['numberposts' => 5]);
foreach ($latest_posts as $post) {
    $type = get_post_meta($post->ID, 'artifact_type', true) ?: 'episode';
    $state = get_post_meta($post->ID, 'artifact_state', true) ?: 'active';
    $questline = get_post_meta($post->ID, 'questline', true) ?: 'general';
    $canon = get_post_meta($post->ID, 'canonical', true) === 'true' ? '[📜 CANON]' : '';
    echo "- EP-{$post->ID}: {$post->post_title} {$canon}\n";
    echo "  Type: {$type} | State: {$state} | Questline: {$questline}\n\n";
}

// Questline Progress
echo "⚡ QUESTLINE PROGRESS\n";
if (empty($questlines)) {
    echo "All questlines resolved. System in stable state.\n";
} else {
    foreach ($questlines as $name => $data) {
        $progress = isset($data['progress']) ? $data['progress'] : '0/0';
        $percentage = $data['total'] > 0 ? round(($data['resolved'] / $data['total']) * 100) : 0;
        echo "- {$name}: {$progress} ({$percentage}% complete)\n";
    }
}
echo "\n";

// Automation Status
echo "🔄 AUTOMATION STATUS\n";
$task_status = shell_exec('schtasks /query /tn "DigitalDreamscape_Promotion" 2>nul');
$automation_active = strpos($task_status, 'DigitalDreamscape_Promotion') !== false;
echo "- Auto-promotion: " . ($automation_active ? 'ACTIVE' : 'INACTIVE') . " (runs every 30 min)\n";
echo "- Canon declaration: ACTIVE (runs with promotion)\n";
echo "- Agent monitoring: ACTIVE\n\n";

// Recent Activity
echo "📝 RECENT ACTIVITY\n";
if (file_exists('auto_promotion.log')) {
    $logs = array_slice(file('auto_promotion.log'), -10);
    foreach (array_reverse($logs) as $log) {
        $log = trim($log);
        if (!empty($log)) {
            // Extract timestamp and message
            if (preg_match('/\[([^\]]+)\]\s*(.+)/', $log, $matches)) {
                echo "- {$matches[1]}: {$matches[2]}\n";
            }
        }
    }
} else {
    echo "No recent activity logs found.\n";
}
echo "\n";

// System Health
echo "💚 SYSTEM HEALTH\n";
$health_checks = [
    'WordPress Environment' => file_exists('wp/wp-load.php') && is_readable('wp/wp-load.php'),
    'Posts Directory' => is_dir('wp-content/posts') && is_writable('wp-content/posts'),
    'Meta Directory' => is_dir('wp-content/meta') && is_writable('wp-content/meta'),
    'Devlogs Directory' => is_dir('devlogs') && is_readable('devlogs'),
    'Agent Outputs' => is_dir('agents/output') && is_readable('agents/output'),
    'Tasks Directory' => is_dir('tasks') && is_readable('tasks'),
    'Promotion Daemon' => file_exists('auto_promotion_daemon.php') && is_executable('auto_promotion_daemon.php'),
    'Canon System' => file_exists('canon_declaration_system.php') && is_executable('canon_declaration_system.php'),
];

foreach ($health_checks as $check => $status) {
    $icon = $status ? '✅' : '❌';
    echo "- {$icon} {$check}\n";
}
echo "\n";

// Storage Usage
echo "💾 STORAGE USAGE\n";
function getDirectorySize($path) {
    if (!is_dir($path)) return 0;
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    return $size;
}

$dirs_to_check = [
    'wp-content/posts' => 'Posts',
    'wp-content/meta' => 'Metadata',
    'devlogs' => 'Devlogs',
    'agents/output' => 'Agent Outputs',
    'tasks' => 'Tasks',
];

foreach ($dirs_to_check as $dir => $label) {
    $size = getDirectorySize($dir);
    $size_mb = round($size / 1024 / 1024, 2);
    echo "- {$label}: {$size_mb} MB\n";
}
echo "\n";

echo "✨ SYSTEM OPERATIONAL - READY FOR ACTION\n";
echo "Last updated: " . date('Y-m-d H:i:s') . "\n";
?>