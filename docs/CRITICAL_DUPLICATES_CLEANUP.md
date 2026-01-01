# Critical Duplicates Cleanup - 2025-01-01

## Summary
Removed critical structural duplicates that were making navigation impossible.

## Critical Issues Fixed

### 1. Autoblogger Duplication ✅
- **Removed**: `autoblogger/` wrapper directory (just imported from `src/autoblogger/`)
- **Fixed**: Import in `ops/deployment/publish_with_autoblogger.py` to use `src.autoblogger`
- **Result**: Single source of truth in `src/autoblogger/`

### 2. Deployment Documentation Chaos ✅
- **Before**: 25+ deployment docs scattered across project
- **After**: Consolidated to `docs/deployment/` with archive for old/site-specific docs
- **Archived**: 
  - Site-specific deployment instructions (moved to `docs/deployment/archive/site-specific/`)
  - Old theme deployment docs (moved to `docs/deployment/archive/`)
- **Kept**: Main deployment guides in `docs/deployment/` and `ops/deployment/`

### 3. Duplicate deploy-ssh.py ✅
- **Removed**: Duplicate files from archive
- **Kept**: Active file in `websites/freerideinvestor.com/wp/wp-content/plugins/freeride-automated-trading-plan/tools/`

## Impact
- **Removed**: 1 duplicate directory (autoblogger/)
- **Archived**: 15+ duplicate deployment docs
- **Removed**: 2 duplicate deploy-ssh.py files
- **Result**: Much cleaner structure, single source of truth for autoblogger

## Notes
- `tbow_bot/` and `src/tbow_tactics/` are NOT duplicates - they're different components (app vs library)
- All archived files preserved
- All imports fixed and tested

