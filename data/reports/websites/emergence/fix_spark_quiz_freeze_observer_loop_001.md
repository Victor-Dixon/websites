# Fix Spark quiz freeze observer loop

Generated: 2026-06-05T07:05:19-05:00

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.5-quiz-freeze-observer-fix-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.5-quiz-freeze-observer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.5-quiz-freeze-observer-fix-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.5-quiz-freeze-observer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.5-quiz-freeze-observer-fix-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:1768:      const observer = new MutationObserver(upgradeStatus);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1511:    var observer = new MutationObserver(function () {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1776:    var observer = new MutationObserver(hydrate);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1997:    new MutationObserver(inject).observe(document.body, { childList: true, subtree: true });
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2291:    new MutationObserver(function (mutations) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2561:    new MutationObserver(function () {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2817:    new MutationObserver(ensureEndOnlyButton).observe(document.body, {childList: true, subtree: true});
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3082:    new MutationObserver(function(){ window.clearTimeout(window.__dreamosDossierObserverTimer); window.__dreamosDossierObserverTimer = window.setTimeout(ensureButton, 150); }).observe(document.body, {childList: true, subtree: true});
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3090:/* DreamOS Spark Quiz Freeze Observer Fix
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:887:    new MutationObserver(function (mutations) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1157:    new MutationObserver(function () {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1413:    new MutationObserver(ensureEndOnlyButton).observe(document.body, {childList: true, subtree: true});
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1678:    new MutationObserver(function(){ window.clearTimeout(window.__dreamosDossierObserverTimer); window.__dreamosDossierObserverTimer = window.setTimeout(ensureButton, 150); }).observe(document.body, {childList: true, subtree: true});
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1686:/* DreamOS Spark Quiz Freeze Observer Fix
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1217:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1256:/* DreamOS Passive Dossier Duplicate Suppression */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1270:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:641:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:680:/* DreamOS Passive Dossier Duplicate Suppression */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:694:.ecg-flavor[data-phase="locked"],
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

Removed attribute-watching MutationObserver behavior from dossier helper patches and converted duplicate control handling to passive CSS suppression. This should prevent mobile render stalls around Q11.

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
[master 3e25e1d0] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- page ---
emergence-cg.css?ver=0.8.5-quiz-freeze-observer-fix-001
emergence-character-generator.css?ver=0.8.5-quiz-freeze-observer-fix-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.5-quiz-freeze-observer-fix-001
emergence-character-generator.js?ver=0.8.5-quiz-freeze-observer-fix-001
--- js markers ---
1511:    var observer = new MutationObserver(function () {
1776:    var observer = new MutationObserver(hydrate);
1997:    new MutationObserver(inject).observe(document.body, { childList: true, subtree: true });
2291:    new MutationObserver(function (mutations) {
2561:    new MutationObserver(function () {
2817:    new MutationObserver(ensureEndOnlyButton).observe(document.body, {childList: true, subtree: true});
3082:    new MutationObserver(function(){ window.clearTimeout(window.__dreamosDossierObserverTimer); window.__dreamosDossierObserverTimer = window.setTimeout(ensureButton, 150); }).observe(document.body, {childList: true, subtree: true});
3090:/* DreamOS Spark Quiz Freeze Observer Fix
--- css markers ---
1217:.ecg-flavor[data-phase="locked"],
1256:/* DreamOS Passive Dossier Duplicate Suppression */
1270:.ecg-flavor[data-phase="locked"],
1281:/* Mobile question cards should not reserve giant empty space. */
```
