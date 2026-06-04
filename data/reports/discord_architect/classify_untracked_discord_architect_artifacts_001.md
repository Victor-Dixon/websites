# Untracked Discord Architect Artifact Classification

- Status: `PASS`
- Roots inspected: `8`
- Existing roots: `8`
- Total files: `12`
- Total bytes: `12862`

## Disposition Counts

- `REPORT_ARTIFACT_REVIEW`: `3`
- `LOCAL_DATA_REVIEW`: `1`
- `SOURCE_CANDIDATE_REVIEW`: `2`
- `TASK_ARTIFACT_REVIEW`: `1`
- `RUNTIME_OUTPUT_DO_NOT_STAGE`: `1`

## Root Inventory

### `data/reports/discord_architect/channel_webhook_manager/`

- Exists: `True`
- Kind: `directory`
- Files: `3`
- Bytes: `1567`
- Disposition: `REPORT_ARTIFACT_REVIEW`
- Next action: Review report content; stage only if it documents durable architecture state.
- Suffix counts: `{".json": 3}`

Sample files:
- `data/reports/discord_architect/channel_webhook_manager/test_channel_receipt.json` — `528` bytes
- `data/reports/discord_architect/channel_webhook_manager/test_live_blocked_receipt.json` — `552` bytes
- `data/reports/discord_architect/channel_webhook_manager/test_webhook_receipt.json` — `487` bytes

### `data/reports/discord_architect/channel_webhook_manager_verification.md`

- Exists: `True`
- Kind: `file`
- Files: `1`
- Bytes: `379`
- Disposition: `REPORT_ARTIFACT_REVIEW`
- Next action: Review report content; stage only if it documents durable architecture state.
- Suffix counts: `{".md": 1}`

Sample files:
- `data/reports/discord_architect/channel_webhook_manager_verification.md` — `379` bytes

### `data/reports/planner_discord_bridge/`

- Exists: `True`
- Kind: `directory`
- Files: `1`
- Bytes: `1194`
- Disposition: `REPORT_ARTIFACT_REVIEW`
- Next action: Review report content; stage only if it documents durable architecture state.
- Suffix counts: `{".json": 1}`

Sample files:
- `data/reports/planner_discord_bridge/latest_receipt.json` — `1194` bytes

### `discord_architect/data/`

- Exists: `True`
- Kind: `directory`
- Files: `2`
- Bytes: `1305`
- Disposition: `LOCAL_DATA_REVIEW`
- Next action: Treat as generated/local data unless proven to be fixture-worthy.
- Suffix counts: `{".json": 1, ".jsonl": 1}`

Sample files:
- `discord_architect/data/runtime/events/latest_planner_event.json` — `675` bytes
- `discord_architect/data/runtime/events/planner_events.jsonl` — `630` bytes

### `discord_architect/src/`

- Exists: `True`
- Kind: `directory`
- Files: `1`
- Bytes: `2939`
- Disposition: `SOURCE_CANDIDATE_REVIEW`
- Next action: Diff against committed adapter/bridge; salvage unique source/tests only after inspection.
- Suffix counts: `{".js": 1}`

Sample files:
- `discord_architect/src/runtime/channelWebhookManager.js` — `2939` bytes

### `discord_architect/tests/`

- Exists: `True`
- Kind: `directory`
- Files: `1`
- Bytes: `2548`
- Disposition: `SOURCE_CANDIDATE_REVIEW`
- Next action: Diff against committed adapter/bridge; salvage unique source/tests only after inspection.
- Suffix counts: `{".js": 1}`

Sample files:
- `discord_architect/tests/channelWebhookManager.test.js` — `2548` bytes

### `runtime/tasks/discord/`

- Exists: `True`
- Kind: `directory`
- Files: `1`
- Bytes: `1330`
- Disposition: `TASK_ARTIFACT_REVIEW`
- Next action: Promote useful task YAMLs into runtime/tasks/discord_architect or quarantine duplicates.
- Suffix counts: `{".yaml": 1}`

Sample files:
- `runtime/tasks/discord/discord_architect_channel_webhook_manager_001.yaml` — `1330` bytes

### `runtime/trading/`

- Exists: `True`
- Kind: `directory`
- Files: `2`
- Bytes: `1600`
- Disposition: `RUNTIME_OUTPUT_DO_NOT_STAGE`
- Next action: Keep ignored/untracked unless payload needs a tracked fixture; do not stage live output.
- Suffix counts: `{".json": 2}`

Sample files:
- `runtime/trading/discord/latest_cpc_closeout_bridge_payload.json` — `427` bytes
- `runtime/trading/discord/latest_paper_trade_payload.json` — `1173` bytes

## Next Lane

`salvage_or_quarantine_discord_architect_artifacts_001`
