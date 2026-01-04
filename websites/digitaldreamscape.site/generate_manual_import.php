<?php
/**
 * Generate Manual Import Instructions
 */

require_once 'wp/wp-load.php';

echo "🎯 DIGITAL DREAMSCAPE - MANUAL IMPORT INSTRUCTIONS\n";
echo "===================================================\n\n";

echo "📋 STEP BY STEP GUIDE TO GET YOUR POSTS ONLINE\n\n";

$posts = get_posts(['numberposts' => -1]);

echo "You have " . count($posts) . " posts ready to import:\n\n";

foreach ($posts as $index => $post) {
    $post_num = $index + 1;
    $artifact_type = get_post_meta($post->ID, 'artifact_type', true) ?: 'episode';
    $questline = get_post_meta($post->ID, 'questline', true) ?: '';

    echo "📝 POST {$post_num}: \"{$post->post_title}\"\n";
    echo str_repeat("-", 50 + strlen($post->post_title)) . "\n\n";

    echo "🔗 WORDPRESS ADMIN URL: https://digitaldreamscape.site/wp-admin/post-new.php\n\n";

    echo "📝 COPY THESE VALUES:\n\n";

    echo "TITLE:\n";
    echo "{$post->post_title}\n\n";

    echo "CONTENT:\n";
    // For JSON content, show a truncated version
    $content = $post->post_content;
    if (strlen($content) > 500) {
        echo substr($content, 0, 500) . "...\n\n";
        echo "⚠️  NOTE: This is a long JSON content. Copy the full content from the exported file.\n\n";
    } else {
        echo "{$content}\n\n";
    }

    echo "EXCERPT:\n";
    echo "{$post->post_excerpt}\n\n";

    echo "CUSTOM FIELDS:\n";
    echo "- artifact_type: {$artifact_type}\n";
    if ($questline) {
        echo "- questline: {$questline}\n";
    }
    echo "\n";

    echo "✅ PUBLISH STEPS:\n";
    echo "1. Paste title, content, and excerpt\n";
    echo "2. Add custom fields (Screen Options > Custom Fields)\n";
    echo "3. Set status to Published\n";
    echo "4. Click Publish\n\n";

    echo str_repeat("=", 60) . "\n\n";
}

echo "🎯 FINAL VERIFICATION:\n\n";
echo "After importing all posts:\n";
echo "1. Visit: https://digitaldreamscape.site/blog/\n";
echo "2. You should see your posts in the Dreamscape Codex\n";
echo "3. Filter and search should work\n";
echo "4. Theme styling should be applied\n\n";

echo "🚀 SUCCESS! Your Digital Dreamscape is now LIVE!\n\n";

echo "💡 OPTIONAL NEXT STEPS:\n";
echo "- Set up automated promotion: setup_cron.bat\n";
echo "- Configure continuous deployment\n";
echo "- Add more devlogs and agent outputs\n\n";

echo "📚 RESOURCES:\n";
echo "- WORDPRESS_INTEGRATION_README.md - Complete technical docs\n";
echo "- SYSTEM_SHOWCASE.md - What we've built\n";
echo "- LIVE_DEPLOYMENT_GUIDE.md - Alternative deployment methods\n";
?>