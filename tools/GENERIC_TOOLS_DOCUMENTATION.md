# Generic WordPress Tools Documentation

## Overview

All WordPress management tools have been refactored to be **generic and reusable** across all sites. Instead of hardcoded site-specific tools, we now use unified tools that work with any site in `site_configs.json`.

## Core Generic Tools

### 1. `generic_update_page_content.py`

Updates page content for any WordPress site.

**Usage:**
```bash
# Update by page slug
python tools/generic_update_page_content.py \
  --site example.com \
  --page about \
  --content "<h1>About</h1><p>Content here</p>"

# Update by page ID
python tools/generic_update_page_content.py \
  --site example.com \
  --page-id 5 \
  --content "<h1>Updated</h1>"

# Update from file
python tools/generic_update_page_content.py \
  --site example.com \
  --page about \
  --content-file content.html
```

**Examples:**
```bash
# Update freerideinvestor.com about page
python tools/generic_update_page_content.py \
  --site freerideinvestor.com \
  --page about \
  --content "<h1>About FreeRide Investor</h1><p>Our mission...</p>"

# Update digitaldreamscape.site blog page
python tools/generic_update_page_content.py \
  --site digitaldreamscape.site \
  --page blog \
  --content-file blog_content.html
```

---

### 2. `generic_update_post_content.py`

Updates post content for any WordPress site.

**Usage:**
```bash
# Update by post slug
python tools/generic_update_post_content.py \
  --site example.com \
  --post-slug my-post \
  --content "<h1>Title</h1><p>Content</p>"

# Update by post ID
python tools/generic_update_post_content.py \
  --site example.com \
  --post-id 123 \
  --content-file content.html

# Append to existing content
python tools/generic_update_post_content.py \
  --site example.com \
  --post-slug my-post \
  --content "<p>Additional content</p>" \
  --append
```

**Examples:**
```bash
# Fix digitaldreamscape.site post vocal patterns
python tools/generic_update_post_content.py \
  --site digitaldreamscape.site \
  --post-slug digital-dreamscape-site-update \
  --content-file rewritten_content.html

# Update any blog post
python tools/generic_update_post_content.py \
  --site example.com \
  --post-slug my-blog-post \
  --content "<p>Updated content</p>"
```

---

### 3. `generic_deploy_template.py`

Deploys page templates and CSS to any WordPress site.

**Usage:**
```bash
# Deploy template only
python tools/generic_deploy_template.py \
  --site example.com \
  --template page-blog.php \
  --local-path themes/theme-name/page-templates/page-blog.php

# Deploy template with CSS
python tools/generic_deploy_template.py \
  --site example.com \
  --template page-blog.php \
  --local-path themes/theme-name/page-templates/page-blog.php \
  --css themes/theme-name/assets/css/blog.css

# Specify theme name
python tools/generic_deploy_template.py \
  --site example.com \
  --template page-blog.php \
  --local-path template.php \
  --css styles.css \
  --theme-name my-theme
```

**Examples:**
```bash
# Deploy beautiful blog template to digitaldreamscape.site
python tools/generic_deploy_template.py \
  --site digitaldreamscape.site \
  --template page-blog-beautiful.php \
  --local-path websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/page-templates/page-blog-beautiful.php \
  --css websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/assets/css/beautiful-blog.css

# Deploy streaming template
python tools/generic_deploy_template.py \
  --site digitaldreamscape.site \
  --template page-streaming-beautiful.php \
  --local-path websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/page-templates/page-streaming-beautiful.php \
  --css websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape/assets/css/beautiful-streaming.css
```

---

### 4. `generic_update_template_mapping.py`

Updates page template mappings in `functions.php` for any WordPress site.

**Usage:**
```bash
# Update template mapping
python tools/generic_update_template_mapping.py \
  --site example.com \
  --page blog \
  --template page-templates/page-blog-beautiful.php

# Update and clear cache
python tools/generic_update_template_mapping.py \
  --site example.com \
  --page streaming \
  --template page-templates/page-streaming-beautiful.php \
  --clear-cache
```

