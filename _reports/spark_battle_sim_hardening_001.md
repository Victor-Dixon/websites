# Spark Battle Sim Hardening 001

## Task
Harden Scott's Spark Battle Sim prototype without overwriting it.

## Actions Taken
- Created runtime task artifact:
  - `runtime/tasks/emergence/harden_spark_battle_sim_proxy_001.yaml`
- Created sealed resolver scaffold:
  - `runtime/spark-battle-sim/server/battleResolver.js`
- Created server-side Anthropic proxy example:
  - `runtime/spark-battle-sim/server/anthropicProxy.example.js`
- Created browser-safe client helper:
  - `runtime/spark-battle-sim/client/sparkBattleClient.js`
- Created verification tests:
  - `runtime/spark-battle-sim/tests/test_resolver.js`
  - `runtime/spark-battle-sim/tests/verify_no_client_key_leaks.js`

## Verification
```text
SPARK_BATTLE_RESOLVER_TEST=PASS
CLIENT_SECRET_LEAK_SCAN=PASS
```

## Decision
Scott's prototype is valuable as the demo shell. Production path is:
frontend selector → server resolver → LLM narrator → story-only response.

## Commit Message
Add Spark battle sim hardening scaffold

## Status
PASS
