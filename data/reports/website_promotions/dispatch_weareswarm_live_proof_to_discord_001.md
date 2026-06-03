# Dispatch WeAreSwarm Live Proof To Discord 001

## Result

Attempted guarded dispatch of WeAreSwarm live proof feed card.

## Dispatch Status

`BLOCKED_MISSING_DISCORD_CLOSEOUT_WEBHOOK_URL`

## Inputs

- Dispatcher: `runtime/scripts/dispatch_closeout_feed_cards_001.py`
- Render dir: `data/reports/closeout_feed_rendered`
- Manifest: `data/reports/closeout_feed_dispatch/closeout_feed_dispatch_manifest_001.json`

## Guardrail

Live dispatch only runs when `DISCORD_CLOSEOUT_WEBHOOK_URL` is present.

## Status

BLOCKED_MISSING_DISCORD_CLOSEOUT_WEBHOOK_URL
