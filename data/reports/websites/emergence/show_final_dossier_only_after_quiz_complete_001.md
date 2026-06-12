# Show final dossier only after Spark quiz completion

Generated: 2026-06-05T06:57:08-05:00

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.3-dossier-end-only-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.3-dossier-end-only-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.3-dossier-end-only-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.3-dossier-end-only-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.3-dossier-end-only-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2570:/* DreamOS Final Dossier End-Only Gate
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2602:        if (!el.hasAttribute("data-dreamos-end-only-final-dossier")) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2603:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2611:      if (!el || el.hasAttribute("data-dreamos-end-only-final-dossier")) return;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2620:        el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2691:    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-end-only-final-dossier-wrap]"), function (el) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2779:    if (document.querySelector("[data-dreamos-end-only-final-dossier='1']")) return;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2783:    wrap.setAttribute("data-dreamos-end-only-final-dossier-wrap", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2787:    btn.className = "dreamos-end-only-dossier-button";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2788:    btn.setAttribute("data-dreamos-end-only-final-dossier", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2796:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-end-only-final-dossier='1']") : null;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1166:/* DreamOS Final Dossier End-Only Gate
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1198:        if (!el.hasAttribute("data-dreamos-end-only-final-dossier")) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1199:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1207:      if (!el || el.hasAttribute("data-dreamos-end-only-final-dossier")) return;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1216:        el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1287:    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-end-only-final-dossier-wrap]"), function (el) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1375:    if (document.querySelector("[data-dreamos-end-only-final-dossier='1']")) return;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1379:    wrap.setAttribute("data-dreamos-end-only-final-dossier-wrap", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1383:    btn.className = "dreamos-end-only-dossier-button";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1384:    btn.setAttribute("data-dreamos-end-only-final-dossier", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1392:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-end-only-final-dossier='1']") : null;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1157:/* DreamOS Final Dossier End-Only Gate */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1158:[data-dreamos-retired-dossier-control="1"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1178:.dreamos-end-only-dossier-button {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:581:/* DreamOS Final Dossier End-Only Gate */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:582:[data-dreamos-retired-dossier-control="1"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:602:.dreamos-end-only-dossier-button {
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

The dossier action no longer floats and no longer appears before the 28-question Spark quiz is complete.

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
5: * Version: 0.8.3-dossier-end-only-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.3-dossier-end-only-001
emergence-character-generator.css?ver=0.8.3-dossier-end-only-001
emergence-cg.js?ver=0.8.3-dossier-end-only-001
emergence-character-generator.js?ver=0.8.3-dossier-end-only-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master b3a9730c] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- page ---
emergence-cg.css?ver=0.8.3-dossier-end-only-001
emergence-character-generator.css?ver=0.8.3-dossier-end-only-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.3-dossier-end-only-001
emergence-character-generator.js?ver=0.8.3-dossier-end-only-001
--- js ---
2570:/* DreamOS Final Dossier End-Only Gate
2602:        if (!el.hasAttribute("data-dreamos-end-only-final-dossier")) {
2603:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
2611:      if (!el || el.hasAttribute("data-dreamos-end-only-final-dossier")) return;
2620:        el.setAttribute("data-dreamos-retired-dossier-control", "1");
2691:    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-end-only-final-dossier-wrap]"), function (el) {
2779:    if (document.querySelector("[data-dreamos-end-only-final-dossier='1']")) return;
2783:    wrap.setAttribute("data-dreamos-end-only-final-dossier-wrap", "1");
2788:    btn.setAttribute("data-dreamos-end-only-final-dossier", "1");
2796:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-end-only-final-dossier='1']") : null;
--- css ---
1157:/* DreamOS Final Dossier End-Only Gate */
1158:[data-dreamos-retired-dossier-control="1"],
1178:.dreamos-end-only-dossier-button {
```
