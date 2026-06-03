# Diagnose WeAreSwarm Domain Health 001

- Generated: `2026-06-03T17:35:28`
- Status: `REPAIR_TARGET_SELECTED`
- Reason: No clean domain exists; selected best repair target using repo lane fit and root evidence.
- Selected domain: `weareswarm.site`
- Selected remote root: `/home/u996867598/domains/weareswarm.site/public_html`
- Selected route file: `/home/u996867598/domains/weareswarm.site/public_html/dreamos-services/index.html`
- Selected route URL: `https://weareswarm.site/dreamos-services/`

## Guardrail

No upload performed. Repair or upload only to /dreamos-services/index.html after route target is confirmed.

## Diagnostics

### `weareswarm.site`
- Score: `5`
- Reasons: `site_domain_matches_current_site_lane, remote_root_evidence_present, current_http_error_present`
- Remote root: `/home/u996867598/domains/weareswarm.site/public_html`
- Route file: `/home/u996867598/domains/weareswarm.site/public_html/dreamos-services/index.html`

Current results:
- `https://weareswarm.site/` -> `500` effective=`https://weareswarm.site/`
- `http://weareswarm.site/` -> `500` effective=`https://weareswarm.site/`

Prior report signals:
- `_reports/website_audit/http_500_root_causes/weareswarm.site__500_root_cause.md` exists=`True`
  - L1: `# HTTP 500 Root Cause: weareswarm.site`
  - L5: `- Root cause: `wordpress_install_returning_500``
  - L6: `- Recommendation: `run_wp_cli_health_check_or_restore_wordpress``
  - L17: `DOMAIN=weareswarm.site`
  - L18: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.site/public_html`
  - L21: `/home/u996867598/domains/weareswarm.site/public_html`
- `_reports/website_audit/http_500_root_causes/weareswarm.site__remote_500_audit.txt` exists=`True`
  - L1: `DOMAIN=weareswarm.site`
  - L2: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.site/public_html`
  - L5: `/home/u996867598/domains/weareswarm.site/public_html`
  - L24: `drwxr-xr-x  3 u996867598 o1008028115  4096 May 10 16:00 public_html`
  - L52: `drwxr-xr-x u996867598 o1008028115 ./public_html`
  - L93: ` * Front to the WordPress application. This file doesn't do anything, but loads`
- `_reports/website_audit/weareswarm.site__audit.md` exists=`True`
  - L9: `| Domain | weareswarm.site |`
  - L11: `| Remote Root | `/home/u996867598/domains/weareswarm.site/public_html` |`
  - L14: `| HTTPS Status | 500 |`
  - L15: `| HTTP Status | 500 |`
  - L29: `- HTTPS: https://weareswarm.site/ => 500`
  - L30: `- HTTP: http://weareswarm.site/ => 500`

### `weareswarm.online`
- Score: `2`
- Reasons: `remote_root_evidence_present, current_http_error_present`
- Remote root: `/home/u996867598/domains/weareswarm.online/public_html`
- Route file: `/home/u996867598/domains/weareswarm.online/public_html/dreamos-services/index.html`

Current results:
- `https://weareswarm.online/` -> `500` effective=`https://weareswarm.online/`
- `http://weareswarm.online/` -> `500` effective=`https://weareswarm.online/`

Prior report signals:
- `_reports/website_audit/http_500_root_causes/weareswarm.online__500_root_cause.md` exists=`True`
  - L1: `# HTTP 500 Root Cause: weareswarm.online`
  - L5: `- Root cause: `wordpress_install_returning_500``
  - L6: `- Recommendation: `run_wp_cli_health_check_or_restore_wordpress``
  - L17: `DOMAIN=weareswarm.online`
  - L18: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.online/public_html`
  - L21: `/home/u996867598/domains/weareswarm.online/public_html`
- `_reports/website_audit/http_500_root_causes/weareswarm.online__remote_500_audit.txt` exists=`True`
  - L1: `DOMAIN=weareswarm.online`
  - L2: `REMOTE_ROOT=/home/u996867598/domains/weareswarm.online/public_html`
  - L5: `/home/u996867598/domains/weareswarm.online/public_html`
  - L77: ` * Front to the WordPress application. This file doesn't do anything, but loads`
  - L78: ` * wp-blog-header.php which does and tells WordPress to load the theme.`
  - L80: ` * @package WordPress`
- `_reports/website_audit/weareswarm.online__audit.md` exists=`True`
  - L9: `| Domain | weareswarm.online |`
  - L11: `| Remote Root | `/home/u996867598/domains/weareswarm.online/public_html` |`
  - L14: `| HTTPS Status | 500 |`
  - L15: `| HTTP Status | 500 |`
  - L29: `- HTTPS: https://weareswarm.online/ => 500`
  - L30: `- HTTP: http://weareswarm.online/ => 500`
