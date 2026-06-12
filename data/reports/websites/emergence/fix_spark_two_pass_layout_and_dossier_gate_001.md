# Fix Spark two-pass layout and dossier gate

Generated: 2026-06-05T07:00:03-05:00

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.4-two-pass-dossier-gate-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:529:        <div id="emergence-cg-flavor" class="ecg-flavor" data-phase="locked">
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.4-two-pass-dossier-gate-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.4-two-pass-dossier-gate-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.4-two-pass-dossier-gate-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.4-two-pass-dossier-gate-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2603:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2620:        el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2824:/* DreamOS Spark Two-Pass Dossier Gate
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2867:        if (!el.hasAttribute("data-dreamos-two-pass-final-dossier")) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2868:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2957:    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-two-pass-final-dossier-wrap]"), function (el) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3042:    if (document.querySelector("[data-dreamos-two-pass-final-dossier='1']")) return;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3046:    wrap.setAttribute("data-dreamos-two-pass-final-dossier-wrap", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3051:    btn.setAttribute("data-dreamos-two-pass-final-dossier", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3059:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-two-pass-final-dossier='1']") : null;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1199:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1216:        el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1420:/* DreamOS Spark Two-Pass Dossier Gate
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1463:        if (!el.hasAttribute("data-dreamos-two-pass-final-dossier")) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1464:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1553:    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-two-pass-final-dossier-wrap]"), function (el) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1638:    if (document.querySelector("[data-dreamos-two-pass-final-dossier='1']")) return;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1642:    wrap.setAttribute("data-dreamos-two-pass-final-dossier-wrap", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1647:    btn.setAttribute("data-dreamos-two-pass-final-dossier", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1655:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-two-pass-final-dossier='1']") : null;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1158:[data-dreamos-retired-dossier-control="1"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1195:/* DreamOS Spark Two-Pass Dossier Gate */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1196:[data-dreamos-retired-dossier-control="1"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1215:[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1217:.ecg-flavor[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:582:[data-dreamos-retired-dossier-control="1"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:619:/* DreamOS Spark Two-Pass Dossier Gate */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:620:[data-dreamos-retired-dossier-control="1"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:639:[data-phase="locked"],
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:641:.ecg-flavor[data-phase="locked"],
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

The final dossier gate now follows the visible active quiz phase instead of hardcoding only the first 28 questions. Locked sections are collapsed to remove mobile blank space.

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
5: * Version: 0.8.4-two-pass-dossier-gate-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.4-two-pass-dossier-gate-001
emergence-character-generator.css?ver=0.8.4-two-pass-dossier-gate-001
emergence-cg.js?ver=0.8.4-two-pass-dossier-gate-001
emergence-character-generator.js?ver=0.8.4-two-pass-dossier-gate-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master 509b9cbf] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- page ---
emergence-cg.css?ver=0.8.4-two-pass-dossier-gate-001
emergence-character-generator.css?ver=0.8.4-two-pass-dossier-gate-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.4-two-pass-dossier-gate-001
emergence-character-generator.js?ver=0.8.4-two-pass-dossier-gate-001
--- js ---
2603:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
2620:        el.setAttribute("data-dreamos-retired-dossier-control", "1");
2824:/* DreamOS Spark Two-Pass Dossier Gate
2867:        if (!el.hasAttribute("data-dreamos-two-pass-final-dossier")) {
2868:          el.setAttribute("data-dreamos-retired-dossier-control", "1");
2957:    Array.prototype.forEach.call(document.querySelectorAll("[data-dreamos-two-pass-final-dossier-wrap]"), function (el) {
3042:    if (document.querySelector("[data-dreamos-two-pass-final-dossier='1']")) return;
3046:    wrap.setAttribute("data-dreamos-two-pass-final-dossier-wrap", "1");
3051:    btn.setAttribute("data-dreamos-two-pass-final-dossier", "1");
3059:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-two-pass-final-dossier='1']") : null;
--- css ---
1158:[data-dreamos-retired-dossier-control="1"],
1195:/* DreamOS Spark Two-Pass Dossier Gate */
1196:[data-dreamos-retired-dossier-control="1"],
1215:[data-phase="locked"],
1217:.ecg-flavor[data-phase="locked"],
1239:.dreamos-two-pass-dossier-button {
```
