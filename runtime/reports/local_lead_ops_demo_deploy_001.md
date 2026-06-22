# Local Lead Ops Demo — Public Deploy Proof

**Task ID:** `local_lead_ops_demo_deploy_001`  
**Date:** 2026-06-22  
**Owner:** Agent-2 (revenue lane)  
**Status:** PASS — `LOCAL_LEAD_OPS_PUBLIC_DEMO_LIVE`

## Summary

Deployed scoped `local-lead-ops/` demo to **weareswarm.site** via SFTP. Theme updated to match WeAreSwarm static surfaces (closeout-feed / feed nav pattern). No unrelated files deployed.

## Deploy Root

| Field | Value |
|-------|-------|
| Domain | `weareswarm.site` |
| Remote base | `domains/weareswarm.site/public_html/local-lead-ops/` |
| Method | Scoped SFTP via `unified_deployer.SimpleWordPressDeployer` |
| Source commit (demo) | `215e7d92` |
| Theme refresh | WeAreSwarm tokens + topnav (deployed with theme update) |

## Public URL

**Primary:** https://weareswarm.site/local-lead-ops/  
**Also live:** https://www.weareswarm.site/local-lead-ops/  
**Sample report:** https://weareswarm.site/local-lead-ops/sample-report.html

## Files Deployed (4 only)

| Local | Remote |
|-------|--------|
| `local-lead-ops/index.html` | `.../local-lead-ops/index.html` |
| `local-lead-ops/sample-report.html` | `.../local-lead-ops/sample-report.html` |
| `local-lead-ops/assets/local-lead-ops.css` | `.../local-lead-ops/assets/local-lead-ops.css` |
| `local-lead-ops/assets/local-lead-ops.js` | `.../local-lead-ops/assets/local-lead-ops.js` |

## Verification (2026-06-22)

| Check | Result |
|-------|--------|
| VERIFY=PASS_PUBLIC_LOCAL_LEAD_OPS_INDEX | 200 — pricing + disclaimer present |
| VERIFY=PASS_PUBLIC_LOCAL_LEAD_OPS_CSS | 200 |
| VERIFY=PASS_PUBLIC_LOCAL_LEAD_OPS_JS | 200 |
| VERIFY=PASS_PUBLIC_LOCAL_LEAD_OPS_SAMPLE_REPORT | 200 |
| VERIFY=PASS_PRICING_VISIBLE | $250 / $750 / $500/mo |
| VERIFY=PASS_NO_CLIENT_DATA_DISCLAIMER | Present on index |
| STATUS=LOCAL_LEAD_OPS_PUBLIC_DEMO_LIVE | PASS |

## Guards Honored

- No unrelated feed/portfolio/project/skill-tree files staged or deployed
- No outbound email/SMS/API keys added
- No real client names
- No Agent-1/3/4 lane changes

## Week 1 Proof Target

✅ One public demo link: https://weareswarm.site/local-lead-ops/  
✅ One report-style proof artifact: https://weareswarm.site/local-lead-ops/sample-report.html

## Manual Smoke (post-deploy)

1. Open https://weareswarm.site/local-lead-ops/
2. Confirm WeAreSwarm topnav + cyan/green theme matches feed pages
3. Submit demo form — expect “Demo lead captured”, zero network requests
4. Open sample report link
