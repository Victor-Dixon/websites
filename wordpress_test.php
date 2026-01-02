<?php
// Simple WordPress test to check if WP can load at all
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
@ini_set('display_errors', 1);

// Try to load minimal WordPress
require_once('wp-load.php');

// If we get here, WordPress loaded
echo "WordPress loaded successfully!\n";
echo "WP Version: " . get_bloginfo('version') . "\n";
echo "Site URL: " . get_site_url() . "\n";
echo "Database: Connected\n";
?>