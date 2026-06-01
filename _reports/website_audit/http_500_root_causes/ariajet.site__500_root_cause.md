# HTTP 500 Root Cause: ariajet.site

## Classification

- Root cause: `wordpress_install_returning_500`
- Recommendation: `run_wp_cli_health_check_or_restore_wordpress`
- Priority: `classify_purpose_before_repair`
- Intended purpose guess: `travel_or_brand_candidate`

## Decision

- Safe next step: confirm intended purpose before changing files.

## Remote Evidence

```text
DOMAIN=ariajet.site
REMOTE_ROOT=/home/u996867598/domains/ariajet.site/public_html
ROOT_EXISTS=YES
== ROOT_STAT ==
/home/u996867598/domains/ariajet.site/public_html
total 908
drwxr-xr-x  6 u996867598 o1008028115  4096 Apr 24 00:03 .
drwxr-xr-x  3 u996867598 o1008028115  4096 Nov 22  2025 ..
-rw-r--r--  1 u996867598 o1008028115  3988 Jan  3 11:40 404.php
-rw-r--r--  1 u996867598 o1008028115   254 Jan 30 22:04 admin.css
-rw-r--r--  1 u996867598 o1008028115  1024 Jan 30 22:04 admin-display.php
-rw-r--r--  1 u996867598 o1008028115    72 Jan 30 22:04 admin.js
-rw-r--r--  1 u996867598 o1008028115   203 Jan 30 22:04 admin.min.css
-rw-r--r--  1 u996867598 o1008028115    47 Jan 30 22:05 admin.min.js
-rw-r--r--  1 u996867598 o1008028115 21090 Jan 30 22:04 ai_context_hero_integration.js
-rw-r--r--  1 u996867598 o1008028115 13425 Jan 30 22:04 ai_context_hero_integration.min.js
-rw-r--r--  1 u996867598 o1008028115  4438 Jan 30 22:04 api-config-utils.js
-rw-r--r--  1 u996867598 o1008028115  2098 Jan 30 22:05 api-config-utils.min.js
-rw-r--r--  1 u996867598 o1008028115  3616 Jan  3 11:41 archive-game.php
-rw-r--r--  1 u996867598 o1008028115  2217 Jan 30 22:04 class-admin.php
-rw-r--r--  1 u996867598 o1008028115  3072 Jan 30 22:04 class-api-client.php
-rw-r--r--  1 u996867598 o1008028115  1124 Jan 30 22:04 class-performance-tracker.php
-rw-r--r--  1 u996867598 o1008028115  3127 Jan 30 22:05 class-public.php
-rw-r--r--  1 u996867598 o1008028115 19227 Jan 30 22:05 class-rest-api-controller.php
-rw-r--r--  1 u996867598 o1008028115  1329 Jan 30 22:05 class-subscription-manager.php
-rw-r--r--  1 u996867598 o1008028115  5076 Jan 30 22:04 class-trading-robot-plug.php
-rw-r--r--  1 u996867598 o1008028115  1588 Jan 30 22:05 class-user-manager.php
-rw-r--r--  1 u996867598 o1008028115 16369 Nov 22  2025 default.php
-rw-r--r--  1 u996867598 o1008028115  3291 Jan 30 22:05 font-corruption-fix.css
-rw-r--r--  1 u996867598 o1008028115   217 Jan 30 22:05 footer.php
-rw-r--r--  1 u996867598 o1008028115  7490 Jan 30 22:04 functions.php
-rw-r--r--  1 u996867598 o1008028115 11644 Jan  3 11:41 games.css
-rw-r--r--  1 u996867598 o1008028115  3493 Jan  3 11:41 games.js
-rw-r--r--  1 u996867598 o1008028115   371 Jan 30 22:04 header.php
-rw-r--r--  1 u996867598 o1008028115 11676 Jan 30 22:04 hero-gaming.php
-rw-r--r--  1 u996867598 o1008028115  1428 Jan 30 23:22 .htaccess
-rw-r--r--  1 u996867598 o1008028115   714 Nov 22  2025 .htaccess.bk
-rw-r--r--  1 u996867598 o1008028115  1429 Jan 24 02:31 .htaccess.old-1769221902
-rw-r--r--  1 u996867598 o1008028115  1428 Jan 24 02:31 .htaccess.old-1769221918
-rw-r--r--  1 u996867598 o1008028115   405 Jan 30 22:24 index.php
-rw-r--r--  1 u996867598 o1008028115 19903 Jan 30 22:24 license.txt
-rw-r--r--  1 u996867598 o1008028115     0 Jan 30 23:22 .litespeed_purge
-rw-r--r--  1 u996867598 o1008028115  1188 Jan 23 19:40 llms.txt
-rw-r--r--  1 u996867598 o1008028115  4546 Jan  3 11:41 main.js
-rw-r--r--  1 u996867598 o1008028115   930 Jan 30 22:04 marketplace-grid.php
-rw-r--r--  1 u996867598 o1008028115  5684 Jan  3 11:40 page-3671.php
-rw-r--r--  1 u996867598 o1008028115  6825 Jan 30 22:04 page-about.php
-rw-r--r--  1 u996867598 o1008028115  6517 Jan  3 11:40 page-music.php
-rw-r--r--  1 u996867598 o1008028115  2940 Jan  3 11:41 page.php
-rw-r--r--  1 u996867598 o1008028115  3147 Jan  3 11:40 page-playlists.php
-rw-r--r--  1 u996867598 o1008028115  4033 Jan  3 11:40 page-projects.php
-rw-r--r--  1 u996867598 o1008028115  1309 Jan 30 22:04 performance-dashboard.php
-rw-r--r--  1 u996867598 o1008028115  1012 Jan 30 22:04 pricing-table.php
drwxr-xr-x  2 u996867598 o1008028115  4096 Nov 22  2025 .private
-rw-r--r--  1 u996867598 o1008028115  2620 Jan 30 22:04 public.css
-rw-r--r--  1 u996867598 o1008028115 13853 Jan 30 22:05 public.js
-rw-r--r--  1 u996867598 o1008028115  2072 Jan 30 22:04 public.min.css
-rw-r--r--  1 u996867598 o1008028115  8278 Jan 30 22:04 public.min.js
-rw-r--r--  1 u996867598 o1008028115  7425 Mar 12 23:54 readme.html
-rw-r--r--  1 u996867598 o1008028115 11378 Jan 30 22:05 security-monitor.php
-rw-r--r--  1 u996867598 o1008028115  9768 Jan 30 22:05 seo-meta-fix.php
-rw-r--r--  1 u996867598 o1008028115  5957 Jan  3 11:40 single-game.php
-rw-r--r--  1 u996867598 o1008028115  7130 Jan  3 11:41 single.php
-rw-r--r--  1 u996867598 o1008028115   372 Jan 30 22:04 style.css
-rw-r--r--  1 u996867598 o1008028115   234 Jan 30 22:05 style.min.css
-rw-r--r--  1 u996867598 o1008028115  1510 Jan 30 22:04 tradingrobotplug.php
-rw-r--r--  1 u996867598 o1008028115  3534 Jan 30 22:04 user-dashboard.php
-rw-r--r--  1 u996867598 o1008028115  7349 Jan 30 22:24 wp-activate.php
drwxr-xr-x  9 u996867598 o1008028115  4096 Jan 24 02:31 wp-admin
-rw-r--r--  1 u996867598 o1008028115   351 Jan 30 22:24 wp-blog-header.php
-rw-r--r--  1 u996867598 o1008028115  2323 Jan 30 22:24 wp-comments-post.php
-rw-r--r--  1 u996867598 o1008028115  3903 May 28 10:57 wp-config.php
-rw-r--r--  1 u996867598 o1008028115  3723 Jan 24 01:24 wp-config.php.bak_dreamos
-rw-r--r--  1 u996867598 o1008028115  3339 Jan 30 22:24 wp-config-sample.php
drwxr-xr-x  9 u996867598 o1008028115  4096 Jan 30 23:22 wp-content
-rw-r--r--  1 u996867598 o1008028115  5617 Jan 30 22:24 wp-cron.php
drwxr-xr-x 31 u996867598 o1008028115 16384 Jan 30 22:24 wp-includes
-rw-r--r--  1 u996867598 o1008028115  2493 Jan 30 22:24 wp-links-opml.php
-rw-r--r--  1 u996867598 o1008028115  3937 Jan 30 22:24 wp-load.php
-rw-r--r--  1 u996867598 o1008028115 51437 Jan 30 22:24 wp-login.php
-rw-r--r--  1 u996867598 o1008028115  8727 Jan 30 22:24 wp-mail.php
-rw-r--r--  1 u996867598 o1008028115 31055 Jan 30 22:24 wp-settings.php
-rw-r--r--  1 u996867598 o1008028115 34516 Jan 30 22:24 wp-signup.php
-rw-r--r--  1 u996867598 o1008028115  5214 Jan 30 22:24 wp-trackback.php
-rw-r--r--  1 u996867598 o1008028115  3205 Jan 30 22:24 xmlrpc.php
== COUNTS ==
FILE_COUNT=17189
DIR_COUNT=2693
== PERMISSIONS ==
ROOT_MODE=755 ROOT_OWNER=u996867598 ROOT_GROUP=o1008028115
drwxr-xr-x u996867598 o1008028115 .
drwxr-xr-x u996867598 o1008028115 ./.private
drwxr-xr-x u996867598 o1008028115 ./wp-admin
drwxr-xr-x u996867598 o1008028115 ./wp-content
drwxr-xr-x u996867598 o1008028115 ./wp-includes
-rw-r--r-- u996867598 o1008028115 ./404.php
-rw-r--r-- u996867598 o1008028115 ./admin.css
-rw-r--r-- u996867598 o1008028115 ./admin-display.php
-rw-r--r-- u996867598 o1008028115 ./admin.js
-rw-r--r-- u996867598 o1008028115 ./admin.min.css
-rw-r--r-- u996867598 o1008028115 ./admin.min.js
-rw-r--r-- u996867598 o1008028115 ./ai_context_hero_integration.js
-rw-r--r-- u996867598 o1008028115 ./ai_context_hero_integration.min.js
-rw-r--r-- u996867598 o1008028115 ./api-config-utils.js
-rw-r--r-- u996867598 o1008028115 ./api-config-utils.min.js
-rw-r--r-- u996867598 o1008028115 ./archive-game.php
-rw-r--r-- u996867598 o1008028115 ./class-admin.php
-rw-r--r-- u996867598 o1008028115 ./class-api-client.php
-rw-r--r-- u996867598 o1008028115 ./class-performance-tracker.php
-rw-r--r-- u996867598 o1008028115 ./class-public.php
-rw-r--r-- u996867598 o1008028115 ./class-rest-api-controller.php
-rw-r--r-- u996867598 o1008028115 ./class-subscription-manager.php
-rw-r--r-- u996867598 o1008028115 ./class-trading-robot-plug.php
-rw-r--r-- u996867598 o1008028115 ./class-user-manager.php
-rw-r--r-- u996867598 o1008028115 ./default.php
-rw-r--r-- u996867598 o1008028115 ./font-corruption-fix.css
-rw-r--r-- u996867598 o1008028115 ./footer.php
-rw-r--r-- u996867598 o1008028115 ./functions.php
-rw-r--r-- u996867598 o1008028115 ./games.css
-rw-r--r-- u996867598 o1008028115 ./games.js
-rw-r--r-- u996867598 o1008028115 ./header.php
-rw-r--r-- u996867598 o1008028115 ./hero-gaming.php
-rw-r--r-- u996867598 o1008028115 ./.htaccess
-rw-r--r-- u996867598 o1008028115 ./.htaccess.bk
-rw-r--r-- u996867598 o1008028115 ./.htaccess.old-1769221902
-rw-r--r-- u996867598 o1008028115 ./.htaccess.old-1769221918
-rw-r--r-- u996867598 o1008028115 ./index.php
-rw-r--r-- u996867598 o1008028115 ./license.txt
-rw-r--r-- u996867598 o1008028115 ./.litespeed_purge
-rw-r--r-- u996867598 o1008028115 ./llms.txt
-rw-r--r-- u996867598 o1008028115 ./main.js
-rw-r--r-- u996867598 o1008028115 ./marketplace-grid.php
-rw-r--r-- u996867598 o1008028115 ./page-3671.php
-rw-r--r-- u996867598 o1008028115 ./page-about.php
-rw-r--r-- u996867598 o1008028115 ./page-music.php
-rw-r--r-- u996867598 o1008028115 ./page.php
-rw-r--r-- u996867598 o1008028115 ./page-playlists.php
-rw-r--r-- u996867598 o1008028115 ./page-projects.php
-rw-r--r-- u996867598 o1008028115 ./performance-dashboard.php
-rw-r--r-- u996867598 o1008028115 ./pricing-table.php
-rw-r--r-- u996867598 o1008028115 ./public.css
-rw-r--r-- u996867598 o1008028115 ./public.js
-rw-r--r-- u996867598 o1008028115 ./public.min.css
-rw-r--r-- u996867598 o1008028115 ./public.min.js
-rw-r--r-- u996867598 o1008028115 ./readme.html
-rw-r--r-- u996867598 o1008028115 ./security-monitor.php
-rw-r--r-- u996867598 o1008028115 ./seo-meta-fix.php
-rw-r--r-- u996867598 o1008028115 ./single-game.php
-rw-r--r-- u996867598 o1008028115 ./single.php
-rw-r--r-- u996867598 o1008028115 ./style.css
-rw-r--r-- u996867598 o1008028115 ./style.min.css
-rw-r--r-- u996867598 o1008028115 ./tradingrobotplug.php
-rw-r--r-- u996867598 o1008028115 ./user-dashboard.php
-rw-r--r-- u996867598 o1008028115 ./wp-activate.php
-rw-r--r-- u996867598 o1008028115 ./wp-blog-header.php
-rw-r--r-- u996867598 o1008028115 ./wp-comments-post.php
-rw-r--r-- u996867598 o1008028115 ./wp-config.php
-rw-r--r-- u996867598 o1008028115 ./wp-config.php.bak_dreamos
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
# The directives (lines) between "BEGIN WordPress" and "END WordPress" are
# dynamically generated, and should only be modified via WordPress filters.
# Any changes to the directives between these markers will be overwritten.
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>

# END WordPressHTACCESS_END
== WORDPRESS_MARKERS ==
WP_CONFIG=YES
WP_CONTENT=YES
WP_ADMIN=YES
WP_LOAD=YES
WP_CONFIG_HEAD_BEGIN
 * * ABSPATH
define( 'DB_NAME', 'u996867598_1hM93' );
define( 'DB_USER', 'u996867598_a3NN9' );
$table_prefix = 'wp_';
if ( ! defined('WP_DEBUG') ) define('WP_DEBUG', false);
if ( ! defined('WP_DEBUG_LOG') ) define('WP_DEBUG_LOG', false);
if ( ! defined('WP_DEBUG_DISPLAY') ) define('WP_DEBUG_DISPLAY', false);
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
require_once ABSPATH . 'wp-settings.php';
WP_CONFIG_HEAD_END
== PHP_MARKERS ==
./404.php
./admin-display.php
./archive-game.php
./class-admin.php
./class-api-client.php
./class-performance-tracker.php
./class-public.php
./class-rest-api-controller.php
./class-subscription-manager.php
./class-trading-robot-plug.php
./class-user-manager.php
./default.php
./footer.php
./functions.php
./header.php
./hero-gaming.php
./index.php
./marketplace-grid.php
./page-3671.php
./page-about.php
./page-music.php
./page.php
./page-playlists.php
./page-projects.php
./performance-dashboard.php
./pricing-table.php
./security-monitor.php
./seo-meta-fix.php
./single-game.php
./single.php
./tradingrobotplug.php
./user-dashboard.php
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

```
