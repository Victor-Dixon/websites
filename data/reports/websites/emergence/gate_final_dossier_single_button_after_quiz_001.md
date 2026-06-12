# Gate final dossier behind completed Spark quiz

Generated: 2026-06-05T06:55:55-05:00

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.2-quiz-gated-single-dossier-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.2-quiz-gated-single-dossier-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.2-quiz-gated-single-dossier-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.2-quiz-gated-single-dossier-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.2-quiz-gated-single-dossier-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2306:/* DreamOS Canonical Quiz-Gated Final Dossier
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2341:        if (el && !el.hasAttribute("data-dreamos-canonical-final-dossier")) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2350:      if (!el || el.hasAttribute("data-dreamos-canonical-final-dossier")) return;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2444:      '<strong>Complete the Spark quiz first.</strong>' +
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2515:    var existing = document.querySelector("[data-dreamos-canonical-final-dossier='1']");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2523:      btn.setAttribute("data-dreamos-canonical-final-dossier", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2536:    existing.textContent = count >= REQUIRED ? "Build Final Dossier" : "Complete Quiz First (" + count + "/" + REQUIRED + ")";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2540:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-canonical-final-dossier='1']") : null;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:902:/* DreamOS Canonical Quiz-Gated Final Dossier
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:937:        if (el && !el.hasAttribute("data-dreamos-canonical-final-dossier")) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:946:      if (!el || el.hasAttribute("data-dreamos-canonical-final-dossier")) return;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1040:      '<strong>Complete the Spark quiz first.</strong>' +
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1111:    var existing = document.querySelector("[data-dreamos-canonical-final-dossier='1']");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1119:      btn.setAttribute("data-dreamos-canonical-final-dossier", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1132:    existing.textContent = count >= REQUIRED ? "Build Final Dossier" : "Complete Quiz First (" + count + "/" + REQUIRED + ")";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1136:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-canonical-final-dossier='1']") : null;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1072:/* DreamOS Canonical Quiz-Gated Final Dossier */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:496:/* DreamOS Canonical Quiz-Gated Final Dossier */
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

Final dossier is now a single canonical action. It blocks fallback/default dossier builds until the player completes the 28-question Spark quiz.

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
5: * Version: 0.8.2-quiz-gated-single-dossier-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.2-quiz-gated-single-dossier-001
emergence-character-generator.css?ver=0.8.2-quiz-gated-single-dossier-001
emergence-cg.js?ver=0.8.2-quiz-gated-single-dossier-001
emergence-character-generator.js?ver=0.8.2-quiz-gated-single-dossier-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master 0b98a292] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- page ---
emergence-cg.css?ver=0.8.2-quiz-gated-single-dossier-001
emergence-character-generator.css?ver=0.8.2-quiz-gated-single-dossier-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.2-quiz-gated-single-dossier-001
emergence-character-generator.js?ver=0.8.2-quiz-gated-single-dossier-001
--- js ---
2306:/* DreamOS Canonical Quiz-Gated Final Dossier
2341:        if (el && !el.hasAttribute("data-dreamos-canonical-final-dossier")) {
2350:      if (!el || el.hasAttribute("data-dreamos-canonical-final-dossier")) return;
2444:      '<strong>Complete the Spark quiz first.</strong>' +
2515:    var existing = document.querySelector("[data-dreamos-canonical-final-dossier='1']");
2523:      btn.setAttribute("data-dreamos-canonical-final-dossier", "1");
2536:    existing.textContent = count >= REQUIRED ? "Build Final Dossier" : "Complete Quiz First (" + count + "/" + REQUIRED + ")";
2540:    var btn = ev.target && ev.target.closest ? ev.target.closest("[data-dreamos-canonical-final-dossier='1']") : null;
--- css ---
930:.dreamos-canonical-dossier-button {
969:  .dreamos-canonical-dossier-button {
1072:/* DreamOS Canonical Quiz-Gated Final Dossier */
1073:[data-dreamos-duplicate-dossier-hidden="1"] {
1087:.dreamos-canonical-dossier-button {
```
