## Websites hub (canonical navigation)

This folder is the **navigation hub** for the 11 websites listed in `configs/sites_registry.json`.

Today, the repo still contains “legacy” layouts (themes/plugins scattered in multiple places). This hub makes each site easy to find, and provides a clear migration target for a standardized layout later.

### Current locations (source + overlays)

- **ariajet.site**
  - Canonical themes: `websites/ariajet.site/wp/wp-content/themes/`
  - Legacy source (symlinked): `ariajet.site/wordpress-theme/`
  - Generated overlays/snippets: `sites/ariajet.site/`

- **crosbyultimateevents.com**
  - Docs/grade cards: `crosbyultimateevents.com/`
  - Theme/plugin overlays: `sites/crosbyultimateevents.com/wp/`

- **dadudekc.com**
  - Docs/grade cards: `dadudekc.com/`
  - Landing page overlays: `sites/dadudekc.com/`

- **digitaldreamscape.site**
  - Generated overlays/snippets: `sites/digitaldreamscape.site/`

- **freerideinvestor.com**
  - Primary site codebase (legacy): `FreeRideInvestor/`
  - Theme candidate: `FreeRideInvestor/wp-content/themes/freerideinvestor-modern/`
  - Generated overlays/snippets: `sites/freerideinvestor.com/`

- **houstonsipqueen.com**
  - Docs/grade cards: `houstonsipqueen.com/`
  - Theme overlays: `sites/houstonsipqueen.com/wp/`

- **prismblossom.online**
  - Canonical theme: `websites/prismblossom.online/wp/wp-content/themes/prismblossom/`
  - Legacy source (symlinked): `prismblossom.online/wordpress-theme/prismblossom/`

- **southwestsecret.com**
  - (Directory exists but is not standardized yet; migrate into this hub when ready.)

- **tradingrobotplug.com**
  - Landing page overlays: `sites/tradingrobotplug.com/`

- **weareswarm.online**
  - Theme overlays/snippets: `sites/weareswarm.online/theme/`

- **weareswarm.site**
  - Current legacy source: `Swarm_website/wp-content/themes/swarm-theme/`

### Target standard (when we migrate)

For each site: `websites/<domain>/wp/wp-content/themes/<theme>/` and `websites/<domain>/wp/wp-content/plugins/<plugin>/`.

