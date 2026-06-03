# Shortcode Exception Verification

Task: expose_shortcode_exception_and_verify_017

Actions:
- Patched shortcode catch block to rethrow only when `SPARK_BATTLE_SIM_TESTING` is true.
- Preserved production behavior: generic "Battle could not start."
- Reran shortcode smoke with stdout/stderr capture.

Smoke exit code:
```
255
```

Artifacts:
- runtime/plugins/spark-battle-sim/spark-battle-sim.php
- _reports/backups/spark_shortcode_exception_verify_017/spark-battle-sim.php.bak
- _reports/spark_shortcode_exception_verify_017.txt
- _reports/spark_shortcode_exception_verify_017.err
- _reports/spark_shortcode_exception_verify_017.html
- _reports/spark_shortcode_exception_verify_017.md

Next:
- If exit code is 0: package plugin.
- If nonzero: patch exact exception shown in stderr.

Status: FAIL_CLASSIFIED
