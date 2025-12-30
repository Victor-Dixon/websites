# Modular Structure Pattern for WordPress Themes

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-27  
**Status:** ACTIVE - Standard Pattern Documentation  
**Purpose:** Standardize modular `inc/` structure pattern for WordPress themes

<!-- SSOT Domain: documentation -->

---

## Executive Summary

This document defines the standard modular structure pattern for WordPress themes, using the `inc/` directory to organize theme functionality into logical, maintainable modules. This pattern improves code organization, maintainability, and follows V2 compliance principles.

**Pattern:** Modular `inc/` structure  
**Standard Files:** `inc/setup.php`, `inc/enqueue.php`, `inc/template-tags.php`, `inc/filters.php`, `inc/security.php`  
**Loading Order:** Defined in `functions.php`  
**V2 Compliance:** ✅ Compliant (modular design, clear separation of concerns)

---

## Standard Module Structure

### Core Modules (Required)

#### 1. `inc/setup.php` - Theme Setup & Configuration
**Purpose:** Theme setup, navigation menus, widget areas, theme support features

**Typical Contents:**
- `after_setup_theme` hook actions
- Theme support declarations (`post-thumbnails`, `title-tag`, `html5`, etc.)
- Navigation menu registration
- Widget area registration
- Image size definitions
- Custom logo support

**Example Structure:**
```php
<?php
/**
 * Theme Setup
 * 
 * Handles theme initialization, menus, widgets, and theme support features.
 *
 * <!-- SSOT Domain: web -->
 */

// Theme setup
function theme_setup() {
    // Theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Navigation menus
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu',
    ));
    
    // Widget areas
    register_sidebar(array(
        'name' => 'Sidebar',
        'id' => 'sidebar-1',
        // ... widget configuration
    ));
}
add_action('after_setup_theme', 'theme_setup');
```

**Line Count Target:** 50-100 lines (V2 compliant)

---

#### 2. `inc/enqueue.php` - Scripts & Styles
**Purpose:** Enqueue stylesheets, JavaScript files, and handle script dependencies

**Typical Contents:**
- Stylesheet enqueuing (`wp_enqueue_style`)
- JavaScript enqueuing (`wp_enqueue_script`)
- Conditional loading (page-specific styles/scripts)
- Script dependencies
- Version management
- Inline styles/scripts (if needed)

**Example Structure:**
```php
<?php
/**
 * Enqueue Scripts and Styles
 * 
 * Handles all stylesheet and JavaScript file enqueuing.
 *
 * <!-- SSOT Domain: web -->
 */

function theme_enqueue_scripts() {
    // Main stylesheet
    wp_enqueue_style('theme-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Main JavaScript
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    
    // Conditional page-specific styles
    if (is_front_page()) {
        wp_enqueue_style('home-style', get_template_directory_uri() . '/assets/css/home.css', array('theme-style'), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
```

**Line Count Target:** 100-200 lines (V2 compliant)

---

#### 3. `inc/template-tags.php` - Template Helper Functions
**Purpose:** Custom template tags and helper functions used in templates

**Typical Contents:**
- Custom post meta functions
- Template helper functions
- Display functions (dates, authors, categories)
- Utility functions for templates
- Custom loop functions

**Example Structure:**
```php
<?php
/**
 * Template Tags
 * 
 * Custom template tags and helper functions for theme templates.
 *
 * <!-- SSOT Domain: web -->
 */

/**
 * Display post date with custom format
 */
function theme_post_date() {
    echo '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>';
}

/**
 * Display post author with link
 */
function theme_post_author() {
    echo '<span class="author">By <a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>';
}
```

**Line Count Target:** 50-150 lines (V2 compliant)

---

#### 4. `inc/filters.php` - WordPress Filters & Hooks
**Purpose:** WordPress filter modifications, content filters, query modifications

**Typical Contents:**
- `the_content` filters
- `excerpt_length` and `excerpt_more` filters
- Query modifications
- Menu filters
- Custom filter hooks
- Content manipulation

**Example Structure:**
```php
<?php
/**
 * WordPress Filters
 * 
 * Custom filter modifications and content manipulation.
 *
 * <!-- SSOT Domain: web -->
 */

/**
 * Custom excerpt length
 */
function theme_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'theme_excerpt_length');

/**
 * Custom excerpt more text
 */
function theme_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'theme_excerpt_more');
```

**Line Count Target:** 50-150 lines (V2 compliant)

---

#### 5. `inc/security.php` - Security & Sanitization
**Purpose:** Security functions, input sanitization, output escaping

**Typical Contents:
- Input sanitization functions
- Output escaping helpers
- Security headers
- Nonce verification helpers
- XSS prevention functions
- CSRF protection

**Example Structure:**
```php
<?php
/**
 * Security Functions
 * 
 * Security utilities, sanitization, and output escaping.
 *
 * <!-- SSOT Domain: web -->
 */

/**
 * Sanitize text input
 */
function theme_sanitize_text($input) {
    return sanitize_text_field($input);
}

/**
 * Escape output for display
 */
function theme_escape_output($output) {
    return esc_html($output);
}
```

**Line Count Target:** 50-100 lines (V2 compliant)

---

### Site-Specific Modules (Optional)

Additional modules can be added based on theme-specific needs:

- `inc/customizer.php` - Theme customizer options
- `inc/post-types.php` - Custom post types
- `inc/taxonomies.php` - Custom taxonomies
- `inc/metaboxes.php` - Custom meta boxes
- `inc/ajax.php` - AJAX handlers
- `inc/api.php` - REST API endpoints
- `inc/admin.php` - Admin area customizations
- `inc/guestbook.php` - Site-specific functionality (e.g., guestbook)
- `inc/analytics.php` - Analytics integration
- `inc/woocommerce.php` - WooCommerce integration (if applicable)

