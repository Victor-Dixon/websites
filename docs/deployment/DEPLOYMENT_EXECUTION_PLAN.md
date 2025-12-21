# Website deployment execution plan

**Date**: 2025-11-30  
**Status**: Ready to deploy

> Note: Some older documentation/scripts reference Windows-only paths or site folders that may not exist in every repo snapshot. Treat any path/site mapping as configuration and update it to match your environment and current directory structure.

## Sites covered by this plan

### FreeRideInvestor
- Files:
  - `functions.php` → `/wp-content/themes/freerideinvestor/functions.php`
  - `css/styles/base/_typography.css` → `/wp-content/themes/freerideinvestor/css/styles/base/_typography.css`
  - `css/styles/base/_variables.css` → `/wp-content/themes/freerideinvestor/css/styles/base/_variables.css`
- Package: `FreeRideInvestor_fixes_20251130_022723.zip`

### prismblossom.online
- Files:
  - `wordpress-theme/prismblossom/functions.php` → `/wp-content/themes/prismblossom/functions.php`
  - `wordpress-theme/prismblossom/page-carmyn.php` → `/wp-content/themes/prismblossom/page-carmyn.php`
- Package: `prismblossom.online_fixes_20251130_022723.zip`

### southwestsecret.com
- Files:
  - `css/style.css` → `/wp-content/themes/southwestsecret/css/style.css`
  - `wordpress-theme/southwestsecret/functions.php` → `/wp-content/themes/southwestsecret/functions.php`
- Package: `southwestsecret.com_fixes_20251130_022723.zip`

### ariajet.site
- No deployment required in this plan (static site / no critical fixes included here).

## Deployment steps (per WordPress site)

1. Backup the current versions of the target files.
2. Deploy via either:
   - WordPress Theme File Editor, or
   - SFTP/hosting file manager into `wp-content/themes/<theme-name>/`
3. Clear WordPress/browser/CDN caches.
4. Verify:
   - Manual spot-check of the changed areas
   - `python tools/verify_website_fixes.py`

## Post-deployment monitoring

- Monitor the sites for 24–48 hours
- Document any follow-up issues and rollbacks (if needed)

