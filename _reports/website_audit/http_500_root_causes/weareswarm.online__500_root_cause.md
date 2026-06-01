# HTTP 500 Root Cause: weareswarm.online

## Classification

- Root cause: `wordpress_install_returning_500`
- Recommendation: `run_wp_cli_health_check_or_restore_wordpress`
- Priority: `classify_purpose_before_repair`
- Intended purpose guess: `dreamos_swarm_brand_candidate`

## Decision

- Safe next step: confirm intended purpose before changing files.

## Remote Evidence

```text
DOMAIN=weareswarm.online
REMOTE_ROOT=/home/u996867598/domains/weareswarm.online/public_html
ROOT_EXISTS=YES
== ROOT_STAT ==
/home/u996867598/domains/weareswarm.online/public_html
total 588
drwxr-xr-x  6 u996867598 o1008028115   4096 May 22 00:10 .
drwxr-xr-x  3 u996867598 o1008028115   4096 Nov 23  2025 ..
-rw-r--r--  1 u996867598 o1008028115  16369 Nov 23  2025 default.php
-rw-r--r--  1 u996867598 o1008028115   1620 Nov 23  2025 .htaccess
-rw-r--r--  1 u996867598 o1008028115    714 Nov 23  2025 .htaccess.bk
-rw-r--r--  1 u996867598 o1008028115    405 Nov 23  2025 index.php
-rw-r--r--  1 u996867598 o1008028115  19903 Nov 23  2025 license.txt
-rw-r--r--  1 u996867598 o1008028115 202815 May 27 12:24 llms.txt
drwxr-xr-x  2 u996867598 o1008028115   4096 Nov 23  2025 .private
-rw-r--r--  1 u996867598 o1008028115   7425 Mar 12 23:55 readme.html
-rw-r--r--  1 u996867598 o1008028115   1088 Dec 22 19:06 SITE_INFO.md
-rw-r--r--  1 u996867598 o1008028115   7781 Dec 30 13:25 swarm-build-feed.php
-rw-r--r--  1 u996867598 o1008028115   1950 Dec 22 19:06 WHAT_IS_WEARESWARM_SITE.md
-rw-r--r--  1 u996867598 o1008028115   7387 Nov 23  2025 wp-activate.php
drwxr-xr-x  9 u996867598 o1008028115   4096 Nov 23  2025 wp-admin
-rw-r--r--  1 u996867598 o1008028115    351 Nov 23  2025 wp-blog-header.php
-rw-r--r--  1 u996867598 o1008028115   2323 Nov 23  2025 wp-comments-post.php
-rw-r--r--  1 u996867598 o1008028115   3666 May 28 11:08 wp-config.php
-rw-r--r--  1 u996867598 o1008028115   3336 Nov 23  2025 wp-config-sample.php
drwxr-xr-x  9 u996867598 o1008028115   4096 Dec 18 23:36 wp-content
-rw-r--r--  1 u996867598 o1008028115   5617 Nov 23  2025 wp-cron.php
drwxr-xr-x 30 u996867598 o1008028115  16384 Nov 23  2025 wp-includes
-rw-r--r--  1 u996867598 o1008028115   2502 Nov 23  2025 wp-links-opml.php
-rw-r--r--  1 u996867598 o1008028115   3937 Nov 23  2025 wp-load.php
-rw-r--r--  1 u996867598 o1008028115  51414 Nov 23  2025 wp-login.php
-rw-r--r--  1 u996867598 o1008028115   8727 Nov 23  2025 wp-mail.php
-rw-r--r--  1 u996867598 o1008028115  30081 Nov 23  2025 wp-settings.php
-rw-r--r--  1 u996867598 o1008028115  34516 Nov 23  2025 wp-signup.php
-rw-r--r--  1 u996867598 o1008028115   5102 Nov 23  2025 wp-trackback.php
-rw-r--r--  1 u996867598 o1008028115   3205 Nov 23  2025 xmlrpc.php
== COUNTS ==
FILE_COUNT=16362
DIR_COUNT=2282
== PERMISSIONS ==
ROOT_MODE=755 ROOT_OWNER=u996867598 ROOT_GROUP=o1008028115
drwxr-xr-x u996867598 o1008028115 .
drwxr-xr-x u996867598 o1008028115 ./.private
drwxr-xr-x u996867598 o1008028115 ./wp-admin
drwxr-xr-x u996867598 o1008028115 ./wp-content
drwxr-xr-x u996867598 o1008028115 ./wp-includes
-rw-r--r-- u996867598 o1008028115 ./default.php
-rw-r--r-- u996867598 o1008028115 ./.htaccess
-rw-r--r-- u996867598 o1008028115 ./.htaccess.bk
-rw-r--r-- u996867598 o1008028115 ./index.php
-rw-r--r-- u996867598 o1008028115 ./license.txt
-rw-r--r-- u996867598 o1008028115 ./llms.txt
-rw-r--r-- u996867598 o1008028115 ./readme.html
-rw-r--r-- u996867598 o1008028115 ./SITE_INFO.md
-rw-r--r-- u996867598 o1008028115 ./swarm-build-feed.php
-rw-r--r-- u996867598 o1008028115 ./WHAT_IS_WEARESWARM_SITE.md
-rw-r--r-- u996867598 o1008028115 ./wp-activate.php
-rw-r--r-- u996867598 o1008028115 ./wp-blog-header.php
-rw-r--r-- u996867598 o1008028115 ./wp-comments-post.php
-rw-r--r-- u996867598 o1008028115 ./wp-config.php
-rw-r--r-- u996867598 o1008028115 ./wp-config-sample.php
-rw-r--r-- u996867598 o1008028115 ./wp-cron.php
-rw-r--r-- u996867598 o1008028115 ./wp-links-opml.php
-rw-r--r-- u996867598 o1008028115 ./wp-load.php
-rw-r--r-- u996867598 o1008028115 ./wp-login.php
-rw-r--r-- u996867598 o1008028115 ./wp-mail.php
-rw-r--r-- u996867598 o1008028115 ./wp-settings.php
-rw-r--r-- u996867598 o1008028115 ./wp-signup.php
-rw-r--r-- u996867598 o1008028115 ./wp-trackback.php
-rw-r--r-- u996867598 o1008028115 ./xmlrpc.php
== INDEX_FILES ==
INDEX_PRESENT=index.php
INDEX_MODE=644
INDEX_HEAD_BEGIN=index.php
<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
require __DIR__ . '/wp-blog-header.php';
INDEX_HEAD_END=index.php
INDEX_PRESENT=default.php
INDEX_MODE=644
INDEX_HEAD_BEGIN=default.php
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Default page</title>
        <link rel="icon" type="image/x-icon" href="https://hpanel.hostinger.com/favicons/hostinger.png">
        <meta charset="utf-8">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta content="Default page" name="description">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">
        <style>
            body {
                margin: 0px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 100vw;
                height: 100vh;
                min-height: 675px;
                background-color: #F4F5FF;
            }
            p {
                width: 100%;
                left: 0px;
                font-size: 16px;
                font-family: 'DM Sans', sans-serif;
                font-weight: 400;
                letter-spacing: 0px;
                text-align: center;
                vertical-align: top;
                max-width: 550px;
                color: #727586;
                margin: 0px;
            }
            a:hover {
                cursor: pointer;
                color: #673DE6;
INDEX_HEAD_END=default.php
== HTACCESS ==
HTACCESS_PRESENT=YES
HTACCESS_MODE=644
HTACCESS_BEGIN
# BEGIN LSCACHE
## LITESPEED WP CACHE PLUGIN - Do not edit the contents of this block! ##
<IfModule LiteSpeed>
RewriteEngine on
CacheLookup on
RewriteRule .* - [E=Cache-Control:no-autoflush]
RewriteRule litespeed/debug/.*\.log$ - [F,L]
RewriteRule \.litespeed_conf\.dat - [F,L]

### marker ASYNC start ###
RewriteCond %{REQUEST_URI} /wp-admin/admin-ajax\.php
RewriteCond %{QUERY_STRING} action=async_litespeed
RewriteRule .* - [E=noabort:1]
### marker ASYNC end ###

### marker DROPQS start ###
CacheKeyModify -qs:fbclid
CacheKeyModify -qs:gclid
CacheKeyModify -qs:utm*
CacheKeyModify -qs:_ga
### marker DROPQS end ###

</IfModule>
## LITESPEED WP CACHE PLUGIN - Do not edit the contents of this block! ##
# END LSCACHE
# BEGIN NON_LSCACHE
## LITESPEED WP CACHE PLUGIN - Do not edit the contents of this block! ##
## LITESPEED WP CACHE PLUGIN - Do not edit the contents of this block! ##
# END NON_LSCACHE
# BEGIN WordPress
<IfModule mod_expires.c>
	ExpiresActive On
	ExpiresByType image/jpg "access plus 1 year"
	ExpiresByType image/jpeg "access plus 1 year"
	ExpiresByType image/gif "access plus 1 year"
	ExpiresByType image/png "access plus 1 year"
	ExpiresByType text/css "access plus 1 month"
	ExpiresByType application/pdf "access plus 1 month"
	ExpiresByType text/javascript "access plus 1 month"
	ExpiresByType image/x-icon "access plus 1 year"
	ExpiresDefault "access plus 1 weeks"
</IfModule>
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>

# END WordPress
HTACCESS_END
== WORDPRESS_MARKERS ==
WP_CONFIG=YES
WP_CONTENT=YES
WP_ADMIN=YES
WP_LOAD=YES
WP_CONFIG_HEAD_BEGIN
 * * ABSPATH
define( 'DB_NAME', 'u996867598_48shp' );
define( 'DB_USER', 'u996867598_mss3K' );
$table_prefix = 'wp_';
if ( ! defined('WP_DEBUG') ) define('WP_DEBUG', false);
if ( ! defined('WP_DEBUG_LOG') ) define('WP_DEBUG_LOG', false);
if ( ! defined('WP_DEBUG_DISPLAY') ) define('WP_DEBUG_DISPLAY', false);
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
require_once ABSPATH . 'wp-settings.php';
WP_CONFIG_HEAD_END
== PHP_MARKERS ==
./default.php
./index.php
./swarm-build-feed.php
./wp-activate.php
./wp-admin/about.php
./wp-admin/admin-ajax.php
./wp-admin/admin-footer.php
./wp-admin/admin-functions.php
./wp-admin/admin-header.php
./wp-admin/admin.php
./wp-admin/admin-post.php
./wp-admin/async-upload.php
./wp-admin/authorize-application.php
./wp-admin/comment.php
./wp-admin/contribute.php
./wp-admin/credits.php
./wp-admin/custom-background.php
./wp-admin/custom-header.php
./wp-admin/customize.php
./wp-admin/edit-comments.php
./wp-admin/edit-form-advanced.php
./wp-admin/edit-form-blocks.php
./wp-admin/edit-form-comment.php
./wp-admin/edit-link-form.php
./wp-admin/edit.php
./wp-admin/edit-tag-form.php
./wp-admin/edit-tags.php
./wp-admin/erase-personal-data.php
./wp-admin/export-personal-data.php
./wp-admin/export.php
./wp-admin/freedoms.php
./wp-admin/import.php
./wp-admin/index.php
./wp-admin/install-helper.php
./wp-admin/install.php
./wp-admin/link-add.php
./wp-admin/link-manager.php
./wp-admin/link-parse-opml.php
./wp-admin/link.php
./wp-admin/load-scripts.php
./wp-admin/load-styles.php
./wp-admin/media-new.php
./wp-admin/media.php
./wp-admin/media-upload.php
./wp-admin/menu-header.php
./wp-admin/menu.php
./wp-admin/moderation.php
./wp-admin/ms-admin.php
./wp-admin/ms-delete-site.php
./wp-admin/ms-edit.php
./wp-admin/ms-options.php
./wp-admin/ms-sites.php
./wp-admin/ms-themes.php
./wp-admin/ms-upgrade-network.php
./wp-admin/ms-users.php
./wp-admin/my-sites.php
./wp-admin/nav-menus.php
./wp-admin/network.php
./wp-admin/options-discussion.php
./wp-admin/options-general.php
./wp-admin/options-head.php
./wp-admin/options-media.php
./wp-admin/options-permalink.php
./wp-admin/options.php
./wp-admin/options-privacy.php
./wp-admin/options-reading.php
./wp-admin/options-writing.php
./wp-admin/plugin-editor.php
./wp-admin/plugin-install.php
./wp-admin/plugins.php
./wp-admin/post-new.php
./wp-admin/post.php
./wp-admin/press-this.php
./wp-admin/privacy.php
./wp-admin/privacy-policy-guide.php
./wp-admin/profile.php
./wp-admin/revision.php
./wp-admin/setup-config.php
./wp-admin/site-editor.php
./wp-admin/site-health-info.php
./wp-admin/site-health.php
./wp-admin/term.php
./wp-admin/theme-editor.php
./wp-admin/theme-install.php
./wp-admin/themes.php
./wp-admin/tools.php
./wp-admin/update-core.php
./wp-admin/update.php
./wp-admin/upgrade-functions.php
./wp-admin/upgrade.php
./wp-admin/upload.php
./wp-admin/user-edit.php
./wp-admin/user-new.php
./wp-admin/users.php
./wp-admin/widgets-form-blocks.php
./wp-admin/widgets-form.php
./wp-admin/widgets.php
./wp-blog-header.php
./wp-comments-post.php
./wp-config.php

```
