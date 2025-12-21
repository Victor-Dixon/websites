<?php
/**
 * Security Headers Implementation
 * ================================
 * 
 * Adds security headers to WordPress sites for improved security posture.
 * 
 * Author: Agent-7 (Web Development Specialist)
 * Date: 2025-11-29
 * 
 * Usage: Add to functions.php or include as separate file
 */

/**
 * Add security headers to WordPress
 */
function add_security_headers() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy (adjust as needed for your site)
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.youtube.com https://www.googletagmanager.com; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
           "font-src 'self' https://fonts.gstatic.com; " .
           "img-src 'self' data: https:; " .
           "connect-src 'self' https://www.youtube.com; " .
           "frame-src 'self' https://www.youtube.com;";
    
    header("Content-Security-Policy: $csp");
    
    // Permissions Policy (formerly Feature Policy)
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    
    // Strict Transport Security (only if using HTTPS)
    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}

// Hook into WordPress
add_action('send_headers', 'add_security_headers');

/**
 * Remove WordPress version from header (security through obscurity)
 */
remove_action('wp_head', 'wp_generator');

/**
 * Remove RSD link (XML-RPC)
 */
remove_action('wp_head', 'rsd_link');

/**
 * Remove wlwmanifest link (Windows Live Writer)
 */
remove_action('wp_head', 'wlwmanifest_link');

/**
 * Remove shortlink
 */
remove_action('wp_head', 'wp_shortlink_wp_head');

