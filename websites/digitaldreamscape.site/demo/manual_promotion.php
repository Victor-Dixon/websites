<?php
/**
 * MANUAL DEVLOG PROMOTION DEMONSTRATION
 *
 * This script demonstrates what happens when a devlog gets promoted
 * to an episode artifact in the World Archive.
 */

// Read the devlog
$devlog_path = __DIR__ . '/../devlogs/2026-01-03_agent_cellphone_cleanup.md';
$content = file_get_contents($devlog_path);

// Parse the devlog content
function parse_devlog($content) {
    $lines = explode("\n", $content);
    $metadata = [];
    $body_start = 0;

    // Extract frontmatter
    if (trim($lines[0]) === '---') {
        $in_frontmatter = true;
        $frontmatter_lines = [];
        $i = 1;

        while ($i < count($lines) && trim($lines[$i]) !== '---') {
            $frontmatter_lines[] = $lines[$i];
            $i++;
        }

        foreach ($frontmatter_lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $metadata[trim($key)] = trim($value);
            }
        }

        $body_start = $i + 1;
    }

    // Extract title
    $title = 'Untitled Episode';
    foreach ($lines as $line) {
        if (preg_match('/^#\s+(.+)$/', $line, $matches)) {
            $title = trim($matches[1]);
            break;
        }
    }

    // Extract excerpt (first paragraph after title)
    $excerpt = '';
    $in_content = false;
    foreach ($lines as $line) {
        if (preg_match('/^#\s+/', $line)) {
            $in_content = true;
            continue;
        }

        if ($in_content && trim($line) !== '' && trim($line) !== '---') {
            $excerpt = trim($line);
            break;
        }
    }

    // Extract questline from content
    $questline = 'general';
    if (preg_match('/\*\*Questline\*\*:\s*(.+)/i', $content, $matches)) {
        $questline = trim($matches[1]);
    }

    return [
        'title' => $title,
        'content' => $content,
        'excerpt' => $excerpt,
        'metadata' => $metadata,
        'questline' => $questline
    ];
}

// Process the devlog
$parsed = parse_devlog($content);

// Generate the promotion result
$promotion_result = [
    'artifact_type' => 'episode',
    'questline' => $parsed['questline'],
    'artifact_state' => 'active',
    'era' => date('Y'),
    'source_system' => 'devlog',
    'internal_source' => 'devlogs/2026-01-03_agent_cellphone_cleanup.md',
    'title' => $parsed['title'],
    'content' => $parsed['content'],
    'excerpt' => $parsed['excerpt'],
    'post_status' => 'publish',
    'post_type' => 'post'
];

// Display the results
echo "🎭 DEVLOG PROMOTION DEMONSTRATION\n";
echo "================================\n\n";

echo "📝 Source File: devlogs/2026-01-03_agent_cellphone_cleanup.md\n\n";

echo "📊 Extracted Metadata:\n";
echo "- Title: {$promotion_result['title']}\n";
echo "- Questline: {$promotion_result['questline']}\n";
echo "- Type: {$promotion_result['artifact_type']}\n";
echo "- State: {$promotion_result['artifact_state']}\n";
echo "- Era: {$promotion_result['era']}\n";
echo "- Source: {$promotion_result['source_system']}\n\n";

echo "📄 Content Preview:\n";
echo substr($promotion_result['excerpt'], 0, 150) . "...\n\n";

echo "🌐 World Archive Entry:\n";
echo "EP-" . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT) . ": {$promotion_result['title']}\n";
echo "Questline: {$promotion_result['questline']}\n";
echo "State: {$promotion_result['artifact_state']}\n";
echo "URL: /blog/" . sanitize_title($promotion_result['title']) . "\n\n";

echo "✅ Promotion Complete!\n";
echo "The devlog is now an episode in the World Archive.\n";
echo "Visitors can find it under 'technical-debt' questline.\n";

/**
 * Helper function for URL slug generation
 */
function sanitize_title($title) {
    return strtolower(str_replace([' ', '_'], '-', $title));
}