**Naming Convention:** Use descriptive, lowercase names with underscores: `inc/{feature}.php`

---

## Loading Order in `functions.php`

### Standard Loading Pattern

```php
<?php
/**
 * Theme Functions
 * 
 * Main functions.php file that loads modular theme components.
 *
 * <!-- SSOT Domain: web -->
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('THEME_VERSION', '1.0.0');
define('THEME_DIR', get_template_directory());
define('THEME_URI', get_template_directory_uri());

// Load core modules in order
$inc_files = array(
    'inc/setup.php',           // Theme setup (must be first)
    'inc/enqueue.php',         // Scripts and styles
    'inc/template-tags.php',   // Template helper functions
    'inc/filters.php',         // WordPress filters
    'inc/security.php',        // Security functions
);

// Load site-specific modules (if they exist)
$site_specific_modules = array(
    'inc/customizer.php',      // Theme customizer
    'inc/post-types.php',      // Custom post types
    'inc/api.php',             // REST API endpoints
    // Add other site-specific modules as needed
);

// Load core modules
foreach ($inc_files as $file) {
    $file_path = THEME_DIR . '/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

// Load site-specific modules
foreach ($site_specific_modules as $file) {
    $file_path = THEME_DIR . '/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}
```

### Loading Order Principles

1. **`inc/setup.php`** - Must be loaded first (theme initialization)
2. **`inc/enqueue.php`** - Load early (scripts/styles registration)
3. **`inc/template-tags.php`** - Load before templates use them
4. **`inc/filters.php`** - Load before content is processed
5. **`inc/security.php`** - Load early (security utilities)
6. **Site-specific modules** - Load after core modules

---

## Best Practices

### 1. File Organization

- **Keep modules focused:** Each module should have a single, clear purpose
- **Avoid circular dependencies:** Modules should not depend on each other
- **Use descriptive names:** Module names should clearly indicate their purpose
- **Maintain V2 compliance:** Keep files under 300 lines, functions under 30 lines

### 2. Code Organization

- **Group related functions:** Keep related functions together within a module
- **Use clear function names:** Prefix functions with theme name to avoid conflicts
- **Add documentation:** Include PHPDoc comments for all functions
- **Follow WordPress coding standards:** Use WordPress PHP coding standards

### 3. Performance

- **Conditional loading:** Only load modules when needed (use `file_exists()` checks)
- **Minimize includes:** Don't load unnecessary modules
- **Cache considerations:** Be aware of caching implications for modular structure

### 4. Maintenance

- **Version control:** Track module changes in version control
- **Documentation:** Keep module documentation up to date
- **Testing:** Test all modules after refactoring
- **Backup:** Always backup before modularizing existing themes

---

## Migration Guide

### From Monolithic `functions.php` to Modular Structure

1. **Analyze existing code:**
   - Review current `functions.php`
   - Identify function groups (setup, enqueue, template tags, filters, security)
   - Count lines and identify V2 violations

2. **Create module structure:**
   - Create `inc/` directory
   - Create standard module files
   - Move functions to appropriate modules

3. **Update `functions.php`:**
   - Replace function definitions with module includes
   - Add loading order logic
   - Test functionality

4. **Verify and test:**
   - Test all theme functionality
   - Check for broken references
   - Verify V2 compliance
   - Test on staging before production

### Example Migration

**Before (Monolithic):**
```php
// functions.php (505 lines - V2 violation)
function theme_setup() { /* ... */ }
function theme_enqueue_scripts() { /* ... */ }
function theme_post_date() { /* ... */ }
// ... 20+ more functions
```

**After (Modular):**
```php
// functions.php (30 lines - V2 compliant)
require_once 'inc/setup.php';
require_once 'inc/enqueue.php';
require_once 'inc/template-tags.php';
// ... module includes

// inc/setup.php (68 lines - V2 compliant)
function theme_setup() { /* ... */ }

// inc/enqueue.php (145 lines - V2 compliant)
function theme_enqueue_scripts() { /* ... */ }

// inc/template-tags.php (86 lines - V2 compliant)
function theme_post_date() { /* ... */ }
```

---

## V2 Compliance Checklist

- [ ] Each module file under 300 lines
- [ ] Each function under 30 lines
- [ ] Clear separation of concerns
- [ ] No circular dependencies
- [ ] Proper documentation (PHPDoc)
- [ ] WordPress coding standards followed
- [ ] SSOT domain tags included
- [ ] Loading order defined and documented

---

## Examples

### Successful Implementations

1. **digitaldreamscape.site:**
   - ✅ Modularized from 505 lines to 6 modules
   - ✅ All modules V2 compliant
   - ✅ 100% functionality maintained
   - ✅ Reference: `websites/digitaldreamscape.site/MODULARIZATION_COMPLETE_2025-12-25.md`

2. **tradingrobotplug.com:**
   - ✅ Modular structure with 6 core modules
   - ✅ REST API module (`inc/api.php`)
   - ✅ V2 compliant architecture
   - ✅ Reference: `websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/MODULAR_FUNCTIONS_DOCUMENTATION.md`

---

## References

- **WordPress Theme Development:** [WordPress Theme Handbook](https://developer.wordpress.org/themes/)
- **V2 Compliance Guidelines:** `docs/V2_COMPLIANCE_GUIDELINES_UPDATE.md`
- **SSOT Domain Mapping:** `docs/SSOT_DOMAIN_MAPPING.md`
- **Example Implementation:** `websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/functions.php`

---

**Last Updated:** 2025-12-27 by Agent-2  
**Status:** ✅ ACTIVE - Standard Pattern Documentation  
**Next Review:** After additional theme modularizations

