# Spark Shortcode Template Compatibility

Task: fix_shortcode_template_compat_020

Actions:
- Added missing CLI WordPress stub: `wpautop()`.
- Patched `SparkProtocolAdapter::resolve()` to return `arena.summary` expected by shortcode template.
- Reran shortcode render smoke.

Verification:
- Adapter lint: PASS
- Smoke lint: PASS
- Main plugin lint: PASS
- Shortcode render smoke: PASS
- Result visible: PASS
- Arena visible: PASS
- No Warning/Fatal output in HTML: PASS
- Player math hidden: PASS

Artifacts:
- runtime/plugins/spark-battle-sim/includes/SparkProtocolAdapter.php
- runtime/plugins/spark-battle-sim/bin/spark-battle-shortcode-render-smoke.php
- _reports/spark_shortcode_template_compat_020.txt
- _reports/spark_shortcode_template_compat_020.err
- _reports/spark_shortcode_template_compat_020.html
- _reports/spark_shortcode_template_compat_020.md

Commit message:
```
Integrate Spark Protocol battle simulation engine
```

Status: PASS
