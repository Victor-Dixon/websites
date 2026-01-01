# Website Artifact Move Log

This document tracks the consolidation of website artifacts from the Agent_Cellphone_V2_Repository into the websites repository (SSOT).

## Move batch: Website artifacts consolidation
**Date**: 2025-12-21
**From**: Agent_Cellphone_V2_Repository
**To**: websites repository (SSOT)

### Moved Artifacts

#### Sales Funnel Landing Pages
- **From**: `temp_sales_funnel_p0/`
- **To**: `sites/<site>/landing_pages/` (per site)
- **Deployment Results**: `deploy/results/temp_sales_funnel_p0/`
- **Sites**: crosbyultimateevents.com, dadudekc.com, freerideinvestor.com, houstonsipqueen.com, tradingrobotplug.com
- **Files**: HTML landing pages, PHP form optimization, PHP A/B test hero variants, deployment results JSON

#### SEO and UX Snippets
- **From**: Root-level `temp_*_site_seo.php` and `temp_*_site_ux.css` files
- **To**: `sites/<site>/seo/` and `sites/<site>/ux/` (per site)
- **Special**: `temp_hsq_functions.php` → `websites/houstonsipqueen.com/overlays/wp/theme/`
- **Sites**: All sites with matching patterns (crosby, dadudekc, freeride, hsq/houston, tradingrobotplug, swarm, ariajet, digitaldreamscape, prismblossom, southwestsecret)

#### WordPress Themes and Plugins
- **From**: `websites/weareswarm.online/overlays/swarm_theme.css`
- **To**: `websites/weareswarm.online/overlays/theme/swarm_theme.css`

- **From**: `temp_repos/crosbyultimateevents.com/wordpress-theme/`
- **To**: `websites/crosbyultimateevents.com/overlays/wp/theme/`

- **From**: `temp_repos/crosbyultimateevents.com/wordpress-plugins/`
- **To**: `websites/crosbyultimateevents.com/overlays/wp/plugins/`

#### Site Configuration Files
- **From**: `site_configs.json`
- **To**: `config/site_configs.json`

- **From**: `sites_registry.json`
- **To**: `config/sites_registry.json`

#### Site Documentation
- **From**: `websites/houstonsipqueen.com/overlays/HSQ_SALES_FUNNEL.md`
- **To**: `docs/per_site/houstonsipqueen/HSQ_SALES_FUNNEL.md`

- **From**: `websites/houstonsipqueen.com/overlays/HSQ_SITE_COPY_HOMEPAGE_AND_BOOKING.md`
- **To**: `docs/per_site/houstonsipqueen/HSQ_SITE_COPY_HOMEPAGE_AND_BOOKING.md`

## Structure

```
websites/
├── sites/
│   ├── <site>/
│   │   ├── landing_pages/
│   │   ├── seo/
│   │   ├── ux/
│   │   ├── wp/
│   │   │   ├── theme/
│   │   │   └── plugins/
│   │   └── docs/
├── config/
│   ├── site_configs.json
│   └── sites_registry.json
├── deploy/
│   └── results/
│       └── temp_sales_funnel_p0/
└── docs/
    └── per_site/
        └── <site>/
```

## Notes

- All website artifacts are now consolidated in the websites repository as the single source of truth (SSOT)
- Old locations in Agent_Cellphone_V2_Repository have been replaced with README stubs pointing to new locations
- Task management files remain in Agent_Cellphone_V2_Repository unless explicitly designated as website SSOT

