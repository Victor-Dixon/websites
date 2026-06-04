# xthunder.site — go live

## Local preview

```bash
cd sites/production/websites/xthunder.site
python -m http.server 8787
```

Open http://localhost:8787

## Deploy to Hostinger (SFTP)

From repo root `D:\websites`:

```bash
# Preview files + remote targets
python ops/deployment/unified_deployer.py --site xthunder.site --dry-run

# Upload index.html + assets/
python ops/deployment/unified_deployer.py --site xthunder.site
```

Config: `config/site_configs.json` → `xthunder.site.sftp`  
Registry: `ops/deployment/sites.yml` → `enabled: true`

## Verify live

https://xthunder.site/
