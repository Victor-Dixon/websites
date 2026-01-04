<?php
// Direct database fix for blog configuration

$conn = new mysqli('localhost', 'root', '', 'digitaldreamscape');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

echo "🔧 FIXING BLOG CONFIGURATION IN DATABASE\n";
echo "========================================\n\n";

// Check current wp_options
echo "CURRENT SETTINGS:\n";
$result = $conn->query("SELECT option_name, option_value FROM wp_options WHERE option_name IN ('page_for_posts', 'show_on_front', 'page_on_front')");
while ($row = $result->fetch_assoc()) {
    echo $row['option_name'] . ': ' . $row['option_value'] . "\n";
}
echo "\n";

// Check if blog page exists
$result = $conn->query("SELECT ID, post_title, post_name FROM wp_posts WHERE post_name = 'blog' AND post_type = 'page' AND post_status = 'publish'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "BLOG PAGE EXISTS:\n";
    echo "ID: {$row['ID']}\n";
    echo "Title: {$row['post_title']}\n";
    echo "Slug: {$row['post_name']}\n\n";

    $page_id = $row['ID'];

    // Check current template
    $template_result = $conn->query("SELECT meta_value FROM wp_postmeta WHERE post_id = $page_id AND meta_key = '_wp_page_template'");
    if ($template_result->num_rows > 0) {
        $meta_row = $template_result->fetch_assoc();
        echo "Current template: {$meta_row['meta_value']}\n";
    } else {
        echo "No template set\n";
    }

    // Set template to page-blog.php
    $conn->query("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES ($page_id, '_wp_page_template', 'page-blog.php') ON DUPLICATE KEY UPDATE meta_value = 'page-blog.php'");
    echo "✅ Set template to page-blog.php\n";

} else {
    echo "BLOG PAGE DOES NOT EXIST - CREATING IT:\n";

    // Create blog page
    $conn->query("INSERT INTO wp_posts (post_title, post_name, post_content, post_status, post_type, post_date, post_date_gmt) VALUES ('Blog', 'blog', 'Welcome to the Digital Dreamscape Blog - our central archive of episodes, lore, and canonical content.', 'publish', 'page', NOW(), NOW())");
    $page_id = $conn->insert_id;

    // Set template
    $conn->query("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES ($page_id, '_wp_page_template', 'page-blog.php')");

    echo "✅ Created blog page (ID: $page_id)\n";
}

// Set as posts page
$conn->query("UPDATE wp_options SET option_value = '$page_id' WHERE option_name = 'page_for_posts'");
echo "✅ Set as posts page\n";

echo "\nVERIFICATION:\n";
$result = $conn->query("SELECT option_name, option_value FROM wp_options WHERE option_name = 'page_for_posts'");
$row = $result->fetch_assoc();
echo "page_for_posts: {$row['option_value']}\n";

$template_result = $conn->query("SELECT meta_value FROM wp_postmeta WHERE post_id = $page_id AND meta_key = '_wp_page_template'");
$meta_row = $template_result->fetch_assoc();
echo "Template: {$meta_row['meta_value']}\n";

$conn->close();

echo "\n🎉 DATABASE UPDATED!\n";
echo "===================\n";
echo "Refresh https://digitaldreamscape.site/blog/ to see your episodes!\n\n";
?>