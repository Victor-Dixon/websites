<?php
/**
 * Digital Dreamscape Theme Functions
 *
 * Modular theme architecture for maintainable development
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ============================================
// MODULAR ARCHITECTURE INCLUDES
// ============================================

// For CLI scripts, skip theme files that require full WordPress
if (php_sapi_name() !== 'cli') {
    // Theme setup and basic functionality
    require_once get_template_directory() . '/inc/setup.php';

    // Asset enqueuing and performance
    require_once get_template_directory() . '/inc/enqueue.php';

    // Template helper functions
    require_once get_template_directory() . '/inc/template-tags.php';

    // Content filters and modifications
    require_once get_template_directory() . '/inc/filters.php';

    // Security hardening
    require_once get_template_directory() . '/inc/security.php';

    // Custom meta fields and admin integration
    require_once get_template_directory() . '/inc/meta.php';

    // REST API endpoints
    require_once get_template_directory() . '/inc/api.php';
}

// ============================================
// THEME INITIALIZATION COMPLETE
// ============================================

/**
 * Theme modularized for maintainability:
 *
 * - inc/setup.php - Theme setup and WordPress integration
 * - inc/enqueue.php - Asset management and performance
 * - inc/template-tags.php - Template helper functions
 * - inc/filters.php - Content processing and SEO
 * - inc/security.php - Security hardening
 * - inc/meta.php - Custom fields and admin interface
 * - inc/api.php - REST API endpoints for automation
 *
 * This architecture prevents the "fatal error from missing function" issues
 * mentioned in the Digital Dreamscape site update post.
 */