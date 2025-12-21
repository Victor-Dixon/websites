# Repo organization (websites/themes/plugins)

This repo contains multiple website codebases, WordPress themes, WordPress plugins, and generated deployment artifacts.

## Canonical inventory

The canonical list of websites is in `configs/sites_registry.json` (currently **11 sites**).

## Current reality (legacy layout)

Right now, themes/plugins are stored in multiple patterns:

- Site root holds theme files and a `plugins/` subtree (example: `FreeRideInvestor/`)
- Site contains `wordpress-theme/<theme>/` (example: `ariajet.site/wordpress-theme/...`, `prismblossom.online/wordpress-theme/...`)
- Site contains `wp-content/themes/...` (example: `Swarm_website/wp-content/themes/...`)
- Generated “overlays” and deployment-ready snippets live under `sites/<domain>/...` (example: `sites/crosbyultimateevents.com/wp/theme/...`, `sites/crosbyultimateevents.com/wp/plugins/...`)
- Shared plugins (not tied to one site) live under `wordpress-plugins/`

There is also an **auto-deploy hook** at `tools/auto_deploy_hook.py` that currently maps a few legacy top-level directories to live sites.

## Target standard (recommended)

To make this repo predictable, the target standard is:

```
websites/
  <domain>/
    docs/                      # site-specific docs, grade cards, notes
    wp/                        # WordPress payload (if WP site)
      wp-content/
        themes/
          <theme-name>/
        plugins/
          <plugin-slug>/
    static/                    # non-WP static site files (if any)
shared/
  wordpress-plugins/           # shared plugins not owned by one site
ops/
  deployment/                  # WordPress deployment tools and automation
  site-overlays/               # generated snippets used by automation (current `sites/`)
```

## Migration approach (safe + systematic)

1. **Inventory**: run `python tools/repo_inventory.py` to list, per site, where themes/plugins currently live.
2. **Plan**: generate a move plan that preserves deploy tooling compatibility.
3. **Migrate** site-by-site:
   - Move theme/plugin code into `websites/<domain>/wp/wp-content/...` (or `static/`).
   - Keep legacy paths working by updating `tools/auto_deploy_hook.py` path detection (or leaving legacy directories until cutover).
4. **Cutover**: once a site is fully migrated, update automation to point at the new location only.

## Security note

Credentials must never be committed. Store them in `.deploy_credentials/` (gitignored) or environment variables.

