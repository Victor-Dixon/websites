# Website tools

Utilities used to package, deploy, verify, and maintain the websites in this repository.

## Available tools

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