**Examples:**
```bash
# Map blog page to beautiful template
python tools/generic_update_template_mapping.py \
  --site digitaldreamscape.site \
  --page blog \
  --template page-templates/page-blog-beautiful.php \
  --clear-cache

# Map streaming page
python tools/generic_update_template_mapping.py \
  --site digitaldreamscape.site \
  --page streaming \
  --template page-templates/page-streaming-beautiful.php \
  --clear-cache
```

---

## Unified WordPress Manager

All generic tools use `unified_wordpress_manager.py` under the hood. You can also use it directly:

```bash
# List themes
python -m unified_wordpress_manager --site example.com --action list-themes

# Activate theme
python -m unified_wordpress_manager --site example.com --action activate-theme --theme my-theme

# Clear cache
python -m unified_wordpress_manager --site example.com --action clear-cache

# Check health
python -m unified_wordpress_manager --site example.com --action health
```

---

## Migration from Site-Specific Tools

### Before (Deprecated)
```bash
# Old way - site-specific
python tools/update_freerideinvestor_about_page.py
python tools/fix_digitaldreamscape_post_vocal_patterns.py
python tools/deploy_beautiful_blog_template.py
```

### After (Generic)
```bash
# New way - generic
python tools/generic_update_page_content.py --site freerideinvestor.com --page about --content "<h1>About</h1>"
python tools/generic_update_post_content.py --site digitaldreamscape.site --post-slug digital-dreamscape-site-update --content-file content.html
python tools/generic_deploy_template.py --site digitaldreamscape.site --template page-blog-beautiful.php --local-path template.php --css styles.css
```

---

## Available Sites

All tools work with any site in `configs/site_configs.json`. Common sites include:

- `digitaldreamscape.site`
- `freerideinvestor.com`
- `ariajet.site`
- `crosbyultimateevents.com`
- `southwestsecret.com`
- `tradingrobotplug.com`
- And many more...

To see all available sites:
```bash
python -c "import json; from pathlib import Path; configs = json.load(open(Path('configs/site_configs.json'))); print('\n'.join(configs.keys()))"
```

---

## Best Practices

1. **Always specify `--site`**: Never hardcode site names in scripts
2. **Use `--content-file` for large content**: Easier to manage than inline `--content`
3. **Clear cache after template changes**: Use `--clear-cache` flag
4. **Test on staging first**: When possible, test changes before production
5. **Use version control**: Commit content files separately for easier rollback

---

## Troubleshooting

### Site not found
```
❌ Site example.com not found in site_configs.json
```
**Solution**: Verify the site exists in `configs/site_configs.json` and the domain matches exactly.

### Template not found
```
❌ Template file not found: path/to/template.php
```
**Solution**: Verify the local path is correct and the file exists.

### Permission errors
```
❌ Failed to connect
```
**Solution**: Check SFTP credentials in `site_configs.json` and ensure the user has write permissions.

---

## Deprecated Tools

The following site-specific tools are deprecated. Use the generic versions instead:

- ❌ `update_freerideinvestor_about_page.py` → ✅ `generic_update_page_content.py`
- ❌ `fix_digitaldreamscape_post_vocal_patterns.py` → ✅ `generic_update_post_content.py`
- ❌ `deploy_beautiful_blog_template.py` → ✅ `generic_deploy_template.py`
- ❌ `deploy_beautiful_streaming_template.py` → ✅ `generic_deploy_template.py`
- ❌ `update_blog_template_mapping.py` → ✅ `generic_update_template_mapping.py`

Deprecated tools may still work but will show warnings. They will be removed in a future version.

---

## Support

For issues or questions:
1. Check this documentation
2. Review `unified_wordpress_manager.py` source code
3. Check `site_configs.json` for site configuration
4. Contact Agent-7 (Web Development Specialist)

