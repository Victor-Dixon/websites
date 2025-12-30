# Website Tools

Utilities used to package, deploy, verify, and maintain the websites in this repository.

---

## 🚀 Quick Reference: Most Common Tools

| Task | Tool | Example |
|------|------|---------|
| **Manage blog posts** | `blog_manager.py` | `python tools/blog_manager.py list ariajet.site` |
| **Update page content** | `generic_update_page_content.py` | `python tools/generic_update_page_content.py --site example.com --page about --content "<h1>About</h1>"` |
| **Deploy templates** | `generic_deploy_template.py` | `python tools/generic_deploy_template.py --site example.com --template page-blog.php` |

**Full documentation:** See `GENERIC_TOOLS_DOCUMENTATION.md`

---

## ⭐ Blog Manager (blog_manager.py)

**The primary tool for managing WordPress blog posts across all sites.**

```bash
# List all posts on a site
python tools/blog_manager.py list ariajet.site

# Create a new blog post
python tools/blog_manager.py create ariajet.site --title "My Post" --content "<p>Content</p>" --status publish

# Edit a post
python tools/blog_manager.py edit ariajet.site --id 5 --title "Updated Title"

# Delete a post (e.g., remove default "Hello World")
python tools/blog_manager.py delete ariajet.site --id 1

# View post details
python tools/blog_manager.py get ariajet.site --id 5

# List all configured sites
python tools/blog_manager.py sites
```

**Configuration:** `configs/site_configs.json` (contains REST API and SFTP credentials for all sites)

---

## Available Tools

### 1) `verify_website_fixes.py`

Verifies that specific fixes are visible on live sites (basic HTTP checks).

Usage:

```bash
python tools/verify_website_fixes.py
```

### 2) `add_security_headers.php`

Adds common security headers to WordPress responses.

Usage (in your theme’s `functions.php`):

```php
require_once get_template_directory() . '/tools/add_security_headers.php';
```

### 3) `wordpress_version_checker.py`

Checks WordPress core/plugin versions for update awareness.

Usage:

```bash
python tools/wordpress_version_checker.py
```

## Notes

- These scripts are intended to be **non-destructive**, but you should still back up before deploying changes.
- Prefer testing in a staging environment when available.

