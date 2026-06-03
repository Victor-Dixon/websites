# Discord Closeout Webhook Provisioning Packet 001

## Status

`DISCORD_ARCHITECT_WEBHOOK_PROVISIONING_REQUIRED`

## Blocker

Existing discovered webhook candidates validate but reject posting with HTTP_403.

## Required Webhook

- Env var: `DISCORD_CLOSEOUT_WEBHOOK_URL`
- Purpose: Dream.OS closeout/proof feed for WeAreSwarm + GitHub Architect lane summaries

## Discord Architect Actions

- Inventory available Discord guilds/channels without posting.
- Select or create an intended closeout/proof channel.
- Create a fresh webhook for that channel.
- Store webhook URL in local secret env file or secure runtime env, not in git.
- Export DISCORD_CLOSEOUT_WEBHOOK_URL only in process for dispatch.
- Run one guarded dispatch of WeAreSwarm proof card.
- Commit only redacted manifest/report.

## Replay Command

```bash
DISCORD_CLOSEOUT_WEBHOOK_URL=<secret> python runtime/scripts/dispatch_closeout_feed_cards_001.py --render-dir data/reports/closeout_feed_rendered --out-dir data/reports/closeout_feed_dispatch --send
```

## Guardrails

- Do not print webhook URL.
- Do not commit webhook URL.
- Do not send to random discovered webhook candidates.
- Only send after Discord Architect confirms intended target channel.
- Commit redacted validation/dispatch result only.

## Source Artifacts

- `data/reports/closeout_feed_dispatch/discord_webhook_failover_dispatch_001.json`
- `data/reports/closeout_feed_dispatch/discord_webhook_validation_001.json`
- `data/reports/website_promotions/dispatch_weareswarm_with_valid_webhook_failover_001.md`
- `data/reports/website_promotions/repair_discord_dispatch_http_error_handling_001.md`

## Status

PROVISIONING_PACKET_READY