# XThunder Discord Webhook Precedent 001

- Generated: 2026-06-03T18:43:04
- Status: `PRECEDENT_FOUND`
- Match count: `2121`

## Current Blocker

Existing discovered Discord webhook candidates validate but reject POST with HTTP_403.

## Guardrail

No webhook URLs printed or committed.

## Selected Precedent

- Path: `../DreamVault/data/reports/repo_fleet/generated_report_governance_audit_001.json`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`

## Top Matches

### `../DreamVault/data/reports/repo_fleet/generated_report_governance_audit_001.json`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L19: `    "discord": 1403,`
- L26: `    "xthunder": 28`
- L52: `        "discord": 6,`
- L76: `          "data/reports/discord/created_trading_webhook_redacted.json",`
- L77: `          "data/reports/discord/discord_channel_inventory.json",`

### `../DreamVault/data/reports/repo_fleet/generated_report_governance_audit_001.md`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L19: `- discord: `1403``
- L28: `- xthunder: `28``
- L43: `- branch: `feature/discord-runtime-cockpit-001``
- L71: `- `data/reports/ops/closeout_discord_spine_20260602_063559.md``
- L72: `- `data/reports/ops/closeout_discord_spine_20260602_063753.md``

### `../DreamVault/data/reports/trading/discord_architect_cleanup_manifest_001.md`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L1: `# Discord Architect Cleanup Manifest`
- L4: `Source: `data/reports/trading/discord_architect_full_candidate_audit_001.json``
- L23: `- score=151 path=`runtime/scripts/send_discord_paper_trade_payload.py` reasons=current_best_primary_provider`
- L24: `- score=85 path=`runtime/scripts/ops/stabilize_closeout_discord_spine_001.sh` reasons=existing_closeout_sender`
- L25: `- score=61 path=`runtime/scripts/audit_discord_architect_candidate_duplicates_001.py` reasons=existing_closeout_sender`

### `../DreamVault/data/reports/trading/discord_architect_full_candidate_audit_001.md`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L1: `# Full Discord Architect Candidate Audit`
- L15: `- score=151 dup=1 path=`runtime/scripts/send_discord_paper_trade_payload.py` reasons=current_best_primary_provider`
- L16: `- score=85 dup=1 path=`runtime/scripts/ops/stabilize_closeout_discord_spine_001.sh` reasons=existing_closeout_sender`
- L17: `- score=61 dup=1 path=`runtime/scripts/audit_discord_architect_candidate_duplicates_001.py` reasons=existing_closeout_sender`
- L18: `- score=36 dup=1 path=`runtime/scripts/audit_discord_architect_full_candidate_set_001.py` reasons=existing_closeout_sender`

### `../DreamVault/data/reports/trading/generated_discord_report_quarantine_plan_001.json`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L4: `  "source_manifest": "data/reports/trading/discord_architect_cleanup_manifest_001.json",`
- L5: `  "quarantine_root": "runtime/quarantine/generated_discord_reports_001",`
- L11: `      "source": "data/reports/runtime_governance/active_discord_event_runtime_classification_001.md",`
- L12: `      "target": "runtime/quarantine/generated_discord_reports_001/data/reports/runtime_governance/active_discord_event_runtime_classification_001.md",`
- L21: `      "target": "runtime/quarantine/generated_discord_reports_001/data/reports/cpc/cpc_20260518_171352_27718.txt",`

### `../DreamVault/data/reports/trading/generated_discord_report_quarantine_plan_002.json`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L4: `  "source_manifest": "data/reports/trading/discord_architect_cleanup_manifest_001.json",`
- L5: `  "quarantine_root": "runtime/quarantine/generated_discord_reports_002",`
- L12: `      "target": "runtime/quarantine/generated_discord_reports_002/data/reports/cpc/cpc_20260518_171352_27718.txt",`
- L22: `      "target": "runtime/quarantine/generated_discord_reports_002/data/reports/cpc/cpc_20260522_102007_26603.txt",`
- L32: `      "target": "runtime/quarantine/generated_discord_reports_002/data/reports/cpc/cpc_20260522_074724_11473.txt",`

### `../DreamVault/data/reports/xthunder/xthunder_discord_source_spine_002.json`
- Score: `32`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L7: `      "path": "runtime/archive/temp_run_scripts_20260510_044539/runtime/scripts/run_add_discord_webhook_sender_050.sh",`
- L11: `          "pattern": "discord",`
- L15: `          "pattern": "webhook",`
- L33: `      "path": "runtime/scripts/build_dreamos_secret_broker_discord_001.sh",`
- L37: `          "pattern": "discord",`

### `../DreamVault/.pytest_cache/v/cache/nodeids`
- Score: `30`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L2: `  "tests/closeout/test_closeout_discord_dispatch.py::test_dispatch_script_exists",`
- L3: `  "tests/closeout/test_closeout_discord_dispatch.py::test_dream_cli_closeout_invokes_dispatcher",`
- L4: `  "tests/closeout/test_closeout_discord_dispatch.py::test_dry_run_builds_valid_closeout_payload",`
- L5: `  "tests/closeout/test_closeout_discord_dispatch.py::test_missing_closeout_can_fail_in_strict_mode",`
- L6: `  "tests/closeout/test_closeout_discord_dispatch.py::test_payload_contains_closeout_reference",`

### `../DreamVault/data/reports/cpc/cpc_20260602_190309_24712.txt`
- Score: `30`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L7: `command=/data/data/com.termux/files/home/.cache/refine_xthunder_discord_source_spine_002.sh`
- L24: `== SCORE DISCORD SOURCE FILES ==`
- L26: `# XThunder Discord Source Spine Discovery 002`
- L29: `Refine Discord Architect discovery to tracked source files only.`
- L32: `- XThunder task exists: `True``

### `../DreamVault/data/reports/cpc/cpc_20260603_065926_19815.txt`
- Score: `30`
- Hits: `xthunder, discord, webhook, channel, planner, architect`
- L7: `command=/data/data/com.termux/files/home/.cache/summarize_discord_full_candidate_audit_001.sh`
- L21: `- path=`data/reports/discord/event_routing/discord_event_routing_audit_001.json` reasons=duplicate_hash_group=2, generated_report_or_bundle`
- L22: `- path=`data/reports/discord/event_routing/discord_event_routing_audit_001.md` reasons=duplicate_hash_group=2, generated_report_or_bundle`
- L27: `- path=`data/reports/trading/discord_manager_webhook_discovery_036c.md` reasons=generated_report_or_bundle`
- L30: `- path=`data/reports/xthunder/dispatch_discord_cli_inspect_008.txt` reasons=generated_report_or_bundle`

## Next Action

Use Discord Architect to inventory channels and provision a fresh intended closeout webhook.

## Status

PRECEDENT_PACKET_READY