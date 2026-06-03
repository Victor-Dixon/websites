# Repair WeAreSwarm Root Htaccess Redirect 001

## Result

Patched root .htaccess so `https://weareswarm.site/` redirects to `/dreamos-services/`.

## Verification

- Root before: `500`
- Root after: `200`
- Root content after: PASS
- Route before: `200`
- Route after: `200`
- Route content after: PASS

## Guardrail

- Backed up root .htaccess to `.htaccess.dreamos_root_redirect_backup_001`
- Added root-only redirect
- Did not modify `/dreamos-services/index.html`

## Status

ROOT_LIVE_PASS
