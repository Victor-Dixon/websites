# Root-Level Site Directories - Explanation

**Date**: 2025-12-21  
**Status**: Clarification Document

## Important: These Are NOT Duplicates!

The root-level site directories (`D:\websites\<domain>/`) and the nested `websites/` directory serve **different purposes**:

### Root-Level Directories (Source/Legacy)
**Purpose**: Source files, documentation, setup files, blog posts, static assets
- `ariajet.site/` - Games, static HTML, index.php
- `crosbyultimateevents.com/` - Setup docs, landing page templates
- `dadudekc.com/` - Blog posts, content
- `houstonsipqueen.com/` - **EMPTY** (can be removed)
- `prismblossom.online/` - **EMPTY** (can be removed)
- `southwestsecret.com/` - Static assets, CSS, JS, games, music, WordPress theme

### Nested `websites/` Directory (Canonical Hub)
**Purpose**: Canonical WordPress structure (themes, plugins, SITE_INFO.md)
- `websites/<domain>/wp/wp-content/themes/` - WordPress themes
- `websites/<domain>/wp/wp-content/plugins/` - WordPress plugins
- `websites/<domain>/docs/` - Site documentation (migrated from root)
- `websites/<domain>/SITE_INFO.md` - Site metadata

## According to README

Per `websites/websites/README.md`:
- Root-level directories are **legacy/source locations**
- They contain **docs, grade cards, setup files** (intentional)
- The `websites/` directory is the **canonical navigation hub** for WordPress themes/plugins
- Both locations are **legitimate** and serve different purposes

## What We Consolidated

We consolidated **duplicate content**:
- ✅ Moved duplicate documentation files to canonical `websites/<domain>/docs/`
- ✅ Removed duplicate theme directories (`wordpress-theme/` that were already in `websites/`)
- ✅ Deleted empty `wp-plugins/` directory

## What Remains (And Why)

### Directories to KEEP (have legitimate content):

1. **ariajet.site/** (2 items)
   - `games/` - Game HTML files
   - `index.php` - Static site entry point

2. **crosbyultimateevents.com/** (2 items)
   - `pages/` - Landing page templates
   - `setup/` - Setup documentation

3. **dadudekc.com/** (1 item)
   - `blog-posts/` - Blog content

4. **southwestsecret.com/** (9 items)
   - Static assets (CSS, JS, images)
   - Games, music, audio files
   - WordPress theme (legacy source)

### Directories to REMOVE (empty):

1. **houstonsipqueen.com/** - Empty (all content moved)
2. **prismblossom.online/** - Empty (all content moved)

## Other Root-Level Directories (Not Site Directories)

These are **legitimate** repository infrastructure:

- `_incoming/` - Temporary/incoming files
- `.benchmarks/` - Test benchmarks
- `.pytest_cache/` - Pytest cache
- `autoblogger/` - Autoblogger system
- `configs/` - Configuration files
- `content/` - Content SSOT
- `deploy/` - Deployment results
- `docs/` - Repository documentation
- `email_sequences/` - Email sequences
- `FreeRideInvestor/` - Legacy monolithic WordPress (per README)
- `FreeRideInvestor_V2/` - Cleaner theme (per README)
- `ops/` - Operations/deployment scripts
- `runtime/` - Runtime state
- `side-projects/` - Side projects
- `sites/` - Site overlays (per README)
- `social_media_setup/` - Social media setup
- `src/` - Python packages
- `Swarm_website/` - Legacy Swarm site (per README)
- `tools/` - Helper scripts
- `TradingRobotPlugWeb/` - Separate project (per README)
- `website_design/` - Design files
- `websites/` - Canonical navigation hub
- `wordpress-plugins/` - Custom plugins
- `wp-themes/` - Themes

## Recommendation

1. ✅ **Keep** root-level directories with content (ariajet.site, crosbyultimateevents.com, dadudekc.com, southwestsecret.com)
2. ⏳ **Remove** empty directories (houstonsipqueen.com, prismblossom.online)
3. ✅ **Keep** all infrastructure directories (autoblogger, configs, content, etc.)

## Summary

The root-level site directories are **NOT duplicates** - they're **source/legacy locations** that contain different content than the canonical `websites/` directory. The consolidation we performed was correct - we moved duplicate documentation and removed duplicate themes, but the root-level directories themselves are legitimate and should remain (except for the 2 empty ones).

---

**Key Takeaway**: Root-level = Source/Legacy, `websites/` = Canonical WordPress structure. Both are legitimate!

