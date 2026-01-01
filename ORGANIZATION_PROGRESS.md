# Organization Progress

## ✅ Completed

### Phase 1: Config Directory Consolidation
- ✅ Confirmed `config/` as the SSOT directory
- ✅ Updated references to `config/` across scripts/docs
- ✅ All configuration files now in `config/`:
  - `config/site_configs.json`
  - `config/sites_registry.json`
  - `config/analytics_ids.json`
  - `config/voice_profiles/`

**Note**: `config/FreeRideInvestor_V2/` is a theme (not config) and needs to be moved to appropriate site directory.

## 🔄 In Progress

### Phase 2: Site Directory Consolidation
**Current State:**
- `sites/` - Autoblogger YAML site configs only
- `websites/` - Canonical navigation hub for WordPress themes/plugins + overlays
- Root-level legacy: `FreeRideInvestor/`, `Swarm_website/`, `southwestsecret.com/`

**Decision:** Keep `websites/` as canonical and move overlays into `websites/<domain>/overlays/`.

## 📋 Pending

### Phase 3: Autoblogger Consolidation
- `autoblogger/` - Entry-point shims
- `src/autoblogger/` - Python package (SSOT)
- `src/autoblogger/ssot/` - SSOT assets (moved)

### Phase 4: Root Directory Cleanup
- ✅ Moved temp files (`temp_*.md`) to `temp/root/`
- Move/archive large legacy `FreeRideInvestor/` (309MB)
- Move `Swarm_website/` to appropriate site directory
- Move `southwestsecret.com/` to `sites/` or `websites/`
- Move misplaced `config/FreeRideInvestor_V2/` theme

### Phase 5: Documentation Organization
- Consolidate scattered docs
- Move site-specific docs to `docs/sites/`
- Create clear documentation index

### Phase 6: Update References
- Update all Python scripts referencing `configs/` → `config/`
- Update deployment scripts
- Update README.md
- Test all scripts

## 📊 Current Structure Overview

```
website/
├── config/                    ✅ CONSOLIDATED
│   ├── site_configs.json
│   ├── sites_registry.json
│   ├── analytics_ids.json
│   ├── voice_profiles/
│   └── FreeRideInvestor_V2/   ⚠️  Theme (needs moving)
│
├── sites/                     ✅ Autoblogger site configs
├── websites/                  ✅ Canonical hub + overlays
│
├── autoblogger/               ⚠️  Legacy?
├── src/autoblogger/           ✅ Preferred
├── src/autoblogger/ssot/      ✅ SSOT assets
│
├── FreeRideInvestor/           ⚠️  309MB legacy
├── Swarm_website/              ⚠️  Should be in sites/
├── southwestsecret.com/        ⚠️  Should be in sites/
│
├── temp/root/                 ✅ Root temp files moved
└── ...
```

## 🎯 Next Steps

1. **Decide on site directory structure** (sites/ vs websites/)
2. **Move misplaced items** (FreeRideInvestor_V2 theme, root-level sites)
3. **Consolidate autoblogger** implementations
4. **Clean root directory** (temp files, legacy dirs)
5. **Update all references** to new paths
6. **Test everything** works with new structure

## ⚠️ Important Notes

- All moves should use `git mv` to preserve history
- Test deployment scripts after each phase
- Keep backup/rollback plan
- Update documentation as we go
