# Deploy canonical Spark select options with env

Generated: 2026-06-05T07:48:52-05:00

## Deploy

```text
== VERIFY ENV ==
== WRITE TASK ==
== DISCOVER REMOTE PLUGIN DIR ==
/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator
REMOTE_PLUGIN_DIR=/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator
REMOTE_ASSET_DIR=/home/u996867598/domains/dadudekc.site/public_html/wp-content/plugins/emergence-character-generator/assets
== UPLOAD PHP AND ASSETS ==
UPLOAD=PASS
== REMOTE CHMOD / CACHE FLUSH ==
Success: The cache was flushed.
Plugin 'emergence-character-generator' deactivated.
Success: Deactivated 1 of 1 plugins.
Plugin 'emergence-character-generator' activated.
Success: Activated 1 of 1 plugins.
Success: The cache was flushed.
5: * Version: 0.8.8-canonical-select-options-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.8-canonical-select-options-001
emergence-character-generator.css?ver=0.8.8-canonical-select-options-001
emergence-cg.js?ver=0.8.8-canonical-select-options-001
emergence-character-generator.js?ver=0.8.8-canonical-select-options-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master 571f1499] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- headers ---
HTTP/2 200 
cache-control: no-store, no-cache, must-revalidate, max-age=0, private
x-dreamos-spark-route: no-store-0.8.1
x-litespeed-cache-control: no-cache
--- page version ---
emergence-cg.css?ver=0.8.8-canonical-select-options-001
emergence-character-generator.css?ver=0.8.8-canonical-select-options-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.8-canonical-select-options-001
emergence-character-generator.js?ver=0.8.8-canonical-select-options-001
--- js marker ---
3527:/* DreamOS Canonical Spark Select Option Hardener
3630:    select.setAttribute("data-dreamos-select-hardened", "1");
3673:      card.setAttribute("data-dreamos-select-options-fixed", "1");
--- css marker ---
628:  appearance: auto;
755:  pointer-events: auto !important;
768:  pointer-events: auto !important;
819:  pointer-events: auto !important;
940:  pointer-events: auto !important;
1007:  pointer-events: auto !important;
1441:/* DreamOS Canonical Spark Select Option Hardener */
1446:select[data-dreamos-select-hardened="1"] {
1447:  pointer-events: auto !important;
1454:select[data-dreamos-select-hardened="1"] {
1456:  appearance: auto !important;
1466:select[data-dreamos-select-hardened="1"] option {
```

## Result

The canonical select option hardener was deployed to the live Spark generator route.
