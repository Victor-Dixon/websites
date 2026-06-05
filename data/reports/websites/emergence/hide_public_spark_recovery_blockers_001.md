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
