# Rebuild Spark generator clean page

Generated: 2026-06-05T07:57:08-05:00

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.9.0-clean-spark-page-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.9.0-clean-spark-page-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.9.0-clean-spark-page-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.9.0-clean-spark-page-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.9.0-clean-spark-page-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3313:/* DreamOS Clean Spark Page Rebuild
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3437:      if (!child.hasAttribute("data-dreamos-clean-spark-page")) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3477:    html += '<div class="dreamos-clean-options">';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3498:      root.innerHTML = '<section data-dreamos-clean-spark-page="1" class="dreamos-clean-spark"><h2>Spark Generator</h2><p>Question bank did not load. Refresh once.</p></section>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3507:    html += '<section data-dreamos-clean-spark-page="1" class="dreamos-clean-spark">';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3517:      html += '<button type="button" class="dreamos-clean-submit" data-clean-submit-domain ' + (domainCount < domain.length ? "disabled" : "") + '>Generate Spark Pass 1</button>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3541:        html += '<button type="button" class="dreamos-clean-submit" data-clean-submit-flavor ' + (flavorCount < flavor.length ? "disabled" : "") + '>Build Final Dossier</button>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3609:    var shell = root.querySelector("[data-dreamos-clean-spark-page='1']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3630:      var domainSubmit = ev.target.closest && ev.target.closest("[data-clean-submit-domain]");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3656:      var flavorSubmit = ev.target.closest && ev.target.closest("[data-clean-submit-flavor], [data-clean-build-final]");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3689:    app.setAttribute("data-dreamos-clean-spark-page", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1909:/* DreamOS Clean Spark Page Rebuild
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2033:      if (!child.hasAttribute("data-dreamos-clean-spark-page")) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2073:    html += '<div class="dreamos-clean-options">';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2094:      root.innerHTML = '<section data-dreamos-clean-spark-page="1" class="dreamos-clean-spark"><h2>Spark Generator</h2><p>Question bank did not load. Refresh once.</p></section>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2103:    html += '<section data-dreamos-clean-spark-page="1" class="dreamos-clean-spark">';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2113:      html += '<button type="button" class="dreamos-clean-submit" data-clean-submit-domain ' + (domainCount < domain.length ? "disabled" : "") + '>Generate Spark Pass 1</button>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2137:        html += '<button type="button" class="dreamos-clean-submit" data-clean-submit-flavor ' + (flavorCount < flavor.length ? "disabled" : "") + '>Build Final Dossier</button>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2205:    var shell = root.querySelector("[data-dreamos-clean-spark-page='1']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2226:      var domainSubmit = ev.target.closest && ev.target.closest("[data-clean-submit-domain]");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2252:      var flavorSubmit = ev.target.closest && ev.target.closest("[data-clean-submit-flavor], [data-clean-build-final]");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2285:    app.setAttribute("data-dreamos-clean-spark-page", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1322:/* DreamOS Clean Spark Page Rebuild */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1379:.dreamos-clean-options {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1385:.dreamos-clean-options button {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1403:.dreamos-clean-options button strong {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1413:.dreamos-clean-options button[data-selected="1"] {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:746:/* DreamOS Clean Spark Page Rebuild */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:803:.dreamos-clean-options {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:809:.dreamos-clean-options button {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:827:.dreamos-clean-options button strong {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:837:.dreamos-clean-options button[data-selected="1"] {
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

Rebuilt the Spark generator UI as a clean button-card app. It avoids select dropdowns and avoids legacy renderer dependency.

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
5: * Version: 0.9.0-clean-spark-page-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.9.0-clean-spark-page-001
emergence-character-generator.css?ver=0.9.0-clean-spark-page-001
emergence-cg.js?ver=0.9.0-clean-spark-page-001
emergence-character-generator.js?ver=0.9.0-clean-spark-page-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master 33f67035] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- page ---
emergence-cg.css?ver=0.9.0-clean-spark-page-001
emergence-character-generator.css?ver=0.9.0-clean-spark-page-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.9.0-clean-spark-page-001
emergence-character-generator.js?ver=0.9.0-clean-spark-page-001
--- js ---
3313:/* DreamOS Clean Spark Page Rebuild
3437:      if (!child.hasAttribute("data-dreamos-clean-spark-page")) {
3498:      root.innerHTML = '<section data-dreamos-clean-spark-page="1" class="dreamos-clean-spark"><h2>Spark Generator</h2><p>Question bank did not load. Refresh once.</p></section>';
3507:    html += '<section data-dreamos-clean-spark-page="1" class="dreamos-clean-spark">';
3517:      html += '<button type="button" class="dreamos-clean-submit" data-clean-submit-domain ' + (domainCount < domain.length ? "disabled" : "") + '>Generate Spark Pass 1</button>';
3541:        html += '<button type="button" class="dreamos-clean-submit" data-clean-submit-flavor ' + (flavorCount < flavor.length ? "disabled" : "") + '>Build Final Dossier</button>';
3609:    var shell = root.querySelector("[data-dreamos-clean-spark-page='1']");
3630:      var domainSubmit = ev.target.closest && ev.target.closest("[data-clean-submit-domain]");
3656:      var flavorSubmit = ev.target.closest && ev.target.closest("[data-clean-submit-flavor], [data-clean-build-final]");
3689:    app.setAttribute("data-dreamos-clean-spark-page", "1");
--- css ---
1322:/* DreamOS Clean Spark Page Rebuild */
1323:[data-dreamos-clean-spark-hidden="1"] {
1379:.dreamos-clean-options {
1385:.dreamos-clean-options button {
1403:.dreamos-clean-options button strong {
1413:.dreamos-clean-options button[data-selected="1"] {
1418:.dreamos-clean-submit,
1435:.dreamos-clean-submit {
1440:.dreamos-clean-submit:disabled {
```
