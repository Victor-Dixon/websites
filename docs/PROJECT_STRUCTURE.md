# Project Structure Documentation

**Last Updated**: 2025-01-01  
**Status**: Organized and Maintainable

## 📁 Directory Structure

```
website/
├── README.md                          # Main project documentation
├── .gitignore                        # Git ignore rules
│
├── config/                            # ⭐ Configuration (SSOT)
│   ├── site_configs.json             # Site deployment configurations
│   ├── sites_registry.json            # Site registry and metadata
│   ├── analytics_ids.json             # Analytics configuration
│   └── voice_profiles/               # Autoblogger voice profiles
│
├── sites/                             # ⭐ Autoblogger Site Configs
│   ├── *.yaml                        # Site routing/config YAMLs
│   └── README.md                     # Autoblogger config documentation
│
├── websites/                          # ⭐ Canonical Site Hub
│   ├── <domain>/                     # Per-site structure
│   │   ├── wp/                       # WordPress structure (if WP site)
│   │   │   └── wp-content/
│   │   │       ├── themes/
│   │   │       └── plugins/
│   │   ├── overlays/                 # Generated overlays/snippets
│   │   ├── docs/                     # Site-specific documentation
│   │   ├── blog/                     # Blog content
│   │   └── SITE_INFO.md             # Site metadata
│   └── README.md                     # Sites directory documentation
│
├── content/                           # ⭐ Content SSOT
│   ├── voices/                       # Voice profiles
│   ├── brands/                       # Brand definitions
│   ├── backlogs/                     # Content backlogs
│   ├── calendars/                    # Content calendars
│   ├── drafts/                       # Generated drafts
│   └── blog_posts/                   # Published blog posts
│
├── src/                              # ⭐ Source Code Packages
│   ├── autoblogger/                  # Autoblogger Python package
│   │   ├── ssot/                     # SSOT assets (yaml, templates)
│   │   └── ...                       # Package modules
│   └── tbow_tactics/                 # TBOW tactics package
│
├── autoblogger/                       # Autoblogger Entry Points
│   ├── run_daily.py                  # Daily pipeline entry point
│   └── run_all_sites.py              # Multi-site runner
│
├── tools/                            # Helper Scripts
│   ├── blog_manager.py               # Blog post management
│   └── ...                           # Various utility scripts
│
├── ops/                              # Operations
│   ├── deployment/                   # Deployment scripts
│   ├── audits/                       # Audit scripts
│   └── results/                      # Deployment results
│
├── docs/                             # Documentation
│   ├── sites/                        # Site-specific docs
│   ├── deployment/                   # Deployment docs
│   ├── consolidation/                # Consolidation history
│   └── ...                           # Other documentation
│
├── runtime/                          # Runtime State Files
│   └── autoblogger_state_*.json      # Autoblogger state files
│
├── tests/                            # Test Files
│
├── wordpress-plugins/                 # Shared WordPress Plugins
│
├── tbow_bot/                         # TBOW Bot System
│
├── temp/                             # Temporary Files (gitignored)
│   └── root/                         # Root-level temp files
│
└── archive/                          # Legacy/Archive (if needed)
    └── FreeRideInvestor/             # Legacy monolithic install (optional)
```

## 🎯 Key Directories Explained

### `config/` - Configuration SSOT
**Purpose**: Single source of truth for all configuration  
**Contents**:
- `site_configs.json` - Site deployment credentials and settings
- `sites_registry.json` - Site registry and metadata
- `analytics_ids.json` - Analytics tracking IDs
- `voice_profiles/` - Autoblogger voice profile definitions

**Usage**: All scripts should reference `config/` (not `configs/`)

### `sites/` - Autoblogger Site Configs
**Purpose**: YAML configuration files for autoblogger pipeline  
**Contents**: One YAML file per site (e.g., `dadudekc.yaml`, `corey.yaml`)  
**Note**: This is NOT for website code - that's in `websites/`

