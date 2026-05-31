# Shareable Spark Handoff Token 101

## Task
Replace localStorage-only handoff with signed short token/link.

## Actions
- Added signed token create/load REST routes.
- Stored safe Spark payloads in WordPress transients.
- Added Battle Simulator token import bridge.
- Preserved localStorage fallback.
- Rejected invalid tokens.
- Verified no raw score leaks.

## Verification
```text
INPUTS=PASS
SHAREABLE_TOKEN_PATCH=PASS
STATIC_TOKEN_SECURITY=PASS
STATIC_NO_RAW_SCORE_EXPORT=PASS
PLUGIN_TARBALLS=PASS
SCP_UPLOAD=PASS
EXISTING_PLUGIN_BACKUP=PASS
No syntax errors detected in wp-content/plugins/emergence-character-generator/emergence-character-generator.php
No syntax errors detected in wp-content/plugins/spark-battle-sim/spark-battle-sim.php
REMOTE_PHP_LINT=PASS
        const response = await fetch('/wp-json/emergence/v1/spark-token', {
    register_rest_route('emergence/v1', '/spark-token', array(
    register_rest_route('emergence/v1', '/spark-token/(?P<token>[A-Za-z0-9_-]{16,80})', array(
      async function createShareableSparkToken(payload) {
          createShareableSparkToken(payload).then(function (tokenData) {
    <script id="dreamos-bs-token-handoff-inline">
REMOTE_TOKEN_SOURCE=PASS
Success: Plugin already activated.
Success: Plugin already activated.
PLUGINS_ACTIVE=PASS
Success: The cache was flushed.
Success: Purged All!
LITESPEED_PURGE=PASS
REMOTE_DEPLOY=PASS
TOKEN_CREATE=PASS
TOKEN_LOAD_VALID=PASS
TOKEN_NO_RAW_SCORE_LEAK=PASS
TOKEN_INVALID_REJECTED=PASS
BATTLE_PAGE_TOKEN_BRIDGE=PASS
SHAREABLE_SPARK_HANDOFF_TOKEN=PASS
```

## Commit
Add shareable Spark battle handoff token

## Status
PASS
