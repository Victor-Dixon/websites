# Validate Discord Webhooks And Dispatch 001

## Result

Validated discovered Discord webhook candidates and ran guarded dispatch for the WeAreSwarm live proof card.

## Status

- Validation status: `WEBHOOK_VALID_SELECTED`
- Dispatch status: `DISPATCH_ATTEMPTED`
- Event status: `HTTP_403`

## Artifacts

- Validation manifest: `data/reports/closeout_feed_dispatch/discord_webhook_validation_001.json`
- Dispatch manifest: `data/reports/closeout_feed_dispatch/closeout_feed_dispatch_manifest_001.json`

## Guardrail

Webhook URLs were not printed or committed.

## Interpretation

- `SENT`: Discord accepted the closeout card.
- `HTTP_403`: webhook was reachable but forbidden for posting.
- `BLOCKED_NO_VALID_WEBHOOK`: no candidate passed validation.

## Status

HTTP_403
