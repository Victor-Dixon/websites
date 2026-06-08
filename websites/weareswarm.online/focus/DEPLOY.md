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
- **Remote:** `public_html/weareswarm.online` on Hostinger (SFTP)

## Refresh planner data

```powershell
python D:\DreamVault\runtime\scripts\sync_planner_reports_to_weareswarm_online_001.py
```

Copies public-safe JSON from `D:\DreamVault\data\reports\planner\` when present; regenerates `spark_panel.json` and `manifest.json`.

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

```powershell
cd D:\websites
python ops\deployment\unified_deployer.py --site weareswarm.online
```

Or full pipeline:

```powershell
python ops\deployment\deployment_pipeline.py --site weareswarm.online
```

**Blocker (inventory):** `MISSING_FTP_ENV` — set `SWARMONLINE_*` / Hostinger FTP env per `docs/deployment/site_identity_map.json` before live upload.

## Verify

```powershell
curl -sI https://weareswarm.online/focus/
curl -s https://weareswarm.online/data/planner/manifest.json
curl -s https://weareswarm.online/.well-known/deploy.json
```

## Task lane

`DreamVault/runtime/tasks/weareswarm_focus_dashboard_001.yaml`
