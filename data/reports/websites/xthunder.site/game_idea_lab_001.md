# xthunder.site — Game Idea Lab

## Intent

xthunder.site is a **game ideation** surface, not a web-services agency page.

## Features (static + JS)

- **Spin full idea** — random hook, mechanic, setting, twist
- **Respin** per slot
- **Save pitch** — up to 20 entries in `localStorage`
- **Copy pitch** — clipboard export
- **Prompt deck** — reference categories for manual riffing

## Preview

```bash
cd sites/production/websites/xthunder.site
python -m http.server 8787
```

Open http://localhost:8787 and use the Idea Lab section.

## Deploy

Still `enabled: false` in `ops/deployment/sites.yml`. Add all static assets to `deploy_files` before dry-run.
