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
    ob_start();
    include $template;
    $html = ob_get_clean();
    $target = $root . '/' . $slug;
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
