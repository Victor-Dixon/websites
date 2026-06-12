# Replace Spark quiz with canonical question-bank renderer

Generated: 2026-06-05T07:12:05-05:00

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.7-canonical-quiz-renderer-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.7-canonical-quiz-renderer-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.7-canonical-quiz-renderer-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.7-canonical-quiz-renderer-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.7-canonical-quiz-renderer-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3265:/* DreamOS Canonical Spark Quiz Renderer
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3319:      if (!child.hasAttribute("data-dreamos-canonical-spark-renderer")) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3440:        submit.textContent = "Generate Spark";
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3457:    if (!root || root.querySelector("[data-dreamos-canonical-spark-renderer='1']")) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3471:    shell.setAttribute("data-dreamos-canonical-spark-renderer", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3478:    html += '<div class="dreamos-canonical-question-list">';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3481:      html += '<article class="dreamos-canonical-question" data-dreamos-q="' + q.num + '">';
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3494:    html += '<button type="button" class="dreamos-canonical-submit" data-dreamos-submit-spark disabled>Generate Spark</button>';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1861:/* DreamOS Canonical Spark Quiz Renderer
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1915:      if (!child.hasAttribute("data-dreamos-canonical-spark-renderer")) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2036:        submit.textContent = "Generate Spark";
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2053:    if (!root || root.querySelector("[data-dreamos-canonical-spark-renderer='1']")) {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2067:    shell.setAttribute("data-dreamos-canonical-spark-renderer", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2074:    html += '<div class="dreamos-canonical-question-list">';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2077:      html += '<article class="dreamos-canonical-question" data-dreamos-q="' + q.num + '">';
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2090:    html += '<button type="button" class="dreamos-canonical-submit" data-dreamos-submit-spark disabled>Generate Spark</button>';
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1320:/* DreamOS Canonical Spark Quiz Renderer */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1349:.dreamos-canonical-question-list {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1355:.dreamos-canonical-question {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1362:.dreamos-canonical-question label {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1369:.dreamos-canonical-question select {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:744:/* DreamOS Canonical Spark Quiz Renderer */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:773:.dreamos-canonical-question-list {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:779:.dreamos-canonical-question {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:786:.dreamos-canonical-question label {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:793:.dreamos-canonical-question select {
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

The unstable legacy quiz UI is hidden after the canonical renderer mounts. Q1-Q28 now render directly from EmergenceCG.question_bank using one deterministic select-based form.

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
5: * Version: 0.8.7-canonical-quiz-renderer-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.7-canonical-quiz-renderer-001
emergence-character-generator.css?ver=0.8.7-canonical-quiz-renderer-001
emergence-cg.js?ver=0.8.7-canonical-quiz-renderer-001
emergence-character-generator.js?ver=0.8.7-canonical-quiz-renderer-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master 05b9b3e2] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live verification

```text
--- page ---
emergence-cg.css?ver=0.8.7-canonical-quiz-renderer-001
emergence-character-generator.css?ver=0.8.7-canonical-quiz-renderer-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.7-canonical-quiz-renderer-001
emergence-character-generator.js?ver=0.8.7-canonical-quiz-renderer-001
--- js ---
3265:/* DreamOS Canonical Spark Quiz Renderer
3319:      if (!child.hasAttribute("data-dreamos-canonical-spark-renderer")) {
3440:        submit.textContent = "Generate Spark";
3457:    if (!root || root.querySelector("[data-dreamos-canonical-spark-renderer='1']")) {
3471:    shell.setAttribute("data-dreamos-canonical-spark-renderer", "1");
3494:    html += '<button type="button" class="dreamos-canonical-submit" data-dreamos-submit-spark disabled>Generate Spark</button>';
--- css ---
1320:/* DreamOS Canonical Spark Quiz Renderer */
1321:[data-dreamos-legacy-spark-ui-hidden="1"] {
1349:.dreamos-canonical-question-list {
1355:.dreamos-canonical-question {
1362:.dreamos-canonical-question label {
1369:.dreamos-canonical-question select {
1381:.dreamos-canonical-submit,
1397:.dreamos-canonical-submit:disabled {
```
