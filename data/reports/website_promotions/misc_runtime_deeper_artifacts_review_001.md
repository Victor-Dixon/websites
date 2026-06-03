# Misc Runtime Deeper Artifacts Review 001

- Generated: `2026-06-03T17:06:19`
- Status: `REVIEWED`
- Item count: `4`

## Unlock

The final deeper misc runtime paths are now decided. Only DaDudeKC promotion content remains after these actions close.

## Decision Counts

- `KEEP_CANDIDATE`: `3`
- `QUARANTINE_CANDIDATE`: `1`

## Items

### `_tmp`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: temporary artifacts should be preserved outside active source
- Risk flags: `temporary_output`
- File count: `67`

### `data/reports/website_promotions`
- Decision: `KEEP_CANDIDATE`
- Reason: website promotion reports are durable audit metadata and should be tracked if not already
- Risk flags: `report_directory`
- File count: `26`

### `runtime/docs`
- Decision: `KEEP_CANDIDATE`
- Reason: runtime docs are durable documentation candidates
- Risk flags: `documentation_directory`
- File count: `1`

### `runtime/spark-battle-sim`
- Decision: `KEEP_CANDIDATE`
- Reason: Spark battle sim runtime package is source-like and should be preserved for plugin/site development
- Risk flags: `runtime_package`
- File count: `5`

## Next Lanes

### commit_misc_runtime_deeper_keep_artifacts_001
- TARGET: KEEP_CANDIDATE deeper misc runtime artifacts
- ACTION: stage only keep directories plus review metadata
- VERIFY: cached scope excludes _tmp and DaDudeKC promotion content
- COMMIT: Commit misc runtime deeper artifacts

### quarantine_tmp_runtime_artifacts_001
- TARGET: _tmp quarantine
- ACTION: move _tmp into runtime/quarantine with manifest
- VERIFY: original cleared and manifest references moved files
- COMMIT: Quarantine temporary runtime artifacts
