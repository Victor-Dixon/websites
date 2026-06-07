# Patch client payload hardening to allow answers safely

Generated: 2026-06-05T06:40:44-05:00

## Status before

```text
?? data/reports/websites/emergence/tmp/
?? runtime/tasks/websites/deploy_spark_assets_native_sftp_001.yaml
?? runtime/tasks/websites/deploy_spark_generator_fail_open_assets_001.yaml
?? runtime/tasks/websites/discover_remote_path_and_deploy_spark_assets_001.yaml
?? runtime/tasks/websites/fix_spark_generate_payload_hardening_001.yaml
?? runtime/tasks/websites/patch_client_payload_hardening_allow_answers_001.yaml
?? runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml
?? runtime/tasks/websites/revert_versioned_spark_route_drift_001.yaml
```

## Before

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.7.8-public-generate-payload-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.8-public-generate-payload-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.8-public-generate-payload-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.8-public-generate-payload-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.8-public-generate-payload-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2936:            throw new Error('Unsafe public demo hardening payload blocked: ' + key);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2007:/* DreamOS Floating Final Dossier FAB
```

## Patch result

```text
PATCHED=runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## After

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.7.9-client-hardening-answers-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.9-client-hardening-answers-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.9-client-hardening-answers-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.9-client-hardening-answers-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.9-client-hardening-answers-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2936:            /* DreamOS client hardening allowlist: public Spark payload keys */
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2938:            throw new Error('Unsafe public demo hardening payload blocked: ' + key);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2007:/* DreamOS Floating Final Dossier FAB
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```
