# Spark Protocol Battle Sim Integration Task

Task:
- runtime/tasks/emergence/integrate_spark_protocol_battle_sim_006.yaml

Inputs:
- _reports/spark-protocol-patched-005.zip
- _reports/spark_protocol_exchange_ceiling_005.md
- _reports/spark_protocol_full_tests_005.txt
- runtime/plugins/spark-battle-sim

Purpose:
- Promote Spark Protocol from standalone deterministic engine to Battle Sim rules kernel.

Next safe lane:
1. Inventory current spark-battle-sim plugin.
2. Decide whether to vendor Spark Protocol under plugin src/ or load as isolated package.
3. Add deterministic adapter.
4. Add smoke test around two fighters + arena + committed odds + winner.
5. Preserve player narration as separate layer.

Status: READY
