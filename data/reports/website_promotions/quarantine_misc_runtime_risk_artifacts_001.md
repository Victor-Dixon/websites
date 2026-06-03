# Quarantine Misc Runtime Risk Artifacts 001

- Generated: `2026-06-03T17:03:12`
- Status: `QUARANTINED`
- Moved count: `2`
- Moved file count: `2`

## Guardrail

Quarantined misc runtime script/backup artifacts are preserved as evidence and must not be treated as active website source unless explicitly promoted later.

## Moved

### `runtime/scripts/hostinger_wp_manager.py.bak_052`
- Quarantine: `runtime/quarantine/misc_runtime_risk_artifacts_001/runtime__scripts__hostinger_wp_manager.py.bak_052`
- Reason: backup script should be preserved outside active source
- Risk flags: `possible_sensitive_surface, backup_file, runtime_script`
- File count: `1`

### `runtime/scripts/rerank_robinhood_contract_identity_raw_sources_001.py`
- Quarantine: `runtime/quarantine/misc_runtime_risk_artifacts_001/runtime__scripts__rerank_robinhood_contract_identity_raw_sources_001.py`
- Reason: runtime script has risk/operational signals; quarantine before reuse
- Risk flags: `possible_sensitive_surface, runtime_script`
- File count: `1`
