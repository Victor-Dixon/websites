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
