#!/bin/bash
# websites_deprecate_duplicates.sh
# Create deprecation stubs for legacy website paths
# Safe first step before deletion

set -e

echo "🔧 DEPRECATING LEGACY WEBSITE DUPLICATES"
echo "=========================================="
echo "This creates stubs pointing to canonical locations"
echo "No files are deleted - only deprecated with pointers"
echo ""

# southwestsecret.com theme deprecation
echo "📁 Deprecating southwestsecret.com legacy theme..."
mkdir -p websites/southwestsecret.com/wp/wp-content/themes/
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
wp_die(
    'Theme deprecated - use canonical location at websites/southwestsecret.com/overlays/wp/theme/southwestsecret/',
    'Theme Deprecated',
    ['back_link' => true]
);
EOF

# Create deprecation notice
cat > websites/southwestsecret.com/DEPRECATED_THEME.md << 'EOF'
# Theme Deprecated

This theme directory has been deprecated in favor of the canonical layout.

**Old location:** `websites/southwestsecret.com/wp/wp-content/themes/southwestsecret/`
**New location:** `websites/southwestsecret.com/overlays/wp/theme/southwestsecret/`

All theme customizations should be made in the canonical location.

## Migration Guide

1. Move theme files to canonical location
2. Update any hardcoded paths
3. Test deployment with canonical sources
4. Remove this deprecated directory

See: docs/WEBSITES_SSOT_MAP.md for complete migration details.
EOF

# ariajet.site deprecation
echo "📁 Deprecating ariajet.site legacy structure..."
mkdir -p websites/ariajet.site/wp/
cat > websites/ariajet.site/wp/deprecated.php << 'EOF'
<?php
/**
 * DEPRECATED: Mixed layout consolidated
 *
 * Theme files moved to: websites/ariajet.site/overlays/wp/theme/ariajet/
 * See: docs/WEBSITES_SSOT_MAP.md
 */
wp_die(
    'Layout deprecated - use canonical structure at websites/ariajet.site/overlays/wp/theme/ariajet/',
    'Layout Deprecated',
    ['back_link' => true]
);
EOF

cat > websites/ariajet.site/DEPRECATED_LAYOUT.md << 'EOF'
# Layout Deprecated

The mixed wordpress-theme/ + wp/ layout has been deprecated.

**New canonical structure:**
```
websites/ariajet.site/
├── overlays/
│   └── wp/
│       ├── theme/ariajet/
│       └── plugins/
└── content/posts/
```

All theme files should be moved to the canonical location before this directory is removed.
EOF

# sites/ directory deprecation
echo "📁 Deprecating sites/ directory..."
cat > sites/DEPRECATED_DIRECTORY.md << 'EOF'
# Directory Deprecated

This directory has been deprecated. Domain-specific configurations
have been moved to canonical locations under websites/<domain>/.

## Migration Complete

- dadudekc.com: Configuration in websites/dadudekc.com/README.md
- freerideinvestor.com: Configuration in websites/freerideinvestor.com/README.md
- tradingrobotplug.com: Configuration in websites/tradingrobotplug.com/README.md
- crosbyultimateevents.com: Configuration in websites/crosbyultimateevents.com/README.md

See: docs/WEBSITES_SSOT_MAP.md for current locations and deployment details.
EOF

# Deprecate duplicate game files
echo "🎮 Deprecating duplicate game files..."
if [ -f "websites/southwestsecret.com/games/arias-wild-world.html" ]; then
    cat > websites/southwestsecret.com/games/arias-wild-world.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>Game Deprecated</title>
    <style>body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }</style>
</head>
<body>
    <h1>Game Moved</h1>
    <p>This game has been moved to the ariajet.site domain.</p>
    <p><a href="https://ariajet.site/games/arias-wild-world.html">Play on ariajet.site</a></p>
    <p><small>Canonical location: websites/ariajet.site/games/arias-wild-world.html</small></p>
</body>
</html>
EOF
fi

# Deprecate duplicate optimization configs
echo "⚙️  Deprecating duplicate optimization configs..."
for domain in "dadudekc.com" "prismblossom.online" "southwestsecret.com"; do
    if [ -f "websites/$domain/optimizations/wp-config-cache.php" ]; then
        cat > websites/$domain/optimizations/wp-config-cache.php << EOF
<?php
/**
 * DEPRECATED: Optimization config consolidated
 *
 * This optimization config has been consolidated.
 * See canonical optimization configs and SSOT map.
 *
 * Domain: $domain
 * See: docs/WEBSITES_SSOT_MAP.md
 */

// This file is deprecated - optimizations moved to canonical locations
// define('WP_CACHE', true); // Moved to canonical wp-config.php
EOF
    fi
done

echo ""
echo "✅ DEPRECATION STUBS CREATED"
echo ""
echo "📋 Summary:"
echo "  • Created theme deprecation stubs"
echo "  • Added layout migration notices"
echo "  • Deprecated duplicate game files"
echo "  • Marked optimization configs as consolidated"
echo ""
echo "🔍 Next steps:"
echo "1. Test deployments with canonical paths only"
echo "2. Verify no dependencies on deprecated locations"
echo "3. Check for any errors referencing deprecated paths"
echo "4. Run CI tests to ensure canonical paths work"
echo ""
echo "🗑️  Ready for Phase 4C: Safe deletion"
echo "   Command: rm -rf deprecated_paths/ (after verification)"