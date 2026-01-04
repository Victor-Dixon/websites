<?php
echo "Testing WordPress connection...\n";

if (file_exists('wp/wp-load.php')) {
    echo "✅ WordPress core files found locally\n";
    try {
        require_once('wp/wp-load.php');
        echo "✅ WordPress loaded successfully\n";
        echo "Site URL: " . get_site_url() . "\n";
        echo "Admin URL: " . admin_url() . "\n";

        // Test if we can create a post
        $test_post = [
            'post_title' => 'Connection Test',
            'post_content' => 'Testing WordPress connection',
            'post_status' => 'draft'
        ];
        $post_id = wp_insert_post($test_post);
        if (!is_wp_error($post_id)) {
            echo "✅ Can create posts (test post ID: $post_id)\n";
            wp_delete_post($post_id, true);
            echo "✅ Can delete posts\n";
        } else {
            echo "❌ Cannot create posts: " . $post_id->get_error_message() . "\n";
        }

    } catch (Exception $e) {
        echo "❌ Error loading WordPress: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ WordPress core files not found locally\n";
    echo "Checking if site is accessible via web...\n";

    // Try to access the site via HTTP
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $content = @file_get_contents('http://digitaldreamscape.site/wp-json/wp/v2/posts?per_page=1', false, $context);

    if ($content !== false) {
        echo "✅ Site is accessible via HTTP\n";
        $data = json_decode($content, true);
        if (isset($data) && is_array($data) && count($data) > 0) {
            echo "✅ WordPress REST API working\n";
            echo "Sample post title: " . ($data[0]['title']['rendered'] ?? 'N/A') . "\n";
        } else {
            echo "❌ WordPress REST API not responding properly\n";
        }
    } else {
        echo "❌ Site not accessible via HTTP\n";
        echo "Possible issues:\n";
        echo "- Site may not be running\n";
        echo "- Firewall blocking requests\n";
        echo "- DNS not resolving\n";
    }
}

echo "\nTest complete.\n";
?>