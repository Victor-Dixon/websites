# Header Issue Investigation Results

## Investigation Date
2025-12-23

## Findings

### 1. Block Theme Templates Check
**Result**: ❌ NOT a block theme
- No `theme.json` file found
- No `templates/` directory found  
- No `parts/` directory found
- Theme uses classic PHP templates
- No `Template:` declaration in style.css (no parent theme)

### 2. Active Plugins Check
**Result**: ⚠️ Cannot access plugins directory locally
- Plugins directory doesn't exist in local repository
- Need to check server-side for active plugins
- **Next Step**: Check WordPress admin or server filesystem

### 3. Template Hierarchy Check
**Result**: ✅ ROOT CAUSE IDENTIFIED

**Blog Page (`/blog/`)**:
- Body class: `wp-singular page-template-default page page-id-5`
- **Using**: WordPress default page template (NOT `page-blog.php`)
- **Reason**: Page in WordPress admin is set to "Default Template"
- **Template file**: Falls back to `index.php` or WordPress core default

**Homepage (`/`)**:
- Body class: `home blog wp-theme-digitaldreamscape`
- **Using**: `front-page.php` ✅ (correct - WordPress auto-detects this)

### 4. Header Source Analysis

**Homepage Header** (from theme's `header.php`):
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

**Blog Page Header** (from WordPress core or plugin):
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

## ROOT CAUSE IDENTIFIED

### The Problem

1. **Blog page is using "Default Template"** instead of `page-blog.php`
2. Since theme has no `page.php`, WordPress falls back to core default or `index.php`
3. The default template uses a different header structure (likely from WordPress core or a plugin)
4. This different header structure doesn't match the theme's `header.php`

### Why This Happens

WordPress template hierarchy for pages:
1. `page-{slug}.php` (e.g., `page-blog.php`) ✅ **Should be used**
2. `page-{id}.php` (e.g., `page-5.php`)
3. `page.php` ❌ **Doesn't exist in theme**
4. `singular.php`
5. `index.php` ⚠️ **Likely being used**

Since `page-blog.php` exists but isn't being used, the page in WordPress admin must be set to "Default Template" instead of "Blog Page Template".

## Solution

### Option 1: Assign Template in WordPress Admin (RECOMMENDED)
1. Go to WordPress Admin → Pages → Blog
2. In Page Attributes, select "Blog Page" template
3. Save page
4. This will make WordPress use `page-blog.php` which calls `get_header()` correctly

### Option 2: Force Template in functions.php
Add to `functions.php` template_include filter:
```php
$page_templates = array(
    'blog' => 'page-blog.php',  // Map blog page slug to template
);
```

### Option 3: Create page.php
Create a `page.php` file that uses the same header structure as `header.php`, ensuring consistency even if default template is used.

### Option 4: Continue CSS Workaround
Keep current high-specificity CSS approach for compatibility with both header structures.

## Current Status

✅ **CSS Fix Deployed**: High-specificity selectors ensure consistent styling  
✅ **Root Cause Found**: Blog page using default template instead of `page-blog.php`  
⚠️ **Solution Needed**: Assign correct template in WordPress admin or force via code

## Recommended Action

**Immediate**: Assign "Blog Page" template to the blog page in WordPress admin  
**Long-term**: Consider adding `page.php` with consistent header structure for all default page templates
