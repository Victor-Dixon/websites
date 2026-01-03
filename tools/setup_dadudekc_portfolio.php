<?php
/**
 * Setup dadudekc.com portfolio
 * Run this on the server to create portfolio page and import sample projects
 */

require_once '../../../wp-load.php';

echo "🚀 Setting up dadudekc.com portfolio...\n\n";

// Check if user can create pages
if (!current_user_can('publish_pages')) {
    echo "❌ Insufficient permissions to create pages\n";
    echo "Please run this as an administrator or login to WordPress admin first\n";
    exit(1);
}

// Check if portfolio page already exists
$existing_page = get_page_by_path('portfolio');
if ($existing_page) {
    echo "ℹ️  Portfolio page already exists: {$existing_page->post_title}\n";
    echo "   URL: " . get_permalink($existing_page->ID) . "\n";
} else {
    // Create portfolio page
    $page_id = wp_insert_post([
        'post_title' => 'Portfolio',
        'post_content' => '<!-- wp:paragraph -->
<p>Welcome to my portfolio of shipped systems and solved problems. Here you\'ll find detailed case studies of projects that went from concept to completion.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Problem → Approach → Outcome</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Each project showcases real results, measurable impact, and the journey from challenge to solution.</p>
<!-- /wp:paragraph -->',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_name' => 'portfolio',
        'page_template' => 'page-portfolio.php'
    ]);

    if ($page_id) {
        echo "✅ Portfolio page created successfully!\n";
        echo "   Page ID: {$page_id}\n";
        echo "   URL: " . get_permalink($page_id) . "\n";
    } else {
        echo "❌ Failed to create portfolio page\n";
    }
}

// Import sample projects if they don't exist
$sample_projects_file = __DIR__ . '/sample_projects.json';
if (file_exists($sample_projects_file)) {
    echo "\n📦 Importing sample projects...\n";

    $projects = json_decode(file_get_contents($sample_projects_file), true);
    $imported = 0;
    $skipped = 0;

    foreach ($projects as $project_data) {
        // Check if project exists
        $existing = get_page_by_title($project_data['title'], OBJECT, 'project');
        if ($existing) {
            echo "   ⏭️  Skipping existing project: {$project_data['title']}\n";
            $skipped++;
            continue;
        }

        // Create project
        $project_id = wp_insert_post([
            'post_title' => $project_data['title'],
            'post_content' => $project_data['content'],
            'post_excerpt' => $project_data['excerpt'],
            'post_status' => 'publish',
            'post_type' => 'project'
        ]);

        if ($project_id) {
            // Add meta fields
            foreach ($project_data['meta'] as $key => $value) {
                update_post_meta($project_id, $key, $value);
            }
            echo "   ✅ Imported: {$project_data['title']}\n";
            $imported++;
        } else {
            echo "   ❌ Failed to import: {$project_data['title']}\n";
        }
    }

    echo "\n📊 Import Summary:\n";
    echo "   Imported: {$imported} projects\n";
    echo "   Skipped: {$skipped} existing projects\n";
} else {
    echo "\n⚠️  Sample projects file not found: {$sample_projects_file}\n";
}

echo "\n🎉 Portfolio setup complete!\n";
echo "   Visit: https://dadudekc.com/portfolio\n";
echo "\nNext steps:\n";
echo "   1. Check the portfolio page loads correctly\n";
echo "   2. Update navigation menu if needed\n";
echo "   3. Add more projects or customize the design\n";