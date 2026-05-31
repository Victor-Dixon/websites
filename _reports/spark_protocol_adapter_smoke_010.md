# Spark Protocol Adapter Smoke

Task: fix_spark_protocol_adapter_smoke_010

Actions:
- Kept vendored Spark Protocol source in `runtime/plugins/spark-battle-sim/includes/Spark`.
- Kept plugin-local autoloader.
- Fixed smoke script to use real `Spark\Model\Sheet` constructor.
- Fixed sheet display calls to use `maskName()`.
- Ran deterministic BattleSimulator smoke.

Verification:
- Spark Protocol autoload: PASS
- Sheet construction: PASS
- BattleSimulator execution: PASS
- Winner emitted: PASS
- Operator show-work emitted: PASS

Artifacts:
- runtime/plugins/spark-battle-sim/bin/spark-protocol-adapter-smoke.php
- _reports/spark_protocol_adapter_smoke_010.txt
- _reports/spark_protocol_adapter_smoke_010.md

Commit message:
```
Integrate Spark Protocol battle simulation engine
```

Status: PASS
