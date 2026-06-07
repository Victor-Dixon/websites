# Rescue Crosby WordPress 500

generated=2026-06-06T15:31:49-05:00
root=/data/data/com.termux/files/home/projects/websites
domain=crosbyultimateevents.com
remote_root=/home/u996867598/domains/crosbyultimateevents.com/public_html

## Repo
/data/data/com.termux/files/home/projects/websites
origin	git@github.com:Victor-Dixon/Websites.git (fetch)
origin	git@github.com:Victor-Dixon/Websites.git (push)
branch=master
?? data/reports/websites/bootstrap_hostinger_ssh_persistence_crosbyultimateevents.com_20260606_151229.md
?? data/reports/websites/create_crosbyultimateevents.com_hostinger_env_20260606_151004.md
?? data/reports/websites/discover_hostinger_access_crosbyultimateevents.com_20260606_150853.md
?? data/reports/websites/patch_rescue_crosby_ssh_port_20260606_153122.md
?? data/reports/websites/probe_crosbyultimateevents.com_20260606_150726.md
?? data/reports/websites/rescue_crosbyultimateevents.comCrosby_wordpress_500_20260606_151828.md
?? data/reports/websites/rescue_crosbyultimateevents.com_wordpress_500_20260606_150828.md
?? data/reports/websites/rescue_crosbyultimateevents.com_wordpress_500_20260606_151358.md
?? data/reports/websites/rescue_crosbyultimateevents.com_wordpress_500_20260606_153149.md
?? data/reports/websites/seed_hostinger_known_host_crosbyultimateevents.com_20260606_151037.md
?? runtime/env/

== LOAD HOSTINGER ENV WITHOUT PRINTING SECRETS ==
ENV_FILE=FOUND:/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/crosbyultimateevents.com.env
SSH_TARGET=FOUND
SSH_PORT=65002

== UPLOAD REMOTE SCRIPT ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html

== RUN REMOTE RESCUE ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
REMOTE_PRECHECK=PASS
== BACKUP MANIFEST ==
backup_stamp=20260606_153149
remote_root=/home/u996867598/domains/crosbyultimateevents.com/public_html

## Important files
BACKUP_FILE=index.php
BACKUP_FILE=.htaccess
BACKUP_FILE=wp-config.php

## Plugin/theme inventory
wp-content/plugins/akismet/akismet.php
wp-content/plugins/akismet/class.akismet-admin.php
wp-content/plugins/akismet/class.akismet-cli.php
wp-content/plugins/akismet/class-akismet-compatible-plugins.php
wp-content/plugins/akismet/class.akismet.php
wp-content/plugins/akismet/class.akismet-rest-api.php
wp-content/plugins/akismet/class.akismet-widget.php
wp-content/plugins/akismet/index.php
wp-content/plugins/akismet/wrapper.php
wp-content/plugins/crosby-business-plan/crosby-business-plan.php
wp-content/plugins/crosby-business-plan/debug_plugin.php
wp-content/plugins/crosby-business-plan/templates\business-plan-display.php
wp-content/plugins/hello.php
wp-content/plugins/hostinger-easy-onboarding/hostinger-easy-onboarding.php
wp-content/plugins/hostinger-easy-onboarding/index.php
wp-content/plugins/hostinger-easy-onboarding/loader.php
wp-content/plugins/hostinger-easy-onboarding/uninstall.php
wp-content/plugins/hostinger/hostinger.php
wp-content/plugins/hostinger/index.php
wp-content/plugins/hostinger-reach/hostinger-reach.php
wp-content/plugins/hostinger/uninstall.php
wp-content/plugins/index.php
wp-content/plugins/litespeed-cache/autoload.php
wp-content/plugins/litespeed-cache/guest.vary.php
wp-content/plugins/litespeed-cache/litespeed-cache.php
wp-content/themes/crosbyultimateevents/style.css
wp-content/themes/twentytwentyfive/style.css
wp-content/themes/twentytwentyfour/style.css
wp-content/themes/twentytwentythree/style.css
== PHP LINT SUSPECTS ==
--- php -l wp-content/plugins/crosby-business-plan/crosby-business-plan.php ---
No syntax errors detected in wp-content/plugins/crosby-business-plan/crosby-business-plan.php
--- php -l wp-content/themes/crosbyultimateevents/functions.php ---
Errors parsing wp-content/themes/crosbyultimateevents/functions.php
--- php -l wp-content/themes/crosbyultimateevents/index.php ---
No syntax errors detected in wp-content/themes/crosbyultimateevents/index.php
--- php -l wp-content/themes/crosbyultimateevents/front-page.php ---
No syntax errors detected in wp-content/themes/crosbyultimateevents/front-page.php
== WP CLI HEALTH CHECK ==
--- plugin list ---
--- theme list ---
--- deactivate crosby-business-plan ---
ACTIVE_THEME=
== FILESYSTEM PLUGIN FAILSAFE ==
PLUGIN_DIR_DISABLED=crosby-business-plan.disabled_20260606_153149
== CLEAR COMMON CACHE DIRS ==
== POST-RESCUE ERROR TAIL ==
REMOTE_LOG=/home/u996867598/domains/crosbyultimateevents.com/public_html/_dreamos_rescue_20260606_153149/remote_rescue.log
REMOTE_RESCUE_DIR=/home/u996867598/domains/crosbyultimateevents.com/public_html/_dreamos_rescue_20260606_153149
REMOTE_STATUS=RESCUE_ATTEMPTED

== LIVE VERIFY ==
--- https://crosbyultimateevents.com ---
  % Total    % Received % Xferd  Average Speed  Time    Time    Time   Current
                                 Dload  Upload  Total   Spent   Left   Speed
  0      0   0      0   0      0      0      0                              0  0      0   0      0   0      0      0      0                              0  0      0   0      0   0      0      0      0                              0  0      0   0      0   0      0      0      0                              0
HTTP/2 500 
x-powered-by: PHP/8.3.30
content-type: text/html; charset=UTF-8
cache-control: public, max-age=604800
expires: Sat, 13 Jun 2026 20:31:54 GMT
date: Sat, 06 Jun 2026 20:31:54 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"


--- https://www.crosbyultimateevents.com ---
  % Total    % Received % Xferd  Average Speed  Time    Time    Time   Current
                                 Dload  Upload  Total   Spent   Left   Speed
  0      0   0      0   0      0      0      0                              0  0      0   0      0   0      0      0      0                              0  0      0   0      0   0      0      0      0                              0  0      0   0      0   0      0      0      0                              0
HTTP/2 500 
x-powered-by: PHP/8.3.30
content-type: text/html; charset=UTF-8
cache-control: public, max-age=604800
expires: Sat, 13 Jun 2026 20:31:54 GMT
date: Sat, 06 Jun 2026 20:31:54 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"



== CLOSEOUT ==
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/rescue_crosbyultimateevents.com_wordpress_500_20260606_153149.md
STATUS=REMOTE_WP_RESCUE_ATTEMPTED
