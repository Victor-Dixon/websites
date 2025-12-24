<?php
/**
 * AriaJet Site - WordPress/Static HTML Fallback
 * 
 * This file serves as a fallback if WordPress is not installed.
 * It will redirect to index.html for static content.
 */

// Check if WordPress is installed
if (file_exists(__DIR__ . '/wp-config.php')) {
    // WordPress is installed, let it handle the request
    require_once(__DIR__ . '/wp-load.php');
} else {
    // Serve static HTML
    if (file_exists(__DIR__ . '/index.html')) {
        readfile(__DIR__ . '/index.html');
        exit;
    } else {
        // Fallback error page
        header('HTTP/1.1 503 Service Unavailable');
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Service Unavailable</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Service Temporarily Unavailable</h1>
    <p>The site is currently being updated. Please check back soon.</p>
</body>
</html>';
        exit;
    }
}

