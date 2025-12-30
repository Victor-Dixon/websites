# Refactor Tools to Use Unified WordPress Manager

## Problem
Many tools are hardcoded to specific sites (digitaldreamscape.site, freerideinvestor.com) instead of being generic and reusable across all sites.

## Solution
Refactor all site-specific tools to use `unified_wordpress_manager.py` and accept site domain as a parameter.

## Tools to Refactor

### High Priority (Most Used)
1. `update_freerideinvestor_about_page.py` → Use `generic_update_page_content.py`
2. `fix_digitaldreamscape_post_vocal_patterns.py` → Use `generic_update_post_content.py`
3. `deploy_beautiful_blog_template.py` → Use `generic_deploy_template.py`
4. `deploy_beautiful_streaming_template.py` → Use `generic_deploy_template.py`
5. `update_blog_template_mapping.py` → Make generic, accept --site parameter

### Medium Priority
6. `deploy_freerideinvestor_functions_complete.py` → Generic functions.php updater
7. `deploy_freerideinvestor_menu_fix_safe.py` → Generic menu fix deployer
8. `create_freerideinvestor_functions.php` → Generic functions.php creator
9. `clear_digitaldreamscape_cache.py` → Use unified_wordpress_manager.clear_cache()

### Low Priority (Site-Specific Logic)
10. Tools with site-specific business logic can remain but should accept --site parameter

## New Generic Tools Created

1. **`generic_update_page_content.py`**
   - Updates page content for any site
   - Accepts: --site, --page, --page-id, --content, --content-file

2. **`generic_deploy_template.py`**
   - Deploys page templates and CSS to any site
   - Accepts: --site, --template, --local-path, --css, --theme-name

3. **`generic_update_post_content.py`**
   - Updates post content for any site
   - Accepts: --site, --post-slug, --post-id, --content, --append

## Migration Guide

### Before (Site-Specific)
```python
SITE_NAME = "freerideinvestor.com"
deployer = SimpleWordPressDeployer(SITE_NAME, load_site_configs())
```

### After (Generic)
```python
parser.add_argument('--site', required=True)
manager = UnifiedWordPressManager(args.site, site_configs.get(args.site))
```

## Benefits

1. **Reusability**: One tool works for all sites
2. **Maintainability**: Changes in one place affect all sites
3. **Consistency**: Same interface across all tools
4. **Scalability**: Easy to add new sites without creating new tools

## Next Steps

1. Update existing tools to use generic versions
2. Add site parameter to all hardcoded tools
3. Update documentation with generic tool usage
4. Deprecate site-specific tools in favor of generic ones

