# Project Organization Plan

## Current Issues Identified

1. **Duplicate Site Directories**
   - `sites/` - Contains overlays and deployment snippets
   - `websites/` - Canonical navigation hub for WordPress themes/plugins
   - Root-level site directories (legacy): `FreeRideInvestor/`, `Swarm_website/`, `southwestsecret.com/`

2. **Multiple Autoblogger Implementations**
   - `autoblogger/` - Legacy?
   - `src/autoblogger/` - Python package (preferred)
   - `ssot_autoblogger/` - SSOT implementation

3. **Config Duplication**
   - `config/` - Contains analytics_ids.json and voice_profiles
   - `configs/` - Contains site_configs.json and sites_registry.json

4. **Root-Level Clutter**
   - Temporary files (`temp_*.md`)
   - Large legacy directories (`FreeRideInvestor/` - 309MB)
   - Site directories that should be in `websites/`

5. **Deployment Scattered**
   - `deploy/` - Deployment results
   - `ops/deployment/` - Deployment scripts

## Proposed Structure

```
website/
├── README.md                          # Main documentation
├── .gitignore                        # Git ignore rules
│
├── sites/                             # ALL website code (consolidated)
│   ├── <domain>/                      # Per-site structure
│   │   ├── wp/                        # WordPress structure (if WP site)
│   │   │   └── wp-content/
│   │   │       ├── themes/
│   │   │       └── plugins/
│   │   ├── static/                    # Static files (if non-WP)
│   │   ├── docs/                      # Site-specific documentation
│   │   └── SITE_INFO.md              # Site metadata
│   └── README.md                      # Sites directory documentation
│
├── config/                            # ALL configuration (consolidated)
│   ├── site_configs.json              # Site configurations
│   ├── sites_registry.json            # Site registry
│   ├── analytics_ids.json            # Analytics configuration
│   └── voice_profiles/                # Voice profiles
│
├── content/                           # Content SSOT (keep as-is)
│   ├── voices/
│   ├── brands/
│   ├── backlogs/
│   ├── calendars/
│   ├── drafts/
│   └── blog_posts/
│
├── src/                               # Source code packages
│   ├── autoblogger/                   # Autoblogger package (consolidate here)
│   └── tbow_tactics/                  # TBOW tactics package
│
├── tools/                             # Helper scripts (keep as-is)
│
├── ops/                               # Operations
│   ├── deployment/                    # Deployment scripts
│   ├── audits/                        # Audit scripts
│   └── results/                       # Deployment results (move from deploy/)
│
├── docs/                              # Documentation (consolidate)
│   ├── sites/                         # Site-specific docs (move from root)
│   ├── deployment/                    # Deployment docs
│   ├── consolidation/                 # Keep consolidation history
│   └── ...                           # Other docs
│
├── runtime/                           # Runtime state files
│
├── tests/                            # Test files
│
├── wordpress-plugins/                 # Shared WordPress plugins
│
├── tbow_bot/                         # TBOW bot system
│
└── temp/                             # Temporary files (gitignored)
```

## Migration Steps

### Phase 1: Consolidate Config Directories
- [ ] Merge `config/` into `configs/` OR rename `configs/` to `config/`
- [ ] Update all references to config paths

### Phase 2: Consolidate Site Directories
- [ ] Move root-level site dirs to `sites/` or `websites/` (standardize on one)
- [ ] Consolidate `sites/` and `websites/` into single `sites/` directory
- [ ] Update deployment scripts and references

### Phase 3: Consolidate Autoblogger
- [ ] Determine which autoblogger is canonical
- [ ] Move/merge other implementations
- [ ] Update imports and references

### Phase 4: Clean Root Directory
- [ ] Move temp files to `temp/`
- [ ] Move legacy `FreeRideInvestor/` to `sites/freerideinvestor.com/` or archive
- [ ] Clean up root-level markdown files

### Phase 5: Organize Documentation
- [ ] Consolidate docs into logical structure
- [ ] Move site-specific docs to `docs/sites/`
- [ ] Create clear documentation index

### Phase 6: Update References
- [ ] Update all scripts to use new paths
- [ ] Update README.md
- [ ] Update .gitignore
- [ ] Test deployment scripts

## Notes

- Preserve git history where possible (use `git mv` instead of `mv`)
- Update all scripts that reference old paths
- Test after each phase
- Keep backup/rollback plan

