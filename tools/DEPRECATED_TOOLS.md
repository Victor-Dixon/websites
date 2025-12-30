# Deprecated WordPress Tools

## Overview

**UPDATE (2025-12-24): All deprecated site-specific tools have been removed.**

This document lists the removed site-specific WordPress tools and their generic replacements.

**All site-specific tools have been deleted.** Use the generic tools instead, which work with all sites in `site_configs.json`.

---

## Removed Tools List (Deleted 2025-12-24)

### Content Management

| Deprecated Tool | Generic Replacement | Migration Guide |
|----------------|---------------------|-----------------|
| `update_freerideinvestor_about_page.py` | `generic_update_page_content.py` | See below |
| `fix_digitaldreamscape_post_vocal_patterns.py` | `generic_update_post_content.py` | See below |

### Template Deployment

| Deprecated Tool | Generic Replacement | Migration Guide |
|----------------|---------------------|-----------------|
| `deploy_beautiful_blog_template.py` | `generic_deploy_template.py` | See below |
| `deploy_beautiful_streaming_template.py` | `generic_deploy_template.py` | See below |
| `update_blog_template_mapping.py` | `generic_update_template_mapping.py` | See below |

### Cache Management

| Deprecated Tool | Generic Replacement | Migration Guide |
|----------------|---------------------|-----------------|
| `clear_digitaldreamscape_cache.py` | `unified_wordpress_manager.py --action clear-cache` | See below |

---

## Migration Examples

### 1. Update Page Content

**Before:**
```bash
python tools/update_freerideinvestor_about_page.py
```

**After:**
```bash
python tools/generic_update_page_content.py \
  --site freerideinvestor.com \
  --page about \
  --content "<h1>About</h1><p>Content here</p>"
```

---

### 2. Update Post Content

**Before:**
```bash
python tools/fix_digitaldreamscape_post_vocal_patterns.py
```

**After:**
```bash
python tools/generic_update_post_content.py \
  --site digitaldreamscape.site \
  --post-slug digital-dreamscape-site-update \
  --content-file rewritten_content.html
```

---

### 3. Deploy Template

**Before:**
```bash
python tools/deploy_beautiful_blog_template.py
```

**After:**
```bash
python tools/generic_deploy_template.py \
  --site digitaldreamscape.site \
  --template page-blog-beautiful.php \
  --local-path websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/page-templates/page-blog-beautiful.php \
  --css websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/assets/css/beautiful-blog.css
```

---

### 4. Update Template Mapping

**Before:**
```bash
python tools/update_blog_template_mapping.py
```

**After:**
```bash
python tools/generic_update_template_mapping.py \
  --site digitaldreamscape.site \
  --page blog \
  --template page-templates/page-blog-beautiful.php \
  --clear-cache
```

---

### 5. Clear Cache

**Before:**
```bash
python tools/clear_digitaldreamscape_cache.py
```

**After:**
```bash
python -m unified_wordpress_manager \
  --site digitaldreamscape.site \
  --action clear-cache
```

---

## Removal Timeline

- **2025-12-24**: Generic tools created and documented
- **2025-12-24**: Deprecation notices added to old tools
- **2025-12-24**: Old tools tested and removed (completed)

---

## Why Deprecate?

1. **Maintainability**: One generic tool is easier to maintain than many site-specific ones
2. **Reusability**: Generic tools work with all sites, not just one
3. **Consistency**: Same interface across all tools
4. **Scalability**: Easy to add new sites without creating new tools

---

## Removal Status

**All deprecated tools have been removed.** They are no longer available.

**Action Required**: Use the generic tools listed above. See `GENERIC_TOOLS_DOCUMENTATION.md` for usage examples.

---

## Questions?

See `GENERIC_TOOLS_DOCUMENTATION.md` for comprehensive usage examples and troubleshooting.

