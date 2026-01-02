# Websites Deprecation Plan

**Generated:** 2026-01-01  
**Status:** Phase 4 - Deprecate legacy paths

## Overview

This plan outlines the deprecation of legacy website paths that conflict with the Single Source of Truth (SSOT) map. All legacy duplicates will be replaced with pointer stubs before removal.

## Deprecation Strategy

### Phase 4A: Create Deprecation Stubs (Current)

Replace duplicate files with stubs that point to canonical locations:

```bash
# Example stub for duplicate file
echo "# DEPRECATED: This file has been moved to canonical location
# See: docs/WEBSITES_SSOT_MAP.md
# Canonical location: websites/southwestsecret.com/overlays/wp/theme/southwestsecret/
source ../canonical/path/to/file.php" > duplicate_file.php
```

### Phase 4B: Verification Testing

Before deletion, verify that:
- All canonical paths work correctly
- Deploy pipeline uses only canonical sources
- No dependencies on deprecated paths

### Phase 4C: Safe Deletion

Remove deprecated stubs after verification passes.

## Files to Deprecate

### southwestsecret.com Duplicates

**Theme Files:**
- `websites/southwestsecret.com/wp/wp-content/themes/southwestsecret/` → DEPRECATE
- Keep: `websites/southwestsecret.com/overlays/wp/theme/southwestsecret/`

**Assets:**
- `websites/southwestsecret.com/assets/` → MOVE to canonical theme
- `websites/southwestsecret.com/wp/wp-content/themes/southwestsecret/assets/` → DEPRECATE

**Stub Creation:**
```bash
# Create theme stub
mkdir -p websites/southwestsecret.com/wp/wp-content/themes/
cat > websites/southwestsecret.com/wp/wp-content/themes/southwestsecret.php << 'EOF'
<?php
// DEPRECATED: Theme moved to canonical location
// Canonical: websites/southwestsecret.com/overlays/wp/theme/southwestsecret/
// See: docs/WEBSITES_SSOT_MAP.md
header("Location: /errors/theme-deprecated.php");
exit;
EOF
```

### ariajet.site Duplicates

**Theme Files:**
- `websites/ariajet.site/wp/` → MOVE to overlays structure
- Keep: `websites/ariajet.site/overlays/wp/theme/ariajet/`

### sites/ Directory

**YAML Configs:**
- Move relevant configs to canonical domain README.md files
- Stub remaining YAML files

## Deprecation Script

```bash
#!/bin/bash
# websites_deprecate_duplicates.sh

echo "🔧 Deprecating legacy website duplicates..."

# southwestsecret.com theme deprecation
echo "📁 Deprecating southwestsecret.com legacy theme..."
cat > websites/southwestsecret.com/wp/wp-content/themes/southwestsecret.php << 'EOF'
<?php
/**
 * DEPRECATED: Theme moved to canonical location
 *
 * This theme has been consolidated to follow SSOT principles.
 * Canonical location: websites/southwestsecret.com/overlays/wp/theme/southwestsecret/
 *
 * See: docs/WEBSITES_SSOT_MAP.md
 *
 * To restore: Run deploy pipeline with canonical sources
 */
wp_die('Theme deprecated - use canonical location', 'Theme Deprecated', 503);
EOF

# Create deprecation notice
cat > websites/southwestsecret.com/DEPRECATED_THEME.md << 'EOF'
# Theme Deprecated

This theme directory has been deprecated in favor of the canonical layout.

**Old location:** `websites/southwestsecret.com/wp/wp-content/themes/southwestsecret/`
**New location:** `websites/southwestsecret.com/overlays/wp/theme/southwestsecret/`

All theme customizations should be made in the canonical location.
EOF

# ariajet.site deprecation
echo "📁 Deprecating ariajet.site legacy structure..."
cat > websites/ariajet.site/wp/deprecated.php << 'EOF'
<?php
/**
 * DEPRECATED: Mixed layout consolidated
 *
 * Theme files moved to: websites/ariajet.site/overlays/wp/theme/ariajet/
 * See: docs/WEBSITES_SSOT_MAP.md
 */
wp_die('Layout deprecated - use canonical structure', 'Layout Deprecated', 503);
EOF

# sites/ directory deprecation
echo "📁 Deprecating sites/ directory..."
cat > sites/DEPRECATED_DIRECTORY.md << 'EOF'
# Directory Deprecated

This directory has been deprecated. Domain-specific configurations
have been moved to canonical locations under websites/<domain>/.

See: docs/WEBSITES_SSOT_MAP.md for current locations.
EOF

echo "✅ Deprecation stubs created"
echo ""
echo "📋 Next steps:"
echo "1. Test deployments with canonical paths only"
echo "2. Verify no dependencies on deprecated locations"
echo "3. Run: rm -rf deprecated_paths/ (after verification)"
```

## Verification Checklist

### Pre-Deprecation
- [ ] All canonical paths working
- [ ] Deploy pipeline uses canonical sources
- [ ] CI tests pass with canonical layout
- [ ] No imports from deprecated paths

### Post-Deprecation
- [ ] Stubs redirect properly
- [ ] Error messages point to canonical locations
- [ ] Documentation updated
- [ ] Team notified of changes

### Pre-Deletion
- [ ] No active references to deprecated paths
- [ ] Backups created
- [ ] Rollback plan documented
- [ ] Emergency restore scripts ready

## Rollback Plan

If issues arise after deprecation:

```bash
# Emergency restore
git checkout HEAD~1 -- websites/southwestsecret.com/wp/wp-content/themes/
git checkout HEAD~1 -- sites/

# Or restore from backups
cp -r backups/themes/southwestsecret/ websites/southwestsecret.com/overlays/wp/theme/
```

## Timeline

- **Week 1:** Create deprecation stubs
- **Week 2:** Test and verify canonical paths
- **Week 3:** Remove deprecated stubs (if no issues)
- **Week 4:** Monitor and address any issues

## Risk Mitigation

### Low-Risk Approach
1. **Stubs first:** Replace with pointers, don't delete
2. **Gradual rollout:** One domain at a time
3. **Monitoring:** Watch for errors referencing deprecated paths
4. **Quick rollback:** Git revert if issues found

### Emergency Procedures
- **Immediate rollback:** `git revert` last commit
- **Alternative access:** Direct file restore from backups
- **Communication:** Notify team of rollback

## Success Metrics

- [ ] 0 duplicate files remaining
- [ ] All domains use canonical layout
- [ ] Deploy pipeline 100% canonical
- [ ] CI prevents new duplicates
- [ ] Documentation accurate and current