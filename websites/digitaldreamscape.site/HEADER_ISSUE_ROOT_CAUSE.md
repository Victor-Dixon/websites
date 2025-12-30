# Header Styling Issue - Root Cause Analysis

## Problem
Homepage header shows white background, while blog page shows purple gradient. Both should have consistent purple gradient styling.

## Root Cause Identified

### Two Different Header Structures

**Homepage (`/`):**
```html
<header id="site-header" class="site-header">
    <div class="header-container">
        <div class="header-content">
            <div class="site-logo">...</div>
            <nav class="main-navigation">...</nav>
        </div>
    </div>
</header>
```
- **Source**: Theme's `header.php` file
- **Body class**: `home blog wp-theme-digitaldreamscape`

**Blog Page (`/blog/`):**
```html
<header class="site-header" role="banner">
    <div class="container">
        <div class="header-inner">
            <a class="brand">...</a>
            <nav class="nav">...</nav>
        </div>
    </div>
</header>
```
- **Source**: Unknown - NOT from theme's `header.php`
- **Body class**: `wp-singular page-template-default page page-id-5 wp-theme-digitaldreamscape`

### Key Differences

| Element | Homepage (header.php) | Blog Page (Unknown Source) |
|---------|----------------------|---------------------------|
| Header ID | `id="site-header"` | Missing |
| Container class | `.header-container` | `.container` |
| Content wrapper | `.header-content` | `.header-inner` |
| Logo container | `.site-logo` | `.brand` |
| Logo link | `.logo-link` | `.brand-text` |
| Navigation | `.main-navigation` | `.nav` |

## Why This Happens

The blog page is using a **completely different header structure** that doesn't come from the theme's `header.php`. Possible causes:

1. **WordPress Block Theme Header**: WordPress core might be using a block theme header template
2. **Plugin Override**: A plugin might be replacing headers on page templates
3. **Template Hierarchy**: WordPress might be using a different template that includes a different header
4. **WordPress Core Change**: WordPress 6.9 might have changed how headers are rendered for pages vs posts

## Current Fix (Workaround)

Using high-specificity CSS selectors with `!important` to force styling on both header structures:
- `body.home .site-header`
- `body.front-page .site-header`
- Multiple header container selectors (`.header-container`, `.header-inner`, `.site-header .container`)
- Multiple navigation selectors (`.main-navigation`, `.nav`)

## Recommended Investigation Steps

1. **Check for Block Theme Template Parts**
   ```bash
   find wp-content/themes/digitaldreamscape -name "*header*" -type f
   ```

2. **Check Active Plugins**
   - Look for plugins that modify headers
   - Check if any plugin hooks into `get_header` or `wp_head`

3. **Check WordPress Template Hierarchy**
   - Verify which template WordPress uses for `/blog/` page
   - Check if `page-blog.php` or a parent template calls a different header

4. **Check Theme Support**
   ```php
   // In functions.php, check:
   add_theme_support('block-templates'); // If true, might explain block theme headers
   ```

5. **Check for Header Filters**
   ```php
   // Search for:
   add_filter('get_header', ...);
   add_action('get_header', ...);
   ```

## Ideal Solution

Once the source of the different header is identified:
1. Standardize on ONE header structure across all pages
2. Update CSS to match the standardized structure
3. Remove workaround `!important` flags and high-specificity selectors
4. Ensure all pages use the same `header.php` template

## Files Modified for Current Fix

- `style.css`: Added high-specificity selectors and `!important` flags
- `functions.php`: Added menu cleanup filters for both `.main-navigation` and `.nav`
- `js/main.js`: Added cleanup for both navigation class types

