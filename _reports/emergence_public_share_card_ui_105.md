# Emergence Public Share Card UI 105

## Task
Make saved character/share links readable and user-facing after dossier save.

## Actions
- Added saved character share card UI.
- Added reload/share/battle link rows.
- Added copy buttons.
- Added battle link CTA.
- Verified REST save result returns reload and battle links.
- Verified no visible raw score leaks.

## Verification
```text
INPUTS=PASS
PUBLIC_SHARE_CARD_UI_PATCH=PASS
STATIC_SHARE_CARD_SAFE=PASS
STATIC_COPY_UI_PRESENT=PASS
PLUGIN_TARBALL=PASS /data/data/com.termux/files/home/projects/websites/_reports/emergence-character-generator_105.tar.gz
SCP_UPLOAD=PASS
EXISTING_PLUGIN_BACKUP=PASS
No syntax errors detected in wp-content/plugins/emergence-character-generator/emergence-character-generator.php
REMOTE_PHP_LINT=PASS
    <script id="dreamos-public-share-card-ui-inline">
          linkRow('Reload Character', links.reload, 'Copy Reload Link'),
          linkRow('Share Character', links.share, 'Copy Share Link'),
          linkRow('Open in Battle Simulator', links.battle, 'Copy Battle Link'),
REMOTE_SHARE_CARD_SOURCE=PASS
Success: Plugin already activated.
PLUGIN_ACTIVE=PASS
Success: The cache was flushed.
Success: Purged All!
LITESPEED_PURGE=PASS
REMOTE_DEPLOY=PASS
HTTP_FETCH=200 url=https://maskzero.site/character-generator/?dreamos_smoke=105
PUBLIC_SHARE_CARD_INLINE=PASS
PUBLIC_COPY_UI_PRESENT=PASS
PUBLIC_SHARE_CARD_NO_RAW_SCORE_LEAK=PASS
HTTP_JSON=200 url=https://maskzero.site/wp-json/emergence/v1/characters?dreamos_smoke=105
SAVE_RESULT_LINKS=PASS
SAVE_RESULT_RELOAD_LINK=PASS
SAVE_RESULT_BATTLE_LINK=PASS
SAVE_RESULT_NO_RAW_SCORE_LEAK=PASS
EMERGENCE_PUBLIC_SHARE_CARD_UI=PASS
```

## Commit
Add Emergence public share card UI

## Status
PASS
