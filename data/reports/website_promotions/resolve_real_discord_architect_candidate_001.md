# Resolve Real Discord Architect Candidate 001

- Status: `REAL_CANDIDATE_SELECTED`
- Candidate count: `664`

## Selected

- Path: `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/send_discord_paper_trade_payload.py`
- Score: `98`
- Executable: `True`
- Name hits: `send_discord`
- Content hits: `discord, webhook, send, payload, DISCORD`

## Top Candidates

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/send_discord_paper_trade_payload.py`
- Score: `98`
- Executable: `True`
- Name hits: `send_discord`
- Content hits: `discord, webhook, send, payload, DISCORD`
- L8: `PAYLOAD = Path("runtime/trading/discord/latest_paper_trade_payload.json")`
- L11: `    if not PAYLOAD.exists():`
- L12: `        print(f"CHECK_FAILED: missing payload {PAYLOAD}")`
- L15: `    payload = json.loads(PAYLOAD.read_text())`
- L16: `    webhook=REDACTED"DISCORD_WEBHOOK_URL", "").strip()`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/send_discord_journal_payload.py`
- Score: `90`
- Executable: `True`
- Name hits: `send_discord`
- Content hits: `discord, webhook, send, payload, DISCORD`
- L16: `from dreamvault.discord_delivery_retry_queue import append_retry_record`
- L17: `from dreamvault.discord_journal_cards import build_discord_webhook_payload`
- L18: `from dreamvault.discord_webhook_sender import send_discord_webhook`
- L22: `    parser = argparse.ArgumentParser(description="Send Dream.OS journal cards to Discord webhook.")`
- L25: `    parser.add_argument("--webhook-url", default=os.environ.get("DREAMOS_DISCORD_WEBHOOK_URL", ""))`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/configure_discord_architect_001.py`
- Score: `82`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, webhook, channel, payload, DISCORD`
- L20: `def create_webhook(bot_token=REDACTED channel_id: str, webhook_name: str) -> str:`
- L21: `    url = f"https://discord.com/api/v10/channels/{channel_id}/webhooks"`
- L24: `        data=json.dumps({"name": webhook_name}).encode("utf-8"),`
- L28: `            "User-Agent": "DreamOS-DiscordArchitect/001",`
- L34: `    return f"DISCORD_WEBHOOK_REDACTED'id']}/{data['token']}"`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/emit_latest_closeout_discord_architect_001.py`
- Score: `79`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, channel, payload, DISCORD`
- L17: `        raise SystemExit("DREAMCLOSE_DISCORD_EVENT=FAIL missing data/closeouts/latest.json and CPC latest")`
- L85: `target_channel = "master-task-log" if event_type == "master_task_closed" else "lane-closeouts"`
- L92: `    "target_channel": target_channel,`
- L98: `    "payload": packet,`
- L101: `event_dir = root / "discord_architect/data/runtime/events"`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/emit_planner_discord_architect_event_001.py`
- Score: `79`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, channel, payload, DISCORD`
- L27: `latest_task = read_text(root / "runtime/tasks/discord/planner_discord_architect_bridge_001.yaml", "")`
- L33: `    "target_channel": "master-task-log",`
- L49: `        "Source task: `planner_discord_architect_bridge_001`",`
- L51: `    "payload": {`
- L58: `event_dir = root / "discord_architect/data/runtime/events"`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/send_discord_trading_test_message.sh`
- Score: `72`
- Executable: `True`
- Name hits: `send_discord`
- Content hits: `discord, webhook, payload, DISCORD`
- L6: `if [ -z "${DISCORD_TRADING_WEBHOOK_URL:-}" ]; then`
- L7: `  echo "DISCORD_TEST=SKIPPED missing_DISCORD_TRADING_WEBHOOK_URL"`
- L11: `PAYLOAD="data/reports/trading/discord_trading_test_payload.json"`
- L12: `mkdir -p "$(dirname "$PAYLOAD")"`
- L14: `cat > "$PAYLOAD" << JSON`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/bootstrap_discord_architect_channel_webhook_manager_001.sh`
- Score: `70`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, webhook, channel, guild, send, DISCORD`
- L4: `TASK="runtime/tasks/discord/discord_architect_channel_webhook_manager_001.yaml"`
- L5: `MANAGER="discord_architect/src/runtime/channelWebhookManager.js"`
- L6: `TEST="discord_architect/tests/channelWebhookManager.test.js"`
- L7: `REPORT="data/reports/discord_architect/channel_webhook_manager_verification.md"`
- L12: `id: discord_architect_channel_webhook_manager_001`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/verify_discord_architect_integrity_001.sh`
- Score: `70`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, webhook, channel, guild, payload, DISCORD`
- L4: `echo "== VERIFY DISCORD ARCHITECT INTEGRITY =="`
- L6: `REPORT_DIR="data/reports/discord_architect"`
- L10: `if git ls-files | grep -q '^discord_architect/node_modules/'; then`
- L17: `test -f discord_architect/package-lock.json`
- L22: `  cd discord_architect`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/connect_capability_feed_to_discord_architect_001.sh`
- Score: `67`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, channel, send, payload, DISCORD`
- L4: `TASK="runtime/tasks/discord/connect_capability_feed_to_discord_architect_001.yaml"`
- L5: `ADAPTER="discord_architect/src/runtime/capabilityEvolutionFeedAdapter.js"`
- L6: `TEST="discord_architect/tests/capabilityEvolutionFeedAdapter.test.js"`
- L7: `REPORT="data/reports/discord/capability_evolution_feed/discord_architect_adapter_verification.md"`
- L12: `id: connect_capability_feed_to_discord_architect_001`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/discord_architect_create_channel_adapter_001.sh`
- Score: `67`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, channel, guild, payload, DISCORD`
- L6: `mkdir -p discord_architect/src/adapters discord_architect/tests runtime/tasks runtime/scripts data/reports/discord_architect`
- L8: `cat > runtime/tasks/discord_architect_create_channel_adapter_001.yaml << 'YAML'`
- L9: `task_id: discord_architect_create_channel_adapter_001`
- L10: `title: Add Discord Architect create-channel adapter`
- L13: `domain: discord_governance`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/inspect_discord_architect_channel_registry_001.sh`
- Score: `67`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, webhook, channel, guild, DISCORD`
- L4: `echo "== CHANNEL REGISTRY DISCOVERY =="`
- L6: `find discord_architect data runtime -type f 2>/dev/null \`
- L7: `  | grep -Ei 'channel|registry|manifest|webhook|runtime' \`
- L11: `env | grep -E 'DISCORD_(BOT|GUILD|CHANNEL|WEBHOOK)' | sed 's/=.*/=<set>/'`
- L13: `echo "CHANNEL_REGISTRY_DISCOVERY=PASS"`

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/inspect_discord_architect_registry_safe_001.sh`
- Score: `67`
- Executable: `True`
- Name hits: `discord_architect`
- Content hits: `discord, webhook, channel, guild, DISCORD`
- L5: `cat discord_architect/config/server_registry.json 2>/dev/null || true`
- L9: `  discord_architect/src/runtime/liveDiscordMutationRuntime.js \`
- L10: `  discord_architect/src/runtime/channelWebhookManager.js \`
- L11: `  discord_architect/src/runtime/ensureRuntimeChannels.js \`
- L12: `  discord_architect/config/server_registry.json`

## Help Results

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/send_discord_paper_trade_payload.py`
- Code: `2`
```text
CHECK_FAILED: missing payload runtime/trading/discord/latest_paper_trade_payload.json


```

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/send_discord_journal_payload.py`
- Code: `0`
```text
usage: send_discord_journal_payload.py [-h] [--journal JOURNAL]
                                       [--limit LIMIT]
                                       [--webhook-url WEBHOOK_URL] [--live]
                                       [--dry-run] [--timeout TIMEOUT]
                                       [--retry-queue RETRY_QUEUE]
                                       [--journal-attempt]

