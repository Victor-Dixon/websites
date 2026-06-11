# Focus Command Center — weareswarm.online deploy

**Domain:** `weareswarm.online` (addon domain — not `weareswarm.site`)

## Routes

| URL | File |
|-----|------|
| https://weareswarm.online/focus/ | `focus/index.html` |
| https://weareswarm.online/projects/ | `projects/index.html` |
| https://weareswarm.online/tasks/ | `tasks/index.html` |
| https://weareswarm.online/data/planner/*.json | Public planner bridge data |

## Local SSOT

- **Deploy root (sites.yml):** `D:\websites\websites\weareswarm.online`
- **Production overlays:** `D:\websites\sites\production\websites\weareswarm.online`
- **Remote:** `domains/weareswarm.online/public_html` on Hostinger (SFTP)

## Refresh planner data (agents — required after planner edits)

```powershell
python D:\DreamVault\runtime\scripts\publish_planner_to_weareswarm_001.py
```

Or:

```powershell
cd D:\websites
.\ops\deployment\auto_publish_planner.ps1
```

Pipeline: dynamic planner refresh (if module present) → `sync_planner_reports` `--local-only` → `unified_deployer` for `weareswarm.online`. Exit **2** = creds missing (local preview only).

Local sync only (no deploy):

```powershell
python D:\DreamVault\runtime\scripts\sync_planner_reports_to_weareswarm_online_001.py --local-only
```

## Deploy manifest

Static Command Center files are **not** WordPress themes. `unified_deployer.py` uploads them via `deploy_files` + `deploy_dirs` in:

- `ops/deployment/sites.yml` (registry SSOT)
- `config/site_configs.json` (credentials + same manifest; merged at runtime)

Pattern matches `xthunder.site` (`deploy_files` list). Directories under `websites/weareswarm.online/`:

- `index.html`, `.well-known/deploy.json`
- `focus/`, `projects/`, `tasks/`, `data/planner/`, `data/shared/`

Dry-run (no SFTP):

```powershell
cd D:\websites
python ops\deployment\unified_deployer.py --site weareswarm.online --dry-run
```

## Deploy

**Recommended (secure creds):**

```powershell
cd D:\websites
.\ops\deployment\deploy_weareswarm.ps1
```

First run prompts for Hostinger SFTP password (`Read-Host -AsSecureString`). Save locally with:

```powershell
.\ops\deployment\deploy_weareswarm.ps1 -SaveCreds
```

Sync planner data then deploy:

```powershell
.\ops\deployment\deploy_weareswarm.ps1 -SyncFirst
```

**Direct (uses `config/site_configs.json` SFTP or `HOSTINGER_*` env):**

```powershell
cd D:\websites
python ops\deployment\unified_deployer.py --site weareswarm.online
```

### Credentials

| Source | Keys | Notes |
|--------|------|-------|
| `D:\websites\.env.deploy.local` | `HOSTINGER_HOST`, `HOSTINGER_USER`, `HOSTINGER_PASS`, `HOSTINGER_PORT` | **Preferred** — gitignored; copy from `.env.deploy.example` |
| `D:\Agent_Cellphone_V2_Repository\.env` | `HOSTINGER_*` | Shared Hostinger account |
| `config/site_configs.json` | `weareswarm.online.sftp.*` | Fallback if env unset |
| WordPress REST (optional) | `SWARMONLINE_WP_*` | Not used for static SFTP deploy |

`unified_deployer.py` does **not** prompt interactively — use `deploy_weareswarm.ps1` or set env before running Python.

Or full pipeline:

```powershell
python ops\deployment\deployment_pipeline.py --site weareswarm.online
```

## Verify

```powershell
curl -sI https://weareswarm.online/focus/
curl -s https://weareswarm.online/data/planner/manifest.json
curl -s https://weareswarm.online/.well-known/deploy.json
```

## Task lane

`DreamVault/runtime/tasks/weareswarm_focus_dashboard_001.yaml`
