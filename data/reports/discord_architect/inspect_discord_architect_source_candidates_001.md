# Discord Architect Source Candidate Inspection

- Status: `PASS`
- Source candidate: `discord_architect/src/runtime/channelWebhookManager.js`
- Test candidate: `discord_architect/tests/channelWebhookManager.test.js`
- Disposition: `QUARANTINE_OBSOLETE_OR_SEPARATE_JS_MANAGER`
- Rationale: Retained JS candidate appears to manage channel/webhook concerns, while committed architecture now uses Python adapter/bridge/live gate. No direct integration point proven.

## Candidate Inventory

### `discord_architect/src/runtime/channelWebhookManager.js`

- Bytes: `2939`
- Lines: `110`
- SHA256: `0aa28c964ebfec18ade4f372205159bf96aa2837daa464855b396aa8c39f4f48`
- Functions: `["buildChannelMutationPlan", "buildWebhookMutationPlan", "channelId", "channelKey", "channelName", "dispatchMutationPlan", "ensureChannel", "ensureDir", "ensureWebhook", "fs", "normalizeChannelName", "nowIso", "path", "plan", "receipt", "receiptPath", "webhookName", "writeJson"]`
- Requires: `["fs", "path"]`
- Env refs: `["DISCORD_CHANNEL_ID", "DISCORD_GUILD_ID"]`
- Keywords: `["channel", "discord", "dry", "manager", "send", "webhook"]`

### `discord_architect/tests/channelWebhookManager.test.js`

- Bytes: `2548`
- Lines: `77`
- SHA256: `4ca092b41cc8722a1edbb8a039e2e9c8bcf9f01a412eff4a506d1c070b5aa2b4`
- Functions: `["assert", "channelPlan", "channelReceipt", "channelReceiptPath", "fs", "liveReceipt", "webhookPlan", "webhookReceipt", "webhookReceiptPath"]`
- Requires: `["../src/runtime/channelWebhookManager", "assert", "fs"]`
- Env refs: `[]`
- Keywords: `["channel", "closeout", "discord", "dry", "manager", "webhook"]`

## Signals

- `js_has_webhook_manager`: `True`
- `js_has_dry_run`: `True`
- `js_has_closeout_bridge`: `False`
- `js_has_runtime_payload_path`: `False`
- `py_has_current_adapter`: `True`
- `test_mentions_webhook`: `True`
- `test_mentions_channel`: `True`

## Reference Python Implementation

- `discord_architect/invocation_adapter.py` functions: `["build_arg_parser", "invoke_selected_sender", "load_json", "main", "normalize_closeout_payload", "write_payload"]`
- `discord_architect/closeout_source_bridge.py` functions: `["build_parser", "canonicalize_cpc_payload", "find_latest_cpc_json", "load_json", "main", "run_adapter", "write_bridge_payload"]`

## Next Lane

`quarantine_discord_architect_source_candidates_001`
