<?php
/**
 * Helper: Load all PHP files from the /inc/ directory and its subdirectories.
 *
 * @package SimplifiedTradingTheme
 */

function simplifiedtheme_load_files($directory) {
    $dir = get_template_directory() . '/' . $directory;

    if (!is_dir($dir)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            require_once $file->getPathname();
        }
    }
}

// Load all helper files (excluding load-files.php itself to prevent circular dependency)
$helpers_dir = get_template_directory() . '/inc/helpers';
if (is_dir($helpers_dir)) {
    $helper_files = glob($helpers_dir . '/*.php');
    foreach ($helper_files as $file) {
        if (basename($file) !== 'load-files.php') {
            require_once $file;
        }
    }
}

// Load all post type files
simplifiedtheme_load_files('inc/post-types');

// Load all taxonomy files
simplifiedtheme_load_files('inc/taxonomies');

// Load all meta box files
simplifiedtheme_load_files('inc/meta-boxes');

// Load admin customizations
simplifiedtheme_load_files('inc/admin');

// Load asset enqueues
simplifiedtheme_load_files('inc/assets');

// Load cron jobs
simplifiedtheme_load_files('inc/cron-jobs');

// DON'T auto-load CLI commands - they should only be run explicitly via wp eval-file
// Auto-loading causes fatal errors when CLI files execute during WordPress load
// CLI commands should be called explicitly: wp eval-file inc/cli-commands/create-brand-core-content.php
// simplifiedtheme_load_files('inc/cli-commands');
