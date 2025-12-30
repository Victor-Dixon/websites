# Header Styling Issue - Root Cause Analysis

## Problem Summary
Homepage header shows white background while blog page shows purple gradient. Both should have consistent purple gradient styling.

## Findings

### HTML Structure Comparison

**Homepage (`/`):**
- Body class: `class="home blog wp-theme-digitaldreamscape"`
- Header: `<header id="site-header" class="site-header">`
- Container: `<div class="header-container">`
- Content wrapper: `<div class="header-content">`
- Logo: Uses `.site-logo` and `.logo-link`
- Navigation: Uses `.main-navigation`
- **Template**: Uses `header.php` from theme

**Blog Page (`/blog/`):**
- Body class: `class="wp-singular page-template-default page page-id-5 wp-theme-digitaldreamscape"`
- Header: `<header class="site-header" role="banner">` (NO `id="site-header"`)
- Container: `<div class="container">` (DIFFERENT!)
- Content wrapper: `<div class="header-inner">` (DIFFERENT!)
- Logo: Uses `.brand` and `.brand-text` (DIFFERENT!)
- Navigation: Uses `.nav` class (DIFFERENT!)
- **Template**: NOT using theme's `header.php` - likely WordPress block theme or plugin override

## Root Cause
The blog page is using a **completely different header structure** that doesn't come from the theme's `header.php` file. This suggests:

1. **WordPress Block Theme Header**: WordPress might be using a block theme header template part
2. **Plugin Override**: A plugin might be replacing the header on certain pages
3. **Template Hierarchy**: WordPress might be using a different template file that includes a different header

## Next Steps to Investigate

1. Check if there are block theme template parts:
   - `wp-content/themes/digitaldreamscape/templates/parts/header.html`
   - `wp-content/themes/digitaldreamscape/parts/header.html`

2. Check for plugin header overrides:
   - Search for plugins that modify headers
   - Check `wp-content/plugins/` for header-related code

3. Check WordPress template hierarchy:
   - Verify which template file WordPress is using for the blog page
   - Check if `page-blog.php` or `archive.php` is calling a different header

4. Verify theme setup:
   - Check if theme is registered as block theme vs classic theme
   - Check `theme.json` or `functions.php` for theme support declarations

## Current Fix
Using high-specificity CSS selectors with `!important` to force styling on both header structures. This is a workaround, not a true fix.

## Ideal Solution
Identify and standardize on ONE header template structure across all pages, then update CSS accordingly.

