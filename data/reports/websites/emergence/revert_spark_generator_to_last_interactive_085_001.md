# Revert Spark generator to last interactive renderer

Generated: 2026-06-05T07:51:22-05:00

## Restore ref

```text
087ca697
0.8.5-quiz-freeze-observer-fix-001
```

## Before

```text
?? data/reports/websites/emergence/tmp/
?? runtime/tasks/websites/deploy_spark_assets_native_sftp_001.yaml
?? runtime/tasks/websites/deploy_spark_generator_fail_open_assets_001.yaml
?? runtime/tasks/websites/discover_remote_path_and_deploy_spark_assets_001.yaml
?? runtime/tasks/websites/fix_exact_spark_generator_route_cache_001.yaml
?? runtime/tasks/websites/fix_spark_generate_payload_hardening_001.yaml
?? runtime/tasks/websites/patch_client_payload_hardening_allow_answers_001.yaml
?? runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml
?? runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml
?? runtime/tasks/websites/revert_versioned_spark_route_drift_001.yaml
?? runtime/tasks/websites/verify_exact_canonical_spark_generator_route_001.yaml
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.8-canonical-select-options-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.8-canonical-select-options-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.8-canonical-select-options-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.8-canonical-select-options-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.8-canonical-select-options-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3123:/* DreamOS Spark Q11 Renderer Fix
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3265:/* DreamOS Canonical Spark Quiz Renderer
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3527:/* DreamOS Canonical Spark Select Option Hardener
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1719:/* DreamOS Spark Q11 Renderer Fix
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:1861:/* DreamOS Canonical Spark Quiz Renderer
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2123:/* DreamOS Canonical Spark Select Option Hardener
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1296:/* DreamOS Spark Q11 Renderer Fix */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1320:/* DreamOS Canonical Spark Quiz Renderer */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1441:/* DreamOS Canonical Spark Select Option Hardener */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:720:/* DreamOS Spark Q11 Renderer Fix */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:744:/* DreamOS Canonical Spark Quiz Renderer */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:865:/* DreamOS Canonical Spark Select Option Hardener */
```

## Restore verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.5-quiz-freeze-observer-fix-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.5-quiz-freeze-observer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.5-quiz-freeze-observer-fix-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.5-quiz-freeze-observer-fix-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.5-quiz-freeze-observer-fix-001', true);
--- bad marker scan ---
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```
