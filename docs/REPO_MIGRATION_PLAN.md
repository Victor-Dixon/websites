# Repository Migration Plan

**Generated:** 2025-12-20 from `tools/repo_inventory.py` output  
**Status:** Planning phase - Inventory complete  
**Reference:** `docs/REPO_ORGANIZATION.md` for target standard

## Current State Assessment

### Sites Already Migrated ✅

- **ariajet.site**: Themes in `websites/ariajet.site/wp/wp-content/themes/` (ariajet, ariajet-cosmic, ariajet-studio)
- **prismblossom.online**: Theme in `websites/prismblossom.online/wp/wp-content/themes/prismblossom/`

### Sites Requiring Migration

#### High Priority (Active Sites with Legacy Layouts)

1. **freerideinvestor.com**
   - **Current**: `FreeRideInvestor/wp-content/themes/freerideinvestor-modern/`
   - **Target**: `websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/`
   - **Notes**: Large codebase, primary site. Legacy pointer exists in `LEGACY_DOMAIN_POINTERS`.

2. **weareswarm.site**
   - **Current**: `Swarm_website/wp-content/themes/swarm-theme/`
   - **Target**: `websites/weareswarm.site/wp/wp-content/themes/swarm-theme/`
   - **Notes**: Legacy pointer exists in `LEGACY_DOMAIN_POINTERS`. ✅ Directory `websites/weareswarm.site/` exists with SITE_INFO.md. Ready for theme migration.

3. **southwestsecret.com**
   - **Current**: `southwestsecret.com/wordpress-theme/southwestsecret/`
   - **Target**: `websites/southwestsecret.com/wp/wp-content/themes/southwestsecret/`
   - **Notes**: Similar pattern to ariajet/prismblossom migration.

4. **tradingrobotplug.com**
   - **Current**: `TradingRobotPlugWeb/wordpress/wp-content/themes/my-custom-theme/`
   - **Target**: `websites/tradingrobotplug.com/wp/wp-content/themes/tradingrobotplug-theme/`
   - **Notes**: Setup documentation exists. Consider renaming theme to match domain.

#### Medium Priority (Generated Overlays)

5. **crosbyultimateevents.com**
   - **Current Overlay**: `sites/crosbyultimateevents.com/wp/theme/crosbyultimateevents/`
   - **Target**: `websites/crosbyultimateevents.com/wp/wp-content/themes/crosbyultimateevents/`
   - **Notes**: Generated overlay, may need source theme identification.

6. **All sites with `sites/<domain>/` overlays**
   - **Current**: Various locations in `sites/<domain>/`
   - **Target**: `ops/site-overlays/<domain>/` (per target standard)
   - **Affected**: ariajet.site, crosbyultimateevents.com, dadudekc.com, digitaldreamscape.site, freerideinvestor.com, houstonsipqueen.com, tradingrobotplug.com, weareswarm.online

## Migration Strategy

### Phase 1: Preparation (Current)
- [x] Run inventory tool
- [x] Create migration plan document
- [x] Validate all source locations exist
- [x] Test migration on one low-risk site (southwestsecret.com ✅ COMPLETE)

### Phase 2: Theme Migration (Site-by-Site)
1. ✅ **southwestsecret.com** - **COMPLETE** (2025-12-20) - Theme migrated to `websites/southwestsecret.com/wp/wp-content/themes/southwestsecret/`, SITE_INFO.md updated, deployment automation verified compatible
2. ✅ **tradingrobotplug.com** - **COMPLETE** (2025-12-20) - Theme and plugins migrated to `websites/tradingrobotplug.com/wp/wp-content/`, theme renamed from my-custom-theme to tradingrobotplug-theme, SITE_INFO.md updated
3. ✅ **weareswarm.site** - **COMPLETE** (2025-12-20) - Theme migrated to `websites/weareswarm.site/wp/wp-content/themes/swarm-theme/`, 12 files migrated, SITE_INFO.md updated
4. ✅ **freerideinvestor.com** - **PHASES 1 & 2 COMPLETE** (2025-12-20) - Theme (13 files) and core plugin (29 files) migrated to `websites/freerideinvestor.com/wp/wp-content/`. Phases 3 & 4 pending (root-level files analysis, non-WordPress components). See `docs/FREERIDEINVESTOR_MIGRATION_PLAN.md` for details.

### Phase 3: Overlay Migration
- Move all `sites/<domain>/` to `ops/site-overlays/<domain>/`
- Update deployment automation to reference new locations

### Phase 4: Shared Resources
- Migrate `wordpress-plugins/` to `shared/wordpress-plugins/`
- Update any references

### Phase 5: Cleanup
- Remove legacy directories after cutover
- Update all documentation references
- Verify deployment automation works

## Migration Steps (Per Site)

For each site migration:

1. **Pre-flight Checks**
   - Verify source location exists
   - Check for active deployments
   - Create backup

2. **Create Target Structure**
   - Run `tools/organize_repo.py` to ensure target directories exist
   - Verify `websites/<domain>/SITE_INFO.md` exists

3. **Migrate Files**
   - Copy theme/plugin files to target location
   - Preserve file permissions and structure
   - Verify no files lost

4. **Update References**
   - Update `tools/auto_deploy_hook.py` if needed
   - Update any hardcoded paths in deployment scripts
   - Update documentation

5. **Test**
   - Verify files accessible in new location
   - Test deployment automation
   - Verify no broken links

6. **Cutover**
   - Update deployment automation to use new path
   - Monitor first deployment
   - Remove legacy location after successful deployment

## Risk Assessment

### Low Risk
- **southwestsecret.com**: Similar pattern to completed migrations, isolated site
- **tradingrobotplug.com**: Well-documented, setup guide exists

### Medium Risk
- **crosbyultimateevents.com**: Generated overlays, need to identify source
- **freerideinvestor.com**: Large codebase, active site, many dependencies

### High Risk
- **freerideinvestor.com**: Primary site, extensive codebase

## Rollback Plan

For each migration:
1. Keep legacy location until cutover verified
2. Maintain symlinks during transition (if needed)
3. Document rollback steps per site
4. Test rollback procedure before migration

## Dependencies

- `tools/organize_repo.py` - Creates target structure
- `tools/repo_inventory.py` - Validates current state
- `tools/auto_deploy_hook.py` - Needs updates for new paths
- `configs/sites_registry.json` - Canonical site list

## Next Actions

1. ✅ **Complete**: Investigated weareswarm.site - Directory exists, ready for migration
2. ✅ **Complete**: Test migration on southwestsecret.com - Successfully migrated, deployment verified
3. ✅ **Complete**: tradingrobotplug.com migration - Theme and plugins migrated, renamed to match setup docs
4. ✅ **Complete**: weareswarm.site migration - Theme successfully migrated
5. **Next**: Handle high-risk migration (freerideinvestor.com - large codebase, primary revenue engine)

## Notes

- All migrations should preserve git history where possible
- Use `git mv` for tracked files to preserve history
- Update `.gitignore` if needed for new structure
- Maintain backward compatibility in deployment automation during transition

