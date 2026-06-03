# Discord Architect Webhook Runbook 001

## Objective

Provision a fresh intended Discord webhook for the Dream.OS closeout/proof feed and dispatch the WeAreSwarm live proof card.

## Current Blocker

Existing discovered webhook candidates validate but reject POST with `HTTP_403`.

## Required Manual/Architect Actions

1. Inventory Discord guild/channel targets.
2. Select or create intended channel:
   - Suggested purpose: Dream.OS closeout/proof feed.
   - Suggested channel name: `dreamos-closeouts` or `proof-feed`.
3. Create a fresh webhook for that channel.
4. Store webhook locally outside git:

```bash
mkdir -p "/data/data/com.termux/files/home/projects/websites/.cache/secure_runtime"
chmod 700 "/data/data/com.termux/files/home/projects/websites/.cache/secure_runtime"
cat > "/data/data/com.termux/files/home/projects/websites/.cache/secure_runtime/discord_closeout_webhook.env" << 'ENV'
export DISCORD_CLOSEOUT_WEBHOOK_URL='PASTE_WEBHOOK_URL_HERE'
ENV
chmod 600 "/data/data/com.termux/files/home/projects/websites/.cache/secure_runtime/discord_closeout_webhook.env"
```

5. Run guarded dispatch:

```bash
cd "/data/data/com.termux/files/home/projects/websites"
. "/data/data/com.termux/files/home/projects/websites/.cache/secure_runtime/discord_closeout_webhook.env"
python runtime/scripts/dispatch_closeout_feed_cards_001.py \
  --render-dir data/reports/closeout_feed_rendered \
  --out-dir data/reports/closeout_feed_dispatch \
  --send
```

## Success Criteria

- Dispatch manifest event status: `SENT`
- Webhook URL not printed
- Webhook URL not committed
- Redacted dispatch result committed only

## Guardrail

Do not paste webhook into chat. Do not commit `.cache/secure_runtime`.