Send Dream.OS journal cards to Discord webhook.

options:
  -h, --help            show this help message and exit
  --journal JOURNAL
  --limit LIMIT
  --webhook-url WEBHOOK_URL
  --live                Actually post to Discord.
  --dry-run             Validate payload without posting.
  --timeout TIMEOUT
  --retry-queue RETRY_QUEUE
                        Path to Discord delivery retry queue JSONL.
  --journal-attempt     Append Discord delivery attempt result to execution
                        journal.


```

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/configure_discord_architect_001.py`
- Code: `0`
```text
usage: configure_discord_architect_001.py [-h] [--bot BOT] [--channel CHANNEL]
                                          [--create-webhook] [--test]
                                          [--dry-run]

Configure Discord Architect through Dream.OS Secret Broker

options:
  -h, --help         show this help message and exit
  --bot BOT
  --channel CHANNEL
  --create-webhook
  --test
  --dry-run


```

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/emit_latest_closeout_discord_architect_001.py`
- Code: `1`
```text

DREAMCLOSE_DISCORD_EVENT=FAIL missing data/closeouts/latest.json and CPC latest

```

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/emit_planner_discord_architect_event_001.py`
- Code: `1`
```text
PLANNER_DISCORD_ARCHITECT_EVENT=FAIL

node:internal/modules/cjs/loader:1478
  throw err;
  ^

Error: Cannot find module './discord_architect/src/runtime/liveCapabilityEventDispatcher.js'
Require stack:
- /data/data/com.termux/files/home/projects/websites/[eval]
    at Module._resolveFilename (node:internal/modules/cjs/loader:1475:15)
    at wrapResolveFilename (node:internal/modules/cjs/loader:1048:27)
    at defaultResolveImplForCJSLoading (node:internal/modules/cjs/loader:1072:10)
    at resolveForCJSWithHooks (node:internal/modules/cjs/loader:1093:12)
    at Module._load (node:internal/modules/cjs/loader:1261:25)
    at wrapModuleLoad (node:internal/modules/cjs/loader:255:19)
    at Module.require (node:internal/modules/cjs/loader:1575:12)
    at require (node:internal/modules/helpers:191:16)
    at [eval]:3:37
    at runScriptInThisContext (node:internal/vm:219:10) {
  code: 'MODULE_NOT_FOUND',
  requireStack: [ '/data/data/com.termux/files/home/projects/websites/[eval]' ]
}

Node.js v25.8.2



```

### `/data/data/com.termux/files/home/projects/DreamVault/runtime/scripts/send_discord_trading_test_message.sh`
- Code: `2`
```text
DISCORD_TEST=SKIPPED missing_DISCORD_TRADING_WEBHOOK_URL


```

## Guardrail

Resolution only. No dispatch. Webhook URLs redacted.

## Status

REAL_CANDIDATE_SELECTED