# Revert Spark generator to last interactive renderer

Generated: 2026-06-05T07:51:22-05:00

## Restore ref

```text
087ca697
0.8.5-quiz-freeze-observer-fix-001
```

## Before

```text
?? data/reports/websites/emergence/tmp/
?? runtime/tasks/websites/deploy_spark_assets_native_sftp_001.yaml
?? runtime/tasks/websites/deploy_spark_generator_fail_open_assets_001.yaml
?? runtime/tasks/websites/discover_remote_path_and_deploy_spark_assets_001.yaml
?? runtime/tasks/websites/fix_exact_spark_generator_route_cache_001.yaml
?? runtime/tasks/websites/fix_spark_generate_payload_hardening_001.yaml
?? runtime/tasks/websites/patch_client_payload_hardening_allow_answers_001.yaml
?? runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml
?? runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml
?? runtime/tasks/websites/revert_versioned_spark_route_drift_001.yaml
?? runtime/tasks/websites/verify_exact_canonical_spark_generator_route_001.yaml
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.8-canonical-select-options-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.8-canonical-select-options-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.8-canonical-select-options-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.8-canonical-select-options-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.8-canonical-select-options-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3123:/* DreamOS Spark Q11 Renderer Fix
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3265:/* DreamOS Canonical Spark Quiz Renderer
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3527:/* DreamOS Canonical Spark Select Option Hardener
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1719:/* DreamOS Spark Q11 Renderer Fix
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1861:/* DreamOS Canonical Spark Quiz Renderer
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2123:/* DreamOS Canonical Spark Select Option Hardener
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1296:/* DreamOS Spark Q11 Renderer Fix */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1320:/* DreamOS Canonical Spark Quiz Renderer */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1441:/* DreamOS Canonical Spark Select Option Hardener */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:720:/* DreamOS Spark Q11 Renderer Fix */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:744:/* DreamOS Canonical Spark Quiz Renderer */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:865:/* DreamOS Canonical Spark Select Option Hardener */
```

## Restore verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.5-quiz-freeze-observer-fix-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.5-quiz-freeze-observer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.5-quiz-freeze-observer-fix-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.5-quiz-freeze-observer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.5-quiz-freeze-observer-fix-001', true);
--- bad marker scan ---
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Deploy

```text
== VERIFY ENV ==
== WRITE TASK ==
== DISCOVER REMOTE PLUGIN DIR ==
/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins/emergence-character-generator
REMOTE_PLUGIN_DIR=/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins/emergence-character-generator
REMOTE_ASSET_DIR=/home/u996867598/domains/maskzero.site/public_html/wp-content/plugins/emergence-character-generator/assets
== UPLOAD PHP AND ASSETS ==
UPLOAD=PASS
== REMOTE CHMOD / CACHE FLUSH ==
Success: The cache was flushed.
Plugin 'emergence-character-generator' deactivated.
Success: Deactivated 1 of 1 plugins.
Plugin 'emergence-character-generator' activated.
Success: Activated 1 of 1 plugins.
Success: The cache was flushed.
5: * Version: 0.8.5-quiz-freeze-observer-fix-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.5-quiz-freeze-observer-fix-001
emergence-character-generator.css?ver=0.8.5-quiz-freeze-observer-fix-001
emergence-cg.js?ver=0.8.5-quiz-freeze-observer-fix-001
emergence-character-generator.js?ver=0.8.5-quiz-freeze-observer-fix-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master c644c764] Deploy Emergence plugin PHP and cache-busted assets
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
emergence-cg.css?ver=0.8.5-quiz-freeze-observer-fix-001
emergence-character-generator.css?ver=0.8.5-quiz-freeze-observer-fix-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.5-quiz-freeze-observer-fix-001
emergence-character-generator.js?ver=0.8.5-quiz-freeze-observer-fix-001
--- live bad marker scan ---
```
