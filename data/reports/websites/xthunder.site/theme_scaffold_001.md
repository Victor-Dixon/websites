# xthunder.site Theme Scaffold

Generated: 2026-06-04

## Summary

| Field | Value |
|---|---|
| Domain | xthunder.site |
| SSOT root | `sites/production/websites/xthunder.site` |
| App type | static_html |
| Remote root (audit) | `/home/u996867598/domains/xthunder.site/public_html` |
| Deploy registry | `ops/deployment/sites.yml` (`enabled: false`) |
| Live reference | https://xthunder.site/ |

## Scaffolded files

- `index.html` — bakery landing (matches live headline/copy)
- `site-config.json` — domain metadata and deploy gate

## Salvaged remote artifacts

- `_reports/website_audit/xthunder.site__audit.md`
- `data/reports/discord_architect/` (worklog spine)
- `data/reports/closeout_feed_dispatch/`
- `data/reports/closeout_feed_rendered/`

## Audit findings (salvaged)

- HTTPS/HTTP 200; homepage title `xthunder`
- Remote inventory empty at audit time; classify as rebuild/static deploy lane
- SSH root write flagged for follow-up before live deploy

## Recommended next action

1. Commit scaffold + salvaged artifacts (no history merge).
2. Run `python ops/deployment/unified_deployer.py --site xthunder.site --dry-run`.
3. Enable `xthunder.site` in `sites.yml` only after dry-run + SFTP creds verified.
4. Do not force-push or hard-reset `D:\websites` master.
