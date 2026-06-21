# Dream.OS websites — VPS deployment and dashboard runtime

Ubuntu 24.04 package for cloning the `websites` repo onto a VPS as a **public static surface** and **dashboard JSON export target**. This lives **beside** existing Hostinger SFTP deploy and GitHub Actions — it does not replace them.

## Repo confirmation

```bash
git remote -v
# origin  git@github.com:Victor-Dixon/websites.git
```

Expected clone location on VPS: `/opt/dreamos/repos/websites`

## Quick start (Ubuntu 24.04)

```bash
sudo apt-get update
sudo apt-get install -y python3 python3-yaml git nginx

git clone git@github.com:Victor-Dixon/websites.git /opt/dreamos/repos/websites
cd /opt/dreamos/repos/websites

bash deploy/vps/websites/scripts/install.sh
bash deploy/vps/websites/scripts/healthcheck.sh
bash deploy/vps/websites/scripts/preview.sh          # http://127.0.0.1:8080
bash deploy/vps/websites/scripts/export_dashboard_inputs.sh
```

Secrets and overrides: copy `deploy/vps/websites/.env.example` to `/opt/dreamos/secrets/websites.env` (created by `install.sh` on first run).

## Environment template

See `.env.example` for placeholders:

| Variable | Purpose |
|----------|---------|
| `DREAMOS_ROOT` | Dream.OS install root |
| `WEBSITES_REPO` | This repo on VPS |
| `DREAMVAULT_REPO` | DreamVault clone (future sync) |
| `DASHBOARD_RUNTIME_DIR` | Dream.OS health/dashboard JSON input |
| `PUBLIC_SITE_ROOT` | nginx/static publish root |
| `SITE_PREVIEW_PORT` | Local preview bind port |

Optional: `DASHBOARD_SITE_DATA_DIR`, `DASHBOARD_PUBLIC_DATA_DIR`, `SITE_PREVIEW_ROOT`, `VPS_REPORTS_DIR`.

## Expected folder layout (VPS)

```
/opt/dreamos/
├── repos/
│   └── websites/                 # this repo
├── runtime/
│   └── dashboard/                # Dream.OS JSON inputs (*.json)
└── secrets/
    └── websites.env              # NOT in git

/var/www/dreamos-sites/           # PUBLIC_SITE_ROOT (static + exported JSON)
├── data/dashboard/               # sanitized public dashboard JSON
└── weareswarm.online/
    └── data/planner/             # site-visible planner feeds
```

In-repo structure healthcheck expects:

```
websites/
├── ops/deployment/sites.yml      # Hostinger registry (unchanged)
├── websites/<domain>/            # per-site trees
├── public/                       # repo-level public JSON (e.g. kids_tasks.json)
└── deploy/vps/websites/          # this package
```

## Commands

| Script | Purpose |
|--------|---------|
| `scripts/install.sh` | Create dirs, chmod scripts, bootstrap `websites.env` |
| `scripts/healthcheck.sh` | Repo structure, sites, JSON validity, secret scan, symlinks |
| `scripts/preview.sh` | Static preview on `127.0.0.1:$SITE_PREVIEW_PORT` |
| `scripts/export_dashboard_inputs.sh` | Copy/sanitize dashboard JSON into public paths |

Reports: `reports/vps/healthcheck_*.json`, `reports/vps/export_dashboard_*.json`

## Nginx (manual)

Example only — **not auto-deployed**:

```bash
sudo cp deploy/vps/websites/nginx/dreamos-sites.conf.example \
    /etc/nginx/sites-available/dreamos-sites.conf
sudo ln -sf /etc/nginx/sites-available/dreamos-sites.conf \
    /etc/nginx/sites-enabled/dreamos-sites.conf
sudo nginx -t && sudo systemctl reload nginx
```

Serves static files from `/var/www/dreamos-sites` with optional `/data/` dashboard JSON routes.

## Coexistence with Hostinger / GitHub Actions

| Path | Role |
|------|------|
| `.github/workflows/deploy*.yml` | Hostinger SFTP deploy (preserved) |
| `ops/deployment/` | Registry-driven Hostinger deploy tools |
| `deploy/vps/websites/` | **New** VPS-side preview, health, dashboard export |

VPS preview and export do **not** require Windows paths, Termux paths, or Hostinger credentials.

## Future VPS dashboard path

Dream.OS runtime JSON lands in `$DASHBOARD_RUNTIME_DIR`. `export_dashboard_inputs.sh` sanitizes and publishes to:

- `$DASHBOARD_SITE_DATA_DIR` (site-specific planner mirror)
- `$DASHBOARD_PUBLIC_DATA_DIR` (aggregate `/data/` route)

Future webhook/API endpoints can read the same sanitized JSON tree under `/var/www/dreamos-sites/data/`.

## Security warning

**Never commit secrets** to the websites repo or to public site data. The export script strips keys matching `password`, `secret`, `token`, `api_key`, `webhook`, etc. Healthcheck scans `deploy/vps/` and `public/` for obvious secret patterns.

Real credentials belong only in `/opt/dreamos/secrets/websites.env` (gitignored pattern) and Hostinger `.env.deploy.local` on operator machines.

## Deployment assumption audit (2026-06-21)

| Finding | Location | VPS action |
|---------|----------|------------|
| `D:/websites` hardcoded | `ops/deployment/*.py`, `docs/deployment/*.json` | **Documented** — Hostinger deploy tools; unchanged |
| `D:/DreamVault` in planner manifest | `websites/weareswarm.online/data/planner/manifest.json` | **Normalized on export** by `export_dashboard_inputs.sh` |
| Termux paths | `data/reports/**` (historical closeout artifacts) | **Not used** by VPS scripts; healthcheck ignores report archives |
| Termux parity comment | `ops/deployment/deploy_weareswarm.ps1` | **Documented** — Windows operator script only |
| Hostinger SFTP | GHA workflows + `unified_deployer.py` | **Preserved** — VPS package is additive |

VPS scripts under `deploy/vps/websites/scripts/` use env vars only — no Windows, Termux, or Hostinger dependencies.
