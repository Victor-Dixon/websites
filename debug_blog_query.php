<?php
/**
 * Debug Blog Query
 */

require_once 'websites/digitaldreamscape.site/wp/wp-load.php';

echo "🔍 DEBUGGING BLOG QUERY\n";
echo "======================\n\n";

// Test the same query used in page-blog.php
$query_args = array(
    'post_type' => 'post',
    'posts_per_page' => 12,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC'
);

echo "Query args:\n";
print_r($query_args);
echo "\n";

$archive_query = new WP_Query($query_args);

echo "Query found: {$archive_query->found_posts} posts\n";
echo "Query returned: {$archive_query->post_count} posts\n\n";

if ($archive_query->have_posts()) {
    echo "First 5 posts returned:\n";
    $count = 0;
    while ($archive_query->have_posts() && $count < 5) {
        $archive_query->the_post();

        $post = $archive_query->post;
        $post_id = $post->ID;
        $artifact_type = get_post_meta($post_id, 'artifact_type', true) ?: 'episode';
        $artifact_state = get_post_meta($post_id, 'artifact_state', true) ?: 'active';

        echo ($count + 1) . ". {$post_id}: " . $post->post_title . "\n";
        echo "   Type: {$artifact_type}, State: {$artifact_state}\n";
        echo "   Date: " . $post->post_date . "\n\n";

        $count++;
    }
    wp_reset_postdata();
} else {
    echo "❌ No posts found by query!\n\n";

    // Check total posts in database
    $total_posts = wp_count_posts()->publish;
    echo "Total published posts in DB: {$total_posts->publish}\n";

    // Check recent posts
    echo "\nLast 5 posts in database:\n";
    $recent = get_posts(array('numberposts' => 5, 'post_status' => 'publish'));
    foreach ($recent as $post) {
        echo "• {$post->ID}: {$post->post_title} (status: {$post->post_status})\n";
    }
}

// Check if the blog page template is being used
echo "\n📄 PAGE TEMPLATE CHECK:\n";
echo "=====================\n";

$page_id = get_option('page_for_posts');
if ($page_id) {
    $template = get_page_template_slug($page_id);
    echo "Posts page ID: {$page_id}\n";
    echo "Template: " . ($template ?: 'default') . "\n";

    $page = get_post($page_id);
    echo "Page slug: {$page->post_name}\n";
    echo "Page URL: " . get_permalink($page_id) . "\n";
} else {
    echo "No posts page set in WordPress settings!\n";
    echo "Go to Settings > Reading and set a Posts page.\n";
}
?>