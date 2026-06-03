# Inspect Closeout Feed Dispatcher Targets 001

- Generated: 2026-06-03T18:17:01
- Status: DISPATCH_TARGET_CANDIDATES_FOUND
- Feed: `runtime/feeds/closeouts/weareswarm_live_proof_001.json`
- Match count: `83`

## Guardrail

No dispatch performed. Inventory only. Do not send Discord/GitHub messages yet.

## Selected Candidate

- Path: `runtime/tasks/add_weareswarm_live_proof_feed_card_001.yaml`
- Score: `14`
- Hits: `discord, closeout, github architect, feed, weareswarm`

## Top Candidates

### `runtime/tasks/add_weareswarm_live_proof_feed_card_001.yaml`
- Score: `14`
- Hits: `discord, closeout, github architect, feed, weareswarm`
- L1: `id: add_weareswarm_live_proof_feed_card_001`
- L2: `title: Add WeAreSwarm live proof feed card`
- L4: `lane: closeout_feed`
- L8: `  Convert committed WeAreSwarm deploy proof artifacts into a durable feed card for`
- L9: `  Discord/GitHub Architect ingestion.`
- L12: `  Feed card must only state claims backed by committed proof artifacts.`

### `data/reports/website_promotions/weareswarm_live_proof_feed_card_001.json`
- Score: `13`
- Hits: `discord, github architect, dispatch, feed, weareswarm`
- L2: `  "id": "weareswarm_live_proof_001",`
- L5: `  "project": "WeAreSwarm DreamOS Services Funnel",`
- L8: `  "live_root": "https://weareswarm.site/",`
- L9: `  "live_route": "https://weareswarm.site/dreamos-services/",`
- L12: `    "WeAreSwarm root routes to Dream.OS services funnel",`
- L14: `    "Hostinger passwordless SSH alias ready: hostinger-weareswarm",`

### `runtime/feeds/closeouts/weareswarm_live_proof_001.json`
- Score: `13`
- Hits: `discord, github architect, dispatch, feed, weareswarm`
- L2: `  "id": "weareswarm_live_proof_001",`
- L5: `  "project": "WeAreSwarm DreamOS Services Funnel",`
- L8: `  "live_root": "https://weareswarm.site/",`
- L9: `  "live_route": "https://weareswarm.site/dreamos-services/",`
- L12: `    "WeAreSwarm root routes to Dream.OS services funnel",`
- L14: `    "Hostinger passwordless SSH alias ready: hostinger-weareswarm",`

### `data/reports/website_promotions/weareswarm_live_proof_feed_card_001.md`
- Score: `9`
- Hits: `discord, github architect, feed, weareswarm`
- L1: `# WeAreSwarm Live Proof Feed Card 001`
- L6: `- Root: `https://weareswarm.site/``
- L7: `- Route: `https://weareswarm.site/dreamos-services/``
- L8: `- SSH alias: `hostinger-weareswarm``
- L13: `- WeAreSwarm root routes to Dream.OS services funnel`
- L15: `- Hostinger passwordless SSH alias ready: hostinger-weareswarm`

### `runtime/scripts/build_freerideinvestor_salvage_manifest_001.py`
- Score: `8`
- Hits: `discord`
- L155: `        "- Workflow core: intake → replay → scorecard → rule candidate → Discord/operator card.",`

### `runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh`
- Score: `7`
- Hits: `closeout`
- L65: `echo "== CLOSEOUT =="`

### `runtime/scripts/collect_hostinger_custom_assets_045.sh`
- Score: `7`
- Hits: `closeout`
- L203: `echo "== CLOSEOUT =="`

### `data/reports/marketing/freerideinvestor_salvage_manifest_001.json`
- Score: `6`
- Hits: `discord`
- L641: `      "path": "FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-discord.php",`

### `data/reports/marketing/freerideinvestor_salvage_manifest_001.md`
- Score: `6`
- Hits: `discord`
- L51: `- Workflow core: intake → replay → scorecard → rule candidate → Discord/operator card.`

### `runtime/scripts/classify_freeride_salvage_promotion_candidates_001.py`
- Score: `6`
- Hits: `webhook`
- L21: `    "password", "token", "secret", "api_key", "webhook"`

### `runtime/tasks/add_emergence_generated_spark_portrait_card_090.yaml`
- Score: `5`
- Hits: `closeout`
- L140: `closeout:`

### `runtime/tasks/marketing/freerideinvestor_ai_trading_journal_showcase_047.yaml`
- Score: `5`
- Hits: `closeout`
- L26: `closeout:`

## Next Action

Review top target and wire runtime/feeds/closeouts/*.json into dispatcher dry-run.
