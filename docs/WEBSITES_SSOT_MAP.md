# Websites Single Source of Truth (SSOT) Map

**Version:** 1.0  
**Last Updated:** 2026-01-01  
**Purpose:** Declare canonical source locations for all website domains

## Canonical Layout Standard

All websites must follow this structure under `websites/<domain>/`:

```
websites/<domain>/
├── overlays/
│   ├── wp/
│   │   ├── theme/<theme_name>/
│   │   │   ├── *.php (templates)
│   │   │   ├── *.css (stylesheets)
│   │   │   ├── *.js (scripts)
│   │   │   ├── inc/ (includes)
│   │   │   └── template-parts/
│   │   └── plugins/<plugin_name>/
│   │       ├── *.php (plugin files)
│   │       ├── *.css (plugin styles)
│   │       └── *.js (plugin scripts)
│   └── landing_pages/
│       └── *.html, *.php (landing page templates)
├── content/
│   └── posts/ (markdown drafts + published content)
├── ops/
│   └── verify/
│       ├── markers.txt (verification strings)
│       └── urls.txt (URLs to check)
└── README.md (domain-specific deploy notes)
```

## Domain Declarations

### dadudekc.com ✅ CANONICAL
**Theme:** dadudekc
**Status:** Fully canonical
**Deploy Source:** `websites/dadudekc.com/overlays/wp/theme/dadudekc/`

**Canonical Paths:**
- **Theme:** `websites/dadudekc.com/overlays/wp/theme/dadudekc/`
- **Plugins:** None (core WordPress only)
- **Content:** `websites/dadudekc.com/blog-posts/`
- **Verification:** `websites/dadudekc.com/ops/verify/`

**Deploy Notes:**
- Front-page.php contains Swarm capabilities section
- Requires dadudekc_get_*() helper functions
- Style.css uses CSS custom properties

### freerideinvestor.com ✅ CANONICAL
**Theme:** freerideinvestor-modern
**Status:** Fully canonical
**Deploy Source:** `websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/`

**Canonical Paths:**
- **Theme:** `websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/`
- **Plugins:** `websites/freerideinvestor.com/wp/wp-content/plugins/freeride-*`
- **Content:** `websites/freerideinvestor.com/blog/`
- **Verification:** `websites/freerideinvestor.com/ops/verify/`

**Deploy Notes:**
- Complex trading system integration
- Requires custom post types for trades
- Extensive SCSS compilation

### tradingrobotplug.com ✅ CANONICAL
**Theme:** tradingrobotplug-theme
**Status:** Fully canonical
**Deploy Source:** `websites/tradingrobotplug.com/overlays/wp/theme/tradingrobotplug-theme/`

**Canonical Paths:**
- **Theme:** `websites/tradingrobotplug.com/overlays/wp/theme/tradingrobotplug-theme/`
- **Plugins:** `websites/tradingrobotplug.com/overlays/wp/plugins/`
- **Content:** `websites/tradingrobotplug.com/blog/`
- **Verification:** `websites/tradingrobotplug.com/ops/verify/`

**Deploy Notes:**
- Marketplace functionality
- Performance dashboard pages
- Python integration components

### crosbyultimateevents.com ✅ CANONICAL
**Theme:** crosbyultimateevents
**Status:** Fully canonical
**Deploy Source:** `websites/crosbyultimateevents.com/overlays/wp/theme/crosbyultimateevents/`

**Canonical Paths:**
- **Theme:** `websites/crosbyultimateevents.com/overlays/wp/theme/crosbyultimateevents/`
- **Plugins:** `websites/crosbyultimateevents.com/overlays/wp/plugins/crosby-*`
- **Content:** `websites/crosbyultimateevents.com/blog/`
- **Verification:** `websites/crosbyultimateevents.com/ops/verify/`

**Deploy Notes:**
- Event management system
- Custom booking functionality
- Calendar integration

## Domains Needing Consolidation

### southwestsecret.com ❌ NEEDS CONSOLIDATION
**Current Issues:**
- Multiple theme locations: `wordpress-theme/` and `wp/wp-content/themes/`
- Scattered optimization configs
- Duplicate assets across directories

**Target Canonical Paths:**
- **Theme:** `websites/southwestsecret.com/overlays/wp/theme/southwestsecret/`
- **Plugins:** `websites/southwestsecret.com/overlays/wp/plugins/`
- **Content:** `websites/southwestsecret.com/content/posts/`
- **Verification:** `websites/southwestsecret.com/ops/verify/`

**Consolidation Plan:**
1. Choose `wordpress-theme/southwestsecret/` as canonical theme
2. Move to `overlays/wp/theme/southwestsecret/`
3. Consolidate optimization configs to `optimizations/` (single location)
4. Remove duplicate assets

