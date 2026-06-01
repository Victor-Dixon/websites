# Emergence Privacy-Safe Event Tracking 111

## Task
Add first-party privacy-safe tracking for generator and battle loop.

## Actions
- Added event tracking REST route.
- Added event summary REST route.
- Added character generator tracking hooks.
- Added battle simulator tracking hooks.
- Added raw-score-shaped payload rejection.
- Verified safe events accepted and unsafe events rejected.

## Verification
```text
INPUTS=PASS
PRIVACY_SAFE_EVENT_TRACKING_PATCH=PASS
STATIC_TRACKING_EVENTS=PASS
STATIC_TRACKING_PRIVACY_GUARDS=PASS
PLUGIN_TARBALLS=PASS
SCP_UPLOAD=PASS
EXISTING_PLUGIN_BACKUP=PASS
No syntax errors detected in wp-content/plugins/emergence-character-generator/emergence-character-generator.php
No syntax errors detected in wp-content/plugins/spark-battle-sim/spark-battle-sim.php
REMOTE_PHP_LINT=PASS
        'callback' => 'emergence_cg_track_event_rest',
function emergence_cg_track_event_rest($request) {
    <script id="dreamos-privacy-safe-event-tracking-inline">
    <script id="dreamos-battle-event-tracking-inline">
REMOTE_TRACKING_SOURCE=PASS
Success: Plugin already activated.
Success: Plugin already activated.
PLUGINS_ACTIVE=PASS
Success: The cache was flushed.
Success: Purged All!
LITESPEED_PURGE=PASS
REMOTE_DEPLOY=PASS
HTTP_EVENT=200
TRACK_EVENT_ACCEPTED=PASS
HTTP_EVENT=200
TRACK_PROMPT_COPY_ACCEPTED=PASS
HTTP_EVENT=400
TRACK_RAW_SCORE_REJECTED=PASS
HTTP_EVENT=400
TRACK_UNKNOWN_EVENT_REJECTED=PASS
HTTP_SUMMARY=200
TRACK_SUMMARY=PASS
TRACKING_NO_SECRET_LEAK=PASS
EMERGENCE_PRIVACY_SAFE_EVENT_TRACKING=PASS
HTTP_CHARACTER=200
HTTP_BATTLE=200
PUBLIC_CHARACTER_TRACKING_HOOK=PASS
PUBLIC_BATTLE_TRACKING_HOOK=PASS
PUBLIC_TRACKING_NO_RAW_SCORE_LEAK=PASS
```

## Commit
Add Emergence privacy-safe event tracking

## Status
PASS
