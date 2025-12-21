# Operations Directory

**Purpose:** Operations tools, deployment automation, and site management utilities

## Directory Structure

```
ops/
├── deployment/          # WordPress deployment tools
│   ├── auto_deploy_hook.py
│   ├── deploy_all_websites.py
│   ├── deploy_website_fixes.py
│   ├── check_wordpress_updates.py
│   ├── check_wordpress_versions.py
│   ├── wordpress_version_checker.py
│   └── verify_website_fixes.py
└── site-overlays/       # Generated snippets used by automation (future migration from sites/)
```

## Deployment Tools

### auto_deploy_hook.py
Auto-deployment hook script triggered by pre-commit hooks. Detects changed files and deploys them to appropriate WordPress sites.

**Usage:**
- Automatically triggered on git commit
- Can be run manually: `python ops/deployment/auto_deploy_hook.py --auto-deploy`

### deploy_all_websites.py
Deploy changes to all websites in the registry.

### deploy_website_fixes.py
Deploy website fixes and updates.

### WordPress Update Tools
- `check_wordpress_updates.py` - Check for WordPress core updates
- `check_wordpress_versions.py` - Check WordPress versions across sites
- `wordpress_version_checker.py` - Version checking utility

### Verification Tools
- `verify_website_fixes.py` - Verify deployed fixes

## Integration

These tools integrate with:
- `configs/sites_registry.json` - Site registry
- `websites/<domain>/` - Canonical site locations
- Legacy paths (during transition period)

## Notes

- Tools in `tools/` directory are maintained for backward compatibility
- New tools should be added to `ops/deployment/`
- Eventually, all deployment tools will be consolidated here

