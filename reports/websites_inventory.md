# Websites Inventory Report

**Generated:** 2026-01-01  
**Scope:** All website-related folders and files

## Directory Structure Overview

### Canonical Layout (`websites/<domain>/`)
The repository uses a canonical layout under `websites/` with overlays for WordPress customizations.

```
websites/
├── <domain>/
│   ├── overlays/
│   │   ├── wp/
│   │   │   ├── theme/<theme_name>/...
│   │   │   └── plugins/<plugin_name>/...
│   │   └── landing_pages/...
│   ├── content/
│   │   └── posts/...
│   ├── ops/
│   │   └── verify/...
│   └── README.md
```

### Legacy Layouts
Several legacy layouts exist that need consolidation:

- `sites/` - YAML configuration files
- `Swarm_website/` - Legacy swarm site (not found)
- Domain-specific root-level folders (consolidated into `websites/`)

## Per-Domain Analysis

### dadudekc.com
**Canonical Path:** `websites/dadudekc.com/`
**Theme:** dadudekc (`overlays/wp/theme/dadudekc/`)
**Plugins:** None specific
**Content:** Blog posts in `blog-posts/`
**Status:** ✅ Canonical layout implemented

**Files:** 1,107 files (331 PHP, 138 MD, 94 CSS, etc.)
**Structure:**
- `overlays/wp/theme/dadudekc/` - Main theme
- `optimizations/` - Performance configs
- `blog-posts/` - Content drafts

### freerideinvestor.com
**Canonical Path:** `websites/freerideinvestor.com/`
**Theme:** freerideinvestor-modern (`wp/wp-content/themes/freerideinvestor-modern/`)
**Plugins:** Custom trading plugins
**Content:** Minimal blog setup
**Status:** ✅ Canonical layout implemented

**Files:** 2,193 files (1,109 PHP, 60 CSS, 22 SCSS, etc.)
**Structure:**
- `wp/wp-content/themes/freerideinvestor-modern/` - Main theme
- `overlays/landing_pages/` - Landing page templates

### tradingrobotplug.com
**Canonical Path:** `websites/tradingrobotplug.com/`
**Theme:** tradingrobotplug-theme (`overlays/wp/theme/tradingrobotplug-theme/`)
**Plugins:** Multiple custom plugins (`overlays/wp/plugins/`)
**Content:** Marketplace and performance pages
**Status:** ✅ Canonical layout implemented

**Files:** 1,058 files (50 PHP, 10 PY, 6 CSS, etc.)
**Structure:**
- `overlays/wp/theme/tradingrobotplug-theme/` - Main theme
- `overlays/wp/plugins/` - 18 plugin files
- `wp/` - Additional WordPress files

### crosbyultimateevents.com
**Canonical Path:** `websites/crosbyultimateevents.com/`
**Theme:** crosbyultimateevents (`overlays/wp/theme/crosbyultimateevents/`)
**Plugins:** Custom event plugins
**Content:** Event-related pages and blog
**Status:** ✅ Canonical layout implemented

**Files:** 476 files (31 PHP, 6 CSS, 6 JS, etc.)
**Structure:**
- `overlays/wp/theme/crosbyultimateevents/` - Main theme
- `docs/` - 8 documentation files
- `pages/` - Content pages

## Additional Domains Found

### ariajet.site
**Path:** `websites/ariajet.site/`
**Status:** Mixed layout (wordpress-theme/ + wp/)
**Issues:** Duplicate game files with southwestsecret.com

### digitaldreamscape.site
**Path:** `websites/digitaldreamscape.site/`
**Status:** Minimal WordPress setup
**Content:** Extensive blog content in `blog/`

### houstonsipqueen.com
**Path:** `websites/houstonsipqueen.com/`
**Status:** Mixed layout with extensive docs

### southwestsecret.com
**Path:** `websites/southwestsecret.com/`
**Status:** ❌ Multiple layout issues
**Issues:**
- Both `wordpress-theme/` and `wp/` directories
- Assets duplicated between directories
- Optimization configs scattered

### weareswarm.site
**Path:** `websites/weareswarm.site/`
**Status:** Minimal setup with theme

### weareswarm.online
**Path:** `websites/weareswarm.online/`
**Status:** Basic structure

## Legacy Directories

### sites/
**Path:** `sites/`
**Contents:** YAML configuration files for 10 domains
**Status:** ⚠️ Should be consolidated into canonical layout

**Files:**
- ariajet.yaml, corey.yaml, crosby.yaml, dadudekc.yaml
- dream.yaml, kiki.yaml, prismblossom.yaml
- swarm.yaml, trading.yaml
- README.md

## Duplicate Analysis

**Total Duplicate Groups:** 54

### Major Duplicates

1. **11 files** - Various theme/plugin files across domains
   - freerideinvestor.com theme files
   - tradingrobotplug.com Python files

2. **3 files** - `wp-config-cache.php` optimization configs
   - dadudekc.com, prismblossom.online, southwestsecret.com

3. **3 files** - SVG assets (favicon, logo)
   - southwestsecret.com theme/plugin/asset directories

4. **Multiple 2-file duplicates** - Documentation files
   - Same content in root + docs/ subdirectories
   - Game files across domains

## Recommendations

### Immediate Actions

1. **Consolidate southwestsecret.com**
   - Choose single theme location (wordpress-theme/ or wp/)
   - Remove duplicate assets
   - Consolidate optimization configs

2. **Standardize ariajet.site layout**
   - Move to canonical `overlays/wp/theme/` structure
   - Remove duplicate game files

3. **Document canonical paths**
   - Create SSOT map for all domains
   - Update deployment scripts

4. **Clean up sites/ directory**
   - Move YAML configs to canonical locations
   - Update deployment references

### Long-term Goals

1. **Single Source of Truth**
   - Each domain: one canonical source folder
   - Clear separation of concerns
   - Automated duplication prevention

2. **Deployment Safety**
   - Verify markers on all domains
   - Cache-busting for theme changes
   - Rollback capability

3. **Maintenance Automation**
   - CI gates for duplication
   - Automated cleanup scripts
   - Health monitoring

## File Counts by Domain

| Domain | Total Files | PHP | MD | CSS | JS | Other |
|--------|-------------|-----|----|-----|----|-------|
| dadudekc.com | 1,107 | 331 | 138 | 94 | - | 544 |
| freerideinvestor.com | 2,193 | 1,109 | - | 60+22 | - | 1,002 |
| tradingrobotplug.com | 1,058 | 50 | - | 6 | - | 1,002 |
| crosbyultimateevents.com | 476 | 31 | - | 6 | 6 | 433 |
| southwestsecret.com | 2,408 | 1,024 | - | - | - | 1,384 |
| ariajet.site | 1,571 | 31 | - | 6 | 6 | 1,528 |
| houstonsipqueen.com | 503 | 8 | - | 1 | 1 | 493 |
| digitaldreamscape.site | 240 | 8 | 6 | 1 | 1 | 224 |
| weareswarm.site | 54 | 14 | - | 4 | 4 | 32 |
| weareswarm.online | 6 | - | - | - | - | 6 |

**Total Website Files:** ~9,616 files across all domains