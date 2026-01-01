# Organization Progress

## ✅ Completed

### Phase 1: Config Directory Consolidation
- ✅ Merged `config/` into `configs/`
- ✅ Renamed `configs/` to `config/` (single unified directory)
- ✅ All configuration files now in `config/`:
  - `config/site_configs.json`
  - `config/sites_registry.json`
  - `config/analytics_ids.json`
  - `config/voice_profiles/`

**Note**: `config/FreeRideInvestor_V2/` is a theme (not config) and needs to be moved to appropriate site directory.

## 🔄 In Progress

### Phase 2: Site Directory Consolidation
**Current State:**
- `sites/` - Contains overlays and deployment snippets (per README)
- `websites/` - Canonical navigation hub for WordPress themes/plugins
- Root-level legacy: `FreeRideInvestor/`, `Swarm_website/`, `southwestsecret.com/`

**Decision Needed:**
1. Should we consolidate `sites/` and `websites/` into a single `sites/` directory?
2. Or keep `websites/` as canonical and move `sites/` content there?
3. Where should root-level legacy directories go?

## 📋 Pending

### Phase 3: Autoblogger Consolidation
- `autoblogger/` - Legacy?
- `src/autoblogger/` - Python package (preferred per README)
- `ssot_autoblogger/` - SSOT implementation

**Action**: Determine canonical implementation and consolidate.

### Phase 4: Root Directory Cleanup
- Move temp files (`temp_*.md`) to `temp/`
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
├── sites/                     ⚠️  Overlays/snippets
├── websites/                  ⚠️  Canonical hub
│
├── autoblogger/               ⚠️  Legacy?
├── src/autoblogger/           ✅ Preferred
├── ssot_autoblogger/          ⚠️  SSOT variant
│
├── FreeRideInvestor/           ⚠️  309MB legacy
├── Swarm_website/              ⚠️  Should be in sites/
├── southwestsecret.com/        ⚠️  Should be in sites/
│
├── temp_*.md                   ⚠️  Should be in temp/
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

