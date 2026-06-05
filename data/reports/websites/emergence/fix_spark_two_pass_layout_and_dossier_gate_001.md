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
