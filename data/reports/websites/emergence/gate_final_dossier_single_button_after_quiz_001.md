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
