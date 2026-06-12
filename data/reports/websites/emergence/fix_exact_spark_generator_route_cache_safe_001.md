# Fix exact Spark generator route cache safely

Generated: 2026-06-05T06:50:56-05:00

Exact URL:

```text
https://maskzero.site/spark-generator
```

## Patch result

```text
PATCHED=runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Local verification

```text
5: * Version: 0.8.1-canonical-route-nostore-001
540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.1-canonical-route-nostore-001');
541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.1-canonical-route-nostore-001', true);
550:add_action('wp_enqueue_scripts', 'emergence_cg_register_assets');
558:            wp_enqueue_script('emergence-cg-script');
598: * DreamOS canonical Spark route no-store guard.
615:                header('X-DreamOS-Spark-Route: no-store-0.8.1');
947:add_action('wp_enqueue_scripts', function () {
958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.1-canonical-route-nostore-001');
959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.1-canonical-route-nostore-001', true);
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```
