# Hide public Spark recovery blockers

Generated: 2026-06-05T06:45:54-05:00

## Baseline

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2938:            throw new Error('Unsafe public demo hardening payload blocked: ' + key);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:913:      '<p class="ecg-kicker">Spark Protocol Recovery Mode</p>',
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:915:      '<p>The full generator interface did not mount cleanly, so this fail-open path keeps the Spark Protocol usable while the frontend is repaired.</p>',
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:918:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',
```

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.0-hide-public-recovery-blockers-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.0-hide-public-recovery-blockers-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.0-hide-public-recovery-blockers-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.0-hide-public-recovery-blockers-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.0-hide-public-recovery-blockers-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2938:            throw new Error('Unsafe public demo hardening payload blocked: ' + key);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:918:      '<button type="button" class="ecg-fail-open-button" data-ecg-fail-open-generate="1">Generate Diagnostic Spark</button>',
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2231:/* DreamOS Public Spark Recovery Blocker Suppression
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2245:      .replace(/SPARK PROTOCOL RECOVERY MODE/gi, "SPARK PROTOCOL")
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2248:      .replace(/Reason:\s*Uncaught Error:\s*Unsafe public demo hardening payload blocked:\s*answers/gi, "")
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2249:      .replace(/Uncaught Error:\s*Unsafe public demo hardening payload blocked:\s*answers/gi, "")
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2250:      .replace(/Unsafe public demo hardening payload blocked:\s*answers/gi, "")
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2251:      .replace(/Generate Diagnostic Spark/gi, "Generate Your Spark");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2268:    if ((el.textContent || "").trim() === "Generate Diagnostic Spark") {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:827:/* DreamOS Public Spark Recovery Blocker Suppression
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:841:      .replace(/SPARK PROTOCOL RECOVERY MODE/gi, "SPARK PROTOCOL")
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:844:      .replace(/Reason:\s*Uncaught Error:\s*Unsafe public demo hardening payload blocked:\s*answers/gi, "")
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:845:      .replace(/Uncaught Error:\s*Unsafe public demo hardening payload blocked:\s*answers/gi, "")
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:846:      .replace(/Unsafe public demo hardening payload blocked:\s*answers/gi, "")
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:847:      .replace(/Generate Diagnostic Spark/gi, "Generate Your Spark");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:864:    if ((el.textContent || "").trim() === "Generate Diagnostic Spark") {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1053:/* DreamOS Public Spark Recovery Blocker Suppression */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:477:/* DreamOS Public Spark Recovery Blocker Suppression */
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

Public recovery/debug blocker language is suppressed from the player-facing generator UI. Future debug/error explanation belongs in the AI-generated response area.

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
5: * Version: 0.8.0-hide-public-recovery-blockers-001
REMOTE_PLUGIN_DEPLOY=PASS
== VERIFY LIVE PAGE ASSET VERSION ==
emergence-cg.css?ver=0.8.0-hide-public-recovery-blockers-001
emergence-character-generator.css?ver=0.8.0-hide-public-recovery-blockers-001
emergence-cg.js?ver=0.8.0-hide-public-recovery-blockers-001
emergence-character-generator.js?ver=0.8.0-hide-public-recovery-blockers-001
== VERIFY LIVE JS MARKER ==
1279:/* DreamOS Guaranteed Final Dossier Injector
== REQUIRE ==
== COMPLETE TASK ==
== WRITE REPORT ==
[master af09e2a0] Deploy Emergence plugin PHP and cache-busted assets
 1 file changed, 6 insertions(+), 6 deletions(-)
== CLOSEOUT ==
STATUS=PASS
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/emergence/deploy_emergence_plugin_php_and_assets_001.md
TASK=/data/data/com.termux/files/home/projects/websites/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml
```

## Live page signals

```text
emergence-cg.css?ver=0.8.0-hide-public-recovery-blockers-001
emergence-character-generator.css?ver=0.8.0-hide-public-recovery-blockers-001
EmergenceCG
EmergenceCG
EmergenceCG
EmergenceCG
question_bank
emergence-cg.js?ver=0.8.0-hide-public-recovery-blockers-001
emergence-character-generator.js?ver=0.8.0-hide-public-recovery-blockers-001
```

## Live markers

```text
2231:/* DreamOS Public Spark Recovery Blocker Suppression
1053:/* DreamOS Public Spark Recovery Blocker Suppression */
```
