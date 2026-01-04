<?php
/**
 * Setup Episodes Page for Digital Dreamscape
 * Creates the /episodes/ page that uses the page-episodes.php template
 */

// Database connection
$servername = "localhost";
$username = "root"; // Adjust as needed
$password = ""; // Adjust as needed
$dbname = "digitaldreamscape"; // Adjust as needed

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if episodes page already exists
    $check_sql = "SELECT ID FROM wp_posts WHERE post_name = 'episodes' AND post_type = 'page' AND post_status = 'publish'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        echo "Episodes page already exists!\n";
        echo "You can access it at: yoursite.com/episodes/\n";
    } else {
        // Create the episodes page
        $title = "Episodes Directory";
        $content = "Browse all converted devlog episodes from the Digital Dreamscape archive.";
        $slug = "episodes";
        $template = "page-episodes.php";

        $insert_sql = "INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
                      VALUES (1, NOW(), NOW(), ?, ?, '', 'publish', 'closed', 'closed', '', ?, '', '', NOW(), NOW(), '', 0, '', 0, 'page', '', 0)";

        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $content, $title, $slug);
        $stmt->execute();

        $page_id = $conn->insert_id;

        // Set the page template
        $meta_sql = "INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (?, '_wp_page_template', ?)";
        $meta_stmt = $conn->prepare($meta_sql);
        $meta_stmt->bind_param("is", $page_id, $template);
        $meta_stmt->execute();

        echo "✅ Episodes page created successfully!\n";
        echo "Page ID: $page_id\n";
        echo "Access it at: yoursite.com/episodes/\n";
    }

    $conn->close();

} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nAlternative: Create the page manually in WordPress admin:\n";
    echo "1. Go to Pages → Add New\n";
    echo "2. Title: Episodes Directory\n";
    echo "3. URL Slug: episodes\n";
    echo "4. Template: Episodes Directory (from Page Attributes)\n";
    echo "5. Publish the page\n";
}

echo "\n📝 Manual Setup Instructions:\n";
echo "If the automatic setup doesn't work, create the page manually:\n\n";

echo "WordPress Admin Steps:\n";
echo "1. Login to your WordPress admin\n";
echo "2. Go to Pages → Add New\n";
echo "3. Set Title: 'Episodes Directory'\n";
echo "4. Set URL Slug: 'episodes' (under Permalink)\n";
echo "5. In Page Attributes (right sidebar), select Template: 'Episodes Directory'\n";
echo "6. Add some basic content if needed\n";
echo "7. Publish the page\n\n";

echo "The page will then be accessible at: https://yourdomain.com/episodes/\n";
?>