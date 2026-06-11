## Websites hub (canonical navigation)

This folder is the **navigation hub** for the 11 websites listed in `config/sites_registry.json`.

Today, the repo still contains “legacy” layouts (themes/plugins scattered in multiple places). This hub makes each site easy to find, and provides a clear migration target for a standardized layout later.

### Current locations (source + overlays)

- **ariajet.site**
  - Canonical themes: `websites/ariajet.site/wp/wp-content/themes/`
  - Legacy source (symlinked): `ariajet.site/wordpress-theme/`
  - Generated overlays/snippets: `websites/ariajet.site/overlays/`

- **crosbyultimateevents.com**
  - Docs/grade cards: `crosbyultimateevents.com/`
  - Theme/plugin overlays: `websites/crosbyultimateevents.com/overlays/wp/`

- **dadudekc.com**
  - Docs/grade cards: `dadudekc.com/`
  - Landing page overlays: `websites/dadudekc.com/overlays/`

- **digitaldreamscape.site**
  - Generated overlays/snippets: `websites/digitaldreamscape.site/overlays/`

- **freerideinvestor.com**
  - Primary site codebase (legacy): `FreeRideInvestor/`
  - Theme candidate: `FreeRideInvestor/wp-content/themes/freerideinvestor-modern/`
  - Generated overlays/snippets: `websites/freerideinvestor.com/overlays/`

- **houstonsipqueen.com**
  - Docs/grade cards: `houstonsipqueen.com/`
  - Theme overlays: `websites/houstonsipqueen.com/overlays/wp/`

- **prismblossom.online**
  - Canonical theme: `websites/prismblossom.online/wp/wp-content/themes/prismblossom/`
  - Legacy source (symlinked): `prismblossom.online/wordpress-theme/prismblossom/`

- **southwestsecret.com**
  - (Directory exists but is not standardized yet; migrate into this hub when ready.)

- **tradingrobotplug.com**
  - Landing page overlays: `websites/tradingrobotplug.com/overlays/`

- **weareswarm.online**
  - Theme overlays/snippets: `websites/weareswarm.online/overlays/theme/`

- **weareswarm.site**
  - Current legacy source: `Swarm_website/wp-content/themes/swarm-theme/`
  - Planet Blue legacy demo subpath: `sites/production/websites/weareswarm.site/planet-blue/` (canonical product is `planetblue.site`)

- **planetblue.site**
  - Planet Blue tactical RPG static MVP (standalone product lane)
  - SSOT: `sites/production/websites/planetblue.site/`
  - Deploy: `python ops/deployment/unified_deployer.py --site planetblue.site`

### Target standard (when we migrate)

For each site: `websites/<domain>/wp/wp-content/themes/<theme>/` and `websites/<domain>/wp/wp-content/plugins/<plugin>/`.