### `websites/` - Canonical Site Hub
**Purpose**: All website code, themes, plugins, and overlays  
**Structure**:
```
websites/<domain>/
├── wp/                              # WordPress structure
│   └── wp-content/
│       ├── themes/                  # WordPress themes
│       └── plugins/                 # WordPress plugins
├── overlays/                        # Generated overlays/snippets
│   ├── seo/                         # SEO overlays
│   ├── ux/                          # UX overlays
│   └── wp/                          # WordPress overlays
├── docs/                            # Site-specific documentation
├── blog/                            # Blog content
└── SITE_INFO.md                     # Site metadata
```

### `content/` - Content SSOT
**Purpose**: Single source of truth for all content  
**Structure**:
- `voices/` - Voice profile definitions
- `brands/` - Brand definitions
- `backlogs/` - Content backlogs
- `calendars/` - Content calendars
- `drafts/` - Generated drafts (by autoblogger)
- `blog_posts/` - Published blog posts

### `src/` - Source Code Packages
**Purpose**: Python packages and reusable code  
**Structure**:
- `src/autoblogger/` - Autoblogger package (SSOT)
- `src/autoblogger/ssot/` - SSOT assets
- `src/tbow_tactics/` - TBOW tactics package

## 🔄 Workflows

### Adding a New Site
1. Add site config to `config/site_configs.json`
2. Add site to `config/sites_registry.json`
3. Create `sites/<site-id>.yaml` for autoblogger
4. Create `websites/<domain>/` directory structure
5. Add theme/plugins to `websites/<domain>/wp/wp-content/`

### Deploying Changes
1. Make changes in `websites/<domain>/`
2. Use deployment scripts in `ops/deployment/`
3. Overlays are generated to `websites/<domain>/overlays/`

### Running Autoblogger
```bash
# Daily pipeline
python3 -m autoblogger.run_daily --site <site-id>

# All sites
python3 -m autoblogger.run_all_sites
```

## 📋 File Naming Conventions

- **Config files**: `*.json`, `*.yaml`
- **Site configs**: `sites/<site-id>.yaml`
- **Themes**: `websites/<domain>/wp/wp-content/themes/<theme-name>/`
- **Plugins**: `websites/<domain>/wp/wp-content/plugins/<plugin-slug>/`
- **Overlays**: `websites/<domain>/overlays/<type>/`

## 🚫 What NOT to Do

- ❌ Don't put website code in `sites/` (that's for YAML configs only)
- ❌ Don't put configs in root or scattered locations (use `config/`)
- ❌ Don't create duplicate site directories (use `websites/`)
- ❌ Don't put temp files in root (use `temp/`)
- ❌ Don't reference old `configs/` path (use `config/`)

## ✅ Best Practices

- ✅ Keep all site code in `websites/<domain>/`
- ✅ Use `config/` for all configuration
- ✅ Generate overlays to `websites/<domain>/overlays/`
- ✅ Keep autoblogger configs in `sites/*.yaml`
- ✅ Use `src/autoblogger/` for autoblogger code
- ✅ Document site-specific info in `websites/<domain>/docs/`

## 📊 Directory Sizes (Approximate)

- `websites/` - ~15MB (site code)
- `FreeRideInvestor/` - 309MB (legacy, consider archiving)
- `tools/` - ~3.6MB
- `docs/` - ~3.5MB
- `src/` - ~448KB
- `config/` - ~244KB

## 🔗 Related Documentation

- `README.md` - Main project documentation
- `websites/README.md` - Sites directory documentation
- `sites/README.md` - Autoblogger configs documentation
- `ORGANIZATION_PLAN.md` - Organization plan
- `ORGANIZATION_VERIFICATION.md` - Implementation verification

## 🎯 Migration Status

- ✅ Config directories consolidated
- ✅ Sites directory cleaned (YAML only)
- ✅ Websites overlays organized
- ✅ Autoblogger consolidated
- ✅ Temp files cleaned
- ✅ Legacy directories moved (except FreeRideInvestor)
- ⚠️ FreeRideInvestor/ - Recommend archiving (see LEGACY_DIRECTORIES_RECOMMENDATIONS.md)

---

**Maintained by**: Project Organization System  
**Last Review**: 2025-01-01

