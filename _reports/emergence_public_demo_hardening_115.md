# Emergence Public Demo Hardening 115

## Task
Run/fix visible user path around scan completion, save, prompt copy, and battle start.

## Actions
- Added character page demo flow guide.
- Added progress strip for scan/style/prompt/save/battle.
- Added user-facing save/copy/battle guidance.
- Added battle page guide.
- Preserved tracking hooks.
- Verified scan reset guard is present.
- Verified no visible private mechanics leaks.

## Verification
```text
INPUTS=PASS
PUBLIC_DEMO_HARDENING_PATCH=PASS
STATIC_DEMO_HARDENING=PASS
STATIC_DEMO_PRIVACY_GUARDS=PASS
STATIC_DEMO_CTA_GUIDANCE=PASS
PLUGIN_TARBALLS=PASS
SCP_UPLOAD=PASS
EXISTING_PLUGIN_BACKUP=PASS
No syntax errors detected in wp-content/plugins/emergence-character-generator/emergence-character-generator.php
No syntax errors detected in wp-content/plugins/spark-battle-sim/spark-battle-sim.php
REMOTE_PHP_LINT=PASS
    <script id="dreamos-public-demo-hardening-inline">
          '<h2>Create, Save, Share, and Battle Your Spark</h2>',
    <script id="dreamos-battle-demo-hardening-inline">
          '<h2>Run the Matchup</h2>',
REMOTE_DEMO_HARDENING_SOURCE=PASS
Success: Plugin already activated.
Success: Plugin already activated.
PLUGINS_ACTIVE=PASS
Success: The cache was flushed.
Success: Purged All!
LITESPEED_PURGE=PASS
REMOTE_DEPLOY=PASS
HTTP_CHARACTER=200
HTTP_BATTLE=200
PUBLIC_CHARACTER_DEMO_GUIDE=PASS
PUBLIC_CHARACTER_SCAN_GUARD_PRESENT=PASS
PUBLIC_CHARACTER_SAVE_COPY_BATTLE_GUIDANCE=PASS
PUBLIC_BATTLE_DEMO_GUIDE=PASS
PUBLIC_DEMO_NO_RAW_SCORE_LEAK=PASS
EMERGENCE_PUBLIC_DEMO_HARDENING=PASS
HTTP_TRACK=200 event=character_started
HTTP_TRACK=200 event=battle_opened
TRACKING_SANITY=PASS
```

## Commit
Harden Emergence public demo flow

## Status
PASS
