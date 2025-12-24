# Website Directory Consolidation Analysis

**Date**: 2025-12-21  
**Agent**: Agent-6 (Coordination & Communication Specialist)  
**Status**: Analysis Complete

## Problem Statement

Duplicate website directories exist at two levels:
1. **Root level** (`D:\websites\<domain>/`) - Legacy/original structure
2. **Nested level** (`D:\websites\websites\<domain>/`) - Canonical navigation hub (target standard)

## Current Structure Analysis

### Root Level Directories (Legacy)
- `ariajet.site/` - Static pages + WordPress theme in `wordpress-theme/`
- `crosbyultimateevents.com/` - Docs, grade cards, content calendars
- `dadudekc.com/` - Blog posts, docs, grade cards
- `houstonsipqueen.com/` - Docs, grade cards, funnel pages
- `prismblossom.online/` - WordPress theme in `wordpress-theme/`
- `southwestsecret.com/` - Theme files and content
- `FreeRideInvestor/` - Legacy monolithic WordPress install (12,541 files)
- `FreeRideInvestor_V2/` - Cleaner standalone theme
- `TradingRobotPlugWeb/` - Trading robot website code
- `Swarm_website/` - Swarm site theme

### Nested `websites/` Directory (Canonical Hub)
According to `websites/websites/README.md`, this is the **navigation hub** and **migration target**:
- `websites/ariajet.site/` - Canonical themes in `wp/wp-content/themes/`
- `websites/crosbyultimateevents.com/` - SITE_INFO.md
- `websites/dadudekc.com/` - SITE_INFO.md
- `websites/digitaldreamscape.site/` - Full WordPress structure
- `websites/freerideinvestor.com/` - Full WordPress structure with themes/plugins
- `websites/houstonsipqueen.com/` - SITE_INFO.md
- `websites/prismblossom.online/` - Full WordPress structure
- `websites/southwestsecret.com/` - Full WordPress structure
- `websites/tradingrobotplug.com/` - Full WordPress structure with plugins
- `websites/weareswarm.online/` - SITE_INFO.md
- `websites/weareswarm.site/` - Full WordPress structure

## Duplicate Sites Identified

Sites existing in both locations:
1. `ariajet.site`
2. `crosbyultimateevents.com`
3. `dadudekc.com`
4. `houstonsipqueen.com`
5. `prismblossom.online`
6. `southwestsecret.com`

## Root Cause

According to `websites/websites/README.md`:
- The `websites/` subdirectory is the **canonical navigation hub**
- It's the **migration target for a standardized layout**
- Legacy layouts still exist at root level
- Migration is **incomplete** - sites haven't been fully consolidated

## Target Standard Structure

Per `websites/websites/README.md`:
```
websites/<domain>/
├── wp/
│   ├── wp-content/
│   │   ├── themes/
│   │   │   └── <theme-name>/
│   │   └── plugins/
│   │       └── <plugin-name>/
└── SITE_INFO.md
```

## Consolidation Strategy

### Phase 1: Content Migration
1. **Documentation**: Move docs from root-level to `websites/<domain>/docs/` or `docs/per_site/<domain>/`
2. **Grade Cards**: Move to `websites/<domain>/` or keep in root if referenced by tools
3. **Content Calendars**: Move to `websites/<domain>/` or `content/calendars/`

### Phase 2: Theme/Code Migration
1. **WordPress Themes**: 
   - Root `wordpress-theme/` → `websites/<domain>/wp/wp-content/themes/<theme>/`
   - Verify no breaking changes
2. **Legacy Monoliths**:
   - `FreeRideInvestor/` - Extract theme to `websites/freerideinvestor.com/wp/wp-content/themes/`
   - Keep legacy as archive or remove after extraction

### Phase 3: Cleanup
1. **Remove empty root directories** after migration
2. **Create symlinks** if root-level access needed (per README mentions symlinks)
3. **Update references** in tools, scripts, and documentation

## Recommended Actions

### Immediate (Low Risk)
1. ✅ **Document the duplication** (this analysis)
2. ⏳ **Create consolidation script** to analyze differences
3. ⏳ **Identify which files are unique** in root vs nested locations

### Short Term (Medium Risk)
1. ⏳ **Migrate documentation** from root to nested structure
2. ⏳ **Consolidate theme files** into canonical location
3. ⏳ **Update tool references** to use canonical paths

### Long Term (Requires Testing)
1. ⏳ **Remove root-level duplicates** after verification
2. ⏳ **Standardize all sites** to `websites/<domain>/` structure
3. ⏳ **Update deployment scripts** to use canonical paths

## Files to Check

1. `configs/sites_registry.json` - Which paths are referenced?
2. `tools/` scripts - Do they reference root or nested paths?
3. `ops/deployment/` - Which paths are used for deployment?
4. Documentation - Are paths documented correctly?

## Risk Assessment

- **Low Risk**: Moving documentation files
- **Medium Risk**: Moving theme files (need to verify no breaking changes)
- **High Risk**: Removing root directories (could break existing workflows)

## Next Steps

1. Create consolidation analysis tool to compare root vs nested directories
2. Identify unique files in each location
3. Propose migration plan per site
4. Coordinate with Agent-7 (Web Development) for execution

---

**Note**: This consolidation aligns with the canonical structure defined in `websites/websites/README.md` and should be coordinated with the websites repository maintainers.


