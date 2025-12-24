## freerideinvestor.com

- **mode**: ACTIVE
- **purpose**: Trading education funnel
- **primary_owner**: Victor
- **notes**: Primary revenue engine

### What is FreeRide Investor?

See **[WHAT_IS_FREERIDEINVESTOR.md](./WHAT_IS_FREERIDEINVESTOR.md)** for the full description.

**TL;DR**: A comprehensive trading education platform that transforms traders into profitable investors through automated plans, educational content, and trading tools. Primary revenue engine for the ecosystem.

### Current source locations

- ✅ **Canonical theme**: `websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/` (MIGRATED 2025-12-20 - Phase 1)
- ✅ **Canonical plugins**: `websites/freerideinvestor.com/wp/wp-content/plugins/` (MIGRATED 2025-12-20 - Phase 2)
- **Legacy source**: `FreeRideInvestor/` (preserved for backward compatibility - 12,619 files)
- **Generated overlays**: `sites/freerideinvestor.com/` (to be migrated to `ops/site-overlays/`)

### Theme structure

- Theme name: **freerideinvestor-modern**
- Location: `wp/wp-content/themes/freerideinvestor-modern/`
- Files migrated: 13 core theme files
- Key files: functions.php, style.css, header.php, footer.php, index.php, home.php, single.php, page.php
- Custom pages: page-contact.php, page-report.php, page-mcp-test-page.php
- Assets: js/theme.js

### Plugin structure

- ✅ **Plugins migrated**: `wp/wp-content/plugins/` (MIGRATED 2025-12-20)
- Plugins: freeride-automated-trading-plan (core membership/planning plugin)
- Legacy source: `FreeRideInvestor/wp-content/plugins/` and `FreeRideInvestor/plugins/` (preserved)

### Migration status

- ✅ **Phase 1 Complete**: Core theme files migration (13 files)
- ✅ **Phase 2 Complete**: Plugin migration from wp-content/plugins/ (29 files)
- ✅ **Phase 3 Complete**: Root-level theme files and supporting directories migrated (2025-12-20)
  - Core theme files: functions.php (comprehensive), header.php, footer.php, home.php, single.php, custom.css
  - Supporting directories: inc/ (24 files), page-templates/ (36 files), scss/ (20 files), css/ (56 files), js/ (10 files), assets/ (partial - large files), template-parts/ (9 files)
- ✅ **Phase 4 Complete**: Non-WordPress components documented - Auto_blogger, scripts/, POSTS/ kept in legacy location

### Comprehensive theme structure

The theme now includes the full consolidated version with:
- **Enhanced functions.php** - REST API endpoints, security features, checklist functionality
- **34+ page templates** - Dashboard, education, stock-research, trading strategies, etc.
- **Modular includes** - Admin, assets, helpers, meta-boxes, post-types, taxonomies
- **SCSS source files** - Complete modular SCSS structure for development
- **Compiled CSS** - Production-ready stylesheets
- **Custom JavaScript** - Checklist dashboard, productivity tools, Pomodoro timer, etc.
- **Template parts** - Reusable template components

### Legacy components (kept in FreeRideInvestor/)

- **Auto_blogger/** - Python-based blog generation tool (separate system)
- **scripts/** - Utility scripts (Python, etc.)
- **POSTS/** - Blog content (Dev_Blog, SecretSmokeSession, TBOWTactics)
- **plugins/** - Theme-integrated plugins (development versions)
- **HTML preview files** - Development/preview files
- **Documentation** - Various .md files

### Important notes

- **Large codebase**: 12,619 files total in legacy location
- **Primary revenue engine**: Migration must be handled with extreme care
- **Root-level files**: Many WordPress files at root level need analysis before migration
- **Auto_blogger system**: Keep in legacy location initially
- **Reference**: See `docs/FREERIDEINVESTOR_MIGRATION_PLAN.md` for detailed migration plan
