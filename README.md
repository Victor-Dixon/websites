# Websites Monorepo

This repository contains the source (themes/templates/content) for multiple small websites and WordPress-based sites, plus internal tooling for packaging and verifying changes.

## What’s in this repo

- **WordPress themes** stored alongside each site/domain folder
- **Static HTML/CSS/JS** for lightweight pages and side projects
- **Custom utilities** under `tools/` (verification, packaging, maintenance scripts)
- **Custom WordPress plugins** under `wordpress-plugins/`

## Repository layout (high level)

| Path | What it contains | Notes |
|------|------------------|-------|
| `FreeRideInvestor/` | WordPress theme code + a large snapshot of plugins/assets | Legacy/monolithic; includes `docker-compose.yml` and many third-party plugin files. |
| `FreeRideInvestor_V2/` | A cleaner standalone WordPress theme | Theme files at the folder root (e.g., `functions.php`, `style.css`). |
| `prismblossom.online/wordpress-theme/prismblossom/` | WordPress theme for `prismblossom.online` | Theme-only folder. |
| `ariajet.site/` | Static pages + WordPress theme | Static `index.html` + games; theme in `wordpress-theme/ariajet/`. |
| `Swarm_website/wp-content/themes/swarm-theme/` | WordPress theme for the Swarm site | Includes theme templates and JS/CSS. |
| `dadudekc.com/blog-posts/` | Blog drafts/content | Markdown + HTML drafts. |
| `crosbyultimateevents.com/` | Site docs + funnel pages | Copy, checklists, and setup notes. |
| `houstonsipqueen.com/` | Site docs + funnel pages | Copy, funnel URL maps, and landing/thank-you pages. |
| `content/` | Content SSOT for autoblogger | Voices, brands, backlogs, calendars, drafts. |
| `src/` | Python packages (autoblogger, helpers) | Prefer importing from here for tooling. |
| `side-projects/` | Small experimental pages | Standalone HTML content. |
| `wordpress-plugins/` | Custom plugins | Each plugin has its own folder and readme. |
| `docs/` | Internal maintenance documentation | Operational notes and guides. |
| `tools/` | Helper scripts | Packaging, verification, and maintenance automation. |

## Working with WordPress themes

- **Theme locations vary** by site. Look for either:
  - `*/wordpress-theme/<theme-name>/` (theme-only folder), or
  - `*/wp-content/themes/<theme-name>/` (WordPress-style tree), or
  - a theme stored at the folder root (e.g., `FreeRideInvestor_V2/`).
- **To install a theme**: copy the theme folder into your WordPress install at `wp-content/themes/`, then activate it in **Appearance → Themes**.

## Deployment (high level)

This repository **does not store production credentials**. Deployment is expected to be done via one of:

- **Manual upload** (SFTP / hosting file manager / WordPress Theme Editor) of the changed theme files
- **Packaging + verification helpers**:
  - `python tools/deploy_website_fixes.py` (creates zip packages and prints file-by-file instructions)
  - `python tools/verify_website_fixes.py` (sanity-checks a few live endpoints)

## Security & secrets

- **Do not commit secrets** (hosting credentials, API keys, application passwords).
- Keep any credentials in **local-only** files (ignored by git) or injected via environment variables.
- This repo contains **third-party code** (notably under `FreeRideInvestor/plugins/`). Treat updates and security reviews as part of routine maintenance.

## Contributing guidelines

 - Keep changes **scoped to one site** when possible (makes review and deployment safer).
 - Prefer small, well-described commits (what changed + why).
 - For WordPress PHP changes, validate syntax before deployment if you have PHP available (`php -l <file>`).

## Autoblogger — calendar + backlog → daily drafts

Autoblogger runs a **daily pipeline** that selects a post from a rolling calendar/backlog, generates a draft in the correct site's voice, validates it, and saves it to `content/drafts/<site_id>/`.

### SSOT file contract

```
content/
  voices/
  brands/
  backlogs/
  calendars/
  drafts/
sites/
  dadudekc.yaml
  corey.yaml
  kiki.yaml
runtime/
  autoblogger_state__<site_id>.json
```

### Run (queue-only by default)

```bash
python3 -m autoblogger.run_daily --site dadudekc
```

### Dry-run (writes prompt to a draft, no LLM, no publish)

```bash
python3 -m autoblogger.run_daily --site dadudekc --dry-run --date 2025-12-20
```

### Enable generation (OpenAI-compatible)

Set env vars:

- `AUTOBLOGGER_OPENAI_API_KEY` (or `OPENAI_API_KEY`)
- `AUTOBLOGGER_OPENAI_MODEL` (default: `gpt-4o-mini`)
- `AUTOBLOGGER_OPENAI_BASE_URL` (default: `https://api.openai.com/v1`)

### Optional: publish to WordPress

Uses per-site WordPress env vars configured in `sites/<site>.yaml` (see `publish.wp_*_env` keys).

```bash
python3 -m autoblogger.run_daily --site dadudekc --auto-publish --wp-status draft
```

### Cron example (06:00 America/Chicago)

```cron
0 6 * * * TZ=America/Chicago cd /path/to/repo && python3 -m autoblogger.run_daily --site dadudekc >> runtime/autoblogger_cron.log 2>&1
```

### Run all sites (single runner)

```bash
python3 -m autoblogger.run_all_sites --dry-run
```

## Notes

- Credentials should be provided via environment variables or local-only (gitignored) files.
- Do not commit hosting credentials, API keys, or WordPress application passwords.