### ariajet.site ❌ NEEDS CONSOLIDATION
**Current Issues:**
- Mixed layout with `wordpress-theme/` and `wp/`
- Duplicate game files with southwestsecret.com

**Target Canonical Paths:**
- **Theme:** `websites/ariajet.site/overlays/wp/theme/ariajet/`
- **Plugins:** None
- **Content:** `websites/ariajet.site/content/posts/`
- **Verification:** `websites/ariajet.site/ops/verify/`

**Consolidation Plan:**
1. Move theme files to canonical structure
2. Remove duplicate game files (keep in southwestsecret.com)
3. Consolidate documentation

## Legacy Path Deprecations

### sites/ Directory (YAML configs)
**Status:** ⚠️ DEPRECATED
**Replacement:** Domain-specific README.md files in canonical locations
**Migration:** Move relevant config to canonical domain folders
**Timeline:** Remove after PR-2

### Swarm_website/ Directory
**Status:** ❌ NOT FOUND
**Note:** Appears to have been previously migrated

### Root-level Domain Directories
**Status:** ✅ MIGRATED
**All domains successfully moved to `websites/<domain>/` structure**

## Deployment Rules

### Deploy Source Priority
1. **Canonical overlays:** `websites/<domain>/overlays/wp/`
2. **Theme files:** `theme/<theme_name>/` (all PHP, CSS, JS)
3. **Plugin files:** `plugins/<plugin_name>/` (all files)
4. **Content files:** Only explicitly included paths

### Excluded from Deploy
- `*.md` documentation files
- `temp_*` temporary files
- `*.log` log files
- Test/development files
- Duplicate files (use canonical version)

## Verification Requirements

### Per-Domain Markers
Each domain must have unique verification markers:

```txt
# websites/<domain>/ops/verify/markers.txt
WEBSITE_DEPLOYED_DADUDEKC_COM_2026
DEPLOYMENT_VERIFIED_FREERIDEINVESTOR_2026
# etc.
```

### URL Check Lists
Each domain must specify URLs to verify:

```txt
# websites/<domain>/ops/verify/urls.txt
/
/wp-admin/
/contact/
/about/
```

### Cache Busting
Theme version must increment on CSS/JS changes:

```php
wp_enqueue_style('theme-style', get_stylesheet_uri(), [], '1.2.4');
wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/script.js', [], '1.2.4');
```

## CI/CD Integration

### Duplication Prevention
```yaml
# .github/workflows/ci.yml
- name: Check for website duplicates
  run: |
    if [ -f reports/websites_dup_scan.json ]; then
      DUPES=$(jq '. | length' reports/websites_dup_scan.json)
      if [ "$DUPES" -gt 0 ]; then
        echo "❌ Found $DUPES duplicate groups in websites/"
        exit 1
      fi
    fi
```

### Legacy Path Blocking
```yaml
- name: Block legacy website paths
  run: |
    LEGACY_PATHS=(
      "sites/"
      "Swarm_website/"
      "*/wordpress-theme/"
      "*/wp/wp-content/themes/*/"
    )
    for path in "${LEGACY_PATHS[@]}"; do
      if find . -path "./websites/*" -prune -o -type f -path "$path" -print | head -1 | grep -q .; then
        echo "❌ Found legacy path: $path"
        exit 1
      fi
    done
```

## Migration Timeline

### Phase 1: Documentation (Current)
- ✅ Create SSOT map
- ✅ Document canonical paths
- ✅ Update deployment scripts

### Phase 2: Consolidation
- 🔄 Consolidate southwestsecret.com layout
- 🔄 Consolidate ariajet.site layout
- 🔄 Move YAML configs to canonical locations

### Phase 3: Cleanup
- ⏳ Remove legacy directories
- ⏳ Implement CI gates
- ⏳ Add duplication monitoring

## Compliance Checklist

### Per-Domain Requirements
- [ ] Single canonical source folder
- [ ] Theme files in `overlays/wp/theme/<name>/`
- [ ] Plugin files in `overlays/wp/plugins/<name>/`
- [ ] Content in `content/posts/`
- [ ] Verification markers defined
- [ ] URLs for post-deploy checks
- [ ] README.md with deploy notes

### Repository-wide Requirements
- [ ] No duplicate files across domains
- [ ] CI blocks legacy path changes
- [ ] CI blocks new duplicates
- [ ] Deploy only from canonical paths
- [ ] Post-deploy verification passes

---

**This SSOT map serves as the authoritative source for all website domain locations and deployment rules.**