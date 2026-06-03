# Misc Runtime Artifacts Review 001

- Generated: `2026-06-03T17:02:00`
- Status: `REVIEWED`
- Item count: `7`

## Unlock

The final non-DaDudeKC websites blocker class is now classified into keep/quarantine lanes.

## Decision Counts

- `KEEP_CANDIDATE`: `1`
- `QUARANTINE_CANDIDATE`: `2`
- `REVIEW_DEEPER`: `4`

## Items

### `_tmp`
- Decision: `REVIEW_DEEPER`
- Reason: misc runtime artifact needs deeper inspection
- Risk flags: `possible_sensitive_surface`
- File count: `67`
- Size: `0`

### `data/reports/website_promotions`
- Decision: `REVIEW_DEEPER`
- Reason: misc runtime artifact needs deeper inspection
- Risk flags: `none`
- File count: `21`
- Size: `0`

### `runtime/docs`
- Decision: `REVIEW_DEEPER`
- Reason: misc runtime artifact needs deeper inspection
- Risk flags: `runtime_docs`
- File count: `1`
- Size: `0`

### `runtime/scripts/hostinger_wp_manager.py.bak_052`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: backup script should be preserved outside active source
- Risk flags: `possible_sensitive_surface, backup_file, runtime_script`
- File count: `1`
- Size: `5356`

### `runtime/scripts/rerank_robinhood_contract_identity_raw_sources_001.py`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: runtime script has risk/operational signals; quarantine before reuse
- Risk flags: `possible_sensitive_surface, runtime_script`
- File count: `1`
- Size: `8925`

### `runtime/spark-battle-sim`
- Decision: `REVIEW_DEEPER`
- Reason: misc runtime artifact needs deeper inspection
- Risk flags: `possible_sensitive_surface, runtime_plugin_or_package`
- File count: `5`
- Size: `0`

### `runtime/tasks/trading/rerank_robinhood_contract_identity_raw_sources_001.yaml`
- Decision: `KEEP_CANDIDATE`
- Reason: trading task is durable lane metadata
- Risk flags: `trading_task`
- File count: `1`
- Size: `1364`

## Next Lanes

### commit_misc_runtime_keep_artifacts_001
- TARGET: KEEP_CANDIDATE misc runtime artifacts
- ACTION: stage only keep candidates plus review metadata
- VERIFY: cached scope excludes quarantine candidates and DaDudeKC promotion content
- COMMIT: Commit misc runtime artifacts

### quarantine_misc_runtime_risk_artifacts_001
- TARGET: QUARANTINE_CANDIDATE misc runtime artifacts
- ACTION: move backup/risk runtime artifacts into quarantine with manifest
- VERIFY: originals cleared, manifest references moved files
- COMMIT: Quarantine misc runtime risk artifacts
