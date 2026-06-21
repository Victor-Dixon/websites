# Websites SSOT

Canonical consolidation root for DreamOS.ai website/domain rebuilds.

## Status

This folder replaces the old standalone website repos as the working SSOT.

## Domains

- `freerideinvestor`: media brand / trading discipline / content engine
- `tradingrobotplug`: trading tools / product demo / lead capture
- `dadudekc`: personal/local/community legacy brand

## Hostinger Upload Candidates

- `_hostinger_build/dist/freerideinvestor-content-engine-0.1.0.zip`
- `_hostinger_build/dist/dreamos-trading-tools-0.1.0.zip`

## Rules

<<<<<<< origin/master
- Rebuild themes from scratch.
- Preserve custom business logic as plugins.
- Do not deploy `source_review/`.
- Archive old GitHub repos only after deprecation manifest review.
=======
## Usage

### Deploy All Sites
```powershell
.\deployment\deploy.ps1 -All
```

### Deploy Specific Site
```powershell
.\deployment\deploy.ps1 -Site tradingrobotplug.com
```

### Deploy Specific Package
```powershell
.\deployment\deploy.ps1 -Package trading-robot-plugin
```

### Dry Run (see what would happen)
```powershell
.\deployment\deploy.ps1 -All -DryRun
```

## Package Development

### Creating a New Package Version
1. Create version directory: `packages/my-package/v1.1.0/`
2. Copy/update code in version directory
3. Update `package.json` with new version
4. Update site configs to use new version
5. Deploy: `.\deployment\deploy.ps1 -Package my-package`

### Package Structure
```
packages/my-package/
├── v1.0.0/           # ← Actual code lives here
├── v1.1.0/           # ← New versions
├── package.json      # ← Package metadata
└── README.md         # ← Package documentation
```

## Site Configuration

Each site has a `site-config.json` that defines:

```json
{
  "packages": {
    "plugins": {
      "trading-robot-plugin": "v1.0.0"
    }
  },
  "environment": {
    "type": "production"
  },
  "api": {
    "fastapi_url": "https://api.tradingrobotplug.com"
  }
}
```

## Development Workflow

1. **Develop** in packages (or repository for testing)
2. **Version** new releases in packages
3. **Update** site configs to use new versions
4. **Deploy** to live sites
5. **Monitor** and rollback if needed

## Benefits

### ✅ **Eliminates Duplication**
- No more duplicate plugin code across sites
- Single source of truth for shared functionality

### ✅ **Version Control**
- Semantic versioning for stability
- Easy rollbacks to previous versions
- Clear upgrade paths

### ✅ **Scalable**
- Easy to add new sites
- Easy to share packages between sites
- Easy to update multiple sites at once

### ✅ **Maintainable**
- Clear separation of concerns
- Automated deployment reduces errors
- Configuration-driven approach

## Migration Notes

### From: Duplicated Architecture
```
sites/
├── site1/wp-content/plugins/plugin/  # ← Duplicate code
├── site2/wp-content/plugins/plugin/  # ← Duplicate code
└── site3/wp-content/plugins/plugin/  # ← Duplicate code
```

### To: Package-Based Architecture
```
packages/
├── plugin/v1.0.0/                   # ← Single source of truth
sites/
├── site1/site-config.json           # ← Uses plugin v1.0.0
├── site2/site-config.json           # ← Uses plugin v1.0.0
└── site3/site-config.json           # ← Uses plugin v1.0.0
```

This architecture eliminates the duplication problem while maintaining flexibility and scalability! 🚀

## VPS deployment (Ubuntu 24.04)

The `deploy/vps/websites/` package prepares this repo for Dream.OS VPS runtime: static preview, health checks, and dashboard JSON export. It runs **beside** Hostinger SFTP deploy and GitHub Actions — workflows under `.github/workflows/` are unchanged.

### Install on VPS

```bash
git clone git@github.com:Victor-Dixon/websites.git /opt/dreamos/repos/websites
cd /opt/dreamos/repos/websites
bash deploy/vps/websites/scripts/install.sh
```

Edit `/opt/dreamos/secrets/websites.env` (from `deploy/vps/websites/.env.example`).

### VPS commands

```bash
bash deploy/vps/websites/scripts/healthcheck.sh
bash deploy/vps/websites/scripts/preview.sh              # http://127.0.0.1:8080
bash deploy/vps/websites/scripts/export_dashboard_inputs.sh
```

### Expected VPS layout

- Repo: `/opt/dreamos/repos/websites`
- Dream.OS dashboard JSON input: `/opt/dreamos/runtime/dashboard`
- Public static root: `/var/www/dreamos-sites`
- Exported dashboard JSON: `/var/www/dreamos-sites/data/dashboard`

See `deploy/vps/websites/README.md` for nginx example, folder layout, and audit notes.

**Warning:** Never commit secrets to `public/` or site `data/` folders. Use `/opt/dreamos/secrets/websites.env` on the VPS only.
>>>>>>> feat/dadudekc-spark-dashboard
