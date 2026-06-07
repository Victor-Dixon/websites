<?php
/**
 * Render Dream.OS theme templates to static route index.html files.
 * Run with WordPress bootstrapped.
 */
if (!defined('ABSPATH')) {
    exit(1);
}

$root = ABSPATH;
$theme = get_template_directory();
$routes = array(
    'projects' => 'page-projects.php',
    'profile' => 'page-profile.php',
    'live-ops' => 'page-live-ops.php',
    'feed' => 'page-feed.php',
    'tasks' => 'page-tasks.php',
    'skill-tree' => 'page-skills.php',
);

foreach ($routes as $dir => $file) {
    $template = $theme . '/' . $file;
    if (!is_readable($template)) {
        fwrite(STDERR, "Missing template: $template\n");
        exit(1);
    }
    ob_start();
    include $template;
    $html = ob_get_clean();
    $target_dir = rtrim($root, '/') . '/' . $dir;
    if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
        fwrite(STDERR, "Failed to create directory: $target_dir\n");
        exit(1);
    }
    $target = $target_dir . '/index.html';
    if (file_put_contents($target, $html) === false) {
        fwrite(STDERR, "Failed to write: $target\n");
        exit(1);
    }
    // Flat alias for skill-tree (legacy URL without trailing slash directory)
    if ($dir === 'skill-tree') {
        file_put_contents(rtrim($root, '/') . '/skill-tree', $html);
    }
    echo "Rendered $target\n";
}

// Ensure WordPress uses www canonical host (stops redirect loops with apex rules).
update_option('siteurl', 'https://www.weareswarm.site');
update_option('home', 'https://www.weareswarm.site');
echo "Updated siteurl/home to https://www.weareswarm.site\n";
