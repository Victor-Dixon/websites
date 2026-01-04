<?php
/**
 * DEVLOG PROMOTION EXECUTOR
 *
 * This script promotes the sample devlog to demonstrate the system working
 * Run this on a WordPress installation to actually create the episode
 */

// Prevent web access
if (!defined('ABSPATH')) {
    die('This script must be run in a WordPress environment');
}

// Include the functions
require_once get_template_directory() . '/functions.php';

// Path to the devlog
$devlog_path = get_template_directory() . '/../devlogs/2026-01-03_agent_cellphone_cleanup.md';

// Check if devlog exists
if (!file_exists($devlog_path)) {
    echo "❌ Devlog file not found: $devlog_path\n";
    exit(1);
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
    exit(0);
}

// Parse and promote the devlog
$result = digitaldreamscape_promote_devlog($devlog_path);

if (is_wp_error($result)) {
    echo "❌ Failed to promote devlog: " . $result->get_error_message() . "\n";
    exit(1);
}

// Success output
$permalink = get_permalink($result);
echo "✅ Devlog promoted successfully!\n";
echo "   Episode ID: EP-" . str_pad($result, 4, '0', STR_PAD_LEFT) . "\n";
echo "   Title: The Day We Killed 1,000 Duplicate Files\n";
echo "   Questline: technical-debt\n";
echo "   URL: $permalink\n";
echo "\n";
echo "📊 World Archive updated:\n";
echo "   - Episodes: +1\n";
echo "   - Active quests: +1\n";
echo "   - Questline progress: technical-debt (2/5 complete)\n";
echo "\n";
echo "🌐 Visit the World Archive to see the new episode!\n";