# Phase 3 Cache Purge + Smoke Evidence (2026-03-24 UTC)

## Scope
Domains:
- weareswarm.online
- freerideinvestor.com
- tradingrobotplug.com

## 1) Privileged cache purge confirmation attempt
Attempted endpoint (unauthenticated):
- `https://<domain>/wp-admin/admin-ajax.php?action=litespeed_purge_all`

Result for all three domains:
- HTTP `400 Bad Request`
- No authenticated session/capability available in this environment
- Privileged purge **not confirmed**

Raw logs:
- `weareswarm.online_purge_attempt.txt`
- `freerideinvestor.com_purge_attempt.txt`
- `tradingrobotplug.com_purge_attempt.txt`

## 2) Post-attempt smoke checks (cache-busting)
Executed cache-busting homepage checks with timestamp query params.

Summary:
- `weareswarm.online`: HTTP 200, title `Home - weareswarm.online`, marker `wp-content`
- `freerideinvestor.com`: HTTP 200, title `freerideinvestor`, marker `wp-content`
- `tradingrobotplug.com`: HTTP 200, title `tradingrobotplug.com`, marker `wp-content`

Raw artifacts:
- `weareswarm.online_headers.txt`, `weareswarm.online_smoke.html`
- `freerideinvestor.com_headers.txt`, `freerideinvestor.com_smoke.html`
- `tradingrobotplug.com_headers.txt`, `tradingrobotplug.com_smoke.html`

## 3) Screenshot evidence attached (non-binary)
- See `SCREENSHOT_URLS_2026-03-24.md` for reproducible capture URLs for all three domains.

## 4) Block + exact unblock action
- **Block reason:** Missing authenticated privileges for WordPress/plugin/CDN cache purge operations.
- **Exact unblock action:** Provide privileged operator access (WP admin or CDN/API token), then run authenticated purge and log timestamps per domain.
