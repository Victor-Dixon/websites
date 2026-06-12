<?php
/**
 * Render Dream.OS theme templates to flat static route files.
 * Run with WordPress bootstrapped.
 */
if (!defined('ABSPATH')) {
    exit(1);
}

$root = rtrim(ABSPATH, '/');
$theme = get_template_directory();
$routes = array(
    'projects' => 'page-projects.php',
    'profile' => 'page-profile.php',
    'live-ops' => 'page-live-ops.php',
    'feed' => 'page-feed.php',
    'tasks' => 'page-tasks.php',
    'skill-tree' => 'page-skills.php',
);

foreach ($routes as $slug => $file) {
    $template = $theme . '/' . $file;
    if (!is_readable($template)) {
        fwrite(STDERR, "Missing template: $template\n");
        exit(1);
    }

    $target = $root . '/' . $slug;
    $legacy_dir = $target . '/index.html';
    if (is_dir($target)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($target);
        echo "Removed legacy directory: $target\n";
    } elseif (is_file($legacy_dir)) {
        unlink($legacy_dir);
        echo "Removed legacy file: $legacy_dir\n";
    }

    ob_start();
    include $template;
    $html = ob_get_clean();
    if (file_put_contents($target, $html) === false) {
        fwrite(STDERR, "Failed to write: $target\n");
        exit(1);
    }
    echo "Rendered $target\n";
}

// Ensure WordPress uses www canonical host (stops redirect loops with apex rules).
update_option('siteurl', 'https://www.weareswarm.site');
update_option('home', 'https://www.weareswarm.site');
echo "Updated siteurl/home to https://www.weareswarm.site\n";
