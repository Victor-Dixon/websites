## `sites/` (generated overlays / snippets)

This folder contains **generated website snippets** (landing page HTML, theme functions injections, SEO/UX overlays, small plugins) used by automation workflows.

It is **not** the canonical “source of truth” for full themes/plugins.

- Canonical site navigation is under `websites/` (see `websites/README.md`)
- Canonical site list is `configs/sites_registry.json`

Migration plan: when a site is fully migrated, overlays can be moved into `websites/<domain>/overlays/` (and `sites/` can eventually be archived).

