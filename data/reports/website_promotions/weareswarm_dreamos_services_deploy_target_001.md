# WeAreSwarm DreamOS Services Deploy Target 001

- Generated: `2026-06-03T17:21:15`
- Status: `DEPLOY_TARGET_REVIEW_READY`
- Canonical host: `weareswarm`
- Canonical path: `/dreamos-services`
- Source route: `routes/weareswarm/dreamos-services/index.html`

## Recommendation

- Deploy mode: `static_page_upload_or_mirror`
- Source file: `routes/weareswarm/dreamos-services/index.html`
- Target path: `weareswarm:/dreamos-services/index.html`
- Guardrail: Do not overwrite live homepage; deploy as route/page only.

## Candidate Paths

- `runtime/deploy` exists=`True` files=`63`
- `runtime/scripts` exists=`True` files=`32`
- `data/reports/website_promotions` exists=`True` files=`39`
- `_hostinger_build` exists=`True` files=`69`

## Next Lane

- TARGET: package deploy artifact
- ACTION: copy route page into deploy package for live WeAreSwarm upload
- VERIFY: deploy package contains index.html and manifest
- COMMIT: Package WeAreSwarm DreamOS services deploy artifact